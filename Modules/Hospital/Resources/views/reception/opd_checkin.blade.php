@extends('hospital::layouts.master') {{-- Étend le layout de base --}}

@section('content')
    <h1>Enregistrement Patient OPD</h1>

    <p>Utilisez ce formulaire pour enregistrer un nouveau patient ou effectuer le check-in d'un patient existant pour une consultation externe (OPD).</p>

    <form action="{{ route('hospital.reception.opd.process_checkin') }}" method="POST">
        @csrf {{-- Protection CSRF --}}

        <div class="card mb-4">
            <div class="card-header">
                Informations Patient
            </div>
            <div class="card-body">
                {{-- Champ pour rechercher un patient existant (peut être un champ de texte avec autocomplétion ou un bouton pour ouvrir un modal) --}}
                <div class="form-group">
                    <label for="patient_search">Rechercher Patient Existant (Nom, Tél, ID Patient):</label>
                    <input type="text" class="form-control" id="patient_search" name="patient_search" placeholder="Commencez à taper pour rechercher..." autocomplete="off">
                    {{-- Champ caché pour stocker l'ID du patient sélectionné --}}
                    <input type="hidden" id="patient_id" name="patient_id">
                     {{-- TODO: Implémenter l'autocomplétion/recherche AJAX ici --}}
                </div>

                <p class="text-center">-- OU --</p>

                {{-- Section pour les informations du Nouveau Patient (à afficher si aucun patient n'est sélectionné via la recherche) --}}
                <div id="new_patient_section">
                    <h4>Nouveau Patient</h4>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-row">
                         <div class="form-group col-md-4">
                            <label for="date_of_birth">Date de Naissance</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="gender">Genre</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Sélectionner...</option>
                                <option value="male">Masculin</option>
                                <option value="female">Féminin</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="phone">Téléphone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="address">Adresse</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Détails de la Visite
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="doctor_id">Médecin Traitant</label> {{-- Assumer une liste de médecins --}}
                    <select id="doctor_id" name="doctor_id" class="form-control">
                        <option value="">Sélectionner un médecin (Optionnel)</option>
                         {{-- TODO: Boucle sur les médecins disponibles, potentiellement depuis le module Essentials --}}
                         {{-- @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                         @endforeach --}}
                         <option value="1">Dr. Jean Dupont</option>
                         <option value="2">Dr. Marie Curie</option>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="notes">Notes de Réception</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                 {{-- Option pour ajouter à une file d'attente spécifique immédiatement --}}
                 <div class="form-group">
                    <label for="queue_id">Ajouter à la file d'attente :</label>
                     <select id="queue_id" name="queue_id" class="form-control">
                        <option value="">Aucune (Ajout manuel plus tard)</option>
                         {{-- TODO: Boucle sur les files d'attente disponibles --}}
                         {{-- @foreach($queues as $queue)
                             <option value="{{ $queue->id }}">{{ $queue->name }}</option>
                         @endforeach --}}
                         <option value="1">File Générale OPD</option>
                         <option value="2">File Dr. Jean Dupont</option>
                     </select>
                 </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer et Check-in</button>
         <a href="{{ route('hospital.reception.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    @push('scripts')
    <script>
        // TODO: Ajouter le script JavaScript pour gérer la recherche de patient et afficher/masquer la section "Nouveau Patient"
        // Si un patient est sélectionné via la recherche, cacher #new_patient_section et remplir #patient_id
        // Si le champ de recherche est vide et qu'aucun patient n'est sélectionné, afficher #new_patient_section
    </script>
    @endpush

@endsection