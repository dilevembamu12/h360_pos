<?php

namespace Modules\Help\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Ajoute le menu du module Help à la barre latérale de l'administration.
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        // Vérifie si l'utilisateur est un superadmin (ou un admin autorisé)
        Menu::modify('admin-sidebar-menu', function ($menu) {
            $menu->url(
                action([\Modules\Help\Http\Controllers\AcademyController::class, 'index']), // Lien corrigé
                'Académie H360', // Nouveau nom
                [
                    'icon' => 'fas fa-graduation-cap', // Nouvelle icône
                    'active' => request()->segment(1) == 'help',
                ]
            )->order(90);
        });
    }
}