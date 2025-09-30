@php
    $code = strtolower($system_currency->code);
@endphp
@php
    $code = strtolower($system_currency->code);
@endphp

<div class="width-100 text-center f-left">
    

    {{-- <form action="{{ route('confirm_payment', ['id' => $package->id]) }}" method="POST"
        onsubmit="triggerMobilemoney(this)" id="flexpay_form"> --}} 
        <img src="{{ asset('uploads/custom/mobilemoney/visa.jpg') }}" width="75" /> 
                    <img src="{{ asset('uploads/custom/mobilemoney/mastercard.jpg') }}" width="80" />
                    <img src="{{ asset('uploads/custom/mobilemoney/americaexpress.jpg') }}" width="80" />
                    <img src="{{ asset('uploads/custom/mobilemoney/dinersclub.jpg') }}" width="80" />
                    <br>
                    <br>     
    <form
        action="{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'trigger_flexpay'], [$package->id]) }}"
        method="POST" onsubmit="triggerMobilemoney(this)" id="flexpay_form2">
        {{ csrf_field() }}
        <input type="hidden" name="gateway_type" value="flexpay_bank">
        <input type="hidden" name="gateway" value="flexpay">
        <input type="hidden" name="phone" value="">
        <input type="hidden" name="coupon_code" value="{{ request()->get('code') ?? null }}">

    </form>

    <div onclick="triggerBank(this)">

        @if (!empty($package->second_currency) && !empty($package->second_currency_rate))
            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $second_currency->code }}'">Carte Bancaire
                    ({{ (float) $package->second_currency_rate * (float) $total_payable_formatted }}{{ $second_currency->code }})</span></button>

            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $system_currency->code }}'">Carte Bancaire
                    ({{ $system_currency->code }})</span></button>
        @else
            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $system_currency->code }}'">Carte Bancaire</span></button>
        @endif
    </div>
</div>





<div class="width-100 text-center f-left">
    <br>
<hr>
<br>
    <img src="{{ asset('uploads/custom/mobilemoney/africell.png') }}" width="75" /> 
                    <img src="{{ asset('uploads/custom/mobilemoney/airtel.png') }}" width="75" />
                    <img src="{{ asset('uploads/custom/mobilemoney/orange.png') }}" width="75" />
                    <img src="{{ asset('uploads/custom/mobilemoney/vodacom.png') }}" width="75" />
                    <br>
                    <br>
    <style>
        .swal2-timer-progress-bar {
            background: rgb(233 20 20);
        }

        .flexpay-button-el {
            overflow: hidden;
            display: inline-block;
            visibility: visible !important;
            background-image: -webkit-linear-gradient(#28a0e5, #015e94);
            background-image: -moz-linear-gradient(#28a0e5, #015e94);
            background-image: -ms-linear-gradient(#28a0e5, #015e94);
            background-image: -o-linear-gradient(#28a0e5, #015e94);
            background-image: -webkit-linear-gradient(#28a0e5, #015e94);
            background-image: -moz-linear-gradient(#28a0e5, #015e94);
            background-image: -ms-linear-gradient(#28a0e5, #015e94);
            background-image: -o-linear-gradient(#28a0e5, #015e94);
            background-image: linear-gradient(#28a0e5, #015e94);
            -webkit-font-smoothing: antialiased;
            border: 0;
            padding: 1px;
            text-decoration: none;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            -ms-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            -o-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
            cursor: pointer
        }

        .flexpay-button-el::-moz-focus-inner {
            border: 0;
            padding: 0
        }

        .flexpay-button-el span {
            display: block;
            position: relative;
            padding: 0 12px;
            height: 30px;
            line-height: 30px;
            background: #1275ff;
            background-image: -webkit-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -moz-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -ms-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -o-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -webkit-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -moz-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -ms-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: -o-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            background-image: linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
            font-size: 14px;
            color: #fff;
            font-weight: bold;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
            -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            -ms-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            -o-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -ms-border-radius: 4px;
            -o-border-radius: 4px;
            border-radius: 4px
        }

        .flexpay-button-el:not(:disabled):active,
        .flexpay-button-el.active {
            background: #005d93
        }

        .flexpay-button-el:not(:disabled):active span,
        .flexpay-button-el.active span {
            color: #eee;
            background: #008cdd;
            background-image: -webkit-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -moz-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -ms-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -o-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -webkit-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -moz-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -ms-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: -o-linear-gradient(#008cdd, #008cdd 85%, #239adf);
            background-image: linear-gradient(#008cdd, #008cdd 85%, #239adf);
            -webkit-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            -moz-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            -ms-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            -o-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1)
        }

        .flexpay-button-el:disabled,
        .flexpay-button-el.disabled {
            background: rgba(0, 0, 0, 0.2);
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            -ms-box-shadow: none;
            -o-box-shadow: none;
            box-shadow: none
        }

        .flexpay-button-el:disabled span,
        .flexpay-button-el.disabled span {
            color: #999;
            background: #f8f9fa;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5)
        }
    </style>
    {{-- <form action="{{ route('confirm_payment', ['id' => $package->id]) }}" method="POST"
        onsubmit="triggerMobilemoney(this)" id="flexpay_form"> --}}

    <form
        action="{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'trigger_flexpay'], [$package->id]) }}"
        method="POST" onsubmit="triggerMobilemoney(this)" id="flexpay_form">

        {{ csrf_field() }}
        <input type="hidden" name="gateway" value="flexpay">
        <input type="hidden" name="phone" value="">
        <input type="hidden" name="coupon_code" value="{{ request()->get('code') ?? null }}">












        @if (!empty($package->second_currency) && !empty($package->second_currency_rate))
            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $second_currency->code }}'">Mobile Money
                    ({{ (float) $package->second_currency_rate * (float) $total_payable_formatted }}{{ $second_currency->code }})</span></button>

            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $system_currency->code }}'">Mobile Money
                    ({{ $system_currency->code }})</span></button>
        @else
            <button type="submit" class="flexpay-button-el" style="visibility: visible;"><span
                    style="display: block; min-height: 30px;"
                    onclick="currency_code='{{ $system_currency->code }}'">Mobile Money</span></button>
        @endif
    </form>
</div>

@section('mycustom_js')
    <script>
        session_timer=150000;
        currency_code = '';
        i = 1;

        function triggerMobilemoney(e) {
            e.preventDefault();
        }
        $("#flexpay_form").submit(function(e) {
            session_timer=150000;
            if (i == 0) {
                //$(this).find('[type="submit"]').click();
                return;
            }
            //alert(currency_code);
            //return 222;
            e.preventDefault();
            inputValue = "";
            Swal.fire({
                allowOutsideClick: false,
                title: '<strong>PAIEMENT <u>MOBILE MONEY</u></strong>',
                html: '<img src="{{ asset('uploads/custom/mobilemoney/africell.png') }}" width="75" />' +
                    '<img src="{{ asset('uploads/custom/mobilemoney/airtel.png') }}" width="75" />' +
                    '<img src="{{ asset('uploads/custom/mobilemoney/orange.png') }}" width="75" />' +
                    '<img src="{{ asset('uploads/custom/mobilemoney/vodacom.png') }}" width="75" />',
                input: 'number',
                inputLabel: 'Entrez le numéro Mobile money au format (243800000000)',
                inputValue: "",
                inputPlaceholder: "243812558314",
                inputAttributes: {
                    maxlength: 12
                },
                //inputRequired: true,
                showCancelButton: true,
                inputValidator: (value) => {

                    if (!value) {
                        return 'You need to write something!'
                        e.preventDefault();
                    }

                    $(this).find('[name="phone"]').val(value);


                    let timerInterval2
                    Swal.fire({
                        allowOutsideClick: false,
                        title: 'En attente du serveur...',
                        timer: session_timer,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                            const b = Swal.getHtmlContainer().querySelector('b')
                            timerInterval2 = setInterval(() => {
                                //b.textContent = Swal.getTimerLeft()
                            }, 100)
                        },
                        willClose: () => {
                            clearInterval(timerInterval2)
                            Swal.fire(
                                'Oops',
                                'Délai dépassé',
                                'error'
                            ).then(
                                function() {
                                    window.location.reload();
                                });
                        }
                    }).then((result) => {})



                    $.ajax({
                        url: '{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'trigger_flexpay'], [$package->id]) }}?currency_code=' +
                            currency_code,
                        type: "POST",
                        data: $(this).serialize(),
                        //contentType: "application/json",
                        //dataType: "json",
                        //container: '.modal-content',
                        //messagePosition: "inline",
                        //disableButton: true,
                        //buttonSelector: "#mobilemoney-button",
                        success: function(response, status, xhr) {
                            //alert('ok');
                            console.log(xhr.status);
                            console.log("status", status);
                            console.log('aaa', response);
                            //alert(response.data.message);

                            if (response.data == null) {
                                Swal.fire(
                                    'Oops',
                                    response.msg,
                                    'error'
                                ).then(
                                    function() {
                                        window.location.reload();
                                    });
                                return 1;
                            }

                            //return ;
                            if (response.data.code == 0) {
                                let timerInterval
                                Swal.fire({
                                    allowOutsideClick: false,
                                    title: 'Paiement initié',
                                    html: response.data.message +
                                        '<br><img src="{{ asset('uploads/custom/mobilemoney/load.gif') }}" width="150" />',
                                    timer: 150000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                        const b = Swal.getHtmlContainer()
                                            .querySelector('b')
                                        timerInterval = setInterval(() => {
                                            //b.textContent = Swal.getTimerLeft()
                                        }, 100)
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval);
                                        Swal.fire(
                                            'Oops',
                                            'session expirée',
                                            'error'
                                        ).then(
                                            function() {
                                                window.location.reload();
                                            });
                                    }
                                }).then((result) => {})



                                setInterval(checkMobileMoneyWebhook(response.data
                                    .orderNumber), 2000);
                            } else {




                                if (response.data.msg) {
                                    Swal.fire(
                                        'Oops',
                                        response.data.msg,
                                        'error'
                                    ).then(
                                        function() {
                                            window.location.reload();
                                        });
                                    return false;
                                } else if (response.data) {
                                    Swal.fire(
                                        'Oops',
                                        response.data,
                                        'error'
                                    ).then(
                                        function() {
                                            window.location.reload();
                                        });
                                    return false;
                                }
                                sweetAlert("Oops!",
                                    "Une erreur inconnue est survenue , nous vous recommandons de réessayer dans quelques minutes",
                                    "error").then(
                                    function() {
                                        window.location.reload();
                                    });
                                return false;
                            }
                        }
                    }).catch(
                        error => {
                            console.log(error.responseJSON);
                            Swal.fire(
                                'Oops',
                                error.responseJSON.msg,
                                'error'
                            ).then(
                                function() {
                                    window.location.reload();
                                });


                        }
                    );



                    i = 0;
                    //$(this).submit();
                    //obj.preventDefault();
                }
            })
        });

        function checkMobileMoneyWebhook(ordernumber) {
            //alert(1111111);
            //return;
            //$(this).find('[name="phone"]').val(value);
            $.ajax({
                url: "{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'confirm_flexpaySubscription']) }}",
                type: "POST",
                data: {
                    ordernumber: ordernumber,
                    'gateway': 'checkflexpay',
                    'phone': $('#flexpay_form').find('[name="phone"]').val(),
                    '_token': '{{ csrf_token() }}'
                },
                container: '.modal-content',
                success: function(response) {
                    console.log(response);
                    if (response == "0") {
                        return;
                    } //on fait rien;
                    /*
                    if (response.status == 'success' && response.webhook) {

                    }
                    */


                    if (response.data.code == "0") {
                        if (response.data.transaction) {
                            if (response.data.transaction.status == 0) {

                                Swal.fire('Reussi!', response.msg, 'success').then(
                                    function() {
                                        window.location.href =
                                            "{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index']) }}";
                                    });
                                return;
                            } else if (response.data.transaction.status == 2) {
                                //on fait rien le paiement est en attente
                            } else {

                                try {
                                    Swal.fire('Oops!', response.msg, 'error').then(
                                        function() {
                                            window.location.reload();
                                        });
                                } catch (error) {
                                    Swal.fire('Oops!', 'Désolé le paiement n\'a pas abouti', 'error').then(
                                        function() {
                                            window.location.reload();
                                        });
                                }


                                return;
                            }
                        }
                        //return;
                    }

                    if (response.data.status == "404") {
                        //on fait rien
                        //swal("Oops...", "Opération non reconnu", "error");
                        //return;
                    }
                    checkMobileMoneyWebhook(ordernumber);
                }

            }).catch(
                error => {
                    if (typeof myVariable === "undefined") {
                        checkMobileMoneyWebhook(ordernumber);
                        return;
                    }
                    console.log(error.responseJSON);
                    response = error.responseJSON;
                    if (response.data.transaction.status == 0) {
                        //reussi , il sera geré au niveau de la fonction success d'ajax
                        return;
                    } else if (response.data.transaction.status == 2) {
                        //on fait rien le paiement est en attente
                        checkMobileMoneyWebhook(ordernumber);
                        return;
                    } else {
                        Swal.fire('Oops!', error.responseJSON.msg, 'error').then(
                            function() {
                                window.location.reload();
                            });
                        return;
                    }



                }
            );
        }
    </script>


    <script>
        function triggerBank(e) {
            session_timer=600000;
            //alert(1111);



            if (i == 0) {
                //$(this).find('[type="submit"]').click();
                return;
            }

            let timerInterval2;
            Swal.fire({
                allowOutsideClick: false,
                title: 'En attente du serveur...',
                timer: session_timer,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    timerInterval2 = setInterval(() => {
                        //b.textContent = Swal.getTimerLeft()
                    }, 100)
                },
                willClose: () => {
                    clearInterval(timerInterval2)
                    Swal.fire(
                        'Oops',
                        'Délai dépassé',
                        'error'
                    ).then(
                        function() {
                            window.location.reload();
                        });
                }
            }).then((result) => {})



            $.ajax({
                url: '{{ action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'trigger_flexpay'], [$package->id]) }}?currency_code=' +
                    currency_code,
                type: "POST",
                data: $('#flexpay_form2').serialize(),
                //contentType: "application/json",
                //dataType: "json",
                //container: '.modal-content',
                //messagePosition: "inline",
                //disableButton: true,
                //buttonSelector: "#mobilemoney-button",
                success: function(response, status, xhr) {
                    //alert('ok');
                    console.log(xhr.status);
                    console.log("status", status);
                    console.log('aaa', response);
                    //alert(response.data.message);

                    if (response.data == null) {
                        Swal.fire(
                            'Oops',
                            response.msg,
                            'error'
                        ).then(
                            function() {
                                window.location.reload();
                            });
                        return 1;
                    }

                    //return ;
                    if (response.data.code == 0) {
                        let timerInterval;
                        //alert(response.data.url);
                        Swal.fire({
                            allowOutsideClick: false,
                            title: 'Paiement initié',
                            html: response.data.message +
                                '<br><img src="{{ asset('uploads/custom/mobilemoney/load.gif') }}" width="150" />',
                            timer: session_timer,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer()
                                    .querySelector('b')
                                timerInterval = setInterval(() => {
                                    //b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                                Swal.fire(
                                    'Oops',
                                    'session expirée',
                                    'error'
                                ).then(
                                    function() {
                                        window.location.reload();
                                    });
                            }
                        }).then((result) => {})
                        setInterval(checkMobileMoneyWebhook(response.data
                            .orderNumber), 2000);

                        var newWindow = window.open(response.data.url,
                            "_blank", "width=700, height=500");
                        setTimeout(() => newWindow.close(), 600 * 1000);

                    } else {

                        if (response.data.msg) {
                            Swal.fire(
                                'Oops',
                                response.data.msg,
                                'error'
                            ).then(
                                function() {
                                    window.location.reload();
                                });
                            return false;
                        } else if (response.data) {
                            Swal.fire(
                                'Oops',
                                response.data,
                                'error'
                            ).then(
                                function() {
                                    window.location.reload();
                                });
                            return false;
                        }
                        sweetAlert("Oops!",
                            "Une erreur inconnue est survenue , nous vous recommandons de réessayer dans quelques minutes",
                            "error").then(
                            function() {
                                window.location.reload();
                            });
                        return false;
                    }
                }
            }).catch(
                error => {
                    console.log(error.responseJSON);
                    Swal.fire(
                        'Oops',
                        error.responseJSON.msg,
                        'error'
                    ).then(
                        function() {
                            window.location.reload();
                        });


                }
            );



            i = 0;

            return 12345;
        }
    </script>
@endsection
