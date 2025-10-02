<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Importer les modèles nécessaires
use Modules\Hospital\Entities\Patient;
//use Modules\Hospital\Entities\Service; // Supposons que les services hospitaliers soient dans ce modèle
use Modules\Hospital\Entities\Bill;
use Modules\Hospital\Entities\BillItem;
use Modules\Hospital\Entities\Visit; // Pour lier aux visites/admissions
use Modules\Hospital\Entities\Bed;   // Pour les admissions IPD
use Modules\Hospital\Entities\QueueItem; // Peut-être mettre à jour l'élément de file d'attente

// Importer potentiellement d'autres modèles si nécessaire (Ex: PaymentMethod, Doctor, User)
// use App\Models\PaymentMethod; // Exemple, dépend de votre structure
// use App\Models\User; // Pour l'utilisateur qui facture

class BillingController extends Controller
{
    /**
     * Affiche l'écran POS/Facturation pour le module Hospital.
     *
     * @return Renderable
     */
    public function create(): Renderable
    {
        // TODO: Charger les données nécessaires pour la vue POS (services, catégories, méthodes de paiement, lits si besoin)
        // Ces données peuvent être filtrées ou chargées dynamiquement via AJAX dans la vue.
        // Pour commencer, on peut charger les services par catégorie.

        //$servicesByCategory = Service::orderBy('name') // Ou grouper par type/catégorie
        //                             ->get()
        //                                     ->groupBy('type'); // Assumons un champ 'type' sur le modèle Service

        // TODO: Charger les méthodes de paiement disponibles
        // $paymentMethods = PaymentMethod::all(); // Dépend de votre implémentation

        // TODO: Charger une liste de lits disponibles (peut être lourd, préférer AJAX)
        // $availableBeds = Bed::where('status', 'available')->get(); // Exemple basique

        // TODO: Charger une liste de docteurs (si la liaison médecin est nécessaire à la facturation)
        // $doctors = User::where('user_type', 'doctor')->get(); // Exemple si les docteurs sont des utilisateurs


        return view('hospital::pos.create', [
            'consultations' => "consultations",
            'dentistryServices' => "dentistryServices",
            'pathologyTests' => "pathologyTests",
            'radiologyServices' => "radiologyServices",
            'maternityServices' => "maternityServices",
            // 'otherServices' => $otherServices, // Passer les autres collections
            // 'pharmacyItems' => $pharmacyItems, // Passer les produits
            // 'paymentMethods' => $paymentMethods,
            // 'availableBeds' => $availableBeds,
            // 'doctors' => $doctors,
        ]);

        return view('hospital::pos.create', [
            'servicesByCategory' => $servicesByCategory,
            // 'paymentMethods' => $paymentMethods,
            // 'availableBeds' => $availableBeds,
            // 'doctors' => $doctors,
        ]);
    }

    /**
     * Gère la soumission du formulaire POS pour créer une nouvelle facture.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // --- 1. Validation des données ---
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'billing_context' => 'required|in:opd,ipd',
            'bed_id' => 'nullable|exists:beds,id', // Rendu nullable ici, validation plus fine si context est IPD
            'visit_admission_id' => 'nullable|exists:visits,id', // Assumons que c'est l'ID de la table 'visits'
            'billing_items_json' => 'required|json',
            'payment_method' => 'required|string', // TODO: Validation plus spécifique selon vos méthodes
            'amount_paid' => 'required|numeric|min:0',
            'payment_note' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0', // Total calculé côté client (pour info/affichage immédiat)
        ]);

        // Validation spécifique pour le contexte IPD
        if ($request->input('billing_context') === 'ipd') {
             // TODO: Ajouter validation si bed_id est requis pour l'IPD
             // $request->validate(['bed_id' => 'required|exists:beds,id']);
             // TODO: Valider que le patient a une admission IPD active et que le lit correspond si bed_id est fourni
             // TODO: Valider que visit_admission_id (si fourni) correspond à une admission IPD active de ce patient
        }

        // --- 2. Récupération et traitement des données ---
        $patient = Patient::findOrFail($request->input('patient_id'));
        $billingItemsData = json_decode($request->input('billing_items_json'), true); // Décoder le JSON des articles

        if (empty($billingItemsData)) {
             return back()->withInput()->withErrors(['billing_items' => 'La facture doit contenir au moins un article.']);
        }

        $visitAdmission = null;
        if ($request->filled('visit_admission_id')) {
             $visitAdmission = Visit::find($request->input('visit_admission_id'));
             // TODO: Gérer l'erreur si visit_admission_id est fourni mais introuvable ou ne correspond pas au patient/contexte
        } else {
             // TODO: Si pas de visit_admission_id fourni, que fait-on?
             // Pour OPD: Créer une nouvelle visite simple si le patient n'en a pas d'active pour aujourd'hui?
             // Pour IPD: Trouver l'admission IPD active du patient? Ou exiger de la lier?
             // Logique simple: on cherche une visite active du jour pour OPD, ou la dernière admission pour IPD.
             // C'est une simplification, la liaison devrait être plus robuste.
              if ($request->input('billing_context') === 'opd') {
                 $visitAdmission = Visit::where('patient_id', $patient->id)
                                        ->where('type', 'OPD')
                                        ->whereDate('check_in_at', today())
                                        ->latest() // Prendre la plus récente d'aujourd'hui
                                        ->first();
                // Si pas de visite OPD aujourd'hui, on pourrait en créer une simple ici, mais c'est souvent fait au check-in.
                // Pour cette implémentation, on pourrait décider de rendre visit_admission_id requis ou de gérer la création.
                // Disons qu'on crée une visite simple si aucune trouvée.
                 if (!$visitAdmission) {
                     $visitAdmission = Visit::create([
                         'patient_id' => $patient->id,
                         'type' => 'OPD',
                         'check_in_at' => now(),
                         'reason' => 'Facturation POS sans visite préalable enregistrée', // Raison par défaut
                         'status' => 'completed', // Marquer comme complétée si c'est juste pour facturer
                     ]);
                 }

              } elseif ($request->input('billing_context') === 'ipd') {
                 $visitAdmission = Visit::where('patient_id', $patient->id)
                                        ->where('type', 'IPD')
                                        ->where('status', 'admitted') // Chercher une admission active
                                        ->latest()
                                        ->first();
                 // TODO: Gérer le cas où un patient est IPD mais n'a pas d'admission active enregistrée.
                 if (!$visitAdmission) {
                      // Potentiellement une erreur ou une création implicite (moins recommandé)
                      Log::warning("Patient IPD {$patient->id} facturé via POS sans admission active.");
                      // Option: Retourner une erreur
                     // return back()->withInput()->withErrors(['patient' => 'Ce patient IPD n\'a pas d\'admission active pour la facturation.']);
                     // Option: Créer une visite/admission IPD simple (nécessiterait plus de données, ex: médecin, lit si pas déjà sélectionné)
                     // $visitAdmission = Visit::create([...]);
                 }
              }
        }


        // --- 3. Calcul du total serveur-side (Important pour la sécurité) ---
        $calculatedTotal = 0;
        $processedItems = [];

        foreach ($billingItemsData as $itemData) {
            // TODO: Valider la structure de chaque itemData (service_id, quantity, etc.)
             if (!isset($itemData['service_id']) || !isset($itemData['quantity'])) {
                 Log::error("Format d'article de facturation invalide reçu", ['item' => $itemData]);
                 continue; // Sauter cet article invalide ou lever une erreur
             }

            // Récupérer le service/produit depuis la base de données pour avoir le prix réel et les taxes
            $service = Service::find($itemData['service_id']); // Ou votre modèle de produit si c'est un produit
            if (!$service) {
                Log::warning("Service/Produit introuvable pour l'ID: " . $itemData['service_id']);
                // TODO: Gérer cet article - l'ignorer ou signaler une erreur ? L'ignorer est plus tolérant.
                continue;
            }

            $unitPrice = $service->price; // Prix réel depuis la base de données
            $quantity = (float) $itemData['quantity'];
            $discountPercent = (float) ($itemData['discount_percent'] ?? 0); // Utiliser la réduction du front-end (peut être validée)
            $taxPercent = (float) ($itemData['tax_percent'] ?? ($service->tax_rate ?? 0)); // Utiliser TVA du service ou du front-end (peut être validée)

            $itemSubtotalBeforeTax = $unitPrice * $quantity;
            $discountAmount = $itemSubtotalBeforeTax * ($discountPercent / 100);
            $itemSubtotalAfterDiscount = $itemSubtotalBeforeTax - $discountAmount;
            $taxAmount = $itemSubtotalAfterDiscount * ($taxPercent / 100);
            $itemTotal = $itemSubtotalAfterDiscount + $taxAmount;

            $calculatedTotal += $itemTotal;

            // Stocker les données de l'article avec le prix réel calculé
            $processedItems[] = [
                'service_id' => $service->id,
                'unit_price' => $unitPrice, // Prix unitaire réel
                'quantity' => $quantity,
                'discount_percent' => $discountPercent,
                'tax_percent' => $taxPercent,
                'subtotal' => $itemTotal, // Total de la ligne
                // TODO: Ajouter d'autres champs si nécessaire (notes sur l'article, etc.)
            ];
        }

         // TODO: Ajouter d'autres frais globaux si applicable (frais d'urgence, etc.) et les inclure dans calculatedTotal

        // Comparer le total calculé serveur avec le total reçu du client (peut y avoir de petites différences de virgule flottante)
        // $clientTotal = (float) $request->input('total_amount');
        // if (abs($calculatedTotal - $clientTotal) > 0.01) { // Tolérance de 0.01
        //     Log::warning("Total client mismatch", ['client_total' => $clientTotal, 'server_total' => $calculatedTotal, 'patient_id' => $patient->id]);
        //     // Option: Lever une erreur ou juste logger
        //     // return back()->withInput()->withErrors(['total' => 'Erreur de calcul du total.']);
        // }


        // --- 4. Enregistrement en base de données ---
        DB::beginTransaction(); // Utiliser une transaction pour s'assurer que tout est enregistré ou rien

        try {
            // Créer la facture principale
            $bill = Bill::create([
                'patient_id' => $patient->id,
                'visit_id' => $visitAdmission ? $visitAdmission->id : null, // Lier à la visite/admission si trouvée/créée
                'billing_date' => now(),
                'total_amount' => $calculatedTotal, // Utiliser le total calculé serveur
                'status' => 'due', // Statut initial : Dû
                // TODO: Ajouter d'autres champs: business_id, location_id, created_by (auth()->user()->id), type (OPD/IPD)
                 'business_id' => session()->get('user.business_id'), // Exemple si business_id est en session
                 'location_id' => session()->get('user.location_id'), // Exemple si location_id est en session
                 'created_by' => auth()->user()->id,
                 'type' => $request->input('billing_context'),
                 'bed_id' => $request->input('bed_id'), // Sera null si pas IPD
            ]);

            // Ajouter les articles de la facture
            foreach ($processedItems as $itemData) {
                $bill->items()->create($itemData); // Utilise la relation 'items' définie sur le modèle Bill
                // TODO: Si l'article est un produit de l'inventaire, décrémenter le stock ici.
                // Interaction avec le module InventoryManagement.
            }

            // Enregistrer le paiement
            $amountPaid = (float) $request->input('amount_paid');
            if ($amountPaid > 0) {
                 // TODO: Utiliser votre modèle/mécanisme de paiement
                 // Exemple basique:
                 DB::table('payments')->insert([ // Assurez-vous que cette table existe et correspond
                     'bill_id' => $bill->id,
                     'patient_id' => $patient->id,
                     'method' => $request->input('payment_method'),
                     'amount' => $amountPaid,
                     'note' => $request->input('payment_note'),
                     'paid_on' => now(),
                      // TODO: Ajouter business_id, location_id, created_by
                     'business_id' => session()->get('user.business_id'),
                     'location_id' => session()->get('user.location_id'),
                     'created_by' => auth()->user()->id,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);

                 // Mettre à jour le statut de la facture en fonction du paiement
                 if ($amountPaid >= $calculatedTotal) {
                     $bill->status = 'paid';
                 } else {
                     $bill->status = 'partial';
                 }
                 $bill->save();
            }

            // TODO: Si un élément de file d'attente (QueueItem) était lié à cette visite/service request,
            // le marquer comme 'served' ou 'completed'.

            // TODO: Si IPD et un lit a été sélectionné/validé, s'assurer que le statut du lit est 'occupied'

            DB::commit(); // Tout s'est bien passé, on valide la transaction

            // --- 5. Redirection ---
            // Rediriger vers la page de la facture créée ou un message de succès
            // return redirect()->route('hospital.billing.show', $bill->id)
            //                ->with('success', 'Facture créée avec succès !');

            // Ou rediriger vers l'écran POS pour une nouvelle facturation
            return redirect()->route('hospital.pos.create') // Assurez-vous que cette route existe et pointe vers create()
                           ->with('success', 'Facture pour ' . $patient->full_name . ' enregistrée avec succès !');


        } catch (\Exception $e) {
            DB::rollBack(); // Quelque chose s'est mal passé, annuler toutes les opérations
            Log::error('Erreur lors de l\'enregistrement de la facture : ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            // Rediriger avec un message d'erreur
            return back()->withInput()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement de la facture. Veuillez réessayer.']);
        }
    }

    /**
     * Affiche une facture spécifique.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id): Renderable
    {
        // TODO: Récupérer la facture avec ses articles et relations (patient, paiements, etc.)
         $bill = Bill::with(['patient', 'items.service', 'payments'])->findOrFail($id);

        // TODO: Vérifier les permissions si nécessaire

        return view('hospital::billing.show', compact('bill'));
    }

     /**
     * Affiche la liste des factures.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        // TODO: Récupérer la liste des factures, potentiellement paginée, filtrée, triée
         $bills = Bill::with(['patient'])->orderByDesc('billing_date')->paginate(20); // Exemple basique

        // TODO: Gérer les filtres, recherche, datatables, etc.

        return view('hospital::billing.index', compact('bills'));
    }


    // TODO: Ajouter d'autres méthodes si nécessaire (edit, update, destroy, addItem, addPayment, printBill, etc.)
}