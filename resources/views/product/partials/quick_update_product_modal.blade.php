{{-- 3. AJOUT DE LA STRUCTURE DE LA FENÊTRE MODALE (POPUP) --}}
{{-- Utilisez la structure de modale de votre framework CSS (ex: Bootstrap) --}}
<div class="modal fade" id="bulkEditProductModal" tabindex="-1" role="dialog" aria-labelledby="bulkEditProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkEditProductModalLabel">Modification Rapide des Produits</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Formulaire pour les champs à modifier en masse --}}
                {{-- Adaptez ces champs à ce que vous voulez modifier en masse --}}
                <form id="bulkEditProductForm">
                    @csrf {{-- N'oubliez pas le token CSRF --}}

                    <div class="form-group">
                        <label for="bulk_category_id">Catégorie :</label>
                        <select class="form-control select2" id="bulk_category_id" name="category_id" style="width: 100%;">
                            <option value="">Ne pas modifier</option>
                            {{-- Les options pour les catégories seront chargées.
                                 Si vous les passez depuis le contrôleur, décommentez et adaptez : --}}
                            {{-- @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach --}}
                             {{-- Sinon, elles peuvent être chargées via AJAX si nécessaire --}}
                        </select>
                    </div>

                     <div class="form-group">
                        <label for="bulk_brand_id">Marque :</label>
                        <select class="form-control select2" id="bulk_brand_id" name="brand_id" style="width: 100%;">
                            <option value="">Ne pas modifier</option>
                             {{-- Les options pour les marques seront chargées --}}
                             {{-- @foreach($brands as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach --}}
                        </select>
                    </div>

                     <div class="form-group">
                        <label for="bulk_unit_id">Unité :</label>
                        <select class="form-control select2" id="bulk_unit_id" name="unit_id" style="width: 100%;">
                            <option value="">Ne pas modifier</option>
                             {{-- Les options pour les unités seront chargées --}}
                             {{-- @foreach($units as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bulk_sell_price">Prix de vente :</label>
                        {{-- Utilisez une classe pour le formatage numérique si vous en avez une --}}
                        <input type="text" class="form-control input-number" id="bulk_sell_price" name="sell_price" placeholder="Ne pas modifier">
                         <p class="help-block">Laissez vide pour ne pas modifier.</p>
                    </div>

                    {{-- Ajoutez d'autres champs selon vos besoins --}}

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="saveBulkEditBtn">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>
{{-- Fin structure modale --}}

{{-- NOTE : Le script JavaScript doit être inclus après la définition de la table et de la modale.
     Si vous avez une section 'javascript' ou 'scripts', placez-le là. --}}

	 