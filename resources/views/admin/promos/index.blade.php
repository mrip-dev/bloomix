@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Code')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Usage')</th>
                                <th>@lang('Start Date')</th>
                                <th>@lang('End Date')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promos as $promo)
                            <tr>
                                <td><span class="fw-bold">{{ $promo->code }}</span></td>
                                <td>{{ showAmount($promo->discount_value) }}</td>
                                <td>{{ ucfirst($promo->discount_type) }}</td>
                                <td>{{ $promo->used_count }} / {{ $promo->usage_limit ?? '∞' }}</td>
                                <td>{{ $promo->start_date ? showDateTime($promo->start_date, 'd M, Y') : '-' }}</td>
                                <td>{{ $promo->end_date ? showDateTime($promo->end_date, 'd M, Y') : '-' }}</td>
                                <td>
                                    @php
                                    $status = ($promo->isValid()) ? 'Active' : 'Expired';
                                    $class = ($status == 'Active') ? 'success' : 'danger';
                                    @endphp
                                    <span class="badge badge--{{ $class }}">{{ $status }}</span>
                                </td>
                                <td>
                                    <div class="button-group">
                                        <a href="{{ route('admin.promo.view', $promo->id) }}"
                                            class="btn btn-sm btn--primary">
                                            <i class="la la-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.promo.delete', $promo->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline--danger" type="submit"
                                                title="@lang('Delete')">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No promo codes found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($promos->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($promos) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<x-search-form placeholder="Search by code or type" />

<a class="btn btn-sm btn-outline--primary" href="{{ route('admin.promo.create') }}">
    <i class="las la-plus"></i>@lang('Add New')
</a>


@endpush
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Attach listener to all delete buttons
$('form.d-inline').on('submit', function(e) {
    e.preventDefault(); // stop form from submitting

    let form = this;

    Swal.fire({
        title: "Are you sure?",
        text: "This promo code will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it",
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // now submit
        }
    });
});
</script>
@endpush