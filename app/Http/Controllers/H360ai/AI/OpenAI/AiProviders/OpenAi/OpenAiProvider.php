<?php

namespace Modules\OpenAI\AiProviders\OpenAi;

use App\Lib\AiProvider;
use Modules\OpenAI\AiProviders\OpenAi\Responses\Chat\ChatResponse;
use Modules\OpenAI\AiProviders\OpenAi\Traits\OpenAiApiTrait;
use Modules\OpenAI\AiProviders\OpenAi\Responses\LongArticle\{
    OutlineResponse,
    TitleResponse
};
use Modules\OpenAI\Contracts\Responses\LongArticle\{
    OutlineResponseContract,
    TitleResponseContract
};
use Modules\OpenAI\Contracts\Responses\VisionChat\VisionChatResponseContract;

use Modules\OpenAI\Contracts\Resources\CodeContract;
use Modules\OpenAI\AiProviders\OpenAi\Resources\TemplateDataProcessor;
use Modules\OpenAI\Contracts\Resources\TemplateContentContract;
use Modules\OpenAI\Contracts\Responses\Code\CodeResponseContract;
use Modules\OpenAI\AiProviders\OpenAi\Responses\Code\CodeResponse;
use Modules\OpenAI\AiProviders\OpenAi\Resources\CodeDataProcessor;
use Modules\OpenAI\Contracts\Resources\SpeechToTextContract;
use Modules\OpenAI\AiProviders\OpenAi\Resources\SpeechToTextDataProcessor;
use Modules\OpenAI\AiProviders\OpenAi\Responses\SpeechToText\SpeechToTextResponse;

use Modules\OpenAI\AiProviders\OpenAi\Resources\{
    LongArticleDataProcessor,
    AiChatDataProcessor,
    AiDocChatDataProcessor,
    AiEmbeddingDataProcessor,
    ChatbotDataProcessor,
    VisionChatDataProcessor
};
use Modules\OpenAI\AiProviders\OpenAi\Resources\ImageDataProcessor;

use Modules\OpenAI\Contracts\Resources\{
    ChatbotContract,
    AiEmbeddingContract
};
use Modules\OpenAI\AiProviders\OpenAi\Responses\AiChat\AiChatResponse;

use Modules\OpenAI\Contracts\Resources\{
    LongArticleContract,
    AiChatContract,
    AiDocChatContract,
    VisionChatContract
};

use Modules\OpenAI\AiProviders\OpenAi\Responses\AiDocChat\{
    AskQuestionResponse
};

use Modules\OpenAI\AiProviders\OpenAi\Responses\AiEmbedding\{
    EmbeddingResponse
};

use Modules\OpenAI\Contracts\Resources\ImageMakerContract;

use Modules\OpenAI\Contracts\Responses\AiEmbedding\{
    AiEmbeddingResponseContract
};
use Modules\OpenAI\AiProviders\OpenAi\Responses\ImageResponse;
use Modules\OpenAI\Contracts\Responses\ImageResponseContract;

use Modules\OpenAI\AiProviders\OpenAi\Responses\VisionChat\ChatResponse as VisionChatResponse;
use Modules\OpenAI\Contracts\Responses\AiChat\AiChatResponseContract;


class OpenAiProvider extends AiProvider implements LongArticleContract, TemplateContentContract, CodeContract, SpeechToTextContract, AiChatContract, AiDocChatContract, AiEmbeddingContract, ChatbotContract, ImageMakerContract, VisionChatContract
{
    use OpenAiApiTrait;

    /**
     * Holds the processed data after it has been manipulated or transformed.
     * This property is typically used within the context of a class to store
     * data that has been modified or processed in some way.
     *
     * @var array Contains an array of data resulting from processing operations.
     */
    protected $processedData;

    public function description(): array
    {
        return [
            'title' => 'Open AI',
            'description' => __('OpenAI pioneers AI breakthroughs in chat, image, voice, and text processing. With cutting-edge models, we excel in natural language understanding, image recognition, and voice synthesis, shaping the future of AI.'),
            'image' => 'Modules/OpenAI/Resources/assets/image/openai.png',
        ];
    }

    # Chatbot Start
    public function chatbotOptions(): array
    {
        return (new ChatbotDataProcessor())->chatbotOptions();
    }

    /**
     * Ask a question to the content based on the provided AI options.
     *
     * This method uses the ChatbotDataProcessor to process the AI options and ask a question to the content,
     * assigns the result to the processedData property, and returns a ChatResponse instance.
     *
     * @param array $aiOptions The AI options for asking a question to the content.
     * @return ChatResponse The chat response after processing the question.
     */
    public function askQuestionToContent(array $aiOptions): ChatResponse
    {
        $this->processedData = (new ChatbotDataProcessor($aiOptions))->askQuestionDataOptions();
        return new ChatResponse($this->chat());
    }

    # Long Article Start
    public function longArticleOptions(): array
    {
        return (new LongArticleDataProcessor)->longarticleOptions();
    }

    /**
     * Generates titles using AI options.
     *
     * @param array $aiOptions Options for AI processing.
     * @return TitleResponseContract Response containing generated titles.
     */
    public function titles(array $aiOptions): TitleResponseContract
    {
        $this->processedData = (new LongArticleDataProcessor($aiOptions))->titleOptions();
        return new TitleResponse($this->chat());
    }

    /**
     * Generates outlines using AI options.
     *
     * @param array $aiOptions Options for AI processing.
     * @return OutlineResponseContract Response containing generated titles.
     */
    public function outlines(array $aiOptions): OutlineResponseContract
    {
        $this->processedData = (new LongArticleDataProcessor($aiOptions))->outlineOptions();
        return new OutlineResponse($this->chat());

    }

    public function fakeTitles(array $aiOptions): TitleResponseContract
    {
        $fakeResponse = json_decode('{"response":{"id":"chatcmpl-8s7P19sosJlDWvy5hsWGszO8TNFkR","object":"chat.completion","created":1707908859,"model":"gpt-4-0613","choices":[{"index":0,"message":{"role":"assistant","content":"[\n\"Unleashing AI Superpowers: Exploring the Future of AI Technology\",\n\"Unlocking the Potential: AI Integration Services for the Future of AI\",\n\"Intelligent Automation and Machine Learning on Demand: Powering the Future of AI\",\n\"The Future of AI: OpenAI Envisions On-Demand AI Superpowers\"\n]","functionCall":null},"finishReason":"stop"}],"usage":{"promptTokens":61,"completionTokens":14,"totalTokens":75}},"content":["Unleashing AI Superpowers: Exploring the Future of AI Technology","Unlocking the Potential: AI Integration Services for the Future of AI","Intelligent Automation and Machine Learning on Demand: Powering the Future of AI","The Future of AI: OpenAI Envisions On-Demand AI Superpowers"],"words":41,"expense":75}');
        $result = $fakeResponse->response;
        return TitleResponse::from($result);

    }

    public function article(array $aiOptions)
    {
        $this->processedData = (new LongArticleDataProcessor($aiOptions))->articleOptions();
        return $this->chatStream();
    }

    public function streamData(object|array $streamResponse): ?string
    {
        if (isset($streamResponse->choices)) {
            $content = $streamResponse->choices[0]->toArray();
            if (isset($content['delta']['content'])) {
                return $content['delta']['content'];
            }
        }
        return null;
    }
    # Long Article End

    /** Generates template options by calling the templateOptions method of the TemplateDataProcessor class.
     * Returns an array of options for the vision chat feature.
     *
     * @return array An array of options for the vision chat feature.
     */
    public function visionChatOptions(): array
    {
        return (new VisionChatDataProcessor)->visionChatOptions();
    }

    /**
     * Initiates a vision chat using the provided AI options.
     *
     *
     * @param array $aiOptions An associative array of AI options to be used for vision chat.
     * @return VisionChatResponseContract The result of the chat.
     */
    public function visionChat(array $aiOptions): VisionChatResponseContract 
    {
        $this->processedData = (new VisionChatDataProcessor($aiOptions))->visionOptions();
        return new VisionChatResponse($this->chat());
    }
   
    /**
     * Generates template options by calling the templateOptions method of the TemplateDataProcessor class.
     *
     * @return array The array of code options.
     */
    public function templatecontentOptions(): array
    {
        return (new TemplateDataProcessor)->templatecontentOptions();
    }

    /**
     * Generates a template using the provided AI options.
     *
     * This method processes the given AI options through the TemplateDataProcessor
     * to generate the necessary template data. After processing, it initiates 
     * a chat stream.
     *
     * @param array $aiOptions An associative array of AI options to be used for template generation.
     * @return mixed The result of the chat stream.
     */
    public function templateGenerate(array $aiOptions)
    {
        $this->processedData = (new TemplateDataProcessor($aiOptions))->template();
        return $this->chatStream();
    }

    /**
     * Processes the stream response to extract the template content.
     *
     *
     * @param mixed $streamResponse The stream response object containing choices.
     * @return string|null The extracted content from the stream response, or null if not available.
     */
    public function templateStreamData($streamResponse): ?string
    {
        if (isset($streamResponse->choices)) {
            $content = $streamResponse->choices[0]->toArray();
            if (isset($content['delta']['content'])) {
                return $content['delta']['content'];
            }
        }
        return null;
    }

    /**
     * Retrieves the character chatbot options by instantiating a AiChatDataProcessor
     * and calling the AiChatOptions method.
     *
     * @return array An array of character chatbot options.
     */
    public function aiChatOptions(): array
    {
        return (new AiChatDataProcessor)->aiChatOptions();
    }

    /**
     * Generates a character chatbot chat using the provided AI options.
     *
     * @param array $aiOptions Options for AI processing.
     * @return AiChatResponse Response containing the chatbot chat.
     */
    public function aiChat(array $aiOptions): AiChatResponseContract
    {
        $this->processedData = (new AiChatDataProcessor($aiOptions))->aiChatDataOptions();
        return new AiChatResponse($this->chat());
    }

    /**
     * Retrieves a dummy chat response for the character chatbot.
     *
     * @return AiChatResponse Response object containing the dummy chat response.
     */
    public function dummyChat(): AiChatResponseContract
    {
        $fakeResponse = json_decode('{"response":{"id":"chatcmpl-9gSMCxsGqfFJfgtMQZDXjW8RFs1Ah", "object":"chat.completion", "created":1719905808, "model":"gpt-4o-2024-05-13", "choices":[{"index":0,"message":{"role":"assistant","content":"Hello! How can I assist you today?","functionCall":null},"finishReason":"stop"}],"usage":{"promptTokens":161,"completionTokens":9,"totalTokens":170}},"expense":170,"character":34,"word":7}');
        return new AiChatResponse($fakeResponse->response);
    }

    /**
     * Retrieves AI Doc Chat options.
     *
     * @return array Options for AI Doc Chat processing.
     */
    public function aiDocChatOptions(): array
    {
        return (new AiDocChatDataProcessor)->aiDocChatOptions();
    }

    /**
     * Method to ask a question using the provided AI options.
     *
     * @param array $aiOptions Options for AI processing.
     * @return AskQuestionResponse Response containing the asked question.
     */
    public function askQuestion(array $aiOptions): AskQuestionResponse
    {
        $this->processedData = (new AiDocChatDataProcessor($aiOptions))->askQuestionOptions();
        return new AskQuestionResponse($this->chat());
    }

    /**
     * Retrieves AI embedding options.
     *
     * @return array An array of AI embedding options.
     */
    public function aiEmbeddingOptions(): array
    {
        return (new AiEmbeddingDataProcessor)->aiembeddingOptions();
    }

    /**
     * Creates embeddings using the provided AI options.
     *
     * @param array $aiOptions Options for AI processing.
     * 
     * @return AiEmbeddingResponseContract  Response containing created embeddings.
     */
    public function createEmbeddings(array $aiOptions): AiEmbeddingResponseContract
    {
        $this->processedData = (new AiEmbeddingDataProcessor($aiOptions))->aiEmbeddingDataOptions();
        return new EmbeddingResponse($this->embeddings());
    }
    
    # Image Start
    public function imageMakerOptions(): array
    {
        return (new ImageDataProcessor)->imageOptions();
    }
    
    public function imageMakerRules(): array
    {
        return (new ImageDataProcessor)->rules();
    }

    public function generateImage(array $aiOptions): ImageResponseContract
    {
        $this->processedData = (new ImageDataProcessor($aiOptions))->imageData();
        return new ImageResponse($this->images());
    }
    # Image End

    /**
     * Generates a CodeResponseContract object by processing the given $aiOptions using the CodeDataProcessor class.
     *
     * @param array $aiOptions The options for AI processing.
     * @return CodeResponseContract The generated CodeResponseContract object.
     */
    public function code(array $aiOptions): CodeResponseContract
    {
        $this->processedData = (new CodeDataProcessor($aiOptions))->code();
        return new CodeResponse($this->chat()->toArray());
    }

    /**
     * Generates code options by calling the codeOptions method of the CodeDataProcessor class.
     *
     * @return array The array of code options.
     */
    public function codeOptions(): array
    {
        return (new CodeDataProcessor)->codeOptions();
    }

    public function speechToTextOptions(): array
    {
       return (new SpeechToTextDataProcessor)->speechToTextOptions();
    }

    /**
     * Generates titles using AI options.
     *
     */
    public function speechToText(array $aiOptions)
    {
        $this->processedData = (new SpeechToTextDataProcessor($aiOptions))->audioDataOptions();
        return new SpeechToTextResponse($this->audio());
    }

    /**
     * Get the validation rules for a specific processor.
     * 
     * @param string $processor The name of the data processor class.
     * 
     * @return array Validation rules for the processor.
     */
    public function getValidationRules(string $processor): array
    {
        $processorClass = "Modules\\OpenAI\\AiProviders\\OpenAi\\Resources" . $processor;

        if (class_exists($processorClass)) {
            return (new $processorClass())->validationRules();
        }

        return [];
    }
}
