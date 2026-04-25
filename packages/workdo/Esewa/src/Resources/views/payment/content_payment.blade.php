<div class="payment-method">
    <div class="payment-title d-flex align-items-center justify-content-between">
        <h2 class="h5">{{ __('Esewa') }}</h2>
        <div class="payment-image">
            <img src="{{ $module->image }}" alt="">
        </div>

    </div>
    <p>{{ __('Pay your order using the most known and secure platform for online money transfers. You will be redirected to esewa to finish complete your purchase.') }}</p>
    <form action="{{ route('content.pay.with.esewa', $store->slug) }}" role="form" method="post"
        class="payment-method-form" id="payment-form">
        @csrf
        <div class="pay-btn text-right">
            <button class="btn" type="submit">{{ __('Pay Now') }}</button>
        </div>
    </form>
</div>
