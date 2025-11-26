@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 card--primary">
            <div class="card-header bg--primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title text-light mb-0">{{ $pageTitle }}</h5>
                <div>
                    <!-- <a href="{{ route('admin.order.invoice', $sale->id) }}" class="btn btn--light me-2">
                        <i class="la la-download"></i> Download Invoice
                    </a> -->
                    <a href="{{ route('admin.order.index') }}" class="btn btn--dark">
                        <i class="la la-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Order Information --}}
                <div class="section-header mb-3 border-bottom pb-2">
                    <h6 class="text--primary fw-bold">Order Information</h6>
                </div>
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Invoice No:</strong>
                        <span>{{ $sale->invoice_no }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Date:</strong>
                        <span>{{ showDateTime($sale->sale_date, 'd M, Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $sale->status === 'delivered' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </li>
                </ul>

                {{-- Customer Information --}}
                <div class="section-header mb-3 border-bottom pb-2">
                    <h6 class="text--primary fw-bold">Customer Information</h6>
                </div>
                <ul class="list-group mb-4">

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>First Name:</strong>
                        <span>{{ $sale->customer_name ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Last Name:</strong>
                        <span>{{ $sale->customer_last_name ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Email:</strong>
                        <span>{{ $sale->email ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Phone:</strong>
                        <span>{{ $sale->customer_phone ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Address:</strong>
                        <span>{{ $sale->customer_address ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Apartment:</strong>
                        <span>{{ $sale->apartment ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>City:</strong>
                        <span>{{ $sale->city ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Postal Code:</strong>
                        <span>{{ $sale->postal_code ?? 'N/A' }}</span>
                    </li>

                </ul>


                {{-- Product Table --}}
                <div class="section-header mb-3 border-bottom pb-2">
                    <h6 class="text--primary fw-bold">Products</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table--light style--two">
                        <thead class="bg--primary text-white">
                            <tr>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                <td>{{ $detail->product->brand->name ?? 'N/A' }}</td>
                                <td>{{ $detail->product->category->name ?? 'N/A' }}</td>
                                <td>{{ $detail->product->unit->name ?? 'N/A' }}</td>
                                <td>{{ showAmount($detail->price) }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ showAmount($detail->total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Notes & Summary --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="section-header mb-2 border-bottom pb-2">
                            <h6 class="text--primary fw-bold">Notes</h6>
                        </div>
                        <p>{{ $sale->note ?? 'No additional notes.' }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="section-header mb-2 border-bottom pb-2">
                            <h6 class="text--primary fw-bold">Summary</h6>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Subtotal:</strong>
                                <span>{{ showAmount($sale->total_price) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Discount:</strong>
                                <span>{{ showAmount($sale->discount_amount) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Receivable:</strong>
                                <span>{{ showAmount($sale->receivable_amount) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Received:</strong>
                                <span>{{ showAmount($sale->received_amount) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Due:</strong>
                                <span>{{ showAmount($sale->due_amount) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg--light text-end">
                <!-- <a href="{{ route('admin.order.invoice', $sale->id) }}" class="btn btn--primary">
                    <i class="la la-download"></i> Download Invoice
                </a> -->
                <a href="{{ route('admin.order.index') }}" class="btn btn--dark">
                    <i class="la la-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection