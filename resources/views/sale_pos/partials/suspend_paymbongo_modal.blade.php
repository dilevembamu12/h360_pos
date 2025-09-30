
<div class="modal fade" tabindex="-1" role="dialog" id="confirmPaymbongoMobileMoneyModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">**Détails de la transaction par MOBILE MONEY</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-xs-12">



                        @if (!empty($pos_settings['flexpay_merchant']) && !empty($pos_settings['flexpay_token']))
                            <div class="form-group">
                                <div class="row tex-center">
                                    <img src="{{ asset('uploads/custom/mobilemoney/africell.png') }}" width="75" />
                                    <img src="{{ asset('uploads/custom/mobilemoney/airtel.png') }}" width="75" />
                                    <img src="{{ asset('uploads/custom/mobilemoney/orange.png') }}" width="75" />
                                    <img src="{{ asset('uploads/custom/mobilemoney/vodacom.png') }}" width="75" />
                                </div>

                                {!! Form::label('mobilemoney_phone', '**Entrez le numéro Mobile money au format (243800000000)') !!}
                                {!! Form::text('mobilemoney_phone', null /* null */, [
                                    'class' => 'form-control',
                                    'placeholder' => "+243800000000",
                                    'id' => 'mobilemoney_phone',
                                    'autofocus',
                                ]) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('additional_notes', __('lang_v1.suspend_note') . ':') !!}
                                {!! Form::textarea(
                                    'additional_notes',
                                    !empty($transaction->additional_notes) ? $transaction->additional_notes : null,
                                    ['class' => 'form-control', 'rows' => '4'],
                                ) !!}
                                {!! Form::hidden('is_suspend', false, ['id' => 'is_suspend']) !!}
                                {!! Form::hidden('get_direct_response', false, ['id' => 'get_direct_response']) !!}

                            </div>
                        @else
                            <h3 style="color: red">
                                Votre entreprise n'a pas encore les access pour recevoir les paiements
                                Mobile Money.
                                Veuillez contacter notre service client pour activer les paiements par MPESA, ORANGE ,
                                AIRTEL et AFRICELL MONEY.
								<a target="_blank"
								href="https://api.whatsapp.com/send?phone=243812558314&text=Bonjour%20H360,%20je%20vais%20passer%20la%20commande%20des%20api%20mobile%20money%20et%20bancaire%20pour%20mon%20entreprise%20({{Session::get('business')->name}})">
								Ecrire sur whatsapp (+243812558314)</a>
                            </h3>
                        @endif






                    </div>
                </div>
            </div>
            @if (!empty($pos_settings['flexpay_merchant']) && !empty($pos_settings['flexpay_token']))
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="pos-mobilemoney">@lang('sale.finalize_payment')</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            @endif

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- ------------------------------------------------------------------------------------------------------------------------------------------- --}}

<div class="modal fade" tabindex="-1" role="dialog" id="confirmPaymbongoCardModal">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.card_transaction_details')</h4>
            </div>
            {{--
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_number', __('lang_v1.card_no')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_no'),
                                    'id' => 'card_number',
                                    'autofocus',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_holder_name', __('lang_v1.card_holder_name')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_holder_name'),
                                    'id' => 'card_holder_name',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_transaction_number', __('lang_v1.card_transaction_no')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_transaction_no'),
                                    'id' => 'card_transaction_number',
                                ]) !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_type', __('lang_v1.card_type')) !!}
                                {!! Form::select('', ['visa' => 'Visa', 'master' => 'MasterCard'], 'visa', [
                                    'class' => 'form-control select2',
                                    'id' => 'card_type',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_month', __('lang_v1.month')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.month'),
                                    'id' => 'card_month',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_year', __('lang_v1.year')) !!}
                                {!! Form::text('', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.year'), 'id' => 'card_year']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_security', __('lang_v1.security_code')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.security_code'),
                                    'id' => 'card_security',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			--}}

            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <h3 style="color: red">

                            @if (!empty($pos_settings['flexpay_merchant']) && !empty($pos_settings['flexpay_token']))
                                Le paiement par carte bancaire n'est pas encore disponible pour le moment, nous
                                travaillons sans relâche
                                pour la rendre opérationnelle dans très bientôt. Merci pour votre compréhension!
                            @else
                                Votre entreprise n'a pas encore les access pour recevoir les paiements
                                bancaires.
                                Veuillez contacter notre service client pour activer les paiements par carte bancaire.
                                <a target="_blank"
                                    href="https://api.whatsapp.com/send?phone=243812558314&text=Bonjour%20H360,%20je%20vais%20passer%20la%20commande%20des%20api%20mobile%20money%20et%20bancaire%20pour%20mon%20entreprise%20({{Session::get('business')->name}})">
                                    Ecrire sur whatsapp (+243812558314)</a>
                            @endif


                        </h3>
                    </div>
                </div>
            </div>
            {{-- 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="pos-save-card">@lang('sale.finalize_payment')</button>
            </div>
			--}}
        </div>
    </div>
</div><!-- /.modal -->
