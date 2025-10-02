<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Hospital\Entities\Patient; // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Visit;   // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Queue;   // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\QueueItem; // Assurez-vous que le modèle existe

class ReceptionController extends Controller
{
    /**
     * Affiche la page d'accueil de la réception.
     * Présente les liens rapides vers les services et potentiellement un aperçu de la file d'attente.
     * @return Renderable
     */
    public function index(): Renderable
    {
        // Optionnel: Charger une file d'attente principale pour l'afficher sur la page d'accueil
        $mainQueue = Queue::where('name', 'File d\'attente principale')->first();

        return view('hospital::reception.index', compact('mainQueue'));
    }

    /**
     * Affiche le formulaire pour le check-in d'un patient OPD (Out-Patient Department).
     * Permet de rechercher un patient existant ou d'en créer un nouveau.
     * @return Renderable
     */
    public function showOpdCheckinForm(): Renderable
    {
        // On peut pré-charger une liste de patients ou laisser la recherche se faire via AJAX
        $patients = Patient::orderBy('name')->get(); // Exemple simple, à optimiser pour la recherche

        return view('hospital::reception.opd_checkin', compact('patients'));
    }

    /**
     * Traite le formulaire de check-in OPD.
     * Trouve/crée le patient, crée une nouvelle visite OPD et ajoute potentiellement à une file d'attente.
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processOpdCheckin(Request $request)
    {
        // --- Validation des données ---
        $request->validate([
            'patient_id' => 'nullable|exists:patients,id', // Si patient existant
            'new_patient_name' => 'required_without:patient_id|string|max:255', // Si nouveau patient
            'new_patient_dob' => 'nullable|date', // Date de naissance si nouveau
            'new_patient_phone' => 'nullable|string|max:20', // Téléphone si nouveau
            // ... autres champs si nécessaire pour la création patient
            'purpose' => 'nullable|string|max:255', // Motif de la visite
            // 'doctor_id' => 'nullable|exists:doctors,id', // Si la visite est pour un docteur spécifique
        ]);

        // --- Trouver ou créer le patient ---
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->input('patient_id'));
        } elseif ($request->filled('new_patient_name')) {
            // Logique de création du nouveau patient
            $patient = Patient::create([
                'name' => $request->input('new_patient_name'),
                'date_of_birth' => $request->input('new_patient_dob'),
                'phone' => $request->input('new_patient_phone'),
                // ... autres champs
            ]);
            // Gérer les erreurs de création si nécessaire
        }

        if (!$patient) {
            return back()->withInput()->withErrors(['patient' => 'Impossible de trouver ou créer le patient.']);
        }

        // --- Créer une entrée de visite (OPD) ---
        $visit = Visit::create([
            'patient_id' => $patient->id,
            'type' => 'OPD', // Ou une constante/enum
            'check_in_at' => now(),
            'purpose' => $request->input('purpose'),
            // 'doctor_id' => $request->input('doctor_id'), // Lier au docteur si sélectionné
            'status' => 'checked-in', // ou 'waiting'
        ]);

        // --- Ajouter le patient à une file d'attente (ex: file d'attente générale OPD ou du médecin) ---
        // Trouver la file d'attente appropriée. Créez-la si elle n'existe pas.
        $queue = Queue::firstOrCreate(['name' => 'File d\'attente OPD']); // Exemple simple

        QueueItem::create([
            'queue_id' => $queue->id,
            'patient_id' => $patient->id,
            'visit_id' => $visit->id, // Lier l'élément de file à la visite
            'status' => 'waiting',
            'order' => QueueItem::where('queue_id', $queue->id)->max('order') + 1, // Simple ordre basé sur l'ajout
        ]);

        // --- Redirection ---
        return redirect()->route('hospital.reception.index')->with('success', 'Patient ' . $patient->name . ' enregistré en OPD avec succès.');

        // Vous pourriez aussi rediriger vers une page de résumé de la visite ou la file d'attente.
    }

    /**
     * Affiche le formulaire pour l'admission d'un patient IPD (In-Patient Department).
     * Inclut potentiellement la sélection d'un lit.
     * @return Renderable
     */
    public function showIpdAdmissionForm(): Renderable
    {
        $patients = Patient::orderBy('name')->get(); // Ou recherche AJAX
        // Charger la liste des lits disponibles (dépend d'une gestion des lits)
        // $availableBeds = Bed::where('status', 'available')->get();

        // return view('hospital::reception.ipd_admission', compact('patients', 'availableBeds'));
        return view('hospital::reception.ipd_admission', compact('patients')); // Version simplifiée
    }

    /**
     * Traite le formulaire d'admission IPD.
     * Trouve/crée le patient, crée une nouvelle visite/admission IPD et gère potentiellement l'allocation de lit.
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processIpdAdmission(Request $request)
    {
         // --- Validation des données ---
         $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'new_patient_name' => 'required_without:patient_id|string|max:255',
            // ... champs patient
            'admission_date' => 'required|date',
            // 'bed_id' => 'required|exists:beds,id', // Si gestion des lits
            'reason' => 'nullable|string',
        ]);

        // --- Trouver ou créer le patient (similaire à OPD) ---
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->input('patient_id'));
        } elseif ($request->filled('new_patient_name')) {
             $patient = Patient::create([
                'name' => $request->input('new_patient_name'),
                // ... autres champs patient
            ]);
        }

        if (!$patient) {
            return back()->withInput()->withErrors(['patient' => 'Impossible de trouver ou créer le patient.']);
        }

        // --- Créer une entrée de visite/admission (IPD) ---
        $visit = Visit::create([
            'patient_id' => $patient->id,
            'type' => 'IPD', // Ou constante/enum
            'check_in_at' => $request->input('admission_date'), // Date d'admission
            'reason' => $request->input('reason'),
            // 'bed_id' => $request->input('bed_id'), // Lier au lit
            'status' => 'admitted',
        ]);

        // --- Gérer l'allocation de lit (si implémenté) ---
        // Marquer le lit comme occupé

        // --- Redirection ---
        return redirect()->route('hospital.reception.index')->with('success', 'Patient ' . $patient->name . ' admis en IPD avec succès.');
    }


    /**
     * Affiche une page pour lister ou gérer d'autres services utiles.
     * Cette page peut être un simple lien vers d'autres sections du module ou afficher des informations.
     * @return Renderable
     */
    public function otherServices(): Renderable
    {
        // Vous pouvez charger des données spécifiques ici si nécessaire
        $usefulLinks = [
            // Exemple: Liens vers la gestion des labos, radiologie, etc.
            // Ces routes devraient exister dans le module Hospital
            // 'Laboratoire' => route('hospital.laboratory.index'),
            // 'Imagerie Médicale' => route('hospital.radiology.index'),
            // ...
        ];

        return view('hospital::reception.other_services', compact('usefulLinks'));
    }

    // Vous pourriez ajouter des méthodes pour rechercher un patient, voir l'historique rapide, etc.
}