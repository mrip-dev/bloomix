@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="card b-radius--10 card--primary">
            <div class="card-header bg--primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title text-light mb-0">{{ 'Promo Details' }}</h5>
                <div>
                    <a href="{{ route('admin.promo.index') }}" class="btn btn--dark">
                        <i class="la la-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('admin.promo.edit', $promo->id) }}" class="btn btn--light">
                        <i class="la la-edit"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Promo Information --}}
                <div class="section-header mb-3 border-bottom pb-2">
                    <h6 class="text--primary fw-bold">Promo Information</h6>
                </div>
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Code:</strong>
                        <span>{{ $promo->code }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Discount Type:</strong>
                        <span>{{ ucfirst($promo->discount_type) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Discount Value:</strong>
                        <span>
                            @if($promo->discount_type === 'percentage')
                                {{ $promo->discount_value }}%
                            @else
                                {{ $promo->discount_value}}
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Usage Limit:</strong>
                        <span>{{ $promo->usage_limit ?? 'Unlimited' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Start Date:</strong>
                        <span>{{ $promo->start_date ? showDateTime($promo->start_date, 'd M, Y') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>End Date:</strong>
                        <span>{{ $promo->end_date ? showDateTime($promo->end_date, 'd M, Y') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $promo->is_active ? 'success' : 'danger' }}">
                            {{ $promo->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </li>
                </ul>

                {{-- Notes / Description --}}
                @if($promo->note)
                <div class="section-header mb-2 border-bottom pb-2">
                    <h6 class="text--primary fw-bold">Notes / Description</h6>
                </div>
                <p>{{ $promo->note }}</p>
                @endif
            </div>

            <div class="card-footer bg--light text-end">
                <a href="{{ route('admin.promo.index') }}" class="btn btn--dark">
                    <i class="la la-arrow-left"></i> Back to Promo List
                </a>
                <a href="{{ route('admin.promo.edit', $promo->id) }}" class="btn btn--primary">
                    <i class="la la-edit"></i> Edit Promo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
