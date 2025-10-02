<?php
namespace Modules\Hospital\Http\Controllers;

// File: Modules/Hospital/Http/Controllers/PatientController.php

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Hospital\Entities\Patient; // Importe le modèle Patient

class PatientController extends Controller
{
    /**
     * Affiche la liste de tous les patients.
     * @return Renderable
     */
    public function index()
    {
        // Récupère tous les patients (pour l'instant, juste une vue simple)
        $patients = Patient::all();

        // Retourne la vue 'index' du dossier patient dans les vues du module Hospital
        return view('hospital::patient.index', compact('patients'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau patient.
     * @return Renderable
     */
    public function create()
    {
        // Retourne la vue 'create' du dossier patient
        return view('hospital::patient.create');
    }

    /**
     * Enregistre un nouveau patient dans la base de données.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valider les données entrantes
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            // 'patient_id' => 'nullable|unique:patients,patient_id', // Validation pour l'ID si nécessaire
        ]);

        // Créer un nouveau patient avec les données validées
        Patient::create($request->all());

        // Rediriger l'utilisateur après l'enregistrement
        // Par exemple, vers la liste des patients avec un message de succès
        return redirect()->route('hospital.patients.index')->with('success', 'Patient enregistré avec succès!');
    }

    // Ajoutez d'autres méthodes (show, edit, update, destroy) ici
}