<?php

namespace Modules\OpenAI;

use App\Facades\AiProviderManager;
use Illuminate\Support\ServiceProvider;

class DefaultAiProvider extends ServiceProvider
{
        
    /**
     * Default providers registered 
     *
     * @return void
     */
    public function register()
    {
        AiProviderManager::add(\Modules\OpenAI\AiProviders\OpenAi\OpenAiProvider::class, 'openai');
        AiProviderManager::add(\Modules\OpenAI\AiProviders\StabilityAi\StabilityAiProvider::class, 'stabilityai');
        AiProviderManager::add(\Modules\OpenAI\AiProviders\Clipdrop\ClipdropProvider::class, 'clipdrop');
    }
    
}
