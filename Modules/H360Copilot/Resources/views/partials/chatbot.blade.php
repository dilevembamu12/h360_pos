<div id="h360-chatbot-container" class="ai-chatbot-container no-print" style="display: none;">
    <div class="ai-chatbot-header">
        
        {{-- NOUVEAU : Bouton pour le menu sur mobile --}}
        <button id="h360-chatbot-menu-toggle" class="ai-chatbot-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <h5>H360Copilot</h5>
        <button id="h360-chatbot-close-btn" class="ai-chatbot-close-btn">&times;</button>
    </div>
    <div class="ai-chatbot-main-content">
        {{-- La barre latérale ne change pas --}}
        <div class="ai-chatbot-sidebar">
            <button class="btn btn-primary btn-block" id="h360-start-new-chat-btn">
                <i class="fas fa-plus"></i> Nouvelle Conversation
            </button>
            <hr>
            <h6><i class="fas fa-tools"></i> Outils</h6>
            <div class="ai-chatbot-tools-list">
                <div class="tool-item" data-tool-name="find_customer">
                    <i class="fas fa-user"></i> Trouver un client
                </div>
            </div>
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