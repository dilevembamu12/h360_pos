<?php

namespace Modules\Help\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Help\Entities\VideoTutorial;
use Illuminate\Support\Facades\Auth; // Assurez-vous que cette ligne est présente

class AcademyController extends Controller
{
    /**
     * Affiche la page principale de l'Académie H360.
     */
    public function index()
    {
        $all_videos = VideoTutorial::orderBy('title')->get();

        // **CORRECTION : Utilise la même logique de permission que le reste de l'application**
        $is_admin = Auth::user()->can('superadmin');

        return view('help::academy.index', compact('all_videos', 'is_admin'));
    }
}