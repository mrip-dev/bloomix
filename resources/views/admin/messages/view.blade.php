@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content->
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 message-card">

            <div class="card-header message-card-header landscape-header">
                
                <div class="header-action-left">
                    <a href="{{ route('admin.messages.index') }}" class="btn gradient-btn-sm text-white">
                        <i class="las la-arrow-left"></i> @lang('Go Back')
                    </a>
                </div>

                <h2 class="mb-0 fw-bold gradient-text"><i class="las la-envelope-open-text"></i> @lang('Inquiry Details')</h2>
                
                <div class="header-action-right"></div>
                
            </div>

            <div class="card-body">
                
                <div class="row">
                    
                    <div class="col-md-6 border-end-sm pe-4"> 
                        <div class="info-section">
                            <h5 class="section-title"><i class="las la-user-circle"></i> @lang('Sender Information')</h5>
                            <hr class="section-divider">
                            
                            <div class="row info-row">
                                <div class="col-4 fw-bold text-dark">@lang('Name'):</div>
                                <div class="col-8">{{ $message->full_name }}</div>
                            </div>

                            <div class="row info-row">
                                <div class="col-4 fw-bold text-dark">@lang('Email'):</div>
                                <div class="col-8">{{ $message->email }}</div>
                            </div>

                            <div class="row info-row">
                                <div class="col-4 fw-bold text-dark">@lang('Phone'):</div>
                                <div class="col-8">{{ $message->phone ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row info-row mt-4">
                                <div class="col-4 fw-bold text-dark">@lang('Sent At'):</div>
                                <div class="col-8">{{ showDateTime($message->created_at) }}</div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="col-md-6 ps-4">
                        <div class="info-section">
                            <h5 class="section-title"><i class="las la-comment-dots"></i> @lang('Message Content')</h5>
                            <hr class="section-divider">

                            <div class="row info-row">
                                <div class="col-12 fw-bold text-dark mb-2">@lang('Subject'):</div>
                                <div class="col-12">
                                    <span class="badge gradient-badge">
                                        {{ $message->subject ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div class="row info-row mt-4">
                                <div class="col-12 fw-bold text-dark mb-2">@lang('Message'):</div>
                                <div class="col-12">
                                    <p class="message-bubble message-bubble-landscape">
                                        {{ $message->message }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> </div>

            <div class="card-footer message-card-footer-simple">
                 <p class="mb-0 text-muted text-end">@lang('End of Details')</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Card Styling */
    .message-card {
        border-radius: 15px;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
    }

    /* Card Header for Landscape/Button placement */
    .message-card-header {
        border-radius: 15px 15px 0 0;
        background-color: #fce4f0;
        padding: 1.5rem;
        border-bottom: 2px solid #f660ad;
    }
    
    .landscape-header {
        display: flex;
        justify-content: space-between; /* Space out the button and the title */
        align-items: center;
        text-align: center;
    }
    
    .landscape-header .gradient-text {
        flex-grow: 1; /* Allows the title to center properly */
    }

    /* Simple Footer - no action button */
    .message-card-footer-simple {
        border-radius: 0 0 15px 15px;
        background-color: #f7f7f7;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
    }

    /* Gradient header text */
    .gradient-text {
        font-size: 1.8rem;
        background: linear-gradient(45deg, #000000, #f660ad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
        white-space: nowrap; /* Prevents title from wrapping if space is tight */
    }
    
    /* Small Gradient button for header */
    .gradient-btn-sm {
        background: linear-gradient(90deg, #f660ad, #000000);
        border: none;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .gradient-btn-sm:hover {
        background: linear-gradient(90deg, #000000, #f660ad);
        color: #fff;
        transform: translateY(-1px);
    }


    /* --- Body Content Styles (Kept from previous version) --- */
    
    /* Section Titles */
    .section-title {
        color: #000000;
        font-weight: 700;
        border-left: 5px solid #f660ad;
        padding-left: 10px;
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }
    
    .section-title i {
        color: #f660ad;
        margin-right: 5px;
    }

    .section-divider {
        margin-top: 0.5rem;
        margin-bottom: 1.5rem;
        border-color: rgba(246, 96, 173, 0.3);
    }

    /* Info Rows */
    .info-row {
        margin-bottom: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px dotted #e9e9e9;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    
    /* Divider for landscape view (only visible on medium screens and up) */
    .border-end-sm {
        border-right: 1px solid #e9e9e9;
    }
    @media (max-width: 767.98px) {
        .border-end-sm {
            border-right: none !important;
            border-bottom: 1px solid #e9e9e9; 
            margin-bottom: 1rem;
            padding-bottom: 1rem !important;
        }
        .ps-4 { padding-left: 0.75rem !important; } 
        .pe-4 { padding-right: 0.75rem !important; }
    }


    /* Gradient badge */
    .gradient-badge {
        background: linear-gradient(90deg, #f660ad, #000000);
        color: #fff;
        padding: 0.5em 0.8em;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Message bubble for landscape view */
    .message-bubble-landscape {
        min-height: 200px;
        background-color: #fce4f0;
        color: #000000;
        padding: 1.5rem;
        border-radius: 12px;
        /* white-space: pre-wrap; */
        font-size: 1rem;
        line-height: 1.6;
        border: 1px solid #f660ad;
        box-shadow: inset 0 0 5px rgba(246, 96, 173, 0.3);
        overflow-y: auto;
    }

    /* Large Gradient button (footer is now simple, but keeping this style for reuse) */
    .gradient-btn {
        background: linear-gradient(90deg, #f660ad, #000000);
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    .gradient-btn:hover {
        background: linear-gradient(90deg, #000000, #f660ad);
        color: #fff;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    .fw-bold { font-weight: 700 !important; }
</style>
@endpush