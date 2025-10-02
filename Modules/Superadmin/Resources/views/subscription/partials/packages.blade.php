{{-- PERSONNALIZE CUSTOM_CODE 24092024-001 ==> PRICE SWICTH  MENSUEL, ANUEL, A VIE --}}
@php
    $count = 0;
    $packages_popular = [];
    $packages_months = [];
    $packages_years = [];
    $packages_live = [];

    foreach ($packages as $key => $package) {
        if($package->mark_package_as_popular == 1){
            $packages_popular[] = $package;
        }
        elseif ($package->interval == 'months' || $package->interval == 'days') {
            $packages_months[] = $package;
        } elseif ($package->interval == 'years' && $package->interval_count == 1) {
            $packages_years[] = $package;
        } else {
            $packages_live[] = $package;
        }
    }
@endphp


<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_0" data-toggle="tab" aria-expanded="true">**Licence <span class="badge bg-green">
            @lang('superadmin::lang.popular')
        </span></a></li>
        <li class=""><a href="#tab_1" data-toggle="tab" aria-expanded="false">**Licence MENSUELLE</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">**Licence ANNUELLE</a></li>
        <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">**Licence A VIE</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_0">
            <div class="row">

                @foreach ($packages_popular as $package)
                    @if ($package->interval == 'months' || $package->interval == 'days')
                        @if ($package->is_private == 1 && !auth()->user()->can('superadmin'))
                            @php
                                continue;
                            @endphp
                        @endif

                        @php
                            $businesses_ids = json_decode($package->businesses);
                        @endphp

                        @if (Route::current()->getName() == 'subscription.index' &&
                                ((is_array($businesses_ids) && in_array(auth()->user()->business_id, $businesses_ids)) ||
                                    is_null($package->businesses)))
                            @php
                                $count++;
                            @endphp
                            @include('superadmin::subscription.partials.package_card')
                        @elseif(Route::current()->getName() == 'pricing' && is_null($package->businesses))
                            @php
                                $count++;
                            @endphp
                            @include('superadmin::subscription.partials.package_card')
                        @endif
                    @endif
                @endforeach

            </div>
        </div>

        <div class="tab-pane " id="tab_1">
            <div class="row">

                @foreach ($packages_months as $package)
                    @if ($package->interval == 'months' || $package->interval == 'days')
                        @if ($package->is_private == 1 && !auth()->user()->can('superadmin'))
                            @php
                                continue;
                            @endphp
                        @endif

                        @php
                            $businesses_ids = json_decode($package->businesses);
                        @endphp

                        @if (Route::current()->getName() == 'subscription.index' &&
                                ((is_array($businesses_ids) && in_array(auth()->user()->business_id, $businesses_ids)) ||
                                    is_null($package->businesses)))
                            @php
                                $count++;
                            @endphp
                            @include('superadmin::subscription.partials.package_card')
                        @elseif(Route::current()->getName() == 'pricing' && is_null($package->businesses))
                            @php
                                $count++;
                            @endphp
                            @include('superadmin::subscription.partials.package_card')
                        @endif
                    @endif
                @endforeach

            </div>
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_2">
            <div class="row">
                <div class="col-md-12">
                    <h4>Avez-vous marre de subir de coupure chaque 30 jours.
                        Souscrivez à une licence annuelle dès aujourd'jui et profitez de plusieurs avantages.</h4>

                    <i class="fa fa-check text-success"></i>Obtenez une resolution plus rapide de vos problemes<br>
                    <i class="fa fa-check text-success"></i>Surveillance active des dossiers et erreurs
                    d'utilisation.<br>
                    <i class="fa fa-check text-success"></i>Support technique amélioré.<br>
                    <i class="fa fa-check text-success"></i>Mobile Money gratuit (MPESA,ORANGE,AIRTEL & AFRICEL
                    MONEY)<br>
                    <i class="fa fa-check text-success"></i>Bénéficier des nouvelles fonctionnalités<br>
                    <br><br>
                    <i class="fa fa-check text-success"></i>Droit de reclammer une reduction , <a
                        href="https://wa.me/+243812558314?text=Bonjour%20H360,%20Je%20veux%20souscrire%20%C3%A0%20une%20licence%20annuelle,%20puis-je%20obtenir%20un%20coupon%20de%20r%C3%A9duction%20?"
                        target="_blank"> cliquez Ici pour obtenir un coupon(Validité 3jours).</a><br><br><br><br>

                    @php
                        $count = 0;
                    @endphp
                    @foreach ($packages_years as $package)
                        @if ($package->interval == 'years' && $package->interval_count == 1)
                            @if ($package->is_private == 1 && !auth()->user()->can('superadmin'))
                                @php
                                    continue;
                                @endphp
                            @endif

                            @php
                                $businesses_ids = json_decode($package->businesses);
                            @endphp

                            @if (Route::current()->getName() == 'subscription.index' &&
                                    ((is_array($businesses_ids) && in_array(auth()->user()->business_id, $businesses_ids)) ||
                                        is_null($package->businesses)))
                                @php
                                    $count++;
                                @endphp
                                @include('superadmin::subscription.partials.package_card')
                            @elseif(Route::current()->getName() == 'pricing' && is_null($package->businesses))
                                @php
                                    $count++;
                                @endphp
                                @include('superadmin::subscription.partials.package_card')
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
            <br>
        </div>

        <div class="tab-pane" id="tab_3">
            <div class="row">
                <div class="col-md-12">
                    <h4>De jour au jour, nous ajoutions des nouvelles fonctionnalités pour rendre à gestion de votre entreprise plus rapide et stable ; Profitez de cet abonnement d’une durée de 50ans en payant que la somme de 3 premières années.</h4>

                    <i class="fa fa-check text-success"></i>Obtenez une resolution plus rapide de vos problemes<br>
                    <i class="fa fa-check text-success"></i>Surveillance active des dossiers et erreurs
                    d'utilisation.<br>
                    <i class="fa fa-check text-success"></i>Support technique amélioré.<br>
                    <i class="fa fa-check text-success"></i>Utilisez fonctionnalités de maniere illimitée<br>
                    <i class="fa fa-check text-success"></i>Espace de stockae illimité<br>
                    <i class="fa fa-check text-success"></i>Mobile Money gratuit (MPESA,ORANGE,AIRTEL & AFRICEL
                    MONEY)<br>
                    <i class="fa fa-check text-success"></i>Bénéficier des nouvelles fonctionnalités<br>
                    <br><br>
                    <i class="fa fa-check text-success"></i>Droit de reclammer une reduction , <a
                        href="https://wa.me/+243812558314?text=Bonjour%20H360,%20Je%20veux%20souscrire%20%C3%A0%20une%20licence%20annuelle,%20puis-je%20obtenir%20un%20coupon%20de%20r%C3%A9duction%20?"
                        target="_blank"> cliquez Ici pour obtenir un coupon(Validité 3jours).</a><br><br><br><br>

                    @php
                        $count = 0;
                    @endphp
                    @foreach ($packages_live as $package)
                        @if ($package->interval == 'years' && $package->interval_count > 1)
                            @if ($package->is_private == 1 && !auth()->user()->can('superadmin'))
                                @php
                                    continue;
                                @endphp
                            @endif

                            @php
                                $businesses_ids = json_decode($package->businesses);
                            @endphp

                            @if (Route::current()->getName() == 'subscription.index' &&
                                    ((is_array($businesses_ids) && in_array(auth()->user()->business_id, $businesses_ids)) ||
                                        is_null($package->businesses)))
                                @php
                                    $count++;
                                @endphp
                                @include('superadmin::subscription.partials.package_card')
                            @elseif(Route::current()->getName() == 'pricing' && is_null($package->businesses))
                                @php
                                    $count++;
                                @endphp
                                @include('superadmin::subscription.partials.package_card')
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
            <br>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>

{{-- ------------------------------------------------------------------------------- --}}
{{-- }}
@php
	$count = 0;
@endphp




@foreach ($packages as $package)
	@if ($package->is_private == 1 && !auth()->user()->can('superadmin'))
		@php
			continue;
		@endphp
	@endif

	@php
		$businesses_ids = json_decode($package->businesses);
	@endphp

	@if (Route::current()->getName() == 'subscription.index' && ((is_array($businesses_ids) && in_array(auth()->user()->business_id, $businesses_ids)) || is_null($package->businesses)))
		@php
			$count++;
		@endphp
		@include('superadmin::subscription.partials.package_card')
	@elseif(Route::current()->getName() == 'pricing' && is_null($package->businesses))
		@php
			$count++;
		@endphp
		@include('superadmin::subscription.partials.package_card')
	@endif
	
@endforeach
--}}
