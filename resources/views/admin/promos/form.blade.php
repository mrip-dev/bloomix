@extends('admin.layouts.app')

@section('panel')
<div class="row" id="promoApp">
    <div class="col-lg-6 offset-lg-3 mb-30">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@lang('Create Promo Code')</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitPromo">
                    @csrf

                    <div class="form-group mb-3">
                        <label>@lang('Promo Code') <span class="text--danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="promo.code"
                            placeholder="@lang('Enter promo code')"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label>@lang('Discount Type') <span class="text--danger">*</span></label>
                        <select class="form-control" v-model="promo.discount_type" required>
                            <option value="">@lang('Select Type')</option>
                            <option value="percentage">@lang('Percentage (%)')</option>
                            <option value="fixed">@lang('Fixed Amount')</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>@lang('Discount Value') <span class="text--danger">*</span></label>
                        <input
                            type="number"
                            class="form-control"
                            v-model.number="promo.discount_value"
                            step="0.01"
                            min="0"
                            placeholder="@lang('Enter discount value')"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label>@lang('Usage Limit')</label>
                        <input
                            type="number"
                            class="form-control"
                            v-model.number="promo.usage_limit"
                            min="1"
                            placeholder="@lang('Max usage count')">
                        <small class="text-muted">@lang('Leave empty for unlimited usage')</small>
                    </div>

                    <div class="form-group mb-3">
                        <label>@lang('Start Date')</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="promo.start_date">
                    </div>

                    <div class="form-group mb-3">
                        <label>@lang('End Date')</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="promo.end_date">
                    </div>

                    <button
                        type="submit"
                        class="btn btn--primary w-100"
                        :disabled="isSubmitting">
                        <span v-if="isSubmitting">
                            <i class="las la-spinner la-spin"></i> @lang('Saving...')
                        </span>
                        <span class="text-light" v-else>@lang('Create Promo Code')</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.js"></script>
<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                promo: {
                    code: '',
                    discount_type: '',
                    discount_value: 0,
                    usage_limit: null,
                    start_date: '',
                    end_date: ''
                },
                isSubmitting: false
            }
        },
        methods: {
            submitPromo() {
                if (!this.promo.code || !this.promo.discount_type || this.promo.discount_value <= 0) {
                    this.showNotification('error', 'Please fill all required fields.');
                    return;
                }

                this.isSubmitting = true;

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('code', this.promo.code);
                formData.append('discount_type', this.promo.discount_type);
                formData.append('discount_value', this.promo.discount_value);
                if (this.promo.usage_limit) formData.append('usage_limit', this.promo.usage_limit);
                if (this.promo.start_date) formData.append('start_date', this.promo.start_date);
                if (this.promo.end_date) formData.append('end_date', this.promo.end_date);

                fetch('{{ route("admin.promo.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    this.isSubmitting = false;
                    if (data.status === 'success') {
                        this.showNotification('success', data.message);
                        // Reset form
                        this.promo = { code: '', discount_type: '', discount_value: 0, usage_limit: null, start_date: '', end_date: '' };
                    } else {
                        this.showNotification('error', data.message || 'Something went wrong.');
                    }
                })
                .catch(err => {
                    this.isSubmitting = false;
                    console.error(err);
                    this.showNotification('error', 'An error occurred while saving promo code.');
                });
            },
            showNotification(type, message) {
                if (typeof iziToast !== 'undefined') {
                    iziToast[type]({ message: message, position: 'topRight' });
                } else {
                    alert(message);
                }
            }
        }
    }).mount('#promoApp');
</script>
@endpush
