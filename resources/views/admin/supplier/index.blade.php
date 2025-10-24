@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table--light table">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Book Let')</th>
                                <th>@lang('Mobile | Email')</th>
                                <th>@lang('Opening Balance')</th>
                                <th>@lang('Advance')</th>
                                <th>@lang('Payable')</th>
                                <th>@lang('Receivable')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>
                                    <span class="fw-bold">{{ $supplier->booklet_no }}</span>

                                </td>
                                <td><span class="fw-bold"> {{ $supplier?->mobile }} </span><br> {{ $supplier->email }}
                                </td>
                                <td>{{ number_format($supplier->opening_balance, 2) }}</td>
                                <td>{{ number_format($supplier->advance, 2) }}</td>
                                <td>{{ showAmount($supplier->totalPayableAmount()) }}</td>
                                <td>{{ showAmount($supplier->totalReceivableAmount()) }}</td>

                                <td>
                                    <div class="button--group">
                                        <button class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $supplier }}"
                                            data-modal_title="@lang('Edit Supplier')" type="button">
                                            <i class="la la-pencil"></i>@lang('Edit')
                                        </button>
                                        <form id="delete-form-{{ $supplier->id }}" action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button onclick="confirmDelete({{ $supplier->id }})" class="btn btn-outline-danger btn-sm">
                                            Delete
                                        </button>
                                        @php
                                        $totalPayable = $supplier->totalPayableAmount() - abs($supplier->totalReceivableAmount());
                                        @endphp
                                        @permit('admin.supplier.payment.index')
                                        <a href="{{ route('admin.supplier.payment.index', $supplier->id) }}" @class([ 'btn btn-sm btn-outline--info' , 'disabled'=> $totalPayable == 0,
                                            ])>
                                            <i class="las la-money-bill-wave-alt"></i>@lang('Payment')
                                        </a>
                                        @endpermit
                                        <a href="{{ route('admin.supplier.view', ['id' => $supplier->id]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="la la-eye"></i> @lang('View')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($suppliers->hasPages())
            <div class="card-footer py-4">
                @php echo paginateLinks($suppliers) @endphp
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

<div class="modal fade" id="cuModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.supplier.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" type="text" value="{{ old('name') }}" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">E-mail</label>
                                <input class="form-control " name="email" type="email" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Book Let No.</label>
                                <input type="text" class="form-control " name="booklet_no" value="{{ old('booklet_no') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Mobile')
                                    <i class="fa fa-info-circle text--primary" title="@lang('Type the mobile number including the country code. Otherwise, SMS won\'t send to that number.')">
                                    </i>
                                </label>
                                <input class="form-control " name="mobile" type="number" value="{{ old('mobile') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Company')</label>
                                <input class="form-control" name="company_name" type="text" value="{{ old('company_name') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="opening_balance">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance') }}">
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="advance">Advance</label>
                            <input type="number" step="0.01" name="advance" class="form-control" value="{{ old('advance') }}">
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>@lang('Address')</label>
                                <input class="form-control" name="address" type="text" value="{{ old('address') }}">
                            </div>
                        </div>

                    </div>

                </div>
                @permit('admin.supplier.store')
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
                @endpermit
            </form>
        </div>
    </div>
</div>
{{-- IMPORT MODAL --}}
<div class="modal fade" id="importModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Import supplier')</h4>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="la la-times" aria-hidden="true"></i>
                </button>
            </div>
            <form id="importForm" method="post" action="{{ route('admin.supplier.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <div class="alert alert-warning p-3" role="alert">
                            <p>
                                - @lang('Format your CSV the same way as the sample file below.') <br>
                                - @lang('The number of columns in your CSV should be the same as the example below.')<br>
                                - @lang('Valid fields Tip: make sure name of fields must be following: name, email, mobile,company_name, address')<br>
                                - @lang("Required field's (name, email,mobile), Unique Field's (email, mobile) column cell must not be empty.")<br>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">@lang('Select File')</label>
                        <input class="form-control" name="file" type="file" accept=".csv" required>
                        <div class="mt-1">
                            <small class="d-block">
                                @lang('Supported files:') <b class="fw-bold">@lang('csv')</b>
                            </small>
                            <small>
                                @lang('Download sample template file from here')
                                <a class="text--primary" href="{{ asset('assets/files/sample/supplier.csv') }}" title="@lang('Download csv file')"
                                    download>
                                    <b>@lang('supplier.csv')</b>
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="Submit">@lang('Import')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<x-search-form />
@permit('admin.supplier.store')
<button class="btn btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Supplier')" type="button">
    <i class="la la-plus"></i>@lang('Add New')
</button>
@endpermit
@php
$params = request()->all();
@endphp
@permit(['admin.supplier.pdf', 'admin.supplier.csv', 'admin.supplier.import'])
<div class="btn-group">
    <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
        @lang('Action')
    </button>
    <ul class="dropdown-menu">
        @permit('admin.supplier.pdf')
        <li>
            <a class="dropdown-item" href="{{ route('admin.supplier.pdf', $params) }}"><i class="la la-download"></i>@lang('Download PDF')</a>
        </li>
        @endpermit
        @permit('admin.supplier.csv')
        <li>
            <a class="dropdown-item" href="{{ route('admin.supplier.csv', $params) }}"><i class="la la-download"></i>@lang('Download CSV')</a>
        </li>
        @endpermit
        @permit('admin.supplier.import')
        <li>
            <a class="dropdown-item importBtn" href="javascript:void(0)">
                <i class="las la-cloud-upload-alt"></i> @lang('Import CSV')</a>
        </li>
        @endpermit
    </ul>
</div>
@endpermit
@endpush

@push('script')
<script>
    (function($) {
        "use strict"
        $(".importBtn").on('click', function(e) {
            let importModal = $("#importModal");
            importModal.modal('show');
        });
    })(jQuery);
</script>
@endpush
@push('script')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the hidden delete form
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

@endpush