@extends('hospital::layouts.master') {{-- Étend le layout de base --}}

@section('content')
    {{-- Déterminer le titre en fonction du type de service passé en paramètre --}}
    @php
        $serviceType = request('service_type', 'other'); // Récupère le type de service de l'URL
        $pageTitle = 'Demande de Service : ' . ucfirst($serviceType);

        // Mappage des types de service vers des titres plus lisibles
        $titles = [
            'pathology' => 'Demande d\'Examen de Pathologie',
            'radiology' => 'Demande d\'Examen de Radiologie',
            'dentistry' => 'Services de Dentisterie',
            'other' => 'Demande d\'Autre Service Utile',
        ];
        $pageTitle = $titles[$serviceType] ?? $titles['other'];
    @endphp

    <h1>{{ $pageTitle }}</h1>

    <p>Utilisez ce formulaire pour enregistrer une demande pour le service {{ strtolower(str_replace('Demande ', '', $pageTitle)) }}.</p>

    <form action="{{ route('hospital.reception.service.store') }}" method="POST">
        @csrf {{-- Protection CSRF --}}

        {{-- Champ caché pour passer le type de service --}}
        <input type="hidden" name="service_type" value="{{ $serviceType }}">

        <div class="card mb-4">
            <div class="card-header">
                Informations Patient
            </div>
            <div class="card-body">
                 {{-- Champ pour rechercher un patient existant --}}
                <div class="form-group">
                    <label for="patient_search">Rechercher Patient Existant:</label>
                    <input type="text" class="form-control" id="patient_search" name="patient_search" placeholder="Commencez à taper pour rechercher..." autocomplete="off">
                    {{-- Champ caché pour stocker l'ID du patient sélectionné --}}
                    <input type="hidden" id="patient_id" name="patient_id" required> {{-- Patient requis pour une demande de service --}}
                     {{-- TODO: Implémenter l'autocomplétion/recherche AJAX ici --}}
                     <small class="form-text text-muted">Le patient doit déjà exister dans le système.</small>
                </div>

                {{-- TODO: Afficher un résumé des informations du patient sélectionné ici --}}
                <div id="selected_patient_summary" style="display: none;">
                    <h4>Patient Sélectionné</h4>
                    <p><strong>Nom:</strong> <span id="summary_name"></span></p>
                     {{-- etc. --}}
                </div>
            </div>
        </div>

         <div class="card mb-4">
            <div class="card-header">
                Détails de la Demande
            </div>
            <div class="card-body">
                {{-- Champ pour sélectionner le service spécifique (ex: type d'examen de radiologie, type de soin dentaire) --}}
                <div class="form-group">
                    <label for="service_id">Service Spécifique <span class="text-danger">*</span></label> {{-- Peut être une liste de services filtrée par $serviceType --}}
                    <select id="service_id" name="service_id" class="form-control" required>
                        <option value="">Sélectionner un service</option>
                        {{-- TODO: Boucle sur les services pertinents (ex: tous les services de type 'radiology') --}}
                         {{-- @foreach($services as $service)
                             <option value="{{ $service->id }}">{{ $service->name }} ({{ number_format($service->price, 2) }} €)</option>
                         @endforeach --}}
                         @if($serviceType == 'radiology')
                             <option value="R1">Radio X-Ray Poignet</option>
                             <option value="R2">Scanner Cérébral</option>
                         @elseif($serviceType == 'pathology')
                             <option value="P1">Analyse Sang Complete</option>
                             <option value="P2">Analyse Urine</option>
                         @elseif($serviceType == 'dentistry')
                             <option value="D1">Détartrage</option>
                             <option value="D2">Consultation Dentaire</option>
                         @else
                             <option value="O1">Service Autre 1</option>
                             <option value="O2">Service Autre 2</option>
                         @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="request_date">Date de la Demande <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="request_date" name="request_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>

                 <div class="form-group">
                    <label for="requesting_doctor_id">Médecin Demandeur (Optionnel)</label> {{-- Si la demande vient d'un médecin --}}
                     <select id="requesting_doctor_id" name="requesting_doctor_id" class="form-control">
                        <option value="">Sélectionner un médecin</option>
                         {{-- TODO: Boucle sur les médecins --}}
                         {{-- @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                         @endforeach --}}
                         <option value="1">Dr. Jean Dupont</option>
                         <option value="2">Dr. Marie Curie</option>
                     </select>
                 </div>

                 <div class="form-group">
                    <label for="notes">Notes/Instructions</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                 {{-- Option pour ajouter à une file d'attente spécifique --}}
                 <div class="form-group">
                    <label for="queue_id">Ajouter à la file d'attente :</label>
                     <select id="queue_id" name="queue_id" class="form-control">
                        <option value="">Aucune (Ajout manuel plus tard)</option>
                         {{-- TODO: Boucle sur les files d'attente pertinentes (ex: files de radiologie) --}}
                         {{-- @foreach($queues as $queue)
                             <option value="{{ $queue->id }}">{{ $queue->name }}</option>
                         @endforeach --}}
                         @if($serviceType == 'radiology')
                              <option value="3">File d'attente Radiologie</option>
                         @elseif($serviceType == 'pathology')
                              <option value="4">File d'attente Labo Pathologie</option>
                         @else
                              <option value="5">File d'attente Services Divers</option>
                         @endif
                     </select>
                 </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer la Demande</button>
         <a href="{{ route('hospital.reception.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

     @push('scripts')
    <script>
         // TODO: Ajouter le script JavaScript pour gérer la recherche de patient, remplir le résumé
        // et potentiellement filtrer la liste déroulante 'service_id' et 'queue_id' en fonction du serviceType.
    </script>
    @endpush

@endsection