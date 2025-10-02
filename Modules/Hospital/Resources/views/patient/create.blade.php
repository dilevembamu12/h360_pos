<!-- File: Modules/Hospital/Resources/views/patient/create.blade.php -->
@extends('hospital::layouts.master') {{-- Assurez-vous que ce layout existe ou ajustez --}}

@section('content')
    <h1>Enregistrer un Nouveau Patient (HOSP-001)</h1>

    <form action="{{ route('hospital.patients.store') }}" method="POST">
        @csrf {{-- Token CSRF pour la sécurité --}}

        {{-- Afficher les erreurs de validation si elles existent --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Champs du formulaire --}}
        <div class="form-group">
            <label for="first_name">Prénom:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
        </div>

        <div class="form-group">
            <label for="last_name">Nom:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date de Naissance:</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
        </div>

        <div class="form-group">
            <label for="gender">Genre:</label>
            <select class="form-control" id="gender" name="gender">
                <option value="">Sélectionner</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Téléphone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="address">Adresse:</label>
            <textarea class="form-control" id="address" name="address">{{ old('address') }}</textarea>
        </div>

        <div class="form-group">
            <label for="emergency_contact_name">Contact d'Urgence (Nom):</label>
            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
        </div>

        <div class="form-group">
            <label for="emergency_contact_phone">Contact d'Urgence (Téléphone):</label>
            <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
        </div>

        {{-- Vous pouvez ajouter le champ patient_id ici si vous voulez qu'il soit saisi manuellement --}}
        {{-- <div class="form-group">
            <label for="patient_id">ID Patient:</label>
            <input type="text" class="form-control" id="patient_id" name="patient_id" value="{{ old('patient_id') }}">
        </div> --}}

        <button type="submit" class="btn btn-primary">Enregistrer le Patient</button>
    </form>
@endsection