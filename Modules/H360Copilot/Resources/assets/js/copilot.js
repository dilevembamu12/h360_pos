$(document).ready(function() {
    var chatContainer = $('#h360-chatbot-container');
    var homeScreen = $('#h360-chatbot-home');
    var conversationScreen = $('#h360-chatbot-conversation');
    var chatBody = $('#h360-chatbot-body');
    var input = $('#h360-chatbot-input');
    var sendBtn = $('#h360-chatbot-send-btn');

    // --- Gérer le menu mobile ---
    $('#h360-chatbot-menu-toggle').on('click', function() {
        chatContainer.toggleClass('sidebar-open');
    });

    // Ferme le menu mobile si on clique sur un outil ou une conversation
    $('.ai-chatbot-sidebar').on('click', '.tool-item, .history-item, #h360-start-new-chat-btn', function() {
        if ($(window).width() < 768) {
            chatContainer.removeClass('sidebar-open');
        }
    });

    // --- Navigation entre les écrans ---
    function showConversation() {
        homeScreen.hide();
        conversationScreen.css('display', 'flex');
        input.focus();
    }

    function showHome() {
        conversationScreen.hide();
        homeScreen.show();
    }

    $('#h360-start-new-chat').on('click', showConversation);
    $('#h360-chatbot-close-btn').on('click', function() {
        chatContainer.fadeOut(function() {
            showHome(); // Réinitialise à l'accueil à la fermeture
            chatBody.html('<div class="ai-chat-message bot">Bonjour ! Je suis votre Copilote. Comment puis-je vous aider ?</div>');
        });
    });

    /**
     * NOUVEAU: Fonction de formatage Markdown avancée pour les réponses du bot.
     * Gère les titres, listes, blocs de code, gras, italique, etc.
     *
     * @param {string} text - Le texte brut de l'IA au format Markdown.
     * @returns {string} - Le texte formaté en HTML.
     */
    function formatBotMessage(text) {
        // Fonction pour échapper les caractères HTML de base dans le contenu
        const escapeHtml = (unsafe) => {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        };

        let html = '\n' + text + '\n';

        // --- Blocs de code (doivent être traités en premier) ---
        // ```lang\n code \n```
        html = html.replace(/\n```(\w*)\n([\s\S]+?)\n```\n/g, (match, lang, code) => {
            const escapedCode = escapeHtml(code);
            // Nous ajoutons une classe pour la coloration syntaxique, qui peut être gérée par une bibliothèque comme Prism.js ou highlight.js
            return `\n<pre><code class="language-${lang || ''}">${escapedCode.trim()}</code></pre>\n`;
        });
        
        // --- Éléments de bloc ---
        
        // Titres (h1-h6)
        html = html.replace(/^\s*(#{1,6})\s+(.*)/gm, (match, hashes, content) => {
            const level = hashes.length;
            return `<h${level}>${content}</h${level}>`;
        });

        // Blockquotes
        html = html.replace(/((?:^> .*(?:\n|$))+)/gm, (match) => {
            const content = match.replace(/^> /gm, '');
            return `<blockquote>${content.replace(/\n$/, '').replace(/\n/g, '<br>')}</blockquote>`;
        });
        
        // Lignes horizontales
        html = html.replace(/^\s*(?:---|\*\*\*|___)\s*$/gm, '<hr>');

        // Listes (regroupe les éléments consécutifs)
        // Non ordonnée
        html = html.replace(/((?:^\s*[\*\-\+]\s.*\n?)+)/gm, (match) => {
            const items = match.trim().split('\n').map(item => `<li>${item.replace(/^\s*[\*\-\+]\s/, '')}</li>`).join('');
            return `<ul>${items}</ul>`;
        });

        // Ordonnée
        html = html.replace(/((?:^\s*\d+\.\s.*\n?)+)/gm, (match) => {
            const items = match.trim().split('\n').map(item => `<li>${item.replace(/^\s*\d+\.\s/, '')}</li>`).join('');
            return `<ol>${items}</ol>`;
        });

        // --- Éléments en ligne (après les blocs) ---
        
        // Liens: [texte](url)
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
        
        // Gras et Italique (***texte*** ou ___text___)
        html = html.replace(/(\*\*\*|___)(.*?)\1/g, '<strong><em>$2</em></strong>');

        // Gras (**text** ou __text__)
        html = html.replace(/(\*\*|__)(.*?)\1/g, '<strong>$2</strong>');

        // Italique (*text* ou _text_)
        html = html.replace(/(\*|_)(.*?)\1/g, '<em>$2</em>');

        // Barré (~~text~~)
        html = html.replace(/~~(.*?)~~/g, '<s>$1</s>');
        
        // Code en ligne: `code`
        html = html.replace(/`([^`]+)`/g, (match, code) => `<code>${escapeHtml(code)}</code>`);

        // --- Paragraphes et sauts de ligne ---
        html = html.split(/\n\s*\n/).map(paragraph => {
            paragraph = paragraph.trim();
            if (!paragraph) return '';

            // Ne pas envelopper les éléments de bloc déjà formatés dans des balises <p>
            if (paragraph.match(/^\s*<(ul|ol|li|h[1-6]|blockquote|pre|hr|table|thead|tbody|tr|th|td)/)) {
                return paragraph;
            }
            
            // Remplace les sauts de ligne simples par <br> à l'intérieur des paragraphes
            return `<p>${paragraph.replace(/\n/g, '<br>')}</p>`;
        }).join('').replace(/<br>\s*<br>/g, '<br>');

        return html.trim();
    }

    // --- Logique de conversation (MISE À JOUR) ---
    function addMessage(text, type) {
        var messageClass = type;
        var message = $('<div class="ai-chat-message ' + messageClass + '"></div>');
        
        // Pour les messages du bot, utiliser .html() pour afficher le formatage.
        // Pour les messages de l'utilisateur, utiliser .text() pour des raisons de sécurité.
        if (type === 'bot' || type === 'bot loading') {
            message.html(formatBotMessage(text));
        } else {
            message.text(text);
        }
        
        chatBody.append(message);
        chatBody.scrollTop(chatBody[0].scrollHeight);
        return message;
    }

    function sendMessage(promptText) {
        var prompt = promptText || input.val().trim();
        if (prompt === '') return;

        showConversation();
        addMessage(prompt, 'user');
        input.val('');
        sendBtn.prop('disabled', true);
        var loadingMessage = addMessage('...', 'bot loading');

        $.ajax({
            method: 'POST',
            url: window.copilot_ask_url,
            data: { prompt: prompt, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                var responseText = response.output || "Désolé, je n'ai pas pu obtenir de réponse.";
                // Mettre à jour le message de chargement avec la réponse formatée
                loadingMessage.html(formatBotMessage(responseText)).removeClass('loading');
            },
            error: function() {
                 // Mettre à jour avec un message d'erreur formaté
                loadingMessage.html(formatBotMessage("Erreur de connexion avec l'assistant.")).removeClass('loading');
            },
            complete: function() {
                sendBtn.prop('disabled', false);
            }
        });
    }

    sendBtn.on('click', () => sendMessage());
    input.on('keypress', function(e) {
        if (e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Gérer le clic sur les suggestions
    $(document).on('click', '.suggestion-btn, .suggestion-pill', function() {
        var prompt = $(this).data('prompt');
        sendMessage(prompt);
    });

    // Ouvre le chatbot
    $(document).on('click', '#start-chatbot', function() {
        chatContainer.fadeIn();
        $('.floating-help-container').removeClass('active');
    });
});

