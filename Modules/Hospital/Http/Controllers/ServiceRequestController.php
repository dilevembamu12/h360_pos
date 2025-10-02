<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Hospital\Entities\ServiceRequest; // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Patient;        // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Service;        // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\Queue;          // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\QueueItem;      // Assurez-vous que le modèle existe

class ServiceRequestController extends Controller
{
    /**
     * Affiche le formulaire pour enregistrer une demande de service.
     * Peut être pré-filtré par type de service (ex: 'pathology', 'radiology').
     * @param  string|null  $service_type  Type de service (optionnel)
     * @return Renderable
     */
    public function create(?string $service_type = null): Renderable
    {
        // Charger la liste des patients (ou utiliser une recherche AJAX)
        $patients = Patient::orderBy('name')->get();

        // Charger la liste des services, potentiellement filtrée par type
        $servicesQuery = Service::query();
        if ($service_type) {
             // Assurez-vous que votre modèle Service a un champ 'type' ou une méthode de relation/scope pour cela
             $servicesQuery->where('type', $service_type);
        }
        $services = $servicesQuery->orderBy('name')->get();

        // Passer le type de service au cas où il serait utile dans la vue
        return view('hospital::reception.service_request', compact('patients', 'services', 'service_type'));
    }

    /**
     * Enregistre une nouvelle demande de service.
     * Valide les données, crée la demande et ajoute potentiellement le patient à une file d'attente spécifique au service.
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // --- Validation des données ---
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string',
            // 'requested_by_doctor_id' => 'nullable|exists:doctors,id', // Qui a demandé le service?
        ]);

        // --- Création de la demande de service ---
        $serviceRequest = ServiceRequest::create([
            'patient_id' => $request->input('patient_id'),
            'service_id' => $request->input('service_id'),
            'notes' => $request->input('notes'),
            'requested_at' => now(),
            'status' => 'pending', // Statut initial
            // 'requested_by_doctor_id' => $request->input('requested_by_doctor_id'),
        ]);

        // --- Ajouter le patient à la file d'attente du service demandé ---
        $service = $serviceRequest->service; // Récupérer le service associé
        // Trouver ou créer une file d'attente pour ce service
        $queueName = 'File d\'attente ' . $service->name;
        $queue = Queue::firstOrCreate(['name' => $queueName]);

        QueueItem::create([
            'queue_id' => $queue->id,
            'patient_id' => $serviceRequest->patient_id,
            // 'visit_id' => null, // Peut être lié à une visite si la demande est faite pendant une consultation
            'service_id' => $service->id, // Lier l'élément de file au service demandé
            'status' => 'waiting',
            'order' => QueueItem::where('queue_id', $queue->id)->max('order') + 1,
            'service_request_id' => $serviceRequest->id, // Lier l'élément de file à la demande spécifique
        ]);


        // --- Redirection ---
        return redirect()->route('hospital.reception.index')->with('success', 'Demande de service (' . $service->name . ') enregistrée avec succès.');

        // Vous pourriez aussi rediriger vers la liste des demandes du patient ou une page spécifique du service (Labo/Radiologie).
    }

    // Optionnel: Ajouter des méthodes pour lister les demandes, les voir, les mettre à jour (changer statut ex: completed)
    // public function index() { ... }
    // public function show(ServiceRequest $serviceRequest) { ... }
    // public function update(Request $request, ServiceRequest $serviceRequest) { ... }
}