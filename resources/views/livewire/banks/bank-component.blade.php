<div>
    <div class="d-flex mb-30 flex-wrap justify-content-end align-items-center">
        <div class="d-flex justify-content-end align-items-center gap-3 w-100">

            <x-search-form />
            @permit('admin.product.unit.store')
            <button type="button" wire:click="newEntry" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Bank')">
                <i class="las la-plus"></i>@lang('Add New')
            </button>

            @endpermit
        </div>

    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--dark">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Name</th>
                                    <th>Account #</th>
                                    <th>Account Holder</th>
                                    <th>Iban</th>
                                    <th>Raast Id</th>
                                    <th>Current Balance</th>
                                    <th>Action(s)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($banks as $bank)
                                <tr>
                                    <td>{{ $loop->iteration  }}</td>
                                    <td>{{ $bank->name  }}</td>
                                    <td>{{ $bank->account_number}}</td>
                                    <td>{{ $bank->account_holder  }}</td>
                                    <td>{{ $bank->iban  }}</td>
                                    <td>{{ $bank->raast_id  }}</td>
                                    <td>{{ $bank->current_balance  }}</td>
                                    <td>
                                        @permit('admin.bank.edit')
                                        <button class="btn btn-sm btn-outline--primary editBtn cuModalBtn" wire:click.prevent="editEntry({{$bank->id}})" data-modal_title="@lang('Edit Bank')" type="button">
                                            <i class="la la-pencil"></i>
                                        </button>
                                        <button wire:click="deleteEntry({{ $bank->id }})"
                                            onclick="confirm('Are you sure to delete this bank?') || event.stopImmediatePropagation()"
                                            class="btn btn-danger btn-sm">
                                            <i class="la la-trash"></i>
                                        </button>
                                        @endpermit
                                        <a href="{{ route('admin.bank.detail', $bank->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye"></i>Transactions
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table><!-- table end -->
                    </div>
                    @if ($banks->hasPages())
                    <div class="card-footer d-flex justify-content-center py-4">
                        @php echo paginateLinks($banks) @endphp
                    </div>
                    @endif
                </div>
            </div><!-- card end -->
        </div>
    </div>


    <!--Create & Update Modal -->
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg--primary text-white">
                    <h5 class="modal-title text-center w-100 text-white"><span class="type"></span> <span class="text-white">Manage Bank</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveEntry">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" wire:model.live='bank.name' class="form-control">
                                    @error('bank.name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account #</label>
                                    <input type="text" wire:model.live='bank.account_number' class="form-control">
                                    @error('bank.account_number')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Holder</label>
                                    <input type="text" wire:model.live='bank.account_holder' class="form-control">
                                    @error('bank.account_holder')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Iban</label>
                                    <input type="text" wire:model.live='bank.iban' class="form-control">
                                    @error('bank.iban')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Raast Id</label>
                                    <input type="text" wire:model.live='bank.raast_id' class="form-control">
                                    @error('bank.raast_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Opening Balance</label>
                                    <input type="text" wire:model.live='bank.opening_balance' class="form-control">
                                    @error('bank.opening_balance')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    @permit('admin.product.unit.store')
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>

</div>