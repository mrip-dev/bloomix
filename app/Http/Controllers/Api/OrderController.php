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
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
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
            'email'                 => 'nullable|email|max:255',
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
            'note'                  => 'nullable|string|max:1500',
        ]);

        $customerId = $validated['customer_id'] ?? 1;

        $products = collect($validated['products']);
        $productModels = Product::whereIn('id', $products->pluck('product_id'))->get()->keyBy('id');

        $totalPrice = 0;
        foreach ($products as $item) {
            $product = $productModels->get($item['product_id']);
            $totalPrice += $product->selling_price * $item['quantity'];
        }

        $discount = $validated['discount'] ?? 0;
        $lastSale = Sale::latest()->first();
        $lastInvoiceNo = $lastSale?->invoice_no;
        $invoiceNo = generateInvoiceNumber($lastInvoiceNo);

        $receivable = $totalPrice - $discount;
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
            'discount_amount'   => $discount,
            'receivable_amount' => $receivable,
            'received_amount'   => $receivedAmount,
            'due_amount'        => $dueAmount,
            'note'              => $validated['note'] ?? null,
        ]);

        foreach ($products as $item) {
            $product = $productModels->get($item['product_id']);
            $price   = $product->selling_price;

            SaleDetails::create([
                'sale_id'    => $sale->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $price,
                'total'      => $price * $item['quantity'],
            ]);
        }

        // Email to admin
        Mail::to('admin@example.com')->send(new OrderNotification($sale, 'admin'));

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data'    => [
                'order_id'   => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'customer'   => [
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
                'details'    => $sale->saleDetails()->with('product:id,name,sku,selling_price')->get(),
            ]
        ], 201);
    }
}
