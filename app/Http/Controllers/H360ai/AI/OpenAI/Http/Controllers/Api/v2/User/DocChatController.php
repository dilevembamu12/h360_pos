<?php

/**
 * @package DocChatController for User
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 17-03-2024
 */
namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use App\Http\Controllers\Controller;

use Modules\OpenAI\Services\{
    ChatService
};
use Modules\OpenAI\Entities\{
    Archive,
};

use Illuminate\Http\Response;

use Modules\OpenAI\Transformers\Api\v2\Chat\ChatDetailsResource;

class DocChatController extends Controller
{
    /**
     * Chat Service
     *
     * @var object
     */
    protected $chatService;

    /**
     * Constructor
     * 
     * @param ChatService $chatService
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    /**
     * Method conversation
     *
     * @param $id
     *
     * @return void
     */
    public function conversation($id)
    {
        $configs = $this->initialize([], request()->all());
        $contents = (new Archive())->contentById($id);


        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }
        $contents = $contents->paginate($configs['rows_per_page']);

       
        if ($contents) {
            return ChatDetailsResource::collection($contents)->response()->getData(true);
        }

        return response()->json(['error' => __('No Data found.')], Response::HTTP_NOT_FOUND);
    }
}


