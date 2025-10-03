@php
$toolsByCategory = [
    [
        'name' => 'Recherche & Consultation',
        'icon' => 'fas fa-search',
        'color_class' => 'text-primary',
        'tools' => [
            'TrouverClient', 'VerifierSoldeClient', 'HistoriqueAchatsClient', 'TrouverFournisseur', 
            'ListerClientsAvecSoldeDu', 'ChercherProduit', 'VerifierStockProduit', 'ListerProduitsEnRupture', 
            'ObtenirPrixProduit', 'ChercherFacture', 'ListerVentesRecentes', 'VerifierStatutPaiementFacture'
        ]
    ],
    [
        'name' => 'Création',
        'icon' => 'fas fa-plus-circle',
        'color_class' => 'text-success',
        'tools' => [
            'CreerFacture', 'CreerDevis', 'EnregistrerRetourVente', 'CreerCommandeFournisseur', 
            'EnregistrerDepense', 'AjouterClient', 'AjouterFournisseur', 'AjouterProduitSimple', 
            'EffectuerAjustementStock'
        ]
    ],
    [
        'name' => 'Mise à Jour',
        'icon' => 'fas fa-edit',
        'color_class' => 'text-warning',
        'tools' => [
            'ModifierPrixProduit', 'MettreAJourStock', 'ChangerStatutCommande', 
            'AppliquerPaiementFacture', 'ModifierInfosClient'
        ]
    ],
    [
        'name' => 'Analyse & Rapports',
        'icon' => 'fas fa-chart-bar',
        'color_class' => 'text-info',
        'tools' => [
           'RapportVentesJournalier', 'RapportVentesPeriode', 'ComparerVentes', 
           'ProduitsPlusVendus', 'ProduitsMoinsVendus', 'MeilleursClients', 'ResumeDepenses'
        ]
    ],
    [
        'name' => 'Notification',
        'icon' => 'fas fa-bell',
        'color_class' => 'text-purple',
        'style' => 'color: #6f42c1;',
        'tools' => [
            'EnvoyerFactureParEmail', 'EnvoyerRappelPaiement', 'CreerRappel', 'NotifierStockFaible'
        ]
    ],
    [
        'name' => 'Procédure',
        'icon' => 'fas fa-cogs',
        'color_class' => 'text-danger',
        'tools' => [
            'CloturerLaCaisse', 'LancerInventaire', 'GenererSuggestionReapprovisionnement'
        ]
    ]
];
@endphp

<div id="h360-chatbot-container" class="ai-chatbot-container no-print" style="display: none;">
    <div class="ai-chatbot-header">
        <button id="h360-chatbot-menu-toggle" class="ai-chatbot-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h5>H360Copilot</h5>
        <button id="h360-chatbot-close-btn" class="ai-chatbot-close-btn">&times;</button>
    </div>
    <div class="ai-chatbot-main-content">
        {{-- Barre latérale avec la nouvelle configuration des outils --}}
        <div class="ai-chatbot-sidebar">
            <button class="btn btn-primary btn-block" id="h360-start-new-chat-btn">
                <i class="fas fa-plus"></i> Nouvelle Conversation
            </button>
            <hr>
            
            {{-- START: NOUVELLE SECTION DE CONFIGURATION DES OUTILS --}}
            <h6><i class="fas fa-tools"></i> Activer les Outils</h6>
            <div class="ai-chatbot-tools-config">
                <div class="tools-actions mb-2">
                    <button id="selectAllTools" class="btn btn-xs btn-default">Tout cocher</button>
                    <button id="deselectAllTools" class="btn btn-xs btn-default">Tout décocher</button>
                </div>

                <div class="tools-accordion" id="toolsAccordion">
                    {{-- Boucle pour générer les catégories et les outils --}}
                    @foreach($toolsByCategory as $category)
                        <div class="tool-category">
                            <div class="tool-category-header">
                                <span><i class="{{ $category['icon'] }} {{ $category['color_class'] ?? '' }}" style="{{ $category['style'] ?? '' }}"></i> {{ $category['name'] }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="tool-category-body">
                                @foreach($category['tools'] as $tool)
                                    <label><input type="checkbox" data-tool-name="{{ $tool }}"> {{ $tool }}</label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button id="saveTools" class="btn btn-success btn-block btn-sm mt-3">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
            {{-- END: NOUVELLE SECTION DE CONFIGURATION DES OUTILS --}}


            <hr>
            <h6><i class="fas fa-history"></i> Historique</h6>
            <div class="ai-chatbot-history" id="h360-chatbot-history">
                <p class="text-muted">Aucune conversation.</p>
            </div>
        </div>

        {{-- La zone de conversation ne change pas --}}
        <div class="ai-chatbot-conversation-area">
            <div class="ai-chatbot-body" id="h360-chatbot-body">
                {{-- Les messages apparaîtront ici --}}
            </div>
            <div class="ai-chatbot-suggestions" id="h360-chatbot-suggestions">
                <div class="suggestion-pill" data-prompt="Quelles ont été mes ventes d'hier ?">Ventes d'hier ?</div>
                <div class="suggestion-pill" data-prompt="Montre-moi les produits avec un stock faible.">Stock faible ?</div>
            </div>
            <div class="ai-chatbot-footer">
                <textarea id="h360-chatbot-input" placeholder="Posez une question ou utilisez un outil..."></textarea>
                <button id="h360-chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<style>
.ai-chatbot-tools-config {
    padding: 0 10px;
}
.tools-actions {
    display: flex;
    justify-content: space-between;
}
.tools-accordion {
    max-height: 250px; /* ou la hauteur que vous préférez */
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.tool-category-header {
    background-color: #f7f7f7;
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
    font-size: 13px;
}
.tool-category-header:hover {
    background-color: #efefef;
}
.tool-category-header span {
    display: flex;
    align-items: center;
}
.tool-category-header span i {
    margin-right: 8px;
    width: 16px;
}
.tool-category-header .fa-chevron-down {
    transition: transform 0.3s ease;
}
.tool-category.open .tool-category-header .fa-chevron-down {
    transform: rotate(180deg);
}
.tool-category-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, padding 0.3s ease-out;
    background-color: #fff;
    padding: 0 12px;
}
.tool-category.open .tool-category-body {
    padding: 10px 12px;
}
.tool-category-body label {
    display: flex;
    align-items: center;
    font-weight: normal;
    font-size: 12px;
    margin-bottom: 5px;
    cursor: pointer;
}
.tool-category-body input[type="checkbox"] {
    margin-right: 8px;
}

/* Scrollbar styles */
.tools-accordion::-webkit-scrollbar {
  width: 5px;
}
.tools-accordion::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.tools-accordion::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 5px;
}
.tools-accordion::-webkit-scrollbar-thumb:hover {
  background: #555;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Accordion functionality
    const accordionHeaders = document.querySelectorAll('.tool-category-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const category = header.parentElement;
            const body = header.nextElementSibling;
            
            category.classList.toggle('open');

            if (category.classList.contains('open')) {
                body.style.maxHeight = body.scrollHeight+50 + "px";
            } else {
                body.style.maxHeight = '0';
            }
        });
    });

    // Open the first category by default
    if (accordionHeaders.length > 0) {
        const firstCategory = accordionHeaders[0].parentElement;
        const firstBody = accordionHeaders[0].nextElementSibling;
        firstCategory.classList.add('open');
        firstBody.style.maxHeight = firstBody.scrollHeight + "px";
    }

    // Tools selection functionality
    const allCheckboxes = document.querySelectorAll('.ai-chatbot-tools-config input[type="checkbox"]');
    const selectAllBtn = document.getElementById('selectAllTools');
    const deselectAllBtn = document.getElementById('deselectAllTools');
    const saveBtn = document.getElementById('saveTools');

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            allCheckboxes.forEach(checkbox => checkbox.checked = true);
        });
    }

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', () => {
            allCheckboxes.forEach(checkbox => checkbox.checked = false);
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            const selectedTools = [];
            allCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedTools.push(checkbox.dataset.toolName);
                }
            });
            
            console.log("Outils enregistrés:", selectedTools);
            
            // Add visual feedback for saving
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-check"></i> Enregistré !';
            saveBtn.disabled = true;
            
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }, 2000);

            // You can now send 'selectedTools' to your backend via an AJAX call
            // For example:
            // $.ajax({
            //     method: 'POST',
            //     url: '/copilot/save-tools',
            //     data: { tools: selectedTools, _token: '{{ csrf_token() }}' },
            //     success: function(response) {
            //         console.log('Configuration sauvegardée !');
            //     }
            // });
        });
    }
});
</script>

