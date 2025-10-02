{{-- File: Modules/Hospital/Resources/views/index.blade.php --}}
@extends('hospital::layouts.master') {{-- Assurez-vous que ce layout existe --}}

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Tableau de Bord de l'Hôpital</h1>
                <p>Bienvenue sur le tableau de bord du module Hospital.</p>

                <div class="card">
                    <div class="card-header">Statistiques Rapides</div>
                    <div class="card-body">
                        <p>Nombre total de patients enregistrés : ** $totalPatients **</p>
                        {{-- Ajoutez ici d'autres statistiques --}}
                    </div>
                </div>

                {{-- Vous pouvez ajouter ici des liens rapides vers d'autres sections --}}
                <div class="mt-4">
                     <a href="{{ route('hospital.patients.create') }}" class="btn btn-primary">Enregistrer un nouveau patient</a>
                     <a href="{{ route('hospital.patients.index') }}" class="btn btn-secondary">Voir la liste des patients</a>
                     {{-- Ajoutez des liens vers d'autres fonctionnalités --}}
                </div>
            </div>
        </div>
    </div>
@endsection