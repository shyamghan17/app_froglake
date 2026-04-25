
<div class="tab-pane fade " id="khalti-payment" role="tabpanel"
    aria-labelledby="khalti-payment">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="amount">{{ __('Amount') }}</label>
                <div class="input-group">
                    <span class="input-group-prepend"><span
                            class="input-group-text">{{ isset($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : '$' }}</span></span>
                    <input class="form-control amount" required="required"
                        min="0" name="amount" type="number"
                        value="{{ \Workdo\GymManagement\Entities\GymMember::getDue($assignmembershipplan->fee,$user->id) }}" min="0"
                        step="0.01" max="{{ \Workdo\GymManagement\Entities\GymMember::getDue($assignmembershipplan->fee,$user->id) }}"
                        id="amount">
                    <input type="hidden" value="{{ $assignmembershipplan->id }}" name="membershipplan_id" id="membershipplan_id">
                    <input type="hidden" value="" name="price" id="price">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="error" style="display: none;">
                    <div class='alert-danger alert'>
                        {{ __('Please correct the errors and try again.') }}</div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-secondary me-1"
                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button class="btn btn-primary"
                type="submit" id="pay_with_khalti">{{ __('Make Payment') }}</button>
        </div>
</div>

<script src="{{ asset('packages/workdo/Khalti/src/Resources/assets/khalti-checkout.iffe.js') }}"></script>

<script>
    // Handle the click event for the .khalti link
    $(document).on("click", ".khalti", function(event) {
        event.preventDefault();
        var amount = $('.amount').val();
        $('#price').val(amount);
    });

    $(document).on("change", ".amount", function(event) {
        var amount = $(this).val();
        $('#price').val(amount);
    });
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
                        url: "{{ route('memberplan.pay.with.khalti') }}",
                        method: 'POST',
                        data : {
                            'payload' : payload,
                            'membershipplan_id' : $('#membershipplan_id').val(),
                            'price' : $('#price').val(),
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
                                }, 100);
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
        var btn = document.getElementById("pay_with_khalti");
        btn.onclick = function () {
            let price =  $('#price').val()*100;
            checkout.show({amount: price});

    }
</script>



