<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request; // Si vous avez besoin de traiter des requêtes
use Illuminate\Routing\Controller;
use Modules\Hospital\Entities\Patient; // Exemple : importer le modèle Patient si le dashboard affiche des stats patients

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du module Hospital.
     * @return Renderable
     */
    public function index()
    {
        // --- Exemple : Récupérer des données pour le tableau de bord ---
        // Ici, vous pouvez récupérer des données pertinentes pour le dashboard,
        // comme le nombre de patients, les rendez-vous du jour, etc.

        // Compter le nombre total de patients enregistrés
        $totalPatients = Patient::count();

        dd($totalPatients);

        // Vous pouvez ajouter d'autres requêtes à la base de données ici
        // $upcomingAppointments = Appointment::where('date', '>=', now())->count();
        // $activeAdmissions = Admission::where('status', 'admitted')->count();

        // --- Passage des données à la vue ---
        // Nous passons les données récupérées à la vue.
        // Le nom de la vue est 'hospital::index', ce qui signifie
        // le fichier `index.blade.php` dans `Modules/Hospital/Resources/views`.
        return view('hospital::index', [
            'totalPatients' => $totalPatients,
            // 'upcomingAppointments' => $upcomingAppointments,
            // 'activeAdmissions' => $activeAdmissions,
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    // public function show($id)
    // {
    //     return view('hospital::show');
    // }
}