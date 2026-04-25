<div class="col-sm-12 col-lg-6 col-md-6">
    <div class="card">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="theme-avtar">
                        <img src="{{ get_module_img('Khalti') }}" alt="" class="img-user" style="max-width: 100%">
                    </div>
                    <div class="ms-3">
                        <label for="khalti-payment">
                            <h5 class="mb-0 text-capitalize pointer">{{ Module_Alias_Name('Khalti') }}</h5>
                        </label>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input payment_method" name="payment_method" id="khalti-payment"
                        type="radio" data-payment-action="{{ route('plan.pay.with.khalti') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('packages/workdo/Khalti/src/Resources/assets/khalti-checkout.iffe.js') }}"></script>


<script>

    var config = {
        "publicKey": "{{ isset($admin_settings['khalti_public_key']) ? $admin_settings['khalti_public_key'] : '' }}",
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
                        url: '{{ route('plan.get.khalti.status') }}',
                        method: 'POST',
                        data : {
                            'payload' : payload,
                            'user_module' : $('.user_module_input').val(),
                            'duration' : $('.time_period_input').val(),
                            'user_counter_input' : $('.user_counter_input').val(),
                            'workspace_counter_input' : $('.workspace_counter_input').val(),
                            'coupon_code' : $('.coupon_code').val(),
                            'plan_id' : $('.plan_id').val(),
                        },
                        beforeSend: function () {
                            $(".loader-wrapper").removeClass('d-none');
                        },
                        success: function(data) {
                            $(".loader-wrapper").addClass('d-none');
                            if(data.status_code === 200){
                                toastrs('Success','Payment Done Successfully', 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
                            else{
                                toastrs('Error','Payment Failed', 'error');
                            }
                        },
                        error: function(err) {
                            toastrs('Error', err.response, 'error')
                        },
                    });
                }
            },
            onError (error) {
                toastrs('Error', error, 'error')
            },
            onClose () {
            }
        }

    };

    var checkout = new KhaltiCheckout(config);
    var btn = document.getElementsByClassName("payment-btn")[0];
</script>

<script>
      $(document).on("click", ".payment-btn", function(event) {
    console.log('dasfd');

        if ($('#khalti-payment').prop('checked')) {
            event.preventDefault()
            get_khalti_status();
        }
    })

    function get_khalti_status(){
        var user_module_input = $('.user_module_input').val();
        var time_period = $('.time_period_input').val();
        var user_counter_input = $('.user_counter_input').val();
        var workspace_counter_input = $('.workspace_counter_input').val();
        var coupon_code = $('.coupon_code').val();
        var plan_id = $('.plan_id').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('plan.pay.with.khalti') }}',
            method: 'POST',
            data : {
                'user_module_input' : user_module_input,
                'time_period' : time_period,
                'user_counter_input' : user_counter_input,
                'workspace_counter_input' : workspace_counter_input,
                'coupon_code' : coupon_code,
                'plan_id' : plan_id,
            },

            beforeSend: function () {
                $(".loader-wrapper").removeClass('d-none');
            },
            success: function (data) {
                $(".loader-wrapper").addClass('d-none');
                if(data == 0)
                {
                    toastrs('Success','Payment Done Successfully', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
                else
                {
                    let price = data*100;
                    checkout.show({amount: price});
                }
            }
        });
    }
</script>
