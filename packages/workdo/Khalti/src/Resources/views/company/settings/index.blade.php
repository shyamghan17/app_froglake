<div class="card" id="khalti-sidenav">
    {{ Form::open(['route' => ['khalti.setting.store'], 'enctype' => 'multipart/form-data', 'id' => 'payment-form']) }}

    <div class="card-header p-3">
        <div class="row align-items-center">
            <div class="col-sm-10 col-9">
                <h5 class="">{{ __('Khalti') }}</h5>
                <small>{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>
            </div>
            <div class="col-sm-2 col-3 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="khalti_payment_is_on" class="form-check-input input-primary"
                        id="khalti_payment_is_on"
                        {{ isset($settings['khalti_payment_is_on']) && $settings['khalti_payment_is_on'] == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="khalti_payment_is_on"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="khalti_public_key" class="form-label">{{ __('Public Key') }}</label>
                    <input type="text" name="khalti_public_key" id="khalti_public_key"
                        class="form-control"
                        value="{{ !isset($settings['khalti_public_key']) || is_null($settings['khalti_public_key']) ? '' : $settings['khalti_public_key'] }}"
                        placeholder="{{ __('Public Key') }}"{{ isset($settings['khalti_payment_is_on']) && $settings['khalti_payment_is_on'] == 'on' ? '' : ' disabled' }}>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="khalti_secret_key" class="form-label">{{ __('Secret Key') }}</label>
                    <input type="text" name="khalti_secret_key" id="khalti_secret_key"
                        class="form-control"
                        value="{{ !isset($settings['khalti_secret_key']) || is_null($settings['khalti_secret_key']) ? '' : $settings['khalti_secret_key'] }}"
                        placeholder="{{ __('Secret Key') }}"
                        {{ isset($settings['khalti_payment_is_on']) && $settings['khalti_payment_is_on'] == 'on' ? '' : ' disabled' }}>
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
        $(document).on('click', '#khalti_payment_is_on', function() {
            if ($('#khalti_payment_is_on').prop('checked')) {
                $("#khalti_public_key").removeAttr("disabled");
                $("#khalti_secret_key").removeAttr("disabled");
            } else {
                $('#khalti_public_key').attr("disabled", "disabled");
                $('#khalti_secret_key').attr("disabled", "disabled");
            }
        });
    </script>
