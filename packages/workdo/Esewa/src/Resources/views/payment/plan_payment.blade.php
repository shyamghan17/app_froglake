<div class="col-sm-12 col-lg-6 col-md-6">
    <div class="card">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="theme-avtar">
                        <img src="{{ get_module_img('Esewa') }}" alt="" class="img-user" style="max-width: 100%">
                    </div>
                    <div class="ms-3">
                        <label for="esewa-payment">
                            <h5 class="mb-0 text-capitalize pointer">{{ Module_Alias_Name('Esewa') }}</h5>
                        </label>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input payment_method" name="payment_method" id="esewa-payment"
                        type="radio" 
                        data-payment-action="{{ route('plan.pay.with.esewa') }}"
                        >
                </div>
            </div>
        </div>
    </div>
</div>
