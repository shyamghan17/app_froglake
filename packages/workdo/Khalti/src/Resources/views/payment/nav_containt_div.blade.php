<div class="tab-pane fade" id="khalti-payment" role="tabpanel" aria-labelledby="khalti-payment">
        @csrf
        @if($type == 'invoice')
            <input type="hidden" name="type" value="invoice" class="type">
        @elseif ($type == 'retainer')
            <input type="hidden" name="type" value="retainer" class="type">
        @endif
        <div class="row">
            <div class="form-group col-md-12">
                <label for="amount">{{ __('Amount') }}</label>
                <div class="input-group">
                    <span class="input-group-prepend"><span
                            class="input-group-text">{{ !empty($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : '$' }}</span>
                    </span>
                    <input class="form-control amount" required="required" min="0"
                        name="amount" type="number"
                        value="{{ $invoice->getDue() }}" min="0"
                        step="0.01" max="{{ $invoice->getDue() }}"
                        id="amount">
                    <input type="hidden" value="{{ $invoice->id }}"
                        name="invoice_id" id="invoice_id">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="error" style="display: none;">
                    <div class='alert-danger alert'>
                        {{ __('Please correct the errors and try again.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-secondary me-1"
                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button class="btn btn-primary"
                type="submit"  id="pay_with_khalti">{{ __('Make Payment') }}</button>
        </div>
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
                        url: '{{ route('invoice.khalti') }}',
                        method: 'POST',
                        data : {
                            'payload' : payload,
                            'invoice_id' : $('#invoice_id').val(),
                            'amount' : $('.amount').val(),
                            'type' : $('.type').val(),
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

    $(document).on('click', '#pay_with_khalti', function () {
        var account = "{{$account}}";
        if(account == '')
        {
            toastrs('Error', '{{ __("Bank account not connected with Khalti.") }}', 'error')
            return;
        }        
        var checkout = new KhaltiCheckout(config);
        var btn = document.getElementById("pay_with_khalti");
            let price =  $('.amount').val()*100;
            checkout.show({amount: price});
    });
</script>

