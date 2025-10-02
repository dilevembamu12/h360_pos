<!-- File: Modules/Hospital/Resources/views/patient/index.blade.php -->
@extends('hospital::layouts.master') {{-- Assurez-vous que ce layout existe ou ajustez --}}

@section('content')
    <h1>Liste des Patients (HOSP-001)</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <p>Voici la liste des patients enregistrés.</p>

    <table class="table">
        <thead>
            <tr>
                <th>ID Interne</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de Naissance</th>
                <th>Genre</th>
                <th>Actions</th> {{-- Colonne pour les liens/boutons (voir, modifier, supprimer) --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $patient)
                <tr>
                    <td>{{ $patient->patient_id ?? 'N/A' }}</td> {{-- Affiche l'ID interne s'il existe --}}
                    <td>{{ $patient->last_name }}</td>
                    <td>{{ $patient->first_name }}</td>
                    <td>{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') : 'N/A' }}</td> {{-- Formate la date --}}
                    <td>{{ $patient->gender ?? 'N/A' }}</td>
                    <td>
                        {{-- Liens vers d'autres actions (à implémenter) --}}
                        {{-- <a href="{{ route('hospital.patients.show', $patient->id) }}" class="btn btn-sm btn-info">Voir</a> --}}
                        {{-- <a href="{{ route('hospital.patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">Modifier</a> --}}
                        {{-- <form action="{{ route('hospital.patients.destroy', $patient->id) }}" method="POST" style="display:inline;"> --}}
                            {{-- @csrf --}}
                            {{-- @method('DELETE') --}}
                            {{-- <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button> --}}
                        {{-- </form> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('hospital.patients.create') }}" class="btn btn-primary">Ajouter un Nouveau Patient</a>
@endsection