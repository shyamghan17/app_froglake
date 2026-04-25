<div class="payment-method">
    <div class="payment-title d-flex align-items-center justify-content-between">
        <h4>{{ __('Esewa') }}</h4>
        <div class="payment-image d-flex align-items-center">
            <img src="{{ $module->image }}" alt="">
        </div>

    </div>
    <p>{{ __('Pay your order using the most known and secure platform for online money transfers. You will be redirected to esewa to finish complete your purchase.') }}</p>
    <form action="{{ route('course.pay.with.esewa', $store->slug) }}" role="form" method="post"
        class="payment-method-form" id="payment-form">
        @csrf
        <div class="form-group text-right">
            <button type="submit" class="btn">{{ __('Pay Now') }}</button>
        </div>
    </form>
</div>
