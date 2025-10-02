@extends('hospital::layouts.master') {{-- Étend le layout de base --}}

@section('content')
    <h1>Admission Patient IPD</h1>

    <p>Utilisez ce formulaire pour admettre un patient pour une hospitalisation (IPD).</p>

    <form action="{{ route('hospital.reception.ipd.process_admission') }}" method="POST">
         @csrf {{-- Protection CSRF --}}

        <div class="card mb-4">
            <div class="card-header">
                Informations Patient
            </div>
            <div class="card-body">
                {{-- Champ pour rechercher un patient existant (similaire au check-in OPD) --}}
                <div class="form-group">
                    <label for="patient_search">Rechercher Patient Existant (Nom, Tél, ID Patient):</label>
                    <input type="text" class="form-control" id="patient_search" name="patient_search" placeholder="Commencez à taper pour rechercher..." autocomplete="off">
                    {{-- Champ caché pour stocker l'ID du patient sélectionné --}}
                    <input type="hidden" id="patient_id" name="patient_id" required> {{-- Patient ID généralement requis pour une admission --}}
                     {{-- TODO: Implémenter l'autocomplétion/recherche AJAX ici --}}
                     <small class="form-text text-muted">Le patient doit déjà exister dans le système.</small>
                </div>

                {{-- TODO: Afficher un résumé des informations du patient sélectionné ici --}}
                <div id="selected_patient_summary" style="display: none;">
                    <h4>Patient Sélectionné</h4>
                    <p><strong>Nom:</strong> <span id="summary_name"></span></p>
                    <p><strong>Date de Naissance:</strong> <span id="summary_dob"></span></p>
                    <p><strong>Téléphone:</strong> <span id="summary_phone"></span></p>
                    {{-- etc. --}}
                </div>

                 {{-- Option pour créer un nouveau patient si nécessaire (moins courant pour IPD immédiat mais possible) --}}
                 {{-- <div id="new_patient_section" style="display: none;">
                    <h4>Nouveau Patient</h4>
                     ... (champs similaires au formulaire OPD si la création est permise ici) ...
                 </div> --}}
            </div>
        </div>

         <div class="card mb-4">
            <div class="card-header">
                Détails de l'Admission
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="admission_date">Date de Début d'Hospitalisation <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="admission_date" name="admission_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group">
                    <label for="doctor_id">Médecin Traitant Principal <span class="text-danger">*</span></label>
                     <select id="doctor_id" name="doctor_id" class="form-control" required>
                        <option value="">Sélectionner un médecin</option>
                         {{-- TODO: Boucle sur les médecins disponibles --}}
                         {{-- @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                         @endforeach --}}
                         <option value="1">Dr. Jean Dupont</option>
                         <option value="2">Dr. Marie Curie</option>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="ward_id">Service/Unité</label> {{-- Assumer une liste de services/unités --}}
                    <select id="ward_id" name="ward_id" class="form-control">
                        <option value="">Sélectionner un service (Optionnel)</option>
                         {{-- TODO: Boucle sur les services/unités --}}
                         {{-- @foreach($wards as $ward)
                             <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                         @endforeach --}}
                         <option value="101">Chirurgie</option>
                         <option value="102">Médecine Interne</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bed_id">Lit Attribué</label> {{-- Assumer une liste de lits, potentiellement filtrée par service --}}
                    <select id="bed_id" name="bed_id" class="form-control">
                        <option value="">Sélectionner un lit (Optionnel)</option>
                        {{-- TODO: Boucle sur les lits disponibles --}}
                         {{-- @foreach($beds as $bed)
                             <option value="{{ $bed->id }}">{{ $bed->name }} (Chambre {{ $bed->room->number }})</option>
                         @endforeach --}}
                         <option value="B10">Lit 101A (Chambre 101)</option>
                         <option value="B11">Lit 101B (Chambre 101)</option>
                    </select>
                     {{-- TODO: Implémenter la gestion des lits (peut-être un lien vers un module spécifique ou une section ici) --}}
                 </div>
                 <div class="form-group">
                    <label for="reason">Raison de l'Admission <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                </div>
                 <div class="form-group">
                    <label for="admission_notes">Notes d'Admission</label>
                    <textarea class="form-control" id="admission_notes" name="admission_notes" rows="3"></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer l'Admission IPD</button>
         <a href="{{ route('hospital.reception.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

     @push('scripts')
    <script>
        // TODO: Ajouter le script JavaScript pour gérer la recherche de patient, remplir le résumé
        // et peut-être filtrer les lits disponibles en fonction du service sélectionné.
    </script>
    @endpush

@endsection