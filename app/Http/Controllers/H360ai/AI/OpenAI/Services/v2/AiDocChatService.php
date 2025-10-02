<?php 

namespace Modules\OpenAI\Services\v2;

use Illuminate\Database\Eloquent\Builder;
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Entities\{
    EmbededResource,
};
use Exception;
use AiProviderManager;


class AiDocChatService
{
    private $aiEmbeddingProvider;

    /**
     * @var int The size of the chunk, default value is 256.
     */
    protected $chunkSize = 256;

    /**
     * Constructor method.
     *
     *
     * @return void
     */
    public function __construct()
    {
        if(! is_null(request('provider'))) {
            $this->aiEmbeddingProvider = AiProviderManager::isActive(request('provider'), 'aiembedding');
            manageProviderValues(request('provider'), 'model', 'aiembedding');
        }
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
     * Get files with relational data.
     *
     * @return mixed
     */
    public function files(): mixed
    {
        return $this->model()->with(['user', 'childs']);
    }

    /**
     * Get the model instance with metas relationship loaded.
     *
     * @return builder The model instance with metas relationship loaded.
     */
    public function model()
    {
        return EmbededResource::with('metas');
    }

    /**
     * Process and store content based on the provided data.
     *
     * @param array $requestData The data containing information about the content to store.
     *
     * @throws Exception When there is a problem with the provided content.
     * @return int The ID of the stored content.
     */
    public function extractedFileIds(array $requestData): int
    {
        if (! $this->aiEmbeddingProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => ucfirst(request('provider')), 'y' => __('Ai Embedding')]));
        }

        $items = [];
        if ($requestData['type'] === 'file') {
            foreach ($requestData['file'] as $file) {
                $items[] = $this->parseContent($file);
            }
        } elseif ($requestData['type'] === 'url') {
            $items[] = (new EmbeddedService())->parseUrl();
        }

        $usages = 0;
        $embedId = null;

        $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
        foreach ($items as $item) {
            if (empty($item['content'])) {
                throw new Exception(__('There was a problem with your provided :x.', ['x' => $requestData['type']]));
            }
            
            $normalizedText = preg_replace("/\n+/", "\n", $item['content']);
            $words = array_filter(explode(' ', $normalizedText));
            $tokens = array_chunk($words, $this->chunkSize);

            $usages += $this->processTokens($tokens, $item, $requestData, $userId, $embedId);
            
            handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $usages, $userId);
        }
        if (is_null($embedId)) {
            throw new Exception(__('Something went worng. Please try again.'));
        }

        return $embedId;
    }

    /**
     * Process tokens to create embeddings and save them as EmbededResource instances.
     *
     * @param array $tokens The tokens to process.
     * @param array $item The item containing file information.
     * @param array $requestData The request data.
     * @param int $userId The user ID.
     * @param int|null $embedId The parent embed ID.
     * 
     * @return int The total usages.
     */
    private function processTokens($tokens, $item, $requestData, $userId, &$embedId): int
    {
        $usages = 0;
        $metaData = isset($item['fileInfo']['fileSize']) ? ['size' => $item['fileInfo']['fileSize']] : null;

        foreach ($tokens as $index => $tokenChunk) {
            $text = implode(' ', $tokenChunk);

            $options = $requestData;
            $options['text'] = $text;

            $vector = $this->aiEmbeddingProvider->createEmbeddings($options);
            $usages += $vector->expense();
            
            $embed = new EmbededResource();
            $embed->user_id = $userId;
            $embed->parent_id = $index > 0 ? $embedId : null;
            $embed->provider = $index == 0 ?  request('provider') : null;
            $embed->name = $item['fileInfo']['file_path_name'];
            $embed->original_name = $item['fileInfo']['originalName'];
            $embed->type = $requestData['type'];
            $embed->content = $text;
            $embed->vector = json_encode($vector->content());
            $embed->expense = $vector->expense();

            $embed->embedding_provider = request('provider');
            $embed->embedding_model = request('model');

            if ($metaData) {
                $embed->setMeta($metaData);
            }
            
            $embed->save();

            if ($index === 0) {
                $embedId = $embed->id;
            }
        }

        return $usages;
    }

    /**
     * Retrieve contents by their IDs.
     *
     * @param array $ids An array of IDs of the contents to retrieve.
     * @return mixed The retrieved contents with user, childs, and metas relationships loaded.
     */
    public function contents(array $ids): mixed
    {
        return $this->model()->with(['user', 'childs', 'metas'])->whereIn('id', $ids)->get();
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
        ];
        // Extract content if the file extension is supported
        if (!isset($methodMap[$ext])) {
            throw new Exception(__('Unsupported file extension: :x', ['x' => $ext]));
        }
        // Call the corresponding method to extract text content
        $response = (new EmbeddedService())->{$methodMap[$ext]}($path);
        // Clean up the extracted text
        $response = trim(preg_replace('/[\t\n\s]+/', ' ', $response));
        return [
            'content' => $response,
            'fileInfo' => $fileInfo,
        ];
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

        // Create a destination path
        $destinationFolder = public_path('uploads' . DIRECTORY_SEPARATOR . 'aiFiles' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR);

        // Ensure the directory exists
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $fileSize = $file->getSize();
        
        // Get the file content
        $fileContent = file_get_contents($file->getRealPath());
        
        // Save the file content to the destination
        file_put_contents($destinationFolder . $fileName, $fileContent);

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
     * Delete file.
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $file = $this->model()->where('id', $id)->first();

        if (!$file) {
            return false;
        }

        $file->unsetMeta(array_keys($file->getMeta()->toArray()));
        $file->save();

        return $file->delete();
    }

}
