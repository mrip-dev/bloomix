<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Mail\OrderNotification;
use App\Models\Action;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\PromoCode;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
   public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id'           => 'nullable|exists:customers,id',
        'customer_name'         => 'required|string|max:255',
        'customer_last_name'    => 'nullable|string|max:255',
        'email'                 => 'required|email|max:255',

        'customer_phone'        => 'required|string|max:20',
        'customer_address'      => 'required|string|max:500',
        'apartment'             => 'nullable|string|max:255',
        'city'                  => 'nullable|string|max:255',
        'postal_code'           => 'nullable|string|max:20',

        'status'                => 'nullable',

        'products'              => 'required|array|min:1',
        'products.*.product_id' => 'required|integer|exists:products,id',
        'products.*.quantity'   => 'required|integer|min:1',

        'discount'              => 'nullable|numeric|min:0',

        'promo_code'            => 'nullable|string|max:50',

        'note'                  => 'nullable|string|max:1500',
    ]);

    $customerId = $validated['customer_id'] ?? 1;

    $products = collect($validated['products']);
    $productModels = Product::whereIn('id', $products->pluck('product_id'))->get()->keyBy('id');

    $totalPrice = 0;
    foreach ($products as $item) {
        $product = $productModels->get($item['product_id']);

        // UPDATED PRICE CALCULATION (sale subtract)
        $price = $product->sale ? ($product->selling_price - $product->sale) : $product->selling_price;

        $totalPrice += $price * $item['quantity'];
    }

    $discount = $validated['discount'] ?? 0;
    $promoDiscount = 0;

    if (!empty($validated['promo_code'])) {
        $promo = PromoCode::where('code', strtoupper($validated['promo_code']))
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();

        if ($promo && $promo->isValid()) {
            if (is_null($promo->usage_limit) || $promo->used_count < $promo->usage_limit) {
                $appliedPromo = $promo;

                if ($promo->discount_type === 'percentage') {
                    $promoDiscount = ($totalPrice * ($promo->discount_value / 100));
                } else {
                    $promoDiscount = $promo->discount_value;
                }

                $promoDiscount = min($promoDiscount, $totalPrice);

                $promo->increment('used_count');
            }
        }
    }

    $lastSale = Sale::latest()->first();
    $lastInvoiceNo = $lastSale?->invoice_no;
    $invoiceNo = generateInvoiceNumber($lastInvoiceNo);

    $finalDiscount = $discount + $promoDiscount;

    $receivable = $totalPrice - $finalDiscount;
    $receivedAmount = 0;
    $dueAmount = $receivable - $receivedAmount;

    $sale = Sale::create([
        'invoice_no'        => $invoiceNo,
        'customer_id'       => $customerId,

        'customer_name'        => $validated['customer_name'],
        'customer_last_name'   => $validated['customer_last_name'] ?? null,
        'email'                => $validated['email'] ?? null,

        'customer_phone'    => $validated['customer_phone'],
        'customer_address'  => $validated['customer_address'],
        'apartment'         => $validated['apartment'] ?? null,
        'city'              => $validated['city'] ?? null,
        'postal_code'       => $validated['postal_code'] ?? null,

        'warehouse_id'      => Warehouse::first()->id ?? 1,
        'sale_date'         => now(),
        'status'            => $validated['status'] ?? 'pending',

        'total_price'       => $totalPrice,
        'discount_amount'   => $finalDiscount,
        'receivable_amount' => $receivable,
        'received_amount'   => $receivedAmount,
        'due_amount'        => $dueAmount,

        'note'              => $validated['note'] ?? null,
    ]);

    foreach ($products as $item) {
        $product = $productModels->get($item['product_id']);

        // UPDATED PRICE CALCULATION (sale subtract)
        $price = $product->sale ? ($product->selling_price - $product->sale) : $product->selling_price;

        SaleDetails::create([
            'sale_id'    => $sale->id,
            'product_id' => $product->id,
            'quantity'   => $item['quantity'],
            'price'      => $price,
            'total'      => $price * $item['quantity'],
        ]);
    }

    Mail::to($validated['email'])->send(new OrderNotification($sale, 'customer'));

    return response()->json([
        'success' => true,
        'message' => 'Order created successfully.',
        'data'    => [
            'order_id'   => $sale->id,
            'invoice_no' => $sale->invoice_no,

            'customer' => [
                'id'         => $sale->customer_id,
                'name'       => $sale->customer_name,
                'last_name'  => $sale->customer_last_name,
                'email'      => $sale->email,
                'phone'      => $sale->customer_phone,
                'address'    => $sale->customer_address,
                'apartment'  => $sale->apartment,
                'city'       => $sale->city,
                'postal'     => $sale->postal_code,
            ],

            'status'     => $sale->status,
            'total'      => $sale->total_price,
            'discount'   => $sale->discount_amount,
            'final'      => $sale->receivable_amount,

            'details'    => $sale->saleDetails()
                ->with('product:id,name,sku,selling_price')
                ->get(),
        ]
    ], 201);
}

}
