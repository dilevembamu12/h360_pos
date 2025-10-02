<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Hospital\Entities\Appointment; // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Patient;     // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Doctor;      // Assurez-vous que ce modèle ou équivalent existe (peut-être lié à Essentials)

class AppointmentController extends Controller
{
    /**
     * Affiche le formulaire pour prendre un nouveau rendez-vous.
     * Permet de sélectionner un patient et un docteur, ainsi qu'une date et heure.
     * @return Renderable
     */
    public function create(): Renderable
    {
        // Charger la liste des patients (ou utiliser une recherche AJAX)
        $patients = Patient::orderBy('name')->get();
        // Charger la liste des docteurs (suppose un modèle Doctor ou équivalent)
        $doctors = Doctor::orderBy('name')->get(); // Assurez-vous que Doctor existe ou utilisez le modèle Personnel d'Essentials

        return view('hospital::reception.schedule_appointment', compact('patients', 'doctors'));
    }

    /**
     * Enregistre un nouveau rendez-vous.
     * Valide les données et crée une nouvelle entrée dans la table des rendez-vous.
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // --- Validation des données ---
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id', // Ou table équivalente du personnel
            'appointment_datetime' => 'required|date', // Assurez-vous que le format inclut l'heure
            'reason' => 'nullable|string|max:255',
        ]);

        // --- Création du rendez-vous ---
        $appointment = Appointment::create([
            'patient_id' => $request->input('patient_id'),
            'doctor_id' => $request->input('doctor_id'),
            'appointment_datetime' => $request->input('appointment_datetime'),
            'reason' => $request->input('reason'),
            'status' => 'scheduled', // Statut initial
        ]);

        // --- Redirection ---
        return redirect()->route('hospital.reception.index')->with('success', 'Rendez-vous créé avec succès pour le patient.');

        // Vous pourriez aussi rediriger vers une page de confirmation ou une liste des rendez-vous.
    }

    // Optionnel: Ajouter des méthodes pour lister les rendez-vous, les voir, les modifier, les annuler
    // public function index() { ... }
    // public function show(Appointment $appointment) { ... }
    // public function edit(Appointment $appointment) { ... }
    // public function update(Request $request, Appointment $appointment) { ... }
    // public function destroy(Appointment $appointment) { ... }
}