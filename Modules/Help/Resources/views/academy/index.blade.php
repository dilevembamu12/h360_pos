@extends('layouts.app')
@section('title', 'Académie H360')

@section('content')
<section class="content-header">
    <h1>Académie H360 <small>Votre centre de formation et de réussite</small></h1>
</section>

<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#videos_tab" data-toggle="tab" aria-expanded="true">
                    <i class="fas fa-video"></i> Tutoriels Vidéo
                </a>
            </li>
            <li>
                <a href="#checklist_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fas fa-tasks"></i> Mon Parcours de Réussite
                </a>
            </li>
            <li>
                <a href="#onboarding_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fas fa-route"></i> Tours Guidés
                </a>
            </li>

            {{-- NOUVEAU : Onglet visible uniquement par les administrateurs --}}
            @if(auth()->user()->can('superadmin'))
            <li class="pull-right bg-purple">
                <a href="#admin_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fas fa-cogs"></i> Administration
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            {{-- Onglet pour tous les utilisateurs --}}
            <div class="tab-pane active" id="videos_tab">
                @include('help::academy.partials.videos_tab')
            </div>

            {{-- Onglet pour tous les utilisateurs --}}
            <div class="tab-pane" id="checklist_tab">
                @include('help::academy.partials.checklist_tab')
            </div>

            {{-- Onglet pour tous les utilisateurs --}}
            <div class="tab-pane" id="onboarding_tab">
                @include('help::academy.partials.onboarding_tab')
            </div>

            {{-- NOUVEAU : Contenu de l'onglet Administration --}}
            @if(auth()->user()->can('superadmin'))
            <div class="tab-pane" id="admin_tab">
                @include('help::academy.partials.admin_tab')
            </div>
            @endif
        </div>
    </div>
</section>
@endsection