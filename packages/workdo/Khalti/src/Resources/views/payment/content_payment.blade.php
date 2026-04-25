<div class="payment-method">
    <div class="payment-title d-flex align-items-center justify-content-between">
        <h2 class="h5">{{ __('Khalti') }}</h2>
        <div class="payment-image">
            <img src="{{ get_module_img('Khalti') }}" alt="">
        </div>
    </div>
    <p>{{ __('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Khalti to finish complete your purchase.') }}
    </p>
    {{-- <form action="{{ route('content.pay.with.khalti', $store->slug) }}" role="form" method="post"
        class="payment-method-form" id="payment-form"> --}}
        @csrf
        <div class="pay-btn text-right">
            <button class="btn" type="submit" id="pay_with_khalti">{{ __('Pay Now') }}</button>
        </div>
    {{-- </form> --}}
</div>

<script src="{{ asset('packages/workdo/Khalti/src/Resources/assets/khalti-checkout.iffe.js') }}"></script>
<script>

    var config = {
        "publicKey": "{{ isset($company_settings['khalti_public_key']) ? $company_settings['khalti_public_key'] : '' }}",
        "productIdentity": "1234567890",
        "productName": "demo",
        "productUrl": "{{env('APP_URL')}}",
        "paymentPreference": [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess (payload) {
                if(payload.status==200) {
                    $.ajaxSetup({
                            headers: {
                                'X-CSRF-Token': '{{csrf_token()}}'
                            }
                        });
                    $.ajax({
                        url: "{{ route('content.pay.with.khalti',$store->slug) }}",
                        method: 'POST',
                        data :{
                            'payload':payload,
                            'coupon_id' : $('.hidden_coupon').attr('data_id'),
                        },
                        beforeSend: function () {
                            $(".loader-wrapper").removeClass('d-none');
                        },
                        success: function(data) {
                            console.log(data);

                            $(".loader-wrapper").addClass('d-none');
                            if(data.status_code === 200){
                                window.location.href = data.store_complete;
                                show_toastr('Success','Payment Done Successfully', 'success');
                                // setTimeout(() => {
                                //     location.reload();
                                // }, 100);
                            }
                            else{
                                show_toastr('Error','Payment Failed', 'error');
                            }
                        },
                        error: function(err) {
                            show_toastr('Error', err.response, 'error')
                        },
                    });
                }
            },
            onError (error) {
                show_toastr('Error', error, 'error')
            },
            onClose () {
            }
        }

    };

    var checkout = new KhaltiCheckout(config);
        var btn = document.getElementById("pay_with_khalti");
        btn.onclick = function () {
            let price =  $('.total_price').attr('data-value')*100;
            checkout.show({amount: price});

    }
</script>
