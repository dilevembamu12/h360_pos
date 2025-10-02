<?php

namespace Modules\OpenAI\Services\v2;

use DB;
use ZipArchive;
use DOMDocument;
use SimpleXMLElement;
use Exception;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Response;
use Modules\OpenAI\Entities\ChatBot;
use Spekulatius\PHPScraper\PHPScraper;
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Entities\EmbededResource;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use Modules\OpenAI\Services\v2\FeaturePreferenceService;
use AiProviderManager;

class ChatBotTrainingService
{

     /**
     * @var int The size of the chunk, default value is 256.
     */
    protected $chunkSize = 256;

    /**
     * The AI provider used for generating embeddings..
     *
     * @var mixed $aiEmbeddingProvider The AI provider instance or configuration used for embedding generation.
     */
    private $aiEmbeddingProvider;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        // For Embedding Response
        if (! is_null(request('embedding_provider'))) {
            $this->aiEmbeddingProvider = AiProviderManager::isActive(request('embedding_provider'), 'aiembedding');
            //dd($this->aiEmbeddingProvider);
            manageProviderValues(request('embedding_provider'), 'embedding_model', 'aiembedding');
        }
    }

    /**
     * Get the owner ID of a widget chat bot based on its ID.
     *
     * @param int $id The ID of the chat bot.
     * @return \Modules\OpenAI\Models\ChatBot|null The chat bot model instance or null if not found.
     */
    public function getBotDetails($chatbotCode): ?\Modules\OpenAI\Entities\ChatBot
    {
        return ChatBot::with('metas')->where('code', $chatbotCode)
            ->where('type', 'widgetChatBot')
            ->first();
    }

    /**
     * Store the content provided in the requestData based on the type (file, url, or text).
     *
     * @param string $code
     * @param array $requestData An array containing the data to be stored.
     *
     * @return Builder[]|Collection The stored EmbededResource objects with their metas, user, and childs.
     *
     * @throws Exception If an error occurs during the storage process.
     */
    public function store(string $code, array $requestData)
    {
        $service = new FeaturePreferenceService();
        $data = $service->processData('chatbot')['training_options'];

        $newArray = ['file' => 'file_upload', 'url' => 'website_url', 'text' => 'pure_text'];

        $type = $requestData['type'] ?? null;

        if (isset($newArray[$type]) && !$data[$newArray[$type]]) {
            throw new Exception(__(':x has been disabled for training. For assistance, please contact the administration.', ['x' => ucfirst(str_replace('_', ' ', $newArray[$type]))]));
        }

        $items = [];

        if ($type === 'file') {
            foreach ($requestData['file'] as $file) {
                $items[] = $this->parseContent($file);
            }
        } elseif ($type === 'url') {
            foreach ($requestData['url'] as $url) {
                
                $items[] = $this->parseUrl($url);
            }
        } else {
            $items[] = [
                'content' => $requestData['text'],
                'fileInfo' => [
                    'file_path_name' => 'Text',
                    'originalName' => 'Text',
                ]
            ];
        }

        DB::beginTransaction();

        try {

            $ids = [];
            foreach ($items as $item) {

                $content = $item['content'];
                $word_count = str_word_count($content);

                $embed = null;
                $serviceData = [
                    'state' => 'Untrained',
                    'words' => $word_count,
                    'last_trained' => 'N\A'
                ];
            
                if ($type === 'url') {
                    $embed = EmbededResource::where(['original_name' => $url, 'category' => 'widgetChatBot', 'user_id' => auth()->user()->id])
                            ->whereHas('metas', function($query) use($code) {
                                $query->where(['key' => 'chatbot_code', 'value' => $code]);
                            })->first();
                    if ($embed) {
                        $embed->content = $content;
                        $embed->updated_at = now();
                    }
                }

                if (is_null($embed)) {
                    $embed = new EmbededResource();
                    $embed->user_id = auth()->user()->id;
                    $embed->name = $item['fileInfo']['file_path_name'];
                    $embed->original_name = $item['fileInfo']['originalName'];
                    $embed->type = $type;
                    $embed->content = $content;
                    $embed->category = 'widgetChatBot';
                    
                    $serviceData['chatbot_code'] = $code;
            
                    if ($type === 'file') {
                        $serviceData['size'] = $item['fileInfo']['fileSize'];
                        $serviceData['file_name'] = $item['fileInfo']['file_path_name'];
                        $serviceData['extension'] = $item['fileInfo']['extension'];
                    }
                }

                $embed->setMeta($serviceData);
                $embed->save();

                $ids[] = $embed->id;
            }

            DB::commit();

            return $this->model()->whereIn('id', $ids)->get();

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Extract text from various file formats.
     *
     * @param mixed $file
     *
     * @return mixed The result of extracting text from the file.
     */
    public function parseContent(mixed $file): mixed
    {
        $fileInfo = $this->storeFile($file);
        $path = $fileInfo['destinationPath'];
        $ext = $fileInfo['extension'];

        // Map file extensions to their respective content extraction methods
        $methodMap = [
            'pdf' => 'pdfToText',
            'doc' => 'docToText',
            'xlsx' => 'xlsxToText',
            'csv' => 'csvToText',
            'docx' => 'docxToText',
            'txt' => 'readText'
        ];

        // Extract content if the file extension is supported
        if (!isset($methodMap[$ext])) {
            throw new Exception('Unsupported file extension: ' . $ext);
        }

        // Call the corresponding method to extract text content
        $response = $this->{$methodMap[$ext]}($path);

        // Clean up the extracted text
        $response = trim(preg_replace('/[\t\n\s]+/', ' ', $response));

        return [
            'content' => $response,
            'fileInfo' => $fileInfo,
        ];
    }

    /**
     * Parse the content and file information from a given URL.
     *
     * @param string $url The URL to parse.
     *
     * @return array An array containing the parsed content and file information.
     */
    public function parseUrl(string $url): array
    {
        $web = new PHPScraper();
        $web->go($url);

        $fileInfo = [
            'file_path_name' => $this->getDomainName($url),
            'originalName' => $url,
        ];

        return [
            'content' => $this->urlContent($web->paragraphs),
            'fileInfo' => $fileInfo,
        ];
    }

    /**
     * Domain name parser.
     *
     * @param string $url The URL to parse.
     *
     * @return string The extracted domain name.
     */
    public function getDomainName(string $url): string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];

        // Remove 'www.' if it exists
        $host = preg_replace('/^www\./', '', $host);

        return explode('.', $host)[0];
    }

    /**
     * Store file.
     *
     * @param mixed $file The file to be stored.
     *
     * @return array Information about the stored file.
     */
    public function storeFile(mixed $file): array
    {
        $this->uploadPath();
        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        $destinationFolder = public_path('uploads') . DIRECTORY_SEPARATOR . 'aiWidgetChatbotFiles' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
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
     * Create upload path.
     *
     * @return string The generated upload path.
     */
    protected function uploadPath(): string
    {
        return createDirectory(implode(DIRECTORY_SEPARATOR, ['public', 'uploads', 'aiWidgetChatbotFiles']));
    }

    /**
     * Reads the contents of a text file.
     *
     * @param string $path The path to the text file.
     * @return string|false The file contents, or false on failure.
     */
    protected static function readText($path)
    {
        return file_get_contents($path);
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
    protected static function docToText(string $path): string
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
    protected static function xlsxToText(string $path): string
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
     * Convert DOCX file to text.
     *
     * @param string $path The path to the DOCX file.
     *
     * @return string The extracted text content.
     */
    protected static function docxToText(string $path): string
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
     * Convert CSV file to text.
     *
     * @param string $path The path to the CSV file.
     *
     * @return string The extracted text content.
     */
    protected static function csvToText(string $path): string
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
     * Retrieve content from URL.
     *
     * @param array $contents The contents retrieved from the URL.
     *
     * @return string The concatenated text content.
     */
    public function urlContent(array $contents): string
    {
        $text = '';
        foreach ($contents as $content) {
            $text .= trim($content) . ' ';
        }

        return $text;
    }

   /**
     * Fetches and validates unique URLs from a given URL using PHPScraper.
     *
     * @param array $requestData An associative array containing the URL to fetch. Example: ['url' => 'http://example.com']
     *
     * @return array An array of validated URLs, excluding URLs with fragments.
     */
    public function fetchUrl(array $requestData): array
    {
        $service = new FeaturePreferenceService();
        $data = $service->processData('chatbot')['training_options'];

        if (isset($data['website_url']) && !$data['website_url']) {
            throw new Exception(__(':x has been disabled for training. For assistance, please contact the administration.', ['x' => __('Website URL')]));
        }

        $url = $requestData['url'];

        $web = new PHPScraper();
        $web->go( $url);

        $uniqueUrls = $web->links;
        $uniqueUrls[] = $url;

        foreach (array_unique($uniqueUrls) as $url) {
            // Check if the URL is valid and ignore URLs with fragments
            if (filter_var($url, FILTER_VALIDATE_URL) !== false && strpos($url, '#') === false) {
                $validUrls[] = $url;
            }
        }

        return $validUrls;
    }

    /**
     * Get the model instance with eager loading of metas, user, and childs relationships.
     *
     * @return Builder The model instance with eager loaded relationships.
     */
    public function model(): Builder
    {
        return EmbededResource::with(['metas', 'user', 'childs'])->whereCategory('widgetChatBot');
    }

    /**
     * Train the chatbot resources based on the provided embeded IDs.
     *
     * @param array $requestData An array containing the embeded IDs to train.
     *
     * @return Builder[]|Collection The trained EmbededResource objects with eager loaded relationships.
     */
    public function train(array $requestData): Builder|Collection
    {
        if (! $this->aiEmbeddingProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => request('embedding_provider'), 'y' => __('Ai Embedding')]));
        }

        $resources = $this->model()->whereIn('id', $requestData['embeded_id'])->whereNull('parent_id')->get();
        $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
        
        foreach ($resources as $resource) {

            if ($resource->type === 'url' && $resource->state === 'Trained') {

                $this->model()->where('parent_id', $resource->id)->delete();

                // Parse URL and update content if type is 'url'
                $content = $this->parseUrl($resource->original_name);
                $resource->content = $content['content'];
            }

            if ($resource->state === 'Untrained' || $resource->type === 'url') {
                // Normalize text, tokenize, and create embeddings
                $normalizedText = preg_replace("/\n+/", "\n", $resource->content);
                $words = explode(' ', $normalizedText);
                $words = array_filter($words);
                $tokens = array_chunk($words, $this->chunkSize);

                $usages = 0;

                foreach ($tokens as $token) {
                    $text = implode(' ', $token);

                    $options['text'] = $text;
                    $options['model'] = request('embedding_model');

                    $vector = $this->aiEmbeddingProvider->createEmbeddings($options);
                    $usages += $vector->expense();

                    $embed = new EmbededResource();

                    $embed->user_id = $resource->user_id;
                    $embed->parent_id = $resource->id;
                    $embed->name = $resource->name;
                    $embed->original_name = $resource->original_name;
                    $embed->type =  $resource->type;
                    $embed->content = $text;
                    $embed->vector = json_encode($vector->content());
                    $embed->category = 'widgetChatbot';

                    $embed->save();

                }

                // Update resource state and last_trained timestamp
                $resource->state = 'Trained';
                $resource->last_trained = now();
                $resource->usages += $usages;

                $resource->embedding_provider = request('embedding_provider');
                $resource->embedding_model = request('embedding_model');

                handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', subscription('tokenToWord', $usages), $userId);
            }

            // Save the updated resource
            $resource->save();
        }

        return $resources;
    }

    /**
     * Delete materials and their child items based on the provided IDs.
     *
     * @param array $requestData An array containing the IDs of the materials to be deleted.
     *
     * @throws Exception If no materials are found with the provided IDs.
     */
    public function delete(array $requestData)
    {
        DB::beginTransaction();
        try {

            $materials = $this->model()->whereIn('id', $requestData['id'])->where('user_id', auth()->user()->id)->get();

            // Check if materials are found
            if ($materials->isEmpty()) {
                throw new Exception(__('No :x found.', ['x' => count($requestData['id']) == 1 ? __('Material') : __('Materials')]), Response::HTTP_NOT_FOUND);
            }

            $foundMaterialIds = $materials->pluck('id')->toArray();

            // Identify IDs that are missing
            $missingMaterialIds = array_diff($requestData['id'], $foundMaterialIds);

            // Check if there are missing materials
            if (!empty($missingMaterialIds)) {
                throw new Exception(__('The selected :x does not belong to the current user. Please kindly check your selection.', ['x' => count($missingMaterialIds) == 1 ? __('Material') : __('Materials')] ), Response::HTTP_NOT_FOUND);
            }

            $this->deleteMeta($materials);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete meta data for each item in the provided array of items.
     *
     * @param array|object $items An array of items for which to delete meta data.
     *
     * @return void
     */
    private function deleteMeta(array|object $items): void
    {
        // Iterate through each chatBot to unset meta data and save
        foreach ($items as $item) {

            if ($item->childs && !$item->childs->isEmpty()) {
                $this->deleteMeta($item->childs);
            }

            $item->save();
            $item->delete();
            $item->unsetMeta(array_keys($item->getMeta()->toArray()));
        }
    }

}
