<label for="pay_with_khalti"
        class="block border border-gray-200 rounded-lg p-4 hover:border-primary cursor-pointer transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-white border">
                    <img src="{{ get_module_img('Khalti') }}" alt="Khalti"  class="object-contain w-full h-full" />
                </div>
                <div>
                    <h5 class="text-base font-medium text-gray-800">{{ Module_Alias_Name('Khalti') }}</h5>
                </div>
            </div>
            <input type="radio" name="payment_method" id="pay_with_khalti"
                class="text-primary focus:ring-0 focus:outline-none payment_method"
                data-payment-action="{{ route('beauty.spa.pay.with.khalti',$slug) }}" />
        </div>
</label>
<script src="{{ asset('packages/workdo/Khalti/src/Resources/assets/khalti-checkout.iffe.js') }}"></script>
<script>
    $(document).ready(function() {

        $(document).on("click", "#submitBtn", function(event) {
            if ($('#pay_with_khalti').prop('checked')) {
                event.preventDefault();
                get_khalti_status();
            }
        })

        function get_khalti_status()
        {
            var service = $('#service').val();
            var person = $('#person').val();
            $.ajax({
                url: "{{ route('beauty.spa.pay.with.khalti',$slug) }}",
                method: 'POST',
                data : {
                        'service' : service,
                        'person' : person,
                        '_token': "{{ csrf_token() }}",
                },

                beforeSend: function () {
                    $(".loader-wrapper").removeClass('d-none');
                },
                success: function (data) {
                    $(".loader-wrapper").addClass('d-none');
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
                            url: "{{ route('beauty.spa.khalti.status',$slug) }}",
                            method: 'POST',
                            data :{
                                'payload':payload,
                                'name' : $('#name').val(),
                                'service' : $('#service_id').val(),
                                'person' : $('#person').val(),
                                'date' : $('#date').val(),
                                'number' : $('#number').val(),
                                'email' : $('#email').val(),
                                'start_time' : $('#start_time').val(),
                                'end_time' : $('#end_time').val(),
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
                                $('#append_div').empty();
                                    $('.error_msg').empty();
                                $('.error_msg').html('Payment has been Fail.');
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
