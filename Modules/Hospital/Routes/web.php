<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('hospital')->group(function() {
    // Route pour le tableau de bord du module Hospital
    //Route::get('/', 'PatientController@index')->name('hospital.dashboard');
    Route::get('/', [\Modules\Hospital\Http\Controllers\DashboardController::class, 'index'])->name('hospital.dashboard'); // Utilisation de la syntaxe de tableau pour le contrôleur (recommandé dans Laravel 8+)


    // Ajoutez ici les autres routes de votre module (patients, rdvs, etc.)
    // Exemple : Route::resource('patients', '\Modules\Hospital\Http\Controllers\PatientController');

    // Routes pour la Réception
    Route::prefix('reception')->name('hopital.reception.')->group(function () {
        Route::get('/', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'index'])->name('index');

        // Check-in OPD
        Route::get('/opd/checkin', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'showOpdCheckinForm'])->name('opd.checkin');
        Route::post('/opd/checkin', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'processOpdCheckin'])->name('opd.process_checkin');

        // Admission IPD
        Route::get('/ipd/admission', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'showIpdAdmissionForm'])->name('ipd.admission');
        Route::post('/ipd/admission', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'processIpdAdmission'])->name('ipd.process_admission');

        // Prise de Rendez-vous (Peut pointer vers AppointmentController)
        Route::get('/appointments/create', 'AppointmentController@create')->name('appointments.create');
        Route::post('/appointments', 'AppointmentController@store')->name('appointments.store');

        // Demande de Service (Pathologie, Radiologie, Dentisterie, etc.)
        Route::get('/service/request/{service_type?}', 'ServiceRequestController@create')->name('service.request');
        Route::post('/service/request', 'ServiceRequestController@store')->name('service.store');

        // Page "Autres services utiles"
        Route::get('/other-services', [\Modules\Hospital\Http\Controllers\ReceptionController::class, 'otherServices'])->name('other_services'); // Créez cette méthode

        // Gestion de la File d'Attente (peut être dans QueueController ou intégré)
        // Route::get('/queue', 'QueueController@index')->name('queue.index');
        // Route::post('/queue/{queue_item}/call', 'QueueController@callItem')->name('queue.call');
        // ... autres routes de file d'attente
    });

    /*
    // Routes pour la Facturation
    Route::prefix('billing')->name('hopital.billing.')->group(function () {
        Route::get('/', [\Modules\Hospital\Http\Controllers\BillingController::class, 'index')->name('index');
        Route::get('/create/{patient_id?}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'create')->name('create');
        Route::post('/', [\Modules\Hospital\Http\Controllers\BillingController::class, 'store')->name('store');
        Route::get('/{bill}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'show')->name('show');
        Route::get('/{bill}/edit', [\Modules\Hospital\Http\Controllers\BillingController::class, 'edit')->name('edit');
        Route::put('/{bill}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'update')->name('update');
        Route::delete('/{bill}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'destroy')->name('destroy');
        // Routes pour ajouter des éléments à la facture, enregistrer un paiement, etc.
        Route::post('/{bill}/add-item', [\Modules\Hospital\Http\Controllers\BillingController::class, 'addItem')->name('add_item');
        Route::post('/{bill}/add-payment', [\Modules\Hospital\Http\Controllers\BillingController::class, 'addPayment')->name('add_payment');
    });
    */
    // --- Routes pour la Facturation (BillingController) ---
    Route::prefix('billing')->name('hospital.billing.')->group(function () {

        // Route pour afficher la liste de toutes les factures
        // URL: /hopital/billing | Nom: hospital.billing.index
        Route::get('/', [\Modules\Hospital\Http\Controllers\BillingController::class, 'index'])->name('index');

        // Route pour afficher le formulaire de création de facture (l'écran POS)
        // URL: /hopital/billing/create | Nom: hospital.billing.create
        Route::get('/create', [\Modules\Hospital\Http\Controllers\BillingController::class, 'create'])->name('create');

        // Route pour traiter la soumission du formulaire de création de facture
        // URL: /hopital/billing | Nom: hospital.billing.store
        Route::post('/', [\Modules\Hospital\Http\Controllers\BillingController::class, 'store'])->name('store');

        // Route pour afficher une facture spécifique par son ID
        // URL: /hopital/billing/{id} | Nom: hospital.billing.show
        Route::get('/{id}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'show'])->name('show');

        // TODO: Ajouter d'autres routes si nécessaire (edit, update, destroy, etc.)
        // Route::get('/{id}/edit', [\Modules\Hospital\Http\Controllers\BillingController::class, 'edit')->name('edit');
        // Route::put('/{id}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'update')->name('update');
        // Route::delete('/{id}', [\Modules\Hospital\Http\Controllers\BillingController::class, 'destroy')->name('destroy');

        // TODO: Ajouter des routes pour les actions spécifiques (ajouter item via AJAX, enregistrer paiement partiel, imprimer...)
        // Route::post('/{bill}/add-item', [\Modules\Hospital\Http\Controllers\BillingController::class, 'addItem')->name('add_item');
        // Route::post('/{bill}/add-payment', [\Modules\Hospital\Http\Controllers\BillingController::class, 'addPayment')->name('add_payment');

    });

    // Ajoutez d'autres routes du module Hopital ici (Gestion Patients, Labo, Urgences, etc.)

    Route::prefix('patients')->group(function() {
        // Route pour afficher la liste des patients (index)
        Route::get('/', [\Modules\Hospital\Http\Controllers\PatientController::class, 'index'])->name('hospital.patients.index');
   
        // Route pour afficher le formulaire de création d'un patient
        Route::get('/create', [\Modules\Hospital\Http\Controllers\PatientController::class, 'create'])->name('hospital.patients.create');
    
        // Route pour gérer la soumission du formulaire de création (enregistrement)
        Route::post('/', [\Modules\Hospital\Http\Controllers\PatientController::class, 'store'])->name('hospital.patients.store');
   });
});

