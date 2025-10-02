@extends('hospital::layouts.master') {{-- Étend le layout de base --}}

@section('content')
    <h1>Planifier un Rendez-vous</h1>

    <p>Utilisez ce formulaire pour planifier un nouveau rendez-vous pour un patient.</p>

    <form action="{{ route('hospital.reception.appointments.store') }}" method="POST">
        @csrf {{-- Protection CSRF --}}

        <div class="card mb-4">
            <div class="card-header">
                Informations Patient
            </div>
            <div class="card-body">
                 {{-- Champ pour rechercher un patient existant --}}
                <div class="form-group">
                    <label for="patient_search">Rechercher ou Créer Patient:</label>
                    <input type="text" class="form-control" id="patient_search" name="patient_search" placeholder="Commencez à taper pour rechercher ou laisser vide pour nouveau patient..." autocomplete="off">
                    {{-- Champ caché pour stocker l'ID du patient sélectionné --}}
                    <input type="hidden" id="patient_id" name="patient_id">
                     {{-- TODO: Implémenter l'autocomplétion/recherche AJAX ici --}}
                </div>

                <p class="text-center">-- OU --</p>

                 {{-- Section pour les informations du Nouveau Patient --}}
                <div id="new_patient_section">
                    <h4>Nouveau Patient</h4>
                     {{-- Champs similaires au formulaire OPD pour nouveau patient --}}
                     <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"> {{-- Rendre requis via JS si #patient_id est vide --}}
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"> {{-- Rendre requis via JS si #patient_id est vide --}}
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
                            <input type="text" class="form-control" id="phone" name="phone"> {{-- Rendre requis via JS si #patient_id est vide --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="card mb-4">
            <div class="card-header">
                Détails du Rendez-vous
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="doctor_id">Médecin <span class="text-danger">*</span></label>
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
                    <label for="appointment_datetime">Date et Heure du Rendez-vous <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="appointment_datetime" name="appointment_datetime" required>
                     {{-- TODO: Optionnellement, afficher les créneaux disponibles en fonction du médecin sélectionné --}}
                </div>
                 <div class="form-group">
                    <label for="purpose">Objet du Rendez-vous</label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="3"></textarea>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary">Planifier le Rendez-vous</button>
         <a href="{{ route('hospital.reception.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    @push('scripts')
    <script>
         // TODO: Ajouter le script JavaScript pour gérer la recherche de patient et afficher/masquer la section "Nouveau Patient"
        // Rendre les champs du nouveau patient requis si #patient_id est vide avant soumission.
    </script>
    @endpush

@endsection