@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 12px; background-color: #ffffff;">

            <!-- Header with gradient text -->
            <div class="card-header text-center" style="border-radius:12px 12px 0 0; background-color:#fff;">
                <h5 class="mb-0 fw-bold gradient-text"><i class="la la-envelope"></i> @lang('Message Details')</h5>
            </div>

            <!-- Body -->
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Name'):</div>
                    <div class="col-md-8">{{ $message->full_name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Email'):</div>
                    <div class="col-md-8">{{ $message->email }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Phone'):</div>
                    <div class="col-md-8">{{ $message->phone ?? 'N/A' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Subject'):</div>
                    <div class="col-md-8">
                        <span class="badge gradient-badge">
                            {{ $message->subject ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Message'):</div>
                    <div class="col-md-8">
                        <p class="message-bubble">
                            {{ $message->message }}
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold text-dark">@lang('Sent At'):</div>
                    <div class="col-md-8">{{ showDateTime($message->created_at) }}</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer text-end bg-light">
                <a href="{{ route('admin.messages.index') }}" class="btn gradient-btn text-white">
                    <i class="la la-arrow-left"></i> @lang('Back')
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Gradient header text */
    .gradient-text {
        font-size: 1.3rem;
        background: linear-gradient(90deg, #f660ad, #000000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    /* Gradient badge */
    .gradient-badge {
        background: linear-gradient(50deg, #f660ad, #000000);
        color: #fff;
        padding: 0.4em 0.7em;
        border-radius: 5px;
        font-size: 0.9rem;
    }

    /* Message bubble */
    .message-bubble {
        background-color: #fce4f0; /* light pink background */
        color: #000;
        padding: 1rem;
        border-radius: 10px;
        white-space: pre-wrap;
    }

    /* Gradient button */
    .gradient-btn {
        background: linear-gradient(90deg, #f660ad, #000000);
        border-radius: 8px;
        transition: 0.3s;
    }

    .gradient-btn:hover {
        background: linear-gradient(90deg, #000000, #f660ad);
        color: #fff;
    }

    .fw-bold { font-weight:600; }
</style>
@endpush
