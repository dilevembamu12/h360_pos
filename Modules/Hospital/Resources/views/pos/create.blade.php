@extends('hospital::layouts.master') {{-- Assurez-vous d'avoir un layout de base pour votre module Hospital --}}

@section('title', 'Hôpital - Point de Vente Facturation')

@section('content')
    <section class="content no-print">
        {{-- Alertes et messages (erreurs, succès, etc.) --}}
        {{--  @include('sweetalert::alert') {{-- Exemple d'inclusion de SweetAlert pour les messages --}}
        {{--  @include('layouts.partials.error') {{-- Inclure un partial pour afficher les erreurs de validation --}}

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Facturation Hospitalière <small>Point de Vente</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            {{-- Colonne Gauche: Sélection Patient & Contexte (OPD/IPD) --}}
                            <div class="col-md-4">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Informations Patient</h4>
                                    </div>
                                    <div class="box-body">
                                        {{-- Champ pour rechercher un patient --}}
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                                <input type="text" class="form-control" id="patient_search" placeholder="Rechercher Patient (Nom, ID, Tél)">
                                            </div>
                                            {{-- Champ caché pour stocker l'ID du patient sélectionné --}}
                                            <input type="hidden" name="patient_id" id="patient_id" value="">
                                             {{-- TODO: Implémenter la recherche AJAX et l'affichage des résultats --}}
                                        </div>

                                        {{-- Affichage des informations du patient sélectionné --}}
                                        <div id="selected_patient_details" style="display: none;">
                                            <h4>Patient Sélectionné:</h4>
                                            <p><strong>Nom:</strong> <span id="display_patient_name"></span></p>
                                            <p><strong>ID Patient:</strong> <span id="display_patient_id"></span></p>
                                            <p><strong>Téléphone:</strong> <span id="display_patient_phone"></span></p>
                                            {{-- TODO: Afficher d'autres infos pertinentes (dernier visite, admissions actives...) --}}
                                            <hr>
                                        </div>

                                        {{-- Sélection du Contexte: OPD ou IPD --}}
                                        <div class="form-group">
                                            <label>Contexte de Facturation:</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="billing_context" id="context_opd" value="opd" checked>
                                                    OPD (Consultation Externe)
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="billing_context" id="context_ipd" value="ipd">
                                                    IPD (Hospitalisation)
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Sélection du Lit (Visible SEULEMENT si le contexte IPD est sélectionné et un patient avec admission active) --}}
                                        <div class="form-group" id="bed_selection_section" style="display: none;">
                                            <label for="bed_id">Lit Attribué (IPD):</label>
                                            <select class="form-control" id="bed_id" name="bed_id">
                                                <option value="">-- Sélectionner Lit --</option>
                                                {{-- TODO: Remplir dynamiquement avec les lits disponibles ou attribués au patient IPD --}}
                                                 {{-- @foreach($availableBeds as $bed)
                                                     <option value="{{ $bed->id }}">{{ $bed->bed_number }} - {{ $bed->room->name }} ({{ $bed->ward->name }})</option>
                                                 @endforeach --}}
                                                 <option value="1">Chambre 101 - Lit A</option>
                                                 <option value="2">Chambre 101 - Lit B</option>
                                            </select>
                                            {{-- TODO: Afficher un message si le patient IPD n'a pas de lit attribué ou pas d'admission active --}}
                                        </div>

                                         {{-- TODO: Ajouter ici la possibilité de lier à une visite/admission spécifique si le patient en a plusieurs --}}
                                          <div class="form-group" id="visit_admission_selection_section" style="display: none;">
                                              <label for="visit_admission_id">Lier à la Visite/Admission:</label>
                                               <select class="form-control" id="visit_admission_id" name="visit_admission_id">
                                                   <option value="">-- Sélectionner Visite/Admission --</option>
                                                   {{-- TODO: Remplir dynamiquement avec les visites/admissions actives du patient sélectionné --}}
                                                   {{-- Exemple : <option value="V123">Visite OPD #123 (2023-10-27)</option> --}}
                                                    {{-- Exemple : <option value="A456">Admission IPD #456 (Dep. Chirurgie)</option> --}}
                                               </select>
                                          </div>

                                    </div>
                                </div>
                            </div>

                             {{-- Colonne Droite: Catégories et Services/Produits --}}
                            <div class="col-md-8">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Services & Produits</h4>
                                    </div>
                                    <div class="box-body">
                                        {{-- Barre de recherche de services/produits --}}
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                                <input type="text" class="form-control" id="service_product_search" placeholder="Rechercher Service ou Produit">
                                            </div>
                                             {{-- TODO: Implémenter la recherche AJAX et l'affichage des résultats --}}
                                        </div>

                                        {{-- Onglets/Boutons des Catégories de Services --}}
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                {{-- TODO: Générer dynamiquement les onglets de catégorie --}}
                                                 <li class="active"><a href="#cat_consultation" data-toggle="tab" aria-expanded="true">Consultation</a></li>
                                                <li class=""><a href="#cat_dentistry" data-toggle="tab" aria-expanded="false">Dentisterie</a></li>
                                                <li class=""><a href="#cat_pathology" data-toggle="tab" aria-expanded="false">Pathologie</a></li>
                                                <li class=""><a href="#cat_radiology" data-toggle="tab" data-toggle="tab" aria-expanded="false">Radiologie</a></li>
                                                <li class=""><a href="#cat_maternity" data-toggle="tab" aria-expanded="false">Maternité</a></li>
                                                 <li class=""><a href="#cat_pharmacy" data-toggle="tab" aria-expanded="false">Pharmacie/Produits</a> {{-- Pour les médicaments/fournitures --}}
                                                </li>
                                                 <li class=""><a href="#cat_other" data-toggle="tab" aria-expanded="false">Autres</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                {{-- Contenu de chaque onglet de catégorie --}}
                                                 {{-- TODO: Remplir dynamiquement le contenu de chaque onglet avec les services/produits de cette catégorie --}}
                                                 {{-- Chaque service/produit devrait être un bouton ou un élément cliquable pour l'ajouter au panier --}}

                                                {{-- Exemple de contenu pour un onglet (Consultation) --}}
                                                <div class="tab-pane active" id="cat_consultation">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <button type="button" class="btn btn-app btn-flat add_service_btn" data-service-id="C1" data-service-name="Consultation Générale" data-price="50.00">
                                                                <i class="fa fa-user-md"></i> Consultation Générale
                                                            </button>
                                                        </div>
                                                         <div class="col-md-4 mb-3">
                                                            <button type="button" class="btn btn-app btn-flat add_service_btn" data-service-id="C2" data-service-name="Consultation Spécialiste" data-price="80.00">
                                                                <i class="fa fa-user-md"></i> Consultation Spécialiste
                                                            </button>
                                                        </div>
                                                        {{-- ... autres services de consultation --}}
                                                    </div>
                                                </div>
                                                {{-- Exemple de contenu pour un autre onglet (Dentisterie) --}}
                                                <div class="tab-pane" id="cat_dentistry">
                                                    <div class="row">
                                                         <div class="col-md-4 mb-3">
                                                            <button type="button" class="btn btn-app btn-flat add_service_btn" data-service-id="D1" data-service-name="Détartrage" data-price="60.00">
                                                                <i class="fa fa-tooth"></i> Détartrage
                                                            </button>
                                                        </div>
                                                         {{-- ... autres services de dentisterie --}}
                                                    </div>
                                                </div>
                                                 {{-- ... autres onglets (Pathologie, Radiologie, Maternité, etc.) --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ligne du Panier / Articles de Facturation --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Articles de Facturation</h4>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-condensed" id="billing_items_table">
                                            <thead>
                                                <tr>
                                                    <th>Service/Produit</th>
                                                    <th>Prix Unitaire</th>
                                                    <th style="width: 80px;">Quantité</th>
                                                     <th>Réduction (%)</th>
                                                     <th>TVA (%)</th> {{-- Si applicable --}}
                                                    <th>Sous-total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Les lignes des articles de facturation seront ajoutées ici par JavaScript --}}
                                                 <tr id="no_items_row"><td colspan="7" class="text-center">Aucun article ajouté.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                     <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-offset-7 col-md-5">
                                                <p><strong>Total Articles:</strong> <span id="total_item_count">0</span></p>
                                                <p><strong>Sous-total:</strong> <span id="subtotal_amount">0.00</span></p>
                                                {{-- TODO: Afficher les réductions, TVA, Frais supplémentaires si applicable --}}
                                                <h3><strong>Total Net:</strong> <span id="total_payable_amount">0.00</span></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         {{-- Ligne Paiement --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Paiement</h4>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_method">Méthode de Paiement <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                                        <option value="">-- Sélectionner Méthode --</option>
                                                        {{-- TODO: Remplir dynamiquement avec les méthodes de paiement disponibles --}}
                                                         {{-- @foreach($paymentMethods as $method)
                                                             <option value="{{ $method->value }}">{{ $method->label }}</option>
                                                         @endforeach --}}
                                                         <option value="cash">Espèces</option>
                                                         <option value="card">Carte Bancaire</option>
                                                         <option value="insurance">Assurance</option>
                                                         <option value="transfer">Virement</option>
                                                    </select>
                                                </div>
                                                 <div class="form-group">
                                                    <label for="amount_paid">Montant Payé <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="amount_paid" name="amount_paid" required min="0" step="0.01" value="0"> {{-- Peut être pré-rempli avec le total --}}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                 <div class="form-group">
                                                    <label for="payment_note">Notes de Paiement</label>
                                                    <textarea class="form-control" id="payment_note" name="payment_note" rows="3"></textarea>
                                                </div>
                                                {{-- TODO: Ajouter d'autres champs de paiement si nécessaire (numéro de transaction, banque, détails assurance...) --}}
                                            </div>
                                        </div>
                                        {{-- TODO: Bouton "Ajouter Paiement" si paiements multiples possibles --}}
                                         {{-- TODO: Afficher le montant dû ou le change --}}
                                          <div class="row">
                                              <div class="col-md-12 text-right">
                                                  <h4>Montant Dû: <span class="text-danger" id="amount_due">0.00</span></h4>
                                              </div>
                                          </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Actions Finale --}}
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-danger btn-lg" onclick="confirmCancelBill()">Annuler</button>
                                <button type="button" class="btn btn-warning btn-lg" onclick="saveBillAsDraft()">Enregistrer Brouillon</button> {{-- Optionnel --}}
                                 {{-- Le bouton Finaliser soumettra le formulaire --}}
                                <button type="submit" class="btn btn-success btn-lg" id="finalize_bill_btn" disabled>Finaliser Facture</button>
                            </div>
                        </div>

                    </div> {{-- /.box-body --}}
                </div> {{-- /.box --}}
            </div> {{-- /.col --}}
        </div> {{-- /.row --}}

    </section>
     {{-- Ce formulaire sera soumis par le bouton Finaliser --}}
    <form id="billing_form" action="{{ route('hospital.billing.store') }}" method="POST" style="display: none;">
         @csrf
         {{-- Inclure les champs nécessaires pour la soumission --}}
         <input type="hidden" name="patient_id" id="form_patient_id">
         <input type="hidden" name="billing_context" id="form_billing_context">
         <input type="hidden" name="bed_id" id="form_bed_id"> {{-- Sera null si pas IPD --}}
          <input type="hidden" name="visit_admission_id" id="form_visit_admission_id">

         {{-- Les articles de facturation seront ajoutés ici en JSON ou en champs cachés par JavaScript --}}
         <input type="hidden" name="billing_items_json" id="form_billing_items_json">

         {{-- Informations de paiement --}}
         <input type="hidden" name="payment_method" id="form_payment_method">
         <input type="hidden" name="amount_paid" id="form_amount_paid">
         <input type="hidden" name="payment_note" id="form_payment_note">
          {{-- TODO: Ajouter d'autres champs de paiement --}}

           {{-- Total et autres calculs --}}
         <input type="hidden" name="total_amount" id="form_total_amount">

    </form>


@endsection

@push('scripts')
<script>
    // Déclarer un tableau pour stocker les articles du panier
    var billingItems = [];

    $(document).ready(function() {
        // Initialiser les événements
        setupEventListeners();
        calculateTotals(); // Calculer les totaux initiaux (devrait être 0)
    });

    function setupEventListeners() {
        // TODO: Implémenter la logique de recherche AJAX pour #patient_search
        // Quand un patient est sélectionné:
        // - Remplir #patient_id et #form_patient_id
        // - Afficher #selected_patient_details et remplir les spans avec les infos du patient
        // - Cacher le message "Aucun patient ajouté." dans le panier

        // TODO: Implémenter la logique de recherche AJAX pour #service_product_search

        // Gérer le clic sur les boutons de catégorie (onglets) pour afficher les services pertinents
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
             var categoryId = $(e.target).attr('href').substring(1); // Ex: 'cat_consultation'
             console.log('Onglet cliqué:', categoryId);
             // TODO: Charger les services/produits de cette catégorie via AJAX et les afficher
             // dans le contenu de l'onglet correspondant.
        });

        // Gérer le clic sur les boutons "Ajouter Service/Produit"
        $('.add_service_btn').on('click', function() {
            var serviceId = $(this).data('service-id');
            var serviceName = $(this).data('service-name');
            var price = parseFloat($(this).data('price'));

            addItemToBill(serviceId, serviceName, price);
        });

        // Gérer le changement du contexte OPD/IPD
        $('input[name="billing_context"]').on('change', function() {
            handleContextChange($(this).val());
        });

        // Gérer les changements de quantité, réduction, etc. dans le panier
        $('#billing_items_table').on('change', 'input[type="number"], input[type="text"]', function() {
             // TODO: Mettre à jour l'article correspondant dans le tableau billingItems
             // et recalculer les totaux.
             // var itemId = $(this).closest('tr').data('item-id');
             calculateTotals();
        });

         // Gérer la suppression d'un article du panier
        $('#billing_items_table').on('click', '.remove_item_btn', function() {
             var itemId = $(this).closest('tr').data('item-id');
             removeItemFromBill(itemId);
        });

         // Gérer le changement du montant payé pour calculer le montant dû
         $('#amount_paid').on('input', function() {
             calculateTotals();
         });

         // Gérer la soumission du formulaire via le bouton "Finaliser Facture"
         $('#finalize_bill_btn').on('click', function() {
             if ($('#patient_id').val() == '') {
                 alert('Veuillez sélectionner un patient.');
                 $('#patient_search').focus();
                 return false;
             }
             if (billingItems.length === 0) {
                  alert('Veuillez ajouter au moins un article à la facture.');
                  return false;
             }
             if ($('#amount_paid').val() === '' || parseFloat($('#amount_paid').val()) < 0) {
                  alert('Veuillez saisir un montant payé valide.');
                  $('#amount_paid').focus();
                  return false;
             }
              if ($('#payment_method').val() === '') {
                  alert('Veuillez sélectionner une méthode de paiement.');
                  $('#payment_method').focus();
                  return false;
             }


             // Remplir le formulaire caché avant de soumettre
             $('#form_patient_id').val($('#patient_id').val());
             $('#form_billing_context').val($('input[name="billing_context"]:checked').val());
             $('#form_bed_id').val($('#bed_id').val()); // Sera vide si pas IPD
              $('#form_visit_admission_id').val($('#visit_admission_id').val());

             // Convertir le tableau billingItems en JSON
             $('#form_billing_items_json').val(JSON.stringify(billingItems));

             // Remplir les infos de paiement
             $('#form_payment_method').val($('#payment_method').val());
             $('#form_amount_paid').val($('#amount_paid').val());
             $('#form_payment_note').val($('#payment_note').val());
             $('#form_total_amount').val($('#total_payable_amount').text()); // Utilise le texte affiché pour l'exemple

             // Soumettre le formulaire caché
             $('#billing_form').submit();
         });

         // TODO: Implémenter confirmCancelBill() et saveBillAsDraft()
    }

    // Fonction pour gérer le changement de contexte (OPD/IPD)
    function handleContextChange(context) {
        console.log('Contexte changé:', context);
        if (context === 'ipd') {
            // Si IPD, afficher la section de sélection du lit
            $('#bed_selection_section').show();
             // TODO: Potentiellement rendre le champ du patient obligatoire si pas déjà fait
             // TODO: Potentiellement filtrer/charger les lits disponibles
        } else {
            // Si OPD, cacher la section de sélection du lit
            $('#bed_selection_section').hide();
             $('#bed_id').val(''); // Réinitialiser la sélection du lit
        }
         // TODO: Afficher/cacher la section de sélection visite/admission si nécessaire
         // Afficher #visit_admission_selection_section si le patient a des visites/admissions actives
    }


    // Fonction pour ajouter un article à la facture (panier)
    function addItemToBill(serviceId, serviceName, price) {
         // TODO: Vérifier si l'article existe déjà et augmenter la quantité
         // Pour l'instant, on ajoute toujours une nouvelle ligne
        var newItem = {
            id: Date.now(), // ID temporaire unique pour l'élément dans le panier
            service_id: serviceId,
            service_name: serviceName,
            unit_price: price,
            quantity: 1,
            discount_percent: 0,
            tax_percent: 0, // TODO: Gérer la TVA
            subtotal: price // Calculé initialement
        };

        billingItems.push(newItem);
        renderBillingItems(); // Mettre à jour l'affichage
        calculateTotals(); // Recalculer les totaux
    }

     // Fonction pour supprimer un article de la facture (panier)
    function removeItemFromBill(itemId) {
        billingItems = billingItems.filter(item => item.id !== itemId);
        renderBillingItems(); // Mettre à jour l'affichage
        calculateTotals(); // Recalculer les totaux
    }


    // Fonction pour afficher les articles dans la table
    function renderBillingItems() {
        var $tbody = $('#billing_items_table tbody');
        $tbody.empty(); // Vider la table actuelle

        if (billingItems.length === 0) {
            $tbody.append('<tr id="no_items_row"><td colspan="7" class="text-center">Aucun article ajouté.</td></tr>');
             $('#finalize_bill_btn').prop('disabled', true); // Désactiver le bouton de finalisation si pas d'articles
             $('#total_item_count').text(0);
        } else {
             $('#finalize_bill_btn').prop('disabled', false); // Activer le bouton
             $('#total_item_count').text(billingItems.length);
            billingItems.forEach(function(item) {
                var rowHtml = `
                    <tr data-item-id="${item.id}">
                        <td>${item.service_name}</td>
                        <td>${item.unit_price.toFixed(2)}</td>
                        <td>
                            <input type="number" class="form-control input-sm quantity" value="${item.quantity}" min="1" step="1" style="width: 60px;">
                        </td>
                        <td>
                             <input type="text" class="form-control input-sm discount" value="${item.discount_percent}" style="width: 60px;">
                        </td>
                         <td>
                             <input type="text" class="form-control input-sm tax" value="${item.tax_percent}" style="width: 60px;"> {{-- TODO: Rendre ceci un select ou un span si la TVA est fixe --}}
                        </td>
                        <td class="item-subtotal">${item.subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-danger remove_item_btn"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `;
                $tbody.append(rowHtml);
            });
        }
         // TODO: Lier les événements 'change' aux nouvelles inputs de quantité/réduction/taxe ici ou utiliser la délégation d'événement
    }

    // Fonction pour calculer les totaux (sous-total, total net, montant dû)
    function calculateTotals() {
        var subtotal = 0;
        var totalPayable = 0;

        billingItems.forEach(function(item) {
            // TODO: Recalculer le sous-total de l'article en fonction de la quantité, réduction, TVA
             var itemSubtotal = item.quantity * item.unit_price;
             var discountAmount = itemSubtotal * (item.discount_percent / 100);
             var taxAmount = (itemSubtotal - discountAmount) * (item.tax_percent / 100);
             item.subtotal = itemSubtotal - discountAmount + taxAmount; // Mettre à jour l'objet item

            subtotal += itemSubtotal; // Sous-total avant réductions/taxes
            totalPayable += item.subtotal; // Total de l'article après réductions/taxes
        });

        // Mettre à jour les affichages
        $('#subtotal_amount').text(subtotal.toFixed(2)); // Afficher le sous-total avant calculs complexes si désiré
        $('#total_payable_amount').text(totalPayable.toFixed(2));

        // Calculer le montant dû
        var amountPaid = parseFloat($('#amount_paid').val()) || 0;
        var amountDue = totalPayable - amountPaid;
        $('#amount_due').text(amountDue.toFixed(2));

        // TODO: Activer/désactiver le bouton finaliser en fonction de la présence d'articles et si le patient est sélectionné.
        // La logique est déjà dans renderBillingItems, mais pourrait être centralisée ici.
    }

    // TODO: Implémenter les fonctions confirmCancelBill() et saveBillAsDraft()
    function confirmCancelBill() {
        if (confirm('Êtes-vous sûr de vouloir annuler cette facture ? Toutes les données non enregistrées seront perdues.')) {
            window.location.href = "route('hospital.reception.index')"; // Ou rediriger vers la liste des factures
        }
    }

     function saveBillAsDraft() {
         // TODO: Implémenter la logique pour enregistrer la facture comme brouillon
         // Cela nécessiterait une route et une méthode de contrôleur séparées.
         alert('Fonctionnalité "Enregistrer Brouillon" non implémentée.');
     }


</script>
@endpush