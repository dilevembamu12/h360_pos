@extends('hospital::layouts.master') {{-- Étend le layout de base du module Hospital --}}

@section('content')
    <h1>Accueil Réception</h1>

    <p>Bienvenue sur le panneau d'accueil et de gestion de la réception.</p>

    <div class="card mb-4"> {{-- Utilisation de classes Bootstrap si disponibles, sinon ajuster --}}
        <div class="card-header">
            <h2>Services et Enregistrements Rapides</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="list-group">
                        {{-- Lien pour prendre rendez-vous --}}
                        <a href="{{ route('hospital.reception.appointments.create') }}" class="list-group-item list-group-item-action">
                            Prendre rendez-vous avec un docteur
                        </a>
                        {{-- Lien pour le check-in OPD --}}
                        <a href="{{ route('hospital.reception.opd.checkin') }}" class="list-group-item list-group-item-action">
                            Enregistrement Patient OPD (Consultation Externe)
                        </a>
                        {{-- Lien pour l'admission IPD --}}
                        <a href="{{ route('hospital.reception.ipd.admission') }}" class="list-group-item list-group-item-action">
                            Admission Patient IPD (Hospitalisation)
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="list-group">
                        {{-- Lien pour demander un examen de Pathologie --}}
                        <a href="{{ route('hospital.reception.service.request', ['service_type' => 'pathology']) }}" class="list-group-item list-group-item-action">
                            Demander un Examen de Pathologie
                        </a>
                        {{-- Lien pour demander un examen de Radiologie --}}
                        <a href="{{ route('hospital.reception.service.request', ['service_type' => 'radiology']) }}" class="list-group-item list-group-item-action">
                            Demander un Examen de Radiologie
                        </a>
                         {{-- Lien pour les services de Dentisterie --}}
                        <a href="{{ route('hospital.reception.service.request', ['service_type' => 'dentistry']) }}" class="list-group-item list-group-item-action">
                           Services de Dentisterie
                        </a>
                        {{-- Lien vers une page pour d'autres services --}}
                         <a href="{{ route('hospital.reception.other_services') }}" class="list-group-item list-group-item-action">
                           Autres services utiles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section pour afficher la file d'attente --}}
    <div class="card">
        <div class="card-header">
            <h2>File d'Attente Principale <button class="btn btn-sm btn-secondary float-right" onclick="refreshQueue()">Actualiser</button></h2> {{-- Bouton d'actualisation --}}
        </div>
        <div class="card-body">
            <div id="main-queue-display">
                {{-- Inclure la vue partielle pour la file d'attente --}}
                @include('hospital::reception.partials.queue_display', ['mainQueue' => $mainQueue ?? null]) {{-- Passer la variable de la file d'attente si elle existe --}}
            </div>
        </div>
    </div>

    {{-- Lien vers la gestion de la facturation --}}
    <div class="mt-4 text-center">
         <a href="{{ route('hospital.billing.index') }}" class="btn btn-primary btn-lg">
            Accéder à la Gestion de la Facturation
         </a>
    </div>

    @push('scripts') {{-- Section pour ajouter des scripts spécifiques si nécessaire --}}
    <script>
        // Fonction JavaScript pour actualiser la file d'attente via AJAX (exemple)
        function refreshQueue() {
            console.log('Actualisation de la file d\'attente...');
            // Ici, vous feriez une requête AJAX pour récupérer les données actualisées de la file d'attente
            // et mettre à jour le contenu de l'élément #main-queue-display
            // Exemple très basique (sans gestion d'erreur et sans remplacer l'inclusion blade initiale) :
            // fetch("{{ route('hospital.reception.queue.data') }}") // Assurez-vous que cette route existe et retourne les données JSON de la file
            //     .then(response => response.text())
            //     .then(html => {
            //         document.getElementById('main-queue-display').innerHTML = html; // Si la route retourne du HTML
            //     })
            //     .catch(error => {
            //         console.error('Erreur lors de l\'actualisation:', error);
            //     });
             alert('La file d\'attente est en cours d\'actualisation...'); // Message temporaire
        }
    </script>
    @endpush

@endsection