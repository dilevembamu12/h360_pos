<?php

namespace Modules\Help\Http\Controllers;

use App\System;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->module_name = 'help';
        $this->appVersion = config('help.module_version');
    }

    /**
     * Page d'installation
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        // Vérifie si le module est déjà installé en utilisant App\System
        $is_installed = System::getProperty($this->module_name . '_version');
        if (!empty($is_installed)) {
            $output = ['success' => 1, 'msg' => 'Module déjà installé'];
            return redirect()
                ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
                ->with('status', $output);
        }

        // Redirige vers la vue d'installation
        $action_url = action([\Modules\Help\Http\Controllers\InstallController::class, 'install']);
        return view('install.install-module')->with('action_url', $action_url);
    }

    /**
     * Lance l'installation
     */
    public function install()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Exécute les migrations du module
            Artisan::call('module:migrate', ['module' => 'Help', '--force' => true]);
            
            // Ajoute la version du module à la table 'system'
            System::addProperty($this->module_name . '_version', $this->appVersion);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Module Help installé avec succès !',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Une erreur est survenue, veuillez vérifier les logs.',
            ];
        }

        return redirect()
                ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
                ->with('status', $output);
    }

    /**
     * Désinstallation (Optionnel mais recommandé)
     */
    public function uninstall()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            System::removeProperty($this->module_name . '_version');
            $output = ['success' => true, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            $output = ['success' => false, 'msg' => $e->getMessage()];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}