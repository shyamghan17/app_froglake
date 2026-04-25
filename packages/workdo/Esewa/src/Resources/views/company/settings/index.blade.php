<div class="card" id="esewa-payment">
    {{ Form::open(['route' => ['esewa.setting.store'], 'enctype' => 'multipart/form-data', 'id' => 'payment-form']) }}

    <div class="card-header p-3">
        <div class="row align-items-center">
            <div class="col-sm-10 col-9">
                <h5 class="">{{ __('Esewa') }}</h5>
                <small>{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>
            </div>
            <div class="col-sm-2 col-3 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="esewa_payment_is_on" class="form-check-input input-primary"
                        id="esewa_payment_is_on"
                        {{ isset($settings['esewa_payment_is_on']) && $settings['esewa_payment_is_on'] == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="esewa_payment_is_on"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-xxl-4 col-sm-6">
                <div class="card">
                    <div class="card-header p-3">
                        <h6 class="paypal-label mb-0">{{ __('Esewa Mode') }}</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="form-check pointer">
                                <label class="form-check-labe text-dark pointer">
                                    <input type="radio" name="esewa_mode" value="Sandbox" class="form-check-input"
                                        {{ !isset($settings['esewa_mode']) || $settings['esewa_mode'] == 'Sandbox' ? 'checked="checked"' : '' }}>
                                    {{ __('Sandbox') }}
                                </label>
                            </div>
                            <div class="form-check pointer">
                                <label class="form-check-labe text-dark pointer">
                                    <input type="radio" name="esewa_mode" value="Live" class="form-check-input"
                                        {{ isset($settings['esewa_mode']) && $settings['esewa_mode'] == 'Live' ? 'checked="checked"' : '' }}>
                                    {{ __('Live') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="form-group">
                    <label for="esewa_merchant_id" class="col-form-label">{{ __('Merchant Id') }}</label>
                    <input type="text" name="esewa_merchant_id" id="esewa_merchant_id" class="form-control"
                        value="{{ !isset($settings['esewa_merchant_id']) || is_null($settings['esewa_merchant_id']) ? '' : $settings['esewa_merchant_id'] }}"
                        placeholder="{{ __('Merchant Id') }}"
                        {{ isset($settings['esewa_payment_is_on']) && $settings['esewa_payment_is_on'] == 'on' ? '' : ' disabled' }}>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="form-group">
                    <label for="esewa_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                    <input type="text" name="esewa_secret_key" id="esewa_secret_key" class="form-control"
                        value="{{ !isset($settings['esewa_secret_key']) || is_null($settings['esewa_secret_key']) ? '' : $settings['esewa_secret_key'] }}"
                        placeholder="{{ __('Secret Key') }}"
                        {{ isset($settings['esewa_payment_is_on']) && $settings['esewa_payment_is_on'] == 'on' ? '' : ' disabled' }}>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end p-3">
        <input class="btn btn-print-invoice btn-primary" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}

</div>

<script>
    $(document).on('click', '#esewa_payment_is_on', function() {
        if ($('#esewa_payment_is_on').prop('checked')) {
            $("#esewa_merchant_id").removeAttr("disabled");
            $("#esewa_secret_key").removeAttr("disabled");
        } else {
            $('#esewa_merchant_id').attr("disabled", "disabled");
            $('#esewa_secret_key').attr("disabled", "disabled");
        }
    });
</script>
