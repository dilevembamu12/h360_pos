<?php

use Modules\OpenAI\Http\Controllers\Admin\v2\ImageController as AdminV2ImageController;
use Modules\OpenAI\Http\Controllers\Admin\v2\AiChatbotController;
use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Http\Controllers\Admin\{
    UseCasesController,
    UseCaseCategoriesController,
    OpenAIController,
    ImageController,
    CodeController,
    TextToSpeechController as AdminTextToSpeechController,
    ChatCategoriesController,
    ChatAssistantsController,
    SpeechToTextController,
    LongArticleController as AdminLongArticleController,
    ProviderManageController,
    FeaturePreferenceController,
    PrebuiltTemplateContentController
};
use Modules\OpenAI\Http\Controllers\Customer\{
    OpenAIController as UserAIController,
    SpeechToTextController as UserSpeechToTextController,
    ImageController as UserImageController,
    UseCasesController as CustomerUseCasesController,
    DocumentsController as CustomerDocumentsController,
    CodeController as CustomerCodeController,
    ChatController,
    TextToSpeechController,
    FolderController,
    LongArticleController,
    PrebuiltTemplateContentController as CustomerPrebuiltTemplateContentController
};
use Modules\OpenAI\Http\Controllers\Api\V1\Admin\{
    UseCasesController as AdminUsecaseAPI,
    UseCaseCategoriesController as AdminUseCaseCategoryAPI,
    OpenAIController as AdminAPI,
    ImageController as AdminImageAPI,
    CodeController as AdminCodeAPI,
};
use Modules\OpenAI\Http\Controllers\Api\V1\User\{
    OpenAIController as OpenAIControllerAPI,
    ImageController as ImageControllerAPI,
    UseCasesController as UseCasesControllerAPI,
    UseCaseCategoriesController as UseCaseCategoriesControllerAPI,
    CodeController as CodeControllerAPI,
    OpenAIPreferenceController as OpenAIPreferenceControllerAPI,
    ChatController as ChatControllerAPI,
    UserController as UserControllerAPI,
    SpeechToTextController as SpeechToTextControllerAPI,
    TextToSpeechController as TextToSpeechControllerAPI
};

use Modules\OpenAI\Http\Controllers\Api\v2\User\{
    ChatBotController,
    DocChatController as DocChatControllerAPI,
    ImageToVideoController as ImageToVideoControllerAPI,
    ChatBotWidgetController,
    ChatBotTrainingController,
    UserAccessController as UserAccessControllerAPI,
    TemplateController,
    FeatureManagerController,
    AiDocChatController,
    FeaturePreferenceController as FeaturePreferenceAPI
};

use Modules\OpenAI\Http\Controllers\Api\v3\User\FeatureManagerController as V3FeatureManagerController;

use Modules\OpenAI\Http\Controllers\Customer\v2\GalleryController;
use Modules\OpenAI\Http\Controllers\Customer\v2\ImageController as V2ImageController;

use Modules\OpenAI\Http\Controllers\Customer\v2\PlagiarismController;
use Modules\OpenAI\Http\Controllers\Api\v2\User\VisionController;
use Modules\OpenAI\Http\Controllers\Customer\v2\AiDetectorController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['web']], function () {
    Route::get('images/share/{slug}', [V2ImageController::class, 'imageShare'])->name('user.image.share');
});

//template
Route::middleware(['web', 'middleware' => 'userPermission:hide_template'])->group(function () {
    Route::get('/user/templates/', [UserAIController::class, 'templates'])->name('openai')->middleware(['auth', 'locale', 'teamAccess:template']);
});
Route::prefix('user')->middleware(['auth', 'locale', 'web'])->name('user.')->group(function () {
    //template
    Route::middleware(['middleware' => 'userPermission:hide_template'])->group(function () {
        Route::get('documents', [CustomerPrebuiltTemplateContentController::class, 'documents'])->name('documents');
        Route::get('favourite-documents', [CustomerPrebuiltTemplateContentController::class, 'favouriteDocuments'])->name('favouriteDocuments');
        Route::get('templates/{slug}', [CustomerPrebuiltTemplateContentController::class, 'template'])->name('template')->middleware('teamAccess:template');
        Route::get('formfiled-usecase/{slug}', [CustomerPrebuiltTemplateContentController::class, 'getFormFiledByUsecase'])->name('formField');
        Route::get('get-content', [CustomerPrebuiltTemplateContentController::class, 'getContent']);
        Route::get('deleteContent', [CustomerPrebuiltTemplateContentController::class, 'deleteContent'])->name('deleteContent');
        Route::get('content/edit/{slug}', [CustomerPrebuiltTemplateContentController::class, 'editContent'])->name('editContent');
        Route::post('update-content', [CustomerPrebuiltTemplateContentController::class, 'updateContent'])->name('updateContent');
    });

    // Text To Speech
    Route::middleware(['middleware' => 'userPermission:hide_text_to_speech'])->group(function () {
        Route::get('text-to-speech', [TextToSpeechController::class, 'textToSpeechTemplate'])->name('textToSpeechTemplate')->middleware('teamAccess:voiceover');
        Route::get('text-to-speech-list', [TextToSpeechController::class, 'textToSpeechList'])->name('textToSpeechList');
        Route::get('text-to-speech/view/{id}', [TextToSpeechController::class, 'show'])->name('textToSpeechView');
        Route::post('text-to-speech/delete', [TextToSpeechController::class, 'delete'])->name('textToSpeechDelete');
        Route::post('text-to-speech/destroy', [TextToSpeechController::class, 'destroy'])->name('textToSpeechDestroy');

    });

    // Image
    Route::group(['prefix' => 'images', 'as' => 'image.', 'middleware' => 'userPermission:hide_image', 'controller' => V2ImageController::class], function() {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create')->middleware('teamAccess:image');
        Route::post('/', 'store')->name('store');
        Route::get('{image}', 'show')->name('show');
        Route::delete('{image}', 'destory')->name('destory');
        Route::post('favourite', 'toggleFavoriteImage')->name('favourite');
    });

    // Image To Video
    Route::get('image-to-video', [\Modules\OpenAI\Http\Controllers\Customer\v2\ImageToVideoController::class, 'template'])->name('videoTempalte')->middleware(['teamAccess:video', 'userPermission:hide_video']);

    // Gallery
    Route::group(['prefix' => 'gallery', 'as' => 'gallery.', 'middleware' => 'userPermission:hide_image', 'controller' => GalleryController::class], function() {
        Route::get('/', 'gallery')->name('show');
        Route::get('list', 'list')->name('list');
    });

    // Folder
    Route::get('/folder', [UserAIController::class, 'index'])->name('folderLists');

    // AI Detector
    Route::get('ai-detector', [AiDetectorController::class, 'template'])->name('aiDetectorTemplate')->middleware('teamAccess:ai_detector', 'userPermission:hide_ai_detector');

    // Plagiarism
    Route::get('plagiarism', [PlagiarismController::class, 'template'])->name('plagiarismTemplate')->middleware('teamAccess:plagiarism', 'userPermission:hide_plagiarism');

    // Code
    Route::middleware(['middleware' => 'userPermission:hide_code'])->group(function () {
        Route::get('code', [UserAIController::class, 'codeTemplate'])->name('codeTemplate')->middleware('teamAccess:code');
        Route::get('code-list', [CustomerCodeController::class, 'index'])->name('codeList');
        Route::get('code/view/{slug}', [CustomerCodeController::class, 'view'])->name('codeView');
        Route::post('code/delete/', [CustomerCodeController::class, 'delete'])->name('deleteCode');
    });

    // Speech To Text
    Route::middleware(['middleware' => 'userPermission:hide_speech_to_text'])->group(function () {
        Route::get('speech-to-text', [UserSpeechToTextController::class, 'template'])->name('speechTemplate')->middleware('teamAccess:speech_to_text');
        Route::get('speech-list', [UserSpeechToTextController::class, 'index'])->name('speechLists');
        Route::get('speech/edit/{id}', [UserSpeechToTextController::class, 'edit'])->name('editSpeech');
        Route::post('update-speech', [UserSpeechToTextController::class, 'update'])->name('updateSpeech');
        Route::post('delete-speech', [UserSpeechToTextController::class, 'delete'])->name('deleteSpeech');
    });

    // Chat
    Route::get('chat-history/{id}', [ChatController::class, 'history'])->name('chat');
    Route::post('delete-chat', [ChatController::class, 'delete'])->name('deleteChat');
    Route::post('update-chat', [ChatController::class, 'update'])->name('updateChat');

    Route::get('chat/bot', [ChatController::class, 'chatBot']);
    Route::get('chat-conversation', [ChatController::class, 'conversation']);

    // Folder
    Route::get('/folder', [FolderController::class, 'index'])->name('folderLists');
    Route::post('/folder-create', [FolderController::class, 'create'])->name('folderCreate');
    Route::post('/folder-update', [FolderController::class, 'update'])->name('folderUpdate');
    Route::get('/folder/{slug}', [FolderController::class, 'view'])->name('folderView');
    Route::get('/folder/download/{id}', [FolderController::class, 'download']);
    Route::post('/folder/download/content', [FolderController::class, 'downloadContent']);

    Route::get('/fetch-folder', [FolderController::class, 'fetchFolder'])->name('fetchFolder');
    Route::get('/fetch/all-folder', [FolderController::class, 'fetchAllFolder']);
    Route::post('/folder/move', [FolderController::class, 'moveData']);
    Route::post('/folder/delete', [FolderController::class, 'delete']);
    Route::post('/folder/toggle/bookmark', [FolderController::class, 'toggleBookmarkFiles']);

    Route::post('download/file', [UserAIController::class, 'downloadFile']);

    // Long Article
    Route::group(['prefix' => 'articles', 'as' => 'long_article.', 'controller' => LongArticleController::class, 'middleware' => 'userPermission:hide_long_article'], function () {
        // Crud routes
        Route::get('/', 'index')->name('index')->middleware('teamAccess:long_article');
        Route::get('create', 'create')->name('create')->middleware('teamAccess:long_article');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::patch('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');

        // Generator routes
        Route::post('generate-titles', 'generateTitles')->name('generate_titles')->middleware('teamAccess:long_article,api');
        Route::post('generate-outlines', 'generateOutlines')->name('generate_outlines')->middleware('teamAccess:long_article,api');
        Route::post('init-article', 'initArticle')->name('init_article')->middleware('teamAccess:long_article,api');
        Route::get('generate-article', 'generateArticle')->name('generate_article');

        // Display onload routes
        Route::post('display-title-data', 'displayTitleData')->name('display_title_data');
        Route::post('display-outline-data', 'displayOutlineData')->name('display_outline_data');
        Route::post('display-article-data', 'displayArticleBlogData')->name('display_article_data');
        Route::post('forget-session-data', 'forgetSessionData')->name('forget_session_data');
    });

    Route::group(['name' => 'template.', 'as' => 'template.', 'controller' => TemplateController::class, 'middleware' => 'teamAccess:template'], function () {
        Route::post('generate', 'generate')->name('generate');
        Route::get('process', 'process')->name('process');
    });
});

Route::middleware(['auth', 'locale', 'web'])->prefix('admin')->group(function () {
    Route::name('admin.use_case.')->group(function () {
        // use case
        Route::get('/use-cases', [UseCasesController::class, 'index'])->name('list');
        Route::match(['get', 'post'], '/use-case/create', [UseCasesController::class, 'create'])->name('create');
        Route::match(['get', 'post'], '/use-case/{id}/edit', [UseCasesController::class, 'edit'])->name('edit');
        Route::post('/use-case/{id}/delete', [UseCasesController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('destroy');
        Route::get('use-case/pdf', [UseCasesController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('pdf');
        Route::get('use-case/csv', [UseCasesController::class, 'csv'])->middleware(['checkForDemoMode'])->name('csv');

        // use case category
        Route::get('/use-case/categories', [UseCaseCategoriesController::class, 'index'])->name('category.list');
        Route::match(['get', 'post'], '/use-case/category/create', [UseCaseCategoriesController::class, 'create'])->name('category.create');
        Route::match(['get', 'post'], '/use-case/category/{id}/edit', [UseCaseCategoriesController::class, 'edit'])->name('category.edit');
        Route::post('/use-case/category/{id}/delete', [UseCaseCategoriesController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('category.destroy');
        Route::get('/use-case/category/search', [UseCaseCategoriesController::class, 'searchCategory'])->name('category.search');
        Route::get('use-case-categories/pdf', [UseCaseCategoriesController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('pdf');
        Route::get('use-case-categories/csv', [UseCaseCategoriesController::class, 'csv'])->middleware(['checkForDemoMode'])->name('csv');

    });

    Route::name('admin.chat.')->group(function () {

        // Chat category
        Route::get('/chat/categories', [ChatCategoriesController::class, 'index'])->name('category.list');
        Route::match(['get', 'post'], '/chat/category/create', [ChatCategoriesController::class, 'create'])->name('category.create');
        Route::match(['get', 'post'], '/chat/category/{id}/edit', [ChatCategoriesController::class, 'edit'])->name('category.edit');
        Route::post('/chat/category/{id}/delete', [ChatCategoriesController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('category.destroy');
        Route::get('chat-categories/pdf', [ChatCategoriesController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('category.pdf');
        Route::get('chat-categories/csv', [ChatCategoriesController::class, 'csv'])->middleware(['checkForDemoMode'])->name('category.csv');


        // Chat Assistants
        Route::get('/chat/assistants', [ChatAssistantsController::class, 'index'])->name('assistant.list');
        Route::match(['get', 'post'], '/chat/assistant/create', [ChatAssistantsController::class, 'create'])->name('assistant.create');
        Route::match(['get', 'post'], '/chat/assistant/{id}/edit', [ChatAssistantsController::class, 'edit'])->name('assistant.edit');
        Route::get('/chat/assistant/delete', [ChatAssistantsController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('assistant.destroy');
        Route::get('chat-assistant/pdf', [ChatAssistantsController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('assistant.pdf');
        Route::get('chat-assistant/csv', [ChatAssistantsController::class, 'csv'])->middleware(['checkForDemoMode'])->name('assistant.csv');

    });

    Route::name('admin.features.')->group(function () {
        // Content
        Route::get('content/list', [PrebuiltTemplateContentController::class, 'index'])->name('contents');
        Route::get('content/edit/{slug}', [PrebuiltTemplateContentController::class, 'edit'])->name('content.edit');
        Route::post('content/update/{id}', [PrebuiltTemplateContentController::class, 'update'])->middleware(['checkForDemoMode'])->name('content.update');
        Route::get('content/delete', [PrebuiltTemplateContentController::class, 'delete'])->middleware(['checkForDemoMode'])->name('content.delete');
        Route::get('content/pdf', [PrebuiltTemplateContentController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('content.pdf');
        Route::get('content/csv', [PrebuiltTemplateContentController::class, 'csv'])->middleware(['checkForDemoMode'])->name('content.csv');


        // Image
        Route::post('delete-images', [ImageController::class, 'deleteImages'])->middleware(['checkForDemoMode'])->name('deleteImage');
        Route::post('save-image', [ImageController::class, 'saveImage'])->name('saveImage');
        Route::get('image/list', [ImageController::class, 'list'])->name('imageList');
        Route::get('image/view/{slug}', [ImageController::class, 'view'])->name('image.view');
        Route::get('image/pdf', [ImageController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('image.pdf');
        Route::get('image/csv', [ImageController::class, 'csv'])->middleware(['checkForDemoMode'])->name('image.csv');

        Route::group(['prefix' => 'images', 'as' => 'admin.image.', 'controller' => AdminV2ImageController::class], function () {
            Route::get('csv', 'csv')->name('export_csv');
            Route::get('pdf', 'pdf')->name('print_pdf');
            Route::get('/', 'index')->name('index');
            Route::delete('{id}', 'destory')->name('destroy');
        });

        // Code
        Route::get('code/list', [CodeController::class, 'index'])->name('code.list');
        Route::get('code/view/{slug}', [CodeController::class, 'view'])->name('code.view');
        Route::post('code/delete', [CodeController::class, 'delete'])->middleware(['checkForDemoMode'])->name('code.delete');
        Route::get('code/pdf', [CodeController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('code.pdf');
        Route::get('code/csv', [CodeController::class, 'csv'])->middleware(['checkForDemoMode'])->name('code.csv');

        // Content Preferences
        Route::get('features/preferences', [OpenAIController::class, 'contentPreferences'])->name('preferences');
        Route::post('features/preferences/create', [OpenAIController::class, 'createContentPreferences'])->middleware(['checkForDemoMode'])->name('preferences.create');

        // Manage Providers
        Route::match(['get', 'post'], 'providers/{feature}/{provider}', [ProviderManageController::class, 'manageProvider'])->name('provider_manage');
        Route::get('providers/{feature?}', [ProviderManageController::class, 'providers'])->name('providers');

        // Feature preference
        Route::get('features/{feature?}', [FeaturePreferenceController::class, 'manageFeature'])->name('feature_preference');
        Route::post('features/store', [FeaturePreferenceController::class, 'store'])->name('feature_preference.options');


        // Text To Speech
        Route::get('text-to-speech/list', [AdminTextToSpeechController::class, 'index'])->name('textToSpeech.lists');
        Route::get('text-to-speech/view/{slug}', [AdminTextToSpeechController::class, 'show'])->name('textToSpeech.view');
        Route::post('text-to-speech/delete', [AdminTextToSpeechController::class, 'delete'])->middleware(['checkForDemoMode'])->name('textToSpeech.delete');
        Route::get('text-to-speech/pdf', [AdminTextToSpeechController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('textToSpeech.pdf');
        Route::get('text-to-speech/csv', [AdminTextToSpeechController::class, 'csv'])->middleware(['checkForDemoMode'])->name('textToSpeech.csv');


        // All Voices
        Route::get('text-to-speech/voice/list', [AdminTextToSpeechController::class, 'allVoices'])->name('textToSpeech.voice.lists');
        Route::match(['get', 'post'], 'text-to-speech/voice/create', [AdminTextToSpeechController::class, 'voiceCreate'])->name('textToSpeech.voice.create');
        Route::get('voice/pdf', [AdminTextToSpeechController::class, 'voicePdf'])->middleware(['checkForDemoMode'])->name('textToSpeech.voice.pdf');
        Route::get('voice/csv', [AdminTextToSpeechController::class, 'voiceCsv'])->middleware(['checkForDemoMode'])->name('textToSpeech.voice.csv');
        Route::match(['get', 'post'], 'text-to-speech/voice/edit/{id}', [AdminTextToSpeechController::class, 'voiceEdit'])->name('textToSpeech.voice.edit');

        // Speech
        Route::get('speech/list', [SpeechToTextController::class, 'index'])->name('speeches');
        Route::get('speech/edit/{id}', [SpeechToTextController::class, 'edit'])->name('speech.edit');
        Route::post('speech/update/{id}', [SpeechToTextController::class, 'update'])->middleware(['checkForDemoMode'])->name('speech.update');
        Route::post('speech/delete', [SpeechToTextController::class, 'delete'])->middleware(['checkForDemoMode'])->name('speech.delete');
        Route::get('speech/pdf', [SpeechToTextController::class, 'pdf'])->middleware(['checkForDemoMode'])->name('speech.pdf');
        Route::get('speech/csv', [SpeechToTextController::class, 'csv'])->middleware(['checkForDemoMode'])->name('speech.csv');

        // Ai Chatbot's
        Route::group(['prefix' => 'ai-chatbot', 'as' => 'ai_chatbot.', 'controller' => AiChatbotController::class], function() {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->middleware(['checkForDemoMode'])->name('update');
            Route::delete('delete/{id}', 'destroy')->middleware(['checkForDemoMode'])->name('delete');
            Route::get('pdf', 'pdf')->middleware(['checkForDemoMode'])->name('pdf');
            Route::get('csv', 'csv')->middleware(['checkForDemoMode'])->name('csv');
        });

        Route::group(['prefix' => 'image-to-video', 'as' => 'image-to-video.', 'controller' => \Modules\OpenAI\Http\Controllers\Admin\v2\ImageToVideoController::class], function() {
            Route::get('/', 'index')->name('index');
            Route::delete('/delete/{id}', 'destory')->middleware(['checkForDemoMode'])->name('delete');
        });

    });

    // Long Article
    Route::group(['prefix' => 'articles', 'as' => 'admin.long_article.', 'controller' => AdminLongArticleController::class], function () {
        Route::get('csv', 'csv')->name('export_csv');
        Route::get('pdf', 'pdf')->name('print_pdf');
        Route::get('/', 'index')->name('index');
        Route::get('{id}', 'edit')->name('edit');
        Route::post('{id}', 'update')->name('update');
        Route::delete('{id}', 'destory')->name('destroy');
    });
});

Route::middleware(['auth', 'locale', 'web'])->prefix('user/openai')->name('user.')->group(function () {
    Route::get('/use-case/search', [CustomerUseCasesController::class, 'searchTabData'])->name('use_case.search');
    Route::post('/use-case/toggle/favorite', [CustomerUseCasesController::class, 'toggleFavorite'])->name('use_case.toggle.favorite');
    Route::get('/documents/fetch', [CustomerDocumentsController::class, 'fetchAndFilter'])->name('document.fetch');
    Route::post('/documents/toggle/bookmark', [CustomerDocumentsController::class, 'toggleBookmark'])->name('document.toggle.bookmark');

    Route::post('/image/toggle/favorite', [UserImageController::class, 'toggleFavoriteImage'])->name('image.toggle.favorite');
});


# API Routes

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {

    // Version 2 Routes
    Route::group(['as' => 'api.', 'prefix' => '/v2', 'middleware' => ['auth:api', 'locale', 'permission-api']], function () {
        
        // Chat Bot
        Route::group(['as' => 'chatbot.', 'prefix' => 'chat/bots', 'controller' => ChatBotController::class], function() {
            Route::get('/', 'index')->name('index');
            Route::get('{chatbot}', 'show')->name('show');
        });

        // Chat Widget Bot
        Route::group(['as' => 'chatbotwidget.', 'prefix' => 'widget/chatbots', 'controller' => ChatBotWidgetController::class, 'middleware' => ['teamAccess:chatbot,api', 'userPermission:hide_aichatbot']], function() {
            Route::get('/', 'index')->name('index');
            Route::post('/create', 'store')->name('store')->middleware(['checkForDemoMode']);
            Route::get('{chatbot}', 'show')->name('show');
            Route::patch('/update/{chatbot}', 'update')->name('update')->middleware(['checkForDemoMode']);
            Route::delete('{chatbot}', 'delete')->name('delete')->middleware(['checkForDemoMode']);

            Route::delete('delete-image/{chatbot}', 'destroyImage')->name('destroyImage');

            // Widget Chatbot's Dashboard
            Route::get('/dashboard/material', 'dashboard')->name('dashboard');
        });

        // Train Chatbot Materials
        Route::group([
            'as' => 'chatbotwidget.training.',
            'prefix' => 'widget/chatbot/materials', 'controller' => ChatBotTrainingController::class,
            'middleware' => ['teamAccess:chatbot,api', 'userPermission:hide_aichatbot']
        ], function() {

            Route::get('/{chatbot}', 'index')->name('index');
            Route::post('/store/{chatbot}', 'store')->name('store')->middleware(['checkForDemoMode']);
            Route::post('/train/{chatbot}', 'train')->name('train')->middleware(['checkForDemoMode']);
            Route::delete('/destroy', 'destroy')->name('destroy')->middleware(['checkForDemoMode']);
            Route::post('/fetch-url', 'fetchUrl')->name('fetchUrl')->middleware(['checkForDemoMode']);
            Route::get('/{chatbot}/download/csv', 'csv')->name('download.csv');

        });

         // Widget chatbot's User Conversation
         Route::group(['as' => 'chatbots.', 'prefix' => '/user/chatbots', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserConversationController::class, 'middleware' => ['teamAccess:chatbot,api', 'userPermission:hide_aichatbot']], function () {
            Route::get('chats', 'index')->name('index');
            Route::get('chats/{conversation_id}', 'show')->name('show');
            Route::delete('chats/{id}', 'delete')->name('delete')->middleware(['checkForDemoMode']);
        });

         // User Conversation test

         Route::group(['as' => 'chatbots.', 'prefix' => '/test/user/chatbots', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserTestConversationController::class,'middleware' => ['teamAccess:chatbot,api', 'userPermission:hide_aichatbot']], function () {
            Route::post('chats', 'store')->name('store')->middleware(['checkForDemoMode']);
            Route::get('chats/{conversation_id}', 'show')->name('show');
        });
        
        // Chat
        Route::group(['as' => 'chat.', 'prefix' => 'chats', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\AiChatController::class], function () {
            Route::get('{chat}', 'show')->name('show');
            Route::post('/', 'store')->name('store')->middleware('userPermission:hide_chat', 'teamAccess:chat,api');
        });

        // Doc Chat
        Route::group(['as' => 'embed.','prefix' => 'embed-resources', 'controller' => AiDocChatController::class, 'middleware' => ['teamAccess:chat,api', 'userPermission:hide_chat']], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('{id}', 'delete')->name('delete');
        });

        Route::group(['as' => 'embed.', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\DocChatAskController::class, 'middleware' => ['teamAccess:chat,api', 'userPermission:hide_chat']], function () {
            Route::post('resources/ask', 'askQuestion')->name('question');
        });

        // User Access
        Route::group(['as' => 'userAccess.', 'controller' => UserAccessControllerAPI::class], function () {
            Route::get('user-access', 'index')->name('index');
        });
        
        Route::group(['as' => 'docChat.', 'controller' => DocChatControllerAPI::class], function () {
            Route::get('conversation/{id}', 'conversation')->name('view');
        });

        Route::group(['as' => 'aiVideo.', 'prefix' => 'image-to-video', 'controller' => ImageToVideoControllerAPI::class, 'middleware' => ['teamAccess:video,api', 'userPermission:hide_video']], function () {
            Route::post('/', 'store')->name('store')->middleware(['checkForDemoMode'])->name('store');
        });

        // Vision
        Route::group(['as' => 'vision.', 'prefix' => 'vision', 'controller' => VisionController::class], function() {
            Route::post('/', 'store')->name('store')->middleware('userPermission:hide_chat', 'teamAccess:chat,api');
        });

        // Code
        Route::group(['as' => 'code.', 'prefix' => 'code', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\CodeController::class, 'middleware' => 'teamAccess:code'], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('{id}', 'show')->name('show');
            Route::delete('{id}', 'delete')->name('delete');
        });

        Route::group(['as' => 'template.', 'prefix' => 'template', 'controller' => TemplateController::class, 'middleware' => 'teamAccess:template'], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'generate')->name('store');
            Route::get('/process', 'process')->name('process');
            Route::post('/edit/{id}', 'update')->name('update');
            Route::get('{id}', 'show')->name('show');
            Route::delete('{id}', 'delete')->name('delete');
            Route::post('toggle/bookmark', 'toggleFavorite')->name('toggleFavorite');
        });

        // Feature manager provider
        Route::group(['as' => 'featureManager.', 'prefix' => 'provider', 'controller' => FeatureManagerController::class], function () {
            Route::get('{feature}', 'providers')->name('providers');
            Route::get('{feature}/{provider}', 'models')->name('models');
            Route::get('{feature}/{provider}/preference', 'preference')->name('preference');
            Route::get('{feature}/{provider}/{model}', 'addiontalOptions');
        });

        Route::group(['as' => 'featurePreference.', 'controller' => FeaturePreferenceAPI::class], function () {
            Route::get('/feature-preferences', 'featureOptions');
        });

        // Speech To Text
        Route::group(['as' => 'speech.', 'prefix' => 'speeches', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\SpeechToTextController::class], function () {
            Route::post('/', 'generate')->name('speechToText');
        });    
        // Image
        Route::group(['as' => 'image.', 'prefix' => 'images', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\ImageController::class, 'middleware' => 'teamAccess:image,api'], function () {
            Route::post('/', 'store')->name('store');
            Route::get('gallery', 'index')->name('index');
            Route::delete('{image}', 'destroy')->name('destroy');
            Route::post('toggle/favorite', 'toggleFavorite')->name('toggleFavorite');
        });
        
        // History
        Route::group(['as' => 'history.', 'prefix' => 'ai-chat', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\HistoryController::class,'middleware' => 'teamAccess:chat,api'], function () {
            Route::get('history', 'index')->name('index');
            Route::get('history/{history_id}', 'show')->name('show');
            Route::delete('history/{history_id}', 'destroy')->name('destroy');
        });

        // Plagiarism
        Route::group(['as' => 'plagiarism.', 'prefix' => 'plagiarism', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\PlagiarismController::class, 'middleware' => 'teamAccess:plagiarism,api'], function () {
            Route::post('/', 'generate')->name('generate');
        });

        // Ai Detector
        Route::group(['as' => 'aidetector.', 'prefix' => 'aidetector', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\AiDetectorController::class, 'middleware' => 'teamAccess:ai_detector,api'], function () {
            Route::post('/', 'generate')->name('generate');
        });

        // Use Case
        Route::group(['as' => 'use.case.', 'prefix' => 'use-cases', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\UseCaseController::class], function () {
            Route::get('/', 'index')->name('index');
        });

    });

    // Version 3 Routes
    Route::group(['as' => 'api.', 'prefix' => '/v3', 'middleware' => ['auth:api', 'locale', 'permission-api']], function () {

         // Feature manager provider
         Route::group(['as' => 'feature.manager.v3.', 'prefix' => 'provider',  'controller' => V3FeatureManagerController::class], function () {
            Route::get('{feature}', 'providers')->name('providers');
        });
    });

    Route::group(['prefix' => '/V1/user/openai', 'middleware' => ['auth:api', 'locale', 'permission-api']], function () {

        Route::post('chat', [OpenAIControllerAPI::class, 'chat']);
        Route::get('chat/conversation', [OpenAIControllerAPI::class, 'chatConversation']);
        Route::get('chat/history/{id}', [OpenAIControllerAPI::class, 'history']);

        Route::post('chat/delete', [ChatControllerAPI::class, 'delete']);
        Route::post('chat/update', [ChatControllerAPI::class, 'update']);

        Route::get('chat/assistant/list', [ChatControllerAPI::class, 'allChatAssistants']);
        
        // Content
        Route::get('content/list', [OpenAIControllerAPI::class, 'index']);
        Route::get('content/view/{slug}', [OpenAIControllerAPI::class, 'view']);
        Route::post('content/edit/{slug}', [OpenAIControllerAPI::class, 'update']);
        Route::delete('content/delete/{id}', [OpenAIControllerAPI::class, 'delete']);
        Route::post('content/toggle/bookmark', [OpenAIControllerAPI::class, 'contentTogglebookmark']);

        // Image
        Route::get('image/list', [ImageControllerAPI::class, 'index']);
        Route::get('image/conversation/list', [ImageControllerAPI::class, 'converstaionList']);
        Route::delete('image/delete', [ImageControllerAPI::class, 'delete']);
        Route::get('image/list/{id}', [ImageControllerAPI::class, 'view']);
        Route::get('image/conversations/{id}', [ImageControllerAPI::class, 'converstaionView']);
        Route::delete('conversations', [ImageControllerAPI::class, 'conversationDelete']);

        

        // Create content and image

        // Create content, image, code, Speech to Text and Text to Speech
        Route::post('ask', [OpenAIControllerAPI::class, 'ask'])->middleware(['userPermission:hide_template', 'teamAccess:template,api']);
        Route::post('image', [OpenAIControllerAPI::class, 'image'])->middleware(['userPermission:hide_image', 'teamAccess:image,api']);
        Route::post('code', [OpenAIControllerAPI::class, 'code'])->middleware(['userPermission:hide_code', 'teamAccess:code,api']);
        Route::post('speech', [OpenAIControllerAPI::class, 'speechToText'])->middleware(['userPermission:hide_speech_to_text', 'teamAccess:speech_to_text,api']);
        Route::post('text-to-speech', [TextToSpeechControllerAPI::class, 'textToSpeech'])->middleware(['userPermission:hide_text_to_speech', 'teamAccess:voiceover,api']);

        // use case
        Route::get('/use-cases', [UseCasesControllerAPI::class, 'index']);
        Route::post('/use-case/create', [UseCasesControllerAPI::class, 'create']);
        Route::get('/use-case/{id}/show', [UseCasesControllerAPI::class, 'show']);
        Route::put('/use-case/{id}/edit', [UseCasesControllerAPI::class, 'edit']);
        Route::delete('/use-case/{id}/delete', [UseCasesControllerAPI::class, 'destroy']);
        Route::post('/use-case/toggle/favorite', [UseCasesControllerAPI::class, 'useCaseToggleFavorite']);

        // use case category
        Route::get('/use-case/categories', [UseCaseCategoriesControllerAPI::class, 'index']);
        Route::post('/use-case/category/create', [UseCaseCategoriesControllerAPI::class, 'create']);
        Route::get('/use-case/category/{id}/show', [UseCaseCategoriesControllerAPI::class, 'show']);
        Route::put('/use-case/category/{id}/edit', [UseCaseCategoriesControllerAPI::class, 'edit']);
        Route::delete('/use-case/category/{id}/delete', [UseCaseCategoriesControllerAPI::class, 'destroy']);

        // Code
        Route::get('code/list', [CodeControllerAPI::class, 'index']);
        Route::get('code/view/{slug}', [CodeControllerAPI::class, 'view']);
        Route::delete('code/delete/{id}', [CodeControllerAPI::class, 'delete']);

        // Speech
        Route::get('speech/list', [SpeechToTextControllerAPI::class, 'index']);
        Route::get('speech/view/{id}', [SpeechToTextControllerAPI::class, 'show']);
        Route::post('speech/edit/{id}', [SpeechToTextControllerAPI::class, 'edit']);
        Route::delete('speech/delete/{id}', [SpeechToTextControllerAPI::class, 'destroy']);

        //Content Preferences
        Route::get('preferences/content', [OpenAIPreferenceControllerAPI::class, 'contentPreferences']);
        Route::get('preferences/image', [OpenAIPreferenceControllerAPI::class, 'imagePreferences']);
        Route::get('preferences/code', [OpenAIPreferenceControllerAPI::class, 'codePreferences']);
        Route::get('preferences/chat', [OpenAIPreferenceControllerAPI::class, 'chatPreferences']);
        Route::get('preferences/providers', [OpenAIPreferenceControllerAPI::class, 'aiProviders']);
        Route::get('conversations', [OpenAIPreferenceControllerAPI::class, 'conversationData']);

        // Text To Speech
        Route::get('text-to-speech/list', [TextToSpeechControllerAPI::class, 'index']);
        Route::get('text-to-speech/view/{id}', [TextToSpeechControllerAPI::class, 'show']);
        Route::delete('text-to-speech/delete/{id}', [TextToSpeechControllerAPI::class, 'destroy']);

        //Update Profile
        Route::post('/profile', [UserControllerAPI::class, 'update']);
        Route::post('/profile/delete', [UserControllerAPI::class, 'destroy']);

        //Subscription Package Info
        Route::get('/package-info', [UserControllerAPI::class, 'index']);

    });

    Route::group(['prefix' => '/V1/admin/openai', 'middleware' => ['auth:api', 'locale', 'permission']], function () {
        // Content
        Route::get('content/list', [AdminAPI::class, 'index']);
        Route::get('content/view/{slug}', [AdminAPI::class, 'view']);
        Route::post('content/edit/{slug}', [AdminAPI::class, 'update']);
        Route::delete('content/delete/{id}', [AdminAPI::class, 'delete']);

        // Image
        Route::get('image/list', [AdminImageAPI::class, 'index']);
        Route::delete('image/delete', [AdminImageAPI::class, 'delete']);
        Route::get('image/view/{id}', [AdminImageAPI::class, 'view']);

        // Create content and image
        Route::post('ask', [AdminAPI::class, 'ask']);
        Route::post('image', [AdminAPI::class, 'image']);
        Route::post('code', [AdminAPI::class, 'code']);

        // use case
        Route::get('/use-cases', [AdminUsecaseAPI::class, 'index']);
        Route::post('/use-case/create', [AdminUsecaseAPI::class, 'create']);
        Route::get('/use-case/{id}/show', [AdminUsecaseAPI::class, 'show']);
        Route::put('/use-case/{id}/edit', [AdminUsecaseAPI::class, 'edit']);
        Route::delete('/use-case/{id}/delete', [AdminUsecaseAPI::class, 'destroy']);

        // use case category
        Route::get('/use-case/categories', [AdminUseCaseCategoryAPI::class, 'index']);
        Route::post('/use-case/category/create', [AdminUseCaseCategoryAPI::class, 'create']);
        Route::get('/use-case/category/{id}/show', [AdminUseCaseCategoryAPI::class, 'show']);
        Route::put('/use-case/category/{id}/edit', [AdminUseCaseCategoryAPI::class, 'edit']);
        Route::delete('/use-case/category/{id}/delete', [AdminUseCaseCategoryAPI::class, 'destroy']);

        // Code
        Route::get('code/list', [AdminCodeAPI::class, 'index']);
        Route::get('code/view/{slug}', [AdminCodeAPI::class, 'view']);
        Route::delete('code/delete/{id}', [AdminCodeAPI::class, 'delete']);

    });
});

# API Visitor ChatBot Conversations Routes

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    Route::group(['prefix' => '/v2', 'middleware' => ['locale']], function () {
        Route::group(['as' => 'chatbots.', 'prefix' => '/chatbots/chats', 'controller' => \Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotConversationController::class], function () {
            Route::get('{id}', 'index')->name('index');
            Route::post('create', 'store')->name('store')->middleware(['checkForDemoMode']);
            Route::get('{visitor_id}/{id}', 'show')->name('show');
            Route::delete('{visitor_id}/{id}', 'destroy')->name('destroy')->middleware(['checkForDemoMode']);
        });

        Route::group(['prefix' => 'visitor/widget/chatbots', 'controller' => ChatBotWidgetController::class], function() {
            Route::get('details/{chatbot}', 'details');
        });
    });
});
