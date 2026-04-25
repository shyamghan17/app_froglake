<div class="single-option">
    <div class="option-input-box">
        <div class="option-inner d-flex">
            <div class="option-icon">
                <img src="{{ get_module_img('Khalti') }}" alt="Payment Logo" class="img-user">
            </div>
            <div>
                <label for="khalti-payment">
                    <p class="mb-0 text-capitalize pointer">{{ Module_Alias_Name('Khalti') }}</p>
                </label>
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input payment_method" name="payment_method" id="khalti-payment"
                type="radio" data-payment-action="{{ route('event.show.booking.pay.with.khalti',[$slug]) }}">
        </div>
    </div>
</div>
<script src="{{ asset('js/jquery.min.js') }}"></script>
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
            var person = $('#person').val();
            var price =  $('[name=price]').val();
            var event =  $('[name=event]').val();
            $.ajax({
                url: "{{ route('event.show.booking.pay.with.khalti',$slug) }}",
                method: 'POST',
                data : {
                        'person' : person,
                        'price' : price,
                        'event' : event,
                        '_token': "{{ csrf_token() }}",
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
                            url: "{{ route('event.show.booking.khalti.status',$slug) }}",
                            method: 'POST',
                            data :{
                                'payload':payload,
                                'event' : $('[name=event]').val(),
                                'date' : $('[name=date]').val(),
                                'start_time' : $('[name=start_time]').val(),
                                'end_time' : $('[name=end_time]').val(),
                                'name' : $('[name=name]').val(),
                                'email' : $('[name=email]').val(),
                                'mobile_number' : $('[name=mobile_number]').val(),
                                'person' : $('[name=person]').val(),
                                'payment_option' : $('input[name="payment_option"]:checked').val(),
                            },
                            beforeSend: function () {
                                $(".loader-wrapper").removeClass('d-none');
                            },
                            success: function(data) {
                                $(".loader-wrapper").addClass('d-none');
                                if(data.status_code === 200){
                                    window.location.href = data.evevt_booking;
                                    show_toastr('Success','Payment Done Successfully', 'success');
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
        console.log(config);
        var btn = document.getElementById("submitBtn")[0];
    });
</script>
