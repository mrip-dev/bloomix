@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--dark style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email') | @lang('Phone')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($messages as $message)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $message->full_name }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $message->email }}</span><br>
                                            {{ $message->phone }}
                                        </td>

                                        <td>
                                            {{ $message->subject }}
                                        </td>

                                        <td>
                                            {{ strLimit($message->message, 40) }}
                                        </td>

                                        <td>
                                            {{ showDateTime($message->created_at) }}
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.messages.view', $message->id) }}"
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="la la-eye"></i> @lang('View')
                                                </a>

                                                <form id="delete-form-{{ $message->id }}"
                                                      action="{{ route('admin.messages.delete', $message->id) }}"
                                                      method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $message->id }})">
                                                    <i class="la la-trash"></i> @lang('Delete')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($messages->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($messages) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    {{-- Search --}}
    <x-search-form />
@endpush

@push('script')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This message will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
