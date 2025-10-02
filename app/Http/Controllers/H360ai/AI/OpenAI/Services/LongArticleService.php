<?php 

namespace Modules\OpenAI\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Modules\OpenAI\Contracts\Responses\LongArticle\{
    OutlineResponseContract,
    TitleResponseContract
};
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\OpenAI\Services\v2\ArchiveService;
use Modules\OpenAI\Entities\Archive;
use App\Facades\AiProviderManager;
use Str, Exception, DB;

class LongArticleService
{
    private $aiProvider;
    private $production = true;
    
    /**
     * Method __construct
     *
     * @param ContentGenerator $generator [decide which AI provider will be used for generate]
     *
     * @return void
     */
    public function __construct() 
    {
        if(! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'longarticle');
        }
    }
    
    /**
     * Method getAllGeneratedArticle
     *
     * @return LengthAwarePaginator
     */
    public function getAllGeneratedArticle(): LengthAwarePaginator
    {
        return Archive::with('metas')->whereType('long_article')->where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(preference('row_per_page'));
    }
    
    /**
     * Method handleTitleGenerate (generate titles based on user input)
     *
     * @param array $requestData [required input for generate title]
     *
     * @return array
     * @throws Exception
     */
    public function handleTitleGenerate(array $requestData): array
    {
        if(! is_null($requestData['provider'])) {
            manageProviderValues($requestData['provider'], 'model', 'longarticle');
        }

        // Checking Credit Balance
        $subscription = null;
        if (!subscription('isAdminSubscribed')) {
            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            $userStatus = (new ContentService())->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message']);
            }
            $validation = subscription('isValidSubscription', $userId, 'word');
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && !auth()->user()->hasCredit('word')) {
                throw new Exception($validation['message']);
            }
        }
        
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

        // Making Request to Ai Providers
        if ($this->production) {
            try {
                $title = $this->aiProvider->titles($requestData);
                if (! ($title instanceof TitleResponseContract)) {
                    throw new Exception(__('Title response must be an instance of TitleResponseContract.'));
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            
        } else {

            try {
                $title = $this->aiProvider->fakeTitles($requestData);
                if (!($title instanceof TitleResponseContract)) {
                    throw new Exception(__('Title response must be an instance of TitleResponseContract.'));
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
         
            }
        }

        $content = $title->content();        
        $words = $title->words();        
        $expense = $title->expense();        
        $response = $title->response(); 
        $options = request(request('provider'));
    
        if (!empty($content)) {

            if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                $increment = subscription('usageIncrement', $subscription?->id, 'word', $words, $userId);
                if ($increment  && $userId != auth()->user()->id) {
                    (new CodeService())->storeTeamMeta($words);
                }
                $wordLeft = subscription('isSubscribed', auth()->id()) ? subscription('fetureUsageLeft', $subscription?->id, 'feature_word') : 0;
                $wordLimit = subscription('isSubscribed', auth()->id()) ? subscription('getActiveFeature', $subscription?->id)['word']['limit'] : 0;
            }

            // Update Database
            if (! is_null($requestData['long_article_id'])) {
                $longArticle = Archive::whereType('long_article')->whereId($requestData['long_article_id'])->first();
                
                if (!is_null($longArticle)) {

                    $longArticle = ArchiveService::update([
                        'expense' => $longArticle->expense + $expense,
                        'total_words' => $longArticle->total_words + $words,
                        'generation_options' => $options
                    ], $requestData['long_article_id']);

                    $title = ArchiveService::create([
                        'parent_id' => $longArticle->id,
                        'user_id' => auth()->id(),
                        'unique_identifier' => (string) Str::uuid(),
                        'type' => 'long_article_title',
                        'title_values' => $content,
                        'title_initiated_by' => auth()->id(),
                        'generation_options' => $options,
                        'title_topic' => $requestData['topic'],
                        'title_keywords' => $requestData['keywords'], 
                        'title_raw_response' => $response,
                        'title_updated_by' => auth()->id(),
                    ]);

                    $allTitles = Archive::with('metas')
                        ->whereType('long_article_title')
                        ->where('parent_id', $longArticle->id)
                        ->latest()
                        ->get();
                    $generatedTitles = $allTitles->pluck('title_values')
                        ->flatten()
                        ->filter(function ($title) {
                            // Exclude null or empty title values
                            return !is_null($title) && $title !== '';
                        })
                        ->all();
                } else {

                    $longArticle = ArchiveService::create([
                        'user_id' => auth()->id(),
                        'title' => $requestData['topic'],
                        'unique_identifier' => (string) Str::uuid(),
                        'provider' => request('provider'),
                        'type' => 'long_article',
                        'status' => 'Incomplete',
                        'expense_type' =>  'token',
                        'expense' => $expense,
                        'total_words' => $words,
                        'completed_step' => 1,
                        'generation_options' => $options,
                    ]);

                    $title = ArchiveService::create([
                        'parent_id' => $longArticle->id,
                        'user_id' => auth()->id(),
                        'unique_identifier' => (string) Str::uuid(),
                        'type' => 'long_article_title',
                        'title_values' => $content,
                        'title_initiated_by' => auth()->id(),
                        'generation_options' => $options,
                        'title_topic' => $requestData['topic'],
                        'title_keywords' => $requestData['keywords'], 
                        'title_word_count' => $words, 
                        'title_expense' => $expense, 
                        'title_raw_response' => $response,
                    ]);

                    $generatedTitles = $title->title_values;
                }
            } else {
                $longArticle = ArchiveService::create([
                    'user_id' => auth()->id(),
                    'title' => $requestData['topic'],
                    'unique_identifier' => (string) Str::uuid(),
                    'provider' => request('provider'),
                    'type' => 'long_article',
                    'status' => 'Incomplete',
                    'expense_type' => 'token',
                    'expense' => $expense,
                    'total_words' => $words,
                    'completed_step' => 1,
                    'generation_options' => $options,
                ]);

                $title = ArchiveService::create([
                    'parent_id' => $longArticle->id,
                    'user_id' => auth()->id(),
                    'unique_identifier' => (string) Str::uuid(),
                    'type' => 'long_article_title',
                    'title_values' => $content,
                    'title_initiated_by' => auth()->id(),
                    'generation_options' => $options,
                    'title_topic' => $requestData['topic'],
                    'title_keywords' => $requestData['keywords'], 
                    'title_raw_response' => $response,
                ]);

                $generatedTitles = $title->title_values;
            }
            return [
                'num_of_title' => count($title->title_values),
                'longArticleId' => $longArticle->id,
                'generatedTitles' => $generatedTitles,
                'wordLeft' => $wordLeft ?? 0,
                'wordUsed' => $words,
                'wordLimit' => $wordLimit ?? 0
            ];
        } else {
            throw new Exception(__("Unable to generate the :x. Please try again.", ['x' => __('titles')]));
        }
    }

    /**
     * Method handleOutlineGenerate (generate outlines based on user input)
     *
     * @param array $requestData [required input for generate outlines]
     *
     * @return array
     * @throws Exception
     */
    public function handleOutlineGenerate(array $requestData): array
    {
        $subscription = null;
        if (!subscription('isAdminSubscribed')) {
            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            $userStatus = (new ContentService())->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message']);
            }
            $validation = subscription('isValidSubscription', $userId, 'word');
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && !auth()->user()->hasCredit('word')) {
                throw new Exception($validation['message']);
            }
        }

        if ($requestData['provider']) {
            $this->aiProvider = AiProviderManager::isActive(session('longarticle')['provider'], 'longarticle');
            manageProviderValues($requestData['provider'], 'model', 'longarticle');
        } else {
            throw new Exception(__('Provider not found.'));
        }

        if ($this->production) {

            try {
                $outline = $this->aiProvider->outlines($requestData);
                if (! ($outline instanceof OutlineResponseContract)) {
                    throw new Exception(__('The outline response is invalid. Please try again, and if the issue persists, kindly contact our support team for assistance.'));
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            
        } else {

            try {
                $outline = $this->aiProvider->fakeOutlines($requestData);
                if (! ($outline instanceof OutlineResponseContract)) {
                    throw new Exception(__('The outline response is invalid. Please try again, and if the issue persists, kindly contact our support team for assistance.'));
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        $content = $outline->content();        
        $words = $outline->words();        
        $expense = $outline->expense();        
        $response = $outline->response(); 
  
        if (!empty($content)) { 
            if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                $increment = subscription('usageIncrement', $subscription?->id, 'word', $words, $userId);
                if ($increment  && $userId != auth()->user()->id) {
                    (new CodeService())->storeTeamMeta($words);
                }
                $wordLeft = subscription('isSubscribed', auth()->id()) ? subscription('fetureUsageLeft', $subscription?->id, 'feature_word') : 0;
                $wordLimit = subscription('isSubscribed', auth()->id()) ? subscription('getActiveFeature', $subscription?->id)['word']['limit'] : 0;
            }

            $longArticle = Archive::whereType('long_article')->whereId($requestData['long_article_id'])->first();
            if (! $longArticle) {
                throw new Exception(__('Article not found. Please reset and try again.'));
            } 

            if (is_array($content)) {
                foreach ($content as $outlineContent) {
                    $outline = ArchiveService::create([
                        'parent_id' => $longArticle->id,
                        'user_id' => auth()->id(),
                        'unique_identifier' => (string) Str::uuid(),
                        'type' => 'long_article_outline',
                        'outline_values' => is_array($outlineContent) ? $outlineContent : json_decode($outlineContent),
                        'outline_initiated_by' => auth()->id(),
                        'outline_title' => $requestData['title'],
                        'outline_keywords'  => $requestData['keywords'],
                        'outline_raw_response' => $response,
                    ]);

                }
            } else {
                $outline = ArchiveService::create([
                    'parent_id' => $longArticle->id,
                    'user_id' => auth()->id(),
                    'unique_identifier' => (string) Str::uuid(),
                    'type' => 'long_article_outline',
                    'outline_values' => is_array($content) ? $content : json_decode($content),
                    'outline_initiated_by' => auth()->id(),
                    'outline_title' => $requestData['title'],
                    'outline_keywords'  => $requestData['keywords'],
                    'outline_raw_response' => $response,
                ]);

            } 

            $longArticle = ArchiveService::update([
                'completed_step' => 2,
                'total_words' => $longArticle->total_words + $words,
                'expense' => $longArticle->expense + $expense,
                'article_title' => $requestData['title'],
                'article_keywords'  => $requestData['keywords'],
            ], $requestData['long_article_id']);

            $allOutlines = Archive::with('metas')
                ->whereType('long_article_outline')
                ->where('parent_id', $longArticle->id)
                ->latest()
                ->get();

            $generatedOutlines = $allOutlines->pluck('outline_values')
                ->filter(function ($outline) {
                    // Exclude null or empty outline values
                    return !is_null($outline) && $outline !== '';
                })
                ->toArray();

            return [
                'num_of_outline' => count($content),
                'longArticleId' => $longArticle->id,
                'generatedOutlines' => $generatedOutlines,
                'wordLeft' => $wordLeft ?? 0,
                'wordUsed' => $words,
                'wordLimit' => $wordLimit ?? 0
            ];
        } else {
            throw new Exception(__("Unable to generate the :x. Please try again.", ['x' => __('outlines')]));
        }
    }

    /**
     * method handleArticleGenerate (Handle article generation and stream the results to the client)
     *
     * @param int $longArticleId The ID of the long article.
     *
     * @return void
     * @throws Exception
     */
    public function handleArticleGenerate(): StreamedResponse
    {
        $longArticle = Archive::whereType('long_article')->whereId(session('longarticle')['long_article_id'])->first();
        $aiProvider = AiProviderManager::isActive(session('longarticle')['provider'], 'longarticle');
        request()->merge(data_get(session('longarticle'), 'options', []));
        manageProviderValues(session('longarticle')['provider'], 'model', 'longarticle');

        if ($this->production) {
            $subscription = null;
            $userId = null;
            if (!subscription('isAdminSubscribed')) {
                $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
                $userStatus = (new ContentService())->checkUserStatus($userId, 'meta');
                if ($userStatus['status'] == 'fail') {
                    throw new Exception($userStatus['message']);
                }
                $validation = subscription('isValidSubscription', $userId, 'word');
                $subscription = subscription('getUserSubscription', $userId);
                if ($validation['status'] == 'fail' && !auth()->user()->hasCredit('word')) {
                    throw new Exception($validation['message']);
                }
            }

            return response()->stream(function () use ($aiProvider, $longArticle, $subscription, $userId) {   
                
                $text = ""; 
                $totalTokens = 0;
                $streamData =  $aiProvider->article(session('longarticle'));

                $textValue = '';
                foreach ($streamData as $response) {
                    
                    $text = $aiProvider->streamData($response);

                    $textValue .= $text;
                    $totalTokens++;
                    if (connection_aborted()) {
                        break;
                    }

                    echo "event: update\n";
                    echo 'data: ' . str_replace("\n", "", $text);
                    echo "\n\n";
                    ob_flush();
                    flush();
                    if (is_null($text)) {
                        break;
                    }
                }

                $totalWords = preference('word_count_method') == 'token' ? subscription('tokenToWord', $totalTokens) : countWords($textValue);

                $longArticle = ArchiveService::update([
                    'status' => 'Completed',
                    'content' => $textValue,
                    'article_value' => $textValue,
                    'expense' => $longArticle->expense + $totalTokens,
                    'total_words' => $longArticle->total_words + $totalWords,
                    'completed_step' => 3,
                ], session('longarticle')['long_article_id']);

                $article = ArchiveService::update([
                    'article_value' => $textValue
                ], session('longarticle')['long_article_id']);


                $wordLeft = 0;
                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $totalWords, $userId);
                    if ($increment  && $userId != auth()->user()->id) {
                        (new CodeService())->storeTeamMeta($totalWords);
                    }
                    $wordLeft = subscription('isSubscribed', auth()->id()) ? subscription('fetureUsageLeft', $subscription?->id, 'feature_word') : 0;

                }

                echo "event: message\n";
                echo 'data: ' . $wordLeft;
                echo "\n\n";
                ob_flush();
                flush();
                
                echo "event: wordused\n";
                echo 'data: ' . $totalWords;
                echo "\n\n";
                ob_flush();
                flush();

                echo "event: update\n";
                echo 'data: <END_STREAMING_SSE>';
                echo "\n\n";
                ob_flush();
                flush();

            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
            ]);
        } else {
            $streamData = ['**Introduction**', 'Artificial Intelligence (AI)', 'imtiaze', 'has emerged', 'as one', 'of the', 'most powerful', 'and game-changing', 'technologies of', 'our time. With', 'its ability', 'to analyze vast', 'amounts of data', 'and make informed', 'predictions, AI', 'is set to', 'revolutionize industries', 'across the globe.', 'In this article,', 'we will delve', 'into the world', 'of AI superpowers,', 'explore the future', 'of AI technology,', 'highlight the significance', 'of AI integration', 'services, discuss', 'the power', 'of intelligent automation,', 'and examine the', 'revolutionary concept of', 'machine learning on', 'demand.', '**AI Superpowers: Enhancing', 'the Future of', 'AI Technology** AI', 'superpowers are', 'the leading', 'players in', 'the AI', 'industry, wielding', 'immense influence', 'and driving innovation', 'in this rapidly', 'evolving field.', 'These superpowers,', 'including Google,', 'Amazon, Facebook,', 'and Microsoft, have', 'made substantial investments', 'in AI', 'research and development.', 'Their cutting-edge technologies', 'and breakthroughs have', 'opened up endless', 'possibilities for the', 'future of AI', 'technology. These AI', 'superpowers are working', 'tirelessly to improve', 'AI algorithms, enhance', 'machine learning capabilities,', 'and develop increasingly', 'advanced AI applications.', 'From self-driving cars', 'to smart virtual', 'assistants, AI is', 'poised to transform', 'various industries, revolutionizing', 'customer experience, optimizing', 'business processes, and', 'driving economic growth.', '**Unlocking the Potential of', 'AI Integration Services** AI', 'integration services play', 'a crucial role', 'in unlocking the', 'full potential of', 'AI technology. As', 'organizations increasingly recognize', 'the value of', 'AI, they are', 'seeking ways to', 'seamlessly incorporate it', 'into their existing', 'systems and processes.', 'AI integration services', 'provide the necessary', 'expertise to integrate', 'AI solutions into', 'businesses, making them', 'more efficient, productive,', 'and intelligent. By', 'integrating AI into', 'their operations, businesses', 'can improve decision-making,', 'automate repetitive tasks,', 'and gain valuable', 'insights from large', 'volumes of data.', 'AI integration services', 'enable organizations to', 'harness the power', 'of AI, regardless', 'of their size', 'or industry, empowering', 'them to stay', 'competitive in an', 'increasingly AI-driven world.', '**The Power of Intelligent', 'Automation** Intelligent automation', 'combines AI technologies', 'with traditional automation,', 'enabling organizations to', 'automate complex processes', 'that traditionally required', 'human intervention. By', 'utilizing AI algorithms,', 'machine learning, and', 'natural language processing,', 'intelligent automation systems', 'can analyze and', 'understand unstructured data,', 'make intelligent decisions,', 'and perform tasks', 'with remarkable accuracy', 'and speed. With', 'intelligent automation, businesses', 'can streamline operations,', 'reduce costs, and', 'enhance efficiency. Manual', 'and repetitive tasks', 'can be automated,', 'freeing up employees', 'to focus on', 'more strategic and', 'creative endeavors. Intelligent', 'automation also improves', 'the accuracy and', 'consistency of processes,', 'reducing the likelihood', 'of human errors.', '**Machine Learning on Demand:', 'Revolutionizing Industries** Machine', 'learning on demand', 'is an innovative', 'concept that holds', 'immense potential for', 'revolutionizing industries. It', 'allows businesses to', 'access machine learning', 'capabilities as a', 'service, without the', 'need for extensive', 'infrastructure or expertise.', 'By leveraging machine', 'learning on demand,', 'organizations can benefit', 'from advanced predictive', 'analytics, anomaly detection,', 'and pattern recognition', 'without the complexities', 'and costs associated', 'with building and', 'maintaining an in-house', 'machine learning infrastructure.', 'This concept opens', 'up new avenues', 'for businesses to', 'harness the power', 'of machine learning,', 'driving innovation and', 'gaining a competitive', 'edge. Machine learning', 'on demand enables', 'organizations to tap', 'into the vast', 'potential of their', 'data and extract', 'valuable insights, enabling', 'more informed decision-making', 'and unlocking new', 'growth opportunities.', '**Conclusion** The', 'power of AI', 'superpowers, the future', 'of AI technology,', 'the significance of', 'AI integration services,', 'the potential of', 'intelligent automation, and', 'the revolution brought', 'about by machine', 'learning on demand', 'cannot be overstated.', 'As AI continues', 'to evolve, it', 'is essential for', 'organizations to embrace', 'these advancements and', 'unlock the immense', 'potential they offer.', 'By effectively leveraging', 'AI superpowers, integrating', 'AI services, harnessing', 'intelligent automation, and', 'embracing machine learning', 'on demand, organizations', 'can thrive in', 'the ever-changing landscape', 'of the AI', 'revolution.'];

            return response()->stream(function () use ($streamData, $longArticle, $options) {   
         
                $totalTokens = 0;
                $textValue = '';
                foreach ($streamData as $text) {
                    $textValue .= $text;
                    if (connection_aborted()) {
                        break;
                    }
                    $totalTokens++;
    
                    echo "event: update\n";
                    echo 'data: ' . $text;
                    echo "\n\n";
                    ob_flush();
                    flush();
                    usleep(70000);
                }

                $totalWords = preference('word_count_method') == 'token' ? subscription('tokenToWord', $totalTokens) : countWords($textValue . ' ' . $options['prompt']);
                
                $longArticle->status = 'Completed';
                $longArticle->content = $textValue;
                $longArticle->expense += $totalTokens;
                $longArticle->total_words += $totalWords;
                $longArticle->completed_step = 3;
                $longArticle->save();

                $article = Archive::where('id', session('article_id'))->first();
                $article->article_prompt = $options['prompt'];
                $article->article_value = $textValue;
                $article->save();
    
                echo "event: message\n";
                echo 'data: ' . '500';
                echo "\n\n";
                ob_flush();
                flush();
    
                echo "event: update\n";
                echo 'data: <END_STREAMING_SSE>';
                echo "\n\n";
                ob_flush();
                flush();
    
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
            ]);
        }

    }

    /**
     * method deleteArticle (Delete an article by its ID)
     *
     * @param int $longArticleId [The ID of the long article]
     *
     * @return array ['status' => string, 'message' => string]
     * @throws Exception If the article is not found.
     */
    public function deleteArticle(int $longArticleId): array
    {
        $longArticle = Archive::where(['id' => $longArticleId, 'type' => 'long_article'])->first();
        $longArticleTitles = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_title'])->get();
        $longArticleOutlines = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_outline'])->get();
        $longArticleArticles = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_article'])->get();
        
        if ($longArticle) {
            DB::beginTransaction();

            try {
                foreach ($longArticleTitles as  $longArticleTitle) {
                    ArchiveService::delete($longArticleTitle->id, 'long_article_title');
                }
    
                foreach ($longArticleOutlines as  $longArticleOutline) {
                    ArchiveService::delete($longArticleOutline->id, 'long_article_outline');
                }
    
                foreach ($longArticleArticles as  $longArticleArticle) {
                    ArchiveService::delete($longArticleArticle->id, 'long_article_article');
                }

                ArchiveService::delete($longArticle->id, 'long_article');


                DB::commit();
                $response = ['status' => 'success', 'message' => __('The :x has been deleted successfully.', ['x' => __('article')])];
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
        } else {
            throw new Exception(__('Article not found.'));
        }
        
        return $response;
    }
}
