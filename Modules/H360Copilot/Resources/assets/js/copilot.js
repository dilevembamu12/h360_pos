$(document).ready(function() {
    var chatContainer = $('#h360-chatbot-container');
    var homeScreen = $('#h360-chatbot-home');
    var conversationScreen = $('#h360-chatbot-conversation');
    var chatBody = $('#h360-chatbot-body');
    var input = $('#h360-chatbot-input');
    var sendBtn = $('#h360-chatbot-send-btn');

    // --- NOUVEAU : Gérer le menu mobile ---
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

    // --- Logique de conversation (mise à jour) ---
    function addMessage(text, type) {
        var messageClass = type;
        var message = $('<div class="ai-chat-message ' + messageClass + '"></div>').text(text);
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
                loadingMessage.text(responseText).removeClass('loading');
                loadingMessage.text(responseText).removeClass('loading');
            },
            error: function() {
                loadingMessage.text("Erreur de connexion avec l'assistant.").removeClass('loading');
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
    $('.suggestion-btn').on('click', function() {
        var prompt = $(this).data('prompt');
        sendMessage(prompt);
    });

    // Ouvre le chatbot
    $(document).on('click', '#start-chatbot', function() {
        chatContainer.fadeIn();
        $('.floating-help-container').removeClass('active');
    });
});