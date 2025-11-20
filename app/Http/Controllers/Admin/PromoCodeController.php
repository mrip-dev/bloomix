<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $pageTitle = 'Promo Codes';

        $promos = PromoCode::query()
            ->when(request()->search, function ($q) {
                $search = request()->search;
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('discount_type', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        $pdfButton = true;
        $routePDF  = route('admin.promo.pdf') . "?";
        $routeCSV  = route('admin.promo.csv') . "?";

        if (request()->search) {
            $routePDF .= "search=" . request()->search . "&";
            $routeCSV .= "search=" . request()->search . "&";
        }

        return view('admin.promos.index', compact('pageTitle', 'promos', 'pdfButton', 'routePDF', 'routeCSV'));
    }

    public function create()
    {
        $pageTitle  = 'New Promo';

        return view('admin.promos.form', compact('pageTitle'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:promo_codes,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $promo = PromoCode::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'usage_limit' => $request->usage_limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Promo code created successfully',
            'data' => $promo
        ]);
    }
    public function edit($id)
    {
        $promo = PromoCode::findOrFail($id);
        $pageTitle = 'Edit Promo Code';

        return view('admin.promos.form', compact('pageTitle', 'promo'));
    }
    public function show($id)
    {
        // Find the promo or fail with 404
        $promo = PromoCode::findOrFail($id);

        // Page title (optional)
        $pageTitle = "";

        // Return the show view with the promo
        return view('admin.promos.show', compact('promo', 'pageTitle'));
    }

   public function destroy($id)
{
    $promo = PromoCode::findOrFail($id);
    $promo->delete();

    return back()->with('success', 'Promo code deleted successfully.');
}

}
