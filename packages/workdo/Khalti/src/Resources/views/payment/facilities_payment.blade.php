<div class="col-sm-12 col-lg-12 col-md-12">
    <div class="card">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="theme-avtar ">
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
                        type="radio" data-payment-action="{{ route('facilities.pay.with.khalti',$slug) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('packages/workdo/Khalti/src/Resources/assets/khalti-checkout.iffe.js') }}"></script>

<script>
    $(document).ready(function() {
        $(document).on("click", "#submitBtn", function(event) {
            if ($('#khalti-payment').prop('checked')) {
                event.preventDefault();
                get_khalti_status();
            }
        })

        function get_khalti_status()
        {
            var service = $('[name=service_id]').val();
            var price = $('.price').val();
            $.ajax({
                url: '{{ route('facilities.pay.with.khalti',$slug) }}',
                method: 'POST',
                data : {
                        'price' : price,
                        'service' : service,
                        '_token': "{{ csrf_token() }}",
                },
                success: function (data) {
                    if(data == 0)
                    {
                        $('#append_div').empty();
                        $('.error_msg').empty();
                        $('.error_msg').html('Payment has been success.');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                    else
                    {
                        let price = data.price*100;
                        checkout.show({amount: price});
                    }
                }
            });
        }

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
                            url: "{{ route('facilities.khalti.status',$slug) }}",
                            method: 'POST',
                            data :{
                                'payload':payload,
                                'name' : $('#name').val(),
                                'service' :$('[name=service_id]').val(),
                                'person' : $('#person').val(),
                                'price' : $('.price').val(),
                                'date' : $('#date').val(),
                                'number' : $('#number').val(),
                                'email' : $('#email').val(),
                                'start_time' : $('.start-time-input').val(),
                                'end_time' : $('.end-time-input').val(),
                                'gender' : $('#gender').val(),
                                'payment_option' : $('input[name="payment_option"]:checked').val(),
                            },
                            success: function(data) {
                                if(data.status_code === 200){
                                    $('#append_div').empty();
                                    $('.error_msg').empty();
                                    $('.error_msg').html('Payment has been success.');
                                }
                                else{
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                    $('.error_msg').html('Payment has been Fail.');
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
        var btn = document.getElementById("submitBtn")[0];
    });
</script>
