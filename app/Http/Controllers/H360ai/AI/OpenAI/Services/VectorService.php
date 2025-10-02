<?php

namespace Modules\OpenAI\Services;

use Modules\OpenAI\Entities\{
    EmbededResource,
    Archive
};
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use ZipArchive;
use DOMDocument;
use SimpleXMLElement;
use Modules\OpenAI\Entities\OpenAI;
use Illuminate\Support\Facades\Auth;
use Modules\OpenAI\Services\ContentService;

class VectorService
{


    /**
     * @var string default model.
     */
    protected $defaultChatModel = 'gpt-3.5-turbo';

    /**
     * @var string The type of chat, default chat is doc_chat.
     */
    protected $chatType = 'doc_chat';

    /**
     * @var int The size of the chunk, default value is 256.
     */
    protected $chunkSize = 256;

    /**
     * @var mixed The ID of the embedded resource.
     */
    protected $embedId;

    /**
     * @var mixed The ID of the user.
     */
    protected $userId;

    /**
     * @var string The embedded string.
     */
    protected $embedString;

    /**
     * @var int The chunk data, default value is 4.
     */
    protected $chunkData = 4;

    /**
     * @var array An array to store file information.
     */
    protected $fileInfo = [];

    /**
     * @var array An array to store metadata.
     */
    protected $metaData = [];

    /**
     * @var array An array to store validated data.
     */
    protected $validatedData = [];

    /**
     * Constructor method.
     *
     * @param  mixed  $embedId
     * 
     * @return void
     */
    public function __construct($embedId = null)
    {
        $this->embedId = $embedId;
        $this->userId = Auth::check() ? Auth::user()->id : null;
    }

    /**
     * Get API Key.
     *
     * @return string
     */
    public function aiKey(): string
    {
        return apiKey('openai');
    }

    /**
     * Embedding model.
     *
     * @return string
     */
    public function embeddingModel(): string
    {
        return 'text-embedding-ada-002';
    }

    public function validate($validatedData)
    {
        $this->validatedData = $validatedData;
    }

    /**
     * Get files with relational data.
     *
     * @return mixed
     */
    public function files()
    {
        return $this->model()->with(['user', 'childs']);
    }

    /**
     * Get model instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function model()
    {
        return EmbededResource::with('metas')->whereNull('category');
    }
    
    /**
     * Delete file.
     *
     * @param  mixed  $id
     * 
     * @return bool
     */
    public function delete($id): bool
    {
        $file = $this->model()->where('id', $id)->first();

        if (empty($file)) {
            return false;
        }

        $metaData = $file->toArray();
        $metaKeys = array_keys($metaData['meta_data']);
        $file->unsetMeta($metaKeys);
        $file->save();

        return $file->delete();
    }

     /**
     * Get content by ID.
     *
     * @param  mixed  $id
     * @return mixed
     */
    public function contentById($id)
    {
        return $this->model()->with(['user', 'childs', 'metas'])->where(['id' => $id])->first();
    }

    /**
     * Retrieve contents with specified IDs along with related user, child contents, and metadata.
     *
     * @param array $ids The array of content IDs to retrieve.
     * 
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Content[] A collection of content models.
     */
    public function contents(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model()->with(['user', 'childs', 'metas'])->whereIn('id', $ids)->get();
    }

     /**
     * Client.
     *
     * @return \OpenAI\Client
     */
    public function client()
    {
        return \OpenAI::client($this->aiKey());
    }

    /**
     * Store embeddings.
     *
     * @return array
     */
    public function storeEmbeddings(): array
    {
        $fileIds = [];

        foreach ($this->validatedData['file'] as $key => $file) {
            $fileIds[$key] = $this->extractFile($file);
        }

        return $fileIds;
    }

    /**
     * Get texts from IDs.
     *
     * @param  array  $ids
     * @return mixed
     */
    public function getTextsFromIds(array $ids)
    {
        $texts = $this->model()->whereIn('id', $ids)->get()->toArray();
        $this->chatType = $texts[0]['type'];
        
        $textsById = [];

        foreach ($texts as $text) {
            $textsById[$text['id']] = $text['content'];
        }

        $textsOrderedByIds = [];

        foreach ($ids as $id) {
            if (isset($textsById[$id])) {
                $textsOrderedByIds[] = $textsById[$id];
            }
        }
        $mergedArray = implode(' ', $textsOrderedByIds);

        return $this->askQuestionStreamed($mergedArray);
    }

    /**
     * define chat model
     * @return array
     */
    public function chatModel()
    {
        return [
            'gpt-3.5-turbo',
            'gpt-4',
            'gpt-3.5-turbo-16k',
            'gpt-4-1106-preview',
            'gpt-3.5-turbo-1106',
            'gpt-4-0125-preview',
            'gpt-3.5-turbo-0125',
        ];
    }
    

    /**
     * Ask a question with streamed content.
     *
     * @param  mixed  $context
     * @return array|string
     */
    public function askQuestionStreamed($context): array|string
    {
        $system_template = "
        Use the following pieces of context to answer the user's question. 
        If you don't know the answer, just say that you don't know, don't try to make up an answer.
        ----------------
        $context
        ";
        $system_prompt = str_replace('{context}', $context, $system_template);

        $model = request('model') ?? $this->defaultChatModel;

        if (in_array($model, $this->chatModel())) {
            $result = OpenAI::contentCreate([
                'model' => $model, // this will be dynamic when settings are done.
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user', 'content' => $this->validatedData['prompt']],
                ],
                'temperature' => isset($this->validatedData['temperature']) && $this->validatedData['temperature'] ? (float) $this->validatedData['temperature'] : 0.7,
            ]);
        } else {
                $result = OpenAI::completions([
                    'prompt' =>  $system_prompt,
                    'temperature' => (float) request('temperature'),
                ]);
            }

        if ($result->usage->totalTokens) {
            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $result->usage->totalTokens, $userId);
        }
        return $this->storeInfo($result);
    }

    /**
     * Store data and create records in database
     *
     * @param  mixed  $result
     * @return array|string
     */
    public function storeInfo($result): array|string
    {
        DB::beginTransaction(); 

        try {
            if (empty(request('parent_id'))) {
                $chat = $this->createNewChat();
                $this->createUserReply($chat->id);
                $botReply = $this->createBotReply($result, $chat->id);
            } else {
                $this->createUserReply(request('parent_id'));
                $botReply = $this->createBotReply($result, request('parent_id'));
            }

            DB::commit();

            return $botReply;
        } catch (\Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Creates a new chat record.
     *
     * @return Archive The newly created chat instance.
     */
    protected function createNewChat()
    {
        $chat = new Archive();
        $chat->title = request('prompt');
        $chat->unique_identifier = \Str::uuid();
        $chat->user_id = auth()->id();
        $chat->provider = 'OpenAi'; // dynamically obtained
        $chat->type = $this->chatType;
        $chat->save();

        return $chat;
    }

    protected function chatType()
    {
       return $this->model()->whereIn('id', request('file_id'))->get('type');
    }

    /**
     * Creates a user reply record for the specified parent chat.
     *
     * @param  int  $parentId  The ID of the parent chat.
     * 
     * @return Archive The newly created user reply instance.
     */
    protected function createUserReply($parentId)
    {
        $userReply = new Archive();
        $userReply->parent_id = $parentId;
        $userReply->user_id = auth()->id();
        $userReply->type = $this->chatType."_chat_reply";
        $userReply->user_reply = request('prompt');
        $userReply->save();

        return $userReply;
    }

    /**
     * Creates a bot reply record for the specified parent chat.
     *
     * @param  mixed  $result  The result object containing bot response data.
     * @param  int  $parentId  The ID of the parent chat.
     * 
     * @return Archive The newly created bot reply instance.
     */
    protected function createBotReply($result, $parentId)
    {
        $model = request('model') ?? $this->defaultChatModel;
        $botReply = new Archive();
        $botReply->parent_id = $parentId;
        $botReply->raw_response = json_encode($result);
        $botReply->provider = 'OpenAi'; // dynamically obtained
        $botReply->expense = $result->usage->completionTokens;
        $botReply->total_token = $result->usage->totalTokens;
        $botReply->expense_type = 'token'; // dynamically obtained
        $botReply->type = $this->chatType."_chat_reply";
        $botReply->file_id = implode(',', request('file_id'));
        if (in_array($model, $this->chatModel())) {
            $botReply->bot_reply = $result->choices[0]->message->content;
            $botReply->total_words = countWords($result->choices[0]->message->content);
        } else {
            $botReply->bot_reply = $result->choices[0]->text;
            $botReply->total_words = countWords($result->choices[0]->text);
        }
       
        $botReply->completion_tokens = $result->usage->completionTokens;
        $botReply->save();
        return $botReply;
    }

    /**
     * Retrieve the most similar vectors for a given vector.
     *
     * @param array $vector The vector for which to find similar vectors.
     * @param int|null $file_id The file ID to filter the vectors.
     * @param int $limit The maximum number of similar vectors to retrieve.
     * 
     * @return array An array containing the most similar vectors.
     */
    public function getMostSimilarVectors(array $vector, $file_id = null, int $limit = 10)
    {
        $embededFiles = $this->model();
        if (filled(request('user_id'))) {
            $embededFiles = $embededFiles->where(['user_id' => request('user_id')]);
        }
        if (filled(request('file_id'))) {
            $embededFiles = $embededFiles->whereIn('id', request('file_id'))->orWhere('parent_id', request('file_id'));
        }

        $vectors = $embededFiles->get()
            ->map(function ($vector) {
                return [
                    'id' => $vector->id,
                    'vector' => json_decode($vector->vector, true),
                ];
            })
            ->toArray();

        $similarVectors = [];
        foreach ($vectors as $v) {
            $cosineSimilarity = $this->calculateCosineSimilarity($vector, $v['vector']);
            $similarVectors[] = [
                'id' => $v['id'],
                'similarity' => $cosineSimilarity,
            ];
        }

        usort($similarVectors, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return $this->getTextsFromIds(array_column(array_slice($similarVectors, 0, $limit), 'id'));
    }

    /**
     * Calculate the cosine similarity between two vectors.
     */
    private function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $vector1Normalization = 0;
        $vector2Normalization = 0;
    
        foreach ($vector1 as $i => $value) {
            $dotProduct += $value * $vector2[$i];
            $vector1Normalization += $value * $value;
            $vector2Normalization += $vector2[$i] * $vector2[$i];
        }
    
        $vector1Normalization = sqrt($vector1Normalization);
        $vector2Normalization = sqrt($vector2Normalization);
    
        return $dotProduct / ($vector1Normalization * $vector2Normalization);
    }

    /**
     * Prepare data for insertion.
     *
     * @param mixed $data The data to be stored.
     * 
     * @return mixed The result of storing the data.
     */
    public function storeData($data)
    {
        $result = EmbededResource::insert($data);
        if (isset($data['resourceMetaData'])) {
            $result->setMeta($data['resourceMetaData']);
            $result->save();
        }

        return $this->embedId;
    }

    /**
     * Prepare data for insertion.
     *
     * @return mixed The result of tokenizing the data.
     */
    public function fileStore()
    {
        return $this->tokenize();
    }

    /**
     * Create upload path.
     *
     * @return string The generated upload path.
     */
    protected function uploadPath()
    {
        return createDirectory(implode(DIRECTORY_SEPARATOR, ['public', 'uploads', 'aiFiles']));
    }

   /**
     * Store file.
     *
     * @param mixed $file The file to be stored.
     * 
     * @return array Information about the stored file.
     */
    public function storeFile($file)
    {
        $this->uploadPath();
        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        $destinationFolder = public_path('uploads') . DIRECTORY_SEPARATOR . 'aiFiles' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        $fileSize = $file->getSize();
        $file->move($destinationFolder, $fileName);

        $path = date('Ymd') . DIRECTORY_SEPARATOR . $fileName;

        return [
            'path' => $path,
            'destinationPath' => $destinationFolder . $fileName,
            'extension' => $file->getClientOriginalExtension(),
            'name' => $fileName,
            'file_path_name' => date('Ymd') . DIRECTORY_SEPARATOR . $fileName,
            'originalName' => $file->getClientOriginalName(),
            'fileSize' => $fileSize,
        ];
    }

    /**
     * extract the files
     */
    public function extractor()
    {
        $type = request('type');
        switch ($type) {
            case 'file':
                return $this->storeEmbeddings();
            case 'url':
                return $this->parseUrl();
        }
    }

    /**
     * Domain name parser.
     *
     * @param string $url The URL to parse.
     * 
     * @return string The extracted domain name.
     */
    public function getDomainName($url)
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];

        // Remove 'www.' if it exists
        $host = preg_replace('/^www\./', '', $host);

        return explode('.', $host)[0];
    }

     /**
     * Retrieve content from URL.
     *
     * @param array $contents The contents retrieved from the URL.
     * 
     * @return string The concatenated text content.
     */
    public function urlContent($contents)
    {
        $text = '';
        foreach ($contents as $content) {
            $text .= trim($content) . ' ';
        }

        return $text;
    }

    /**
     * URL scraper.
     *
     * @return mixed The result of parsing the URL.
     */
    public function parseUrl()
    {
        $web = new \Spekulatius\PHPScraper\PHPScraper();
        $web->go(request('url'));

        $fileInfo = [
            'file_path_name' => $this->getDomainName(request('url')),
            'originalName' => request('url'),
        ];
        $data = [
            'content' => $this->urlContent($web->paragraphs),
            'fileInfo' => $fileInfo,
        ];
        $this->fileInfo = $data;

        return $this->fileStore();
    }

     /**
     * Extract text from various file formats.
     *
     * @param mixed $file The file to extract text from.
     * 
     * @return mixed The result of extracting text from the file.
     */
    public function extractFile($file)
    {
        $fileInfo = $this->storeFile($file);
        $path = $fileInfo['destinationPath'];
        $ext = $fileInfo['extension'];

        $response = '';

        switch ($ext) {
            case 'pdf':
                $response = $this->pdfToText($path);

                break;
            case 'doc':
                $response = $this->docToText($path);

                break;
            case 'xlsx':
                $response = $this->xlsxToText($path);

                break;
            case 'csv':
                $response = $this->csvToText($path);

                break;
            case 'docx':
                $response = $this->docxToText($path);

                break;
        }
        $response = preg_replace('/[\t\n\s]+/', ' ', $response);
        $response = trim($response);
        if (empty($response)) {
            return false;
        }

        $data = [
            'content' => $response,
            'fileInfo' => $fileInfo,
        ];
        $this->fileInfo = $data;

        return $this->fileStore();
    }

    /**
     * Convert PDF file to text.
     *
     * @param string $path The path to the PDF file.
     * 
     * @return string The extracted text content.
     */
    protected static function pdfToText($path)
    {
        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($path);

        return $pdf->getText();
    }

    /**
     * Convert DOC file to text.
     *
     * @param string $path The path to the DOC file.
     * 
     * @return string The extracted text content.
     */
    protected static function docToText($path)
    {
        $fileContent = file_get_contents($path);
        $response = '';

        $fileContent = strip_tags($fileContent);

        $pattern = '/[a-zA-Z0-9\s,\.\-@\(\)_\/]+/';

        preg_match_all($pattern, $fileContent, $matches);
        $response = implode(' ', $matches[0]);

        return str_replace('Export HTML to Word Document with JavaScript', '', $response);
    }

    /**
     * Convert XLSX file to text.
     *
     * @param string $path The path to the XLSX file.
     * 
     * @return string The extracted text content.
     */
    protected static function xlsxToText($path)
    {
        $zip_handle   = new ZipArchive();
        $response     = '';
        if ($zip_handle->open($path) === true) {

            if (($xml_index = $zip_handle->locateName('xl/sharedStrings.xml')) !== false) {
                $doc = new DOMDocument();
                $xml_data   = $zip_handle->getFromIndex($xml_index);
                $doc->loadXML($xml_data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $response   = strip_tags($doc->saveXML());
            }

            $zip_handle->close();
        }

        return $response;
    }

     /**
     * Ask a question and generate a response.
     *
     * @return mixed The generated response.
     */
    public function askQuestion()
    {
        $vector = $this->createEmbede($this->validatedData['prompt']);

        handleSubscriptionAndCredit(subscription('getUserSubscription', $this->userId), $vector->usage->totalTokens, $this->userId, new ContentService());

        if (filled($vector['data'][0]['embedding'])) {
            return $this->getMostSimilarVectors($vector['data'][0]['embedding'], '', $this->chunkSize());
        }

        return false;
    }

    /**
     * Retrieve the chunk size for processing.
     *
     * @return int The chunk size.
     */
    public function chunkSize()
    {
        return filled(request('chunk')) ? request('chunk') : $this->chunkData;
    }

    /**
     * Convert CSV file to text.
     *
     * @param string $path The path to the CSV file.
     * 
     * @return string The extracted text content.
     */
    protected static function csvToText($path)
    {
        $response = '';

        if (file_exists($path) && is_readable($path) && ($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $response .= implode(' ', $data);
            }
            fclose($handle);
        }

        return $response;
    }

     /**
     * Convert DOCX file to text.
     *
     * @param string $path The path to the DOCX file.
     * 
     * @return string The extracted text content.
     */
    protected static function docxToText($path)
    {
        $text = '';

        if (! file_exists($path)) {
            return $text;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $content = $zip->getFromName('word/document.xml');

            if ($content !== false) {
                $xml = new SimpleXMLElement($content);

                foreach ($xml->xpath('//w:t') as $element) {
                    $text .= trim($element) . ' ';
                }

                $text = str_replace("\xC2\xA0", ' ', $text);

                return $text;
            }

            $zip->close();
        }

        return $text;
    }

     /**
     * Tokenize the content.
     *
     * @return mixed The tokenized content.
     */
    public function tokenize()
    {
        if ( empty($this->fileInfo['content'])) {
            throw new \Exception(__('There was a problem with your provided :x.', ['x' => request('type')]));
        }

        $normalizedText = preg_replace("/\n+/", "\n", $this->fileInfo['content']);
        $words = explode(' ', $normalizedText);
        $words = array_filter($words);
        return $this->store(array_chunk($words, $this->chunkSize));
    }

    /**
     * Create an embedding for the given text.
     *
     * @param mixed $text The text to create an embedding for.
     * 
     * @return mixed The created embedding.
     */
    public function createEmbede($text)
    {
        return $this->client()->embeddings()->create([
            'model' => $this->embeddingModel(),
            'input' => $text,
        ]);
    }

    /**
     * Check the existence of data.
     *
     * @param mixed $id The ID of the data to check.
     * 
     * @return mixed The existing data.
     */
    public function checkExistance($id)
    {
        return $this->model()->where(['id' => $id])->firstOrFail();
    }

    /**
     * Store the tokenized data.
     *
     * @param mixed $tokens The tokens to store.
     * 
     * @return mixed The stored data.
     */
    public function store($tokens)
    {
        $usages = 0;
        $userId = auth('api')->user()->id;
        $text = implode(' ', $tokens[0]);
        $vector = $this->createEmbede($text);
        $usages += $vector['usage']['total_tokens'];
        
        if (isset($this->fileInfo['fileInfo']['fileSize'])) {
            $this->metaData = [
                'size' => $this->fileInfo['fileInfo']['fileSize']
            ];
        }

        $embed = new EmbededResource();
        $embed->user_id = $userId;
        $embed->name = $this->fileInfo['fileInfo']['file_path_name'];
        $embed->original_name = $this->fileInfo['fileInfo']['originalName'];
        $embed->type = $this->validatedData['type'];
        $embed->content = $text;
        $embed->vector = json_encode($vector['data'][0]['embedding']);

        if (isset($this->fileInfo['fileInfo']['fileSize'])) {
            $embed->setMeta($this->metaData);
        }

        $embed->save();

        $this->embedId = $embed->id;
        $arrays = $tokens;
        $totalToken = count(array_shift($tokens));
        if ($totalToken > 0) {
            unset($tokens[0]); // remove the first element as we store it first.
            $secondArray = array_values($arrays);
            foreach ($secondArray as $token) {
                $text = implode(' ', $token);
                $vector = $this->createEmbede($text);
                $usages += $vector['usage']['total_tokens'];
                $embed = new EmbededResource();
                $embed->user_id = $userId;
                $embed->parent_id = $this->embedId;
                $embed->name = $this->fileInfo['fileInfo']['file_path_name'];
                $embed->original_name = $this->fileInfo['fileInfo']['originalName'];
                $embed->type = $this->validatedData['type'];
                $embed->content = $text;
                $embed->vector = json_encode($vector['data'][0]['embedding']);
                if (isset($this->fileInfo['fileInfo']['fileSize'])) {
                    $embed->setMeta($this->metaData);
                }
                $embed->save();
            }

            handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), $usages, $userId, new ContentService());
            $embedId = $this->embedId;
        }

        return $embedId ?? $this->embedId;
    }
}
