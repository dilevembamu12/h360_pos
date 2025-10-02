<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Hospital\Entities\Queue;      // Assurez-vous que le modèle existe
use Modules\Hospital\Entities\QueueItem;  // Assurez-vous que le modèle existe

class QueueController extends Controller
{
    /**
     * Affiche une vue listant les files d'attente ou une file d'attente spécifique.
     * Cette méthode pourrait être utilisée pour l'affichage sur un écran public ou un tableau de bord interne.
     * @param  int|null  $queue_id  ID de la file d'attente spécifique (optionnel)
     * @return Renderable
     */
    public function index(?int $queue_id = null): Renderable
    {
        if ($queue_id) {
            // Afficher une file d'attente spécifique avec ses éléments
            $queue = Queue::with(['items' => function ($query) {
                $query->whereIn('status', ['waiting', 'serving'])->orderBy('order'); // N'afficher que les éléments en attente ou en service
            }, 'items.patient', 'items.service']) // Charger les relations Patient et Service
            ->find($queue_id);

            if (!$queue) {
                 abort(404, 'File d\'attente non trouvée');
            }

            return view('hospital::queue.show', compact('queue')); // Vue pour une seule file d'attente

        } else {
            // Afficher une liste de files d'attente (ex: sur un tableau de bord de supervision)
            $queues = Queue::withCount(['items' => function($query) {
                $query->where('status', 'waiting'); // Compter les éléments en attente
            }])->get();

            return view('hospital::queue.index', compact('queues')); // Vue pour la liste des files
        }
    }

    /**
     * Marque un élément de la file d'attente comme "en service" (called).
     * Utile pour indiquer quel patient doit se présenter.
     * @param  QueueItem  $queueItem  L'élément de file d'attente à appeler.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function callItem(QueueItem $queueItem)
    {
        // Vérifier si l'élément n'est pas déjà servi ou annulé
        if ($queueItem->status === 'waiting') {
            $queueItem->status = 'serving'; // Ou 'called'
            $queueItem->called_at = now();
            $queueItem->save();

            // Optionnel: Déclencher un événement pour mise à jour en temps réel (via WebSockets)
            // event(new QueueItemCalled($queueItem));

            // Retourner une réponse (redirection ou JSON pour AJAX)
             if (request()->expectsJson()) {
                return response()->json(['success' => true, 'item' => $queueItem]);
            }
            return back()->with('success', 'Patient ' . $queueItem->patient->name . ' appelé.');

        }

        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'L\'élément n\'est pas en attente.'], 400);
        }
        return back()->with('error', 'Impossible d\'appeler cet élément de file.');
    }

     /**
     * Marque un élément de la file d'attente comme "servi".
     * Indique que la consultation ou le service pour ce patient est terminé.
     * @param  QueueItem  $queueItem  L'élément de file d'attente à marquer comme servi.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function serveItem(QueueItem $queueItem)
    {
         // Vérifier si l'élément est en attente ou en service
        if (in_array($queueItem->status, ['waiting', 'serving'])) {
            $queueItem->status = 'served';
            $queueItem->served_at = now();
            $queueItem->save();

            // Optionnel: Déclencher un événement
            // event(new QueueItemServed($queueItem));

             // Retourner une réponse (redirection ou JSON pour AJAX)
             if (request()->expectsJson()) {
                return response()->json(['success' => true, 'item' => $queueItem]);
            }
            return back()->with('success', 'Patient ' . $queueItem->patient->name . ' marqué comme servi.');
        }

         if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'L\'élément n\'est pas en attente ou en service.'], 400);
        }
        return back()->with('error', 'Impossible de marquer cet élément comme servi.');
    }

    /**
     * Marque un élément de la file d'attente comme "annulé" ou "non-présent".
     * @param  QueueItem  $queueItem  L'élément de file d'attente à annuler.
      * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function cancelItem(QueueItem $queueItem)
    {
         // Marquer l'élément comme annulé ou non-présent
        $queueItem->status = 'cancelled'; // ou 'no-show' selon le cas
        $queueItem->save();

        // Optionnel: Déclencher un événement
        // event(new QueueItemCancelled($queueItem));

         // Retourner une réponse (redirection ou JSON pour AJAX)
         if (request()->expectsJson()) {
            return response()->json(['success' => true, 'item' => $queueItem]);
        }
        return back()->with('success', 'Élément de file pour ' . $queueItem->patient->name . ' annulé.');
    }

    // Vous pourriez ajouter des méthodes pour ajouter un élément (bien que souvent fait dans Reception/ServiceRequest),
    // réordonner les éléments, voir l'historique de la file, etc.
}