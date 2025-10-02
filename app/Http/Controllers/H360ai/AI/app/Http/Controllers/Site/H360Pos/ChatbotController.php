<?php

/**
 * @package UserController
 * @author TechVillage <support@techvill.org>
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @contributor Soumik Datta <[soumik.techvill@gmail.com]>
 * @created 22-11-2021
 */

 namespace App\Http\Controllers\Site\H360Pos;

use Auth, Hash, DB, Crypt, Session;
use App\Models\{User, Team, TeamMemberMeta};
use App\Models\{PasswordReset, Role, RoleUser};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Services\UserService;
use App\Services\Mail\{
    UserVerificationMail,
    MemberMailService,
};
use App\Rules\CheckValidEmail;
use Illuminate\Support\Facades\Validator;

use Modules\Subscription\Entities\{
    Credit, SubscriptionDetails, PackageSubscription
};
use Modules\Subscription\Services\CreditService;
use App\Services\SubscriptionService;
use Modules\Subscription\Services\PackageService;
/*--------------------------------------------------------------------------------------*/
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotWidgetResource;
use Modules\OpenAI\Entities\{
    ChatBot,
    Archive,
    EmbededResource,
    FeaturePreference
};

use App\Models\File;
use Exception, App;
use Illuminate\Http\Response;
use App\Traits\ReportHelperTrait;
use Modules\OpenAI\Services\ContentService;
use Modules\MediaManager\Http\Models\ObjectFile;
class ChatbotController extends Controller
{
    use ReportHelperTrait;

    private $contentService;

    /**
     * ChatBotWidgetService constructor.
     *
     */
    public function __construct()
    {
        $this->contentService = new ContentService();
    }

    public function create(Request $request, SubscriptionService $subscriptionService): JsonResponse{
        
        //dd(111);
        //je me connecte
        $data = [
            'email' => "hospitality360.congo@gmail.Com",
            'password' => "123456789",
        ];
        
        //si la connexion refuse je quite
        if(!auth()->attempt($data)){
            return $this->unprocessableResponse([], __('Invalid email or password'));
        }


        /******creation chatbot **/
        //on recupere le business_id a partir du payload pour recuperer le user dans le db
        //on recupere 
        $requestData=[];
        $subscription = null;
        $userId = $this->contentService->getCurrentMemberUserId('meta', null);

        /*
        if (! subscription('isAdminSubscribed')) {
            $userStatus = $this->contentService->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message'], Response::HTTP_FORBIDDEN);
            }

            $validation = subscription('isValidSubscription', $userId, 'chatbot', null, null);
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && ! auth()->user()->hasCredit('chatbot')) {
                throw new Exception($validation['message'], Response::HTTP_FORBIDDEN);
            }
        }
        */
        $requestData['lang']="French";
        $requestData['theme_color'];
        $requestData['name'];
        $requestData['code']="chatbot_4_31";

        DB::beginTransaction();

        try {
            // Store New Chat Bot
            $newChatBot = new ChatBot();
            $newChatBot->user_id = $userId;
            $newChatBot->name =  $requestData['name'];
            $newChatBot->code = substr(str_replace('-', '', (string) \Str::uuid()), 0, 15);
            $newChatBot->message ="Bonjour, Je suis votre assistant virtuel " . $requestData['name'] . ". Comment puis-je vous aider aujourd'hui?";
            $newChatBot->description = "Qu'est-ce qui vous amène ici aujourd'hui ? N'hésitez pas à poser des questions.";
            $newChatBot->role = "Ai Assistant";
            $newChatBot->status = "Active";
            $newChatBot->type = 'widgetChatBot';
            $newChatBot->is_default = 0;
            $newChatBot->theme_color = $requestData['theme_color'];
            $newChatBot->language = $requestData['lang'];
            $newChatBot->brand = (boolean) true;

            // Chat Provider & Model
            $newChatBot->provider = $requestData['provider'];
            $newChatBot->model = $requestData['model'];

            // Chat Provider & Model
            $newChatBot->embedding_provider = $requestData['embedding_provider'];
            $newChatBot->embedding_model = $requestData['embedding_model'];

            $newChatBot->image = [
               'url' => $this->chatbotSettings('image'),
               'is_delete' => false
            ];

            $newChatBot->floating_image = [
                'url' => $this->chatbotSettings('floating_image'),
                'is_delete' => false
            ];

            // NOTE:: will be dynamic
            $newChatBot->script_code = "<script src='" . url('/') . "/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src=\"" . url("/chatbot/embed/chatbot_code={$newChatBot->code}/welcome") . "\" data-iframe-height=\"532\" data-iframe-width=\"400\"></script>";

            $newChatBot->save();

            handleSubscriptionAndCredit(subscription('getUserSubscription',$userId), 'chatbot', 1, $userId);
            
            DB::commit();
            return new ChatBotWidgetResource($newChatBot);

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }













        /******************************************************************************** */
        $role = Role::getAll()->where('slug', 'user')->first();
        $request['status'] = preference('user_default_signup_status') ?? 'Pending';
        $request['first_name'] = "AAA";
        $request['last_name'] ="BBB";
        $request['email']="user_h360gpt_1@h360.cd";
        $request['password'] ="H360POS";

        $request['name'] = $request->first_name .' '. $request->last_name;
        $request['password'] = \Hash::make($request->password);
        $request['email'] = $request->email;


        $request['activation_code'] = Null;// \Str::random(10);
        $request['activation_otp'] = Null;//random_int(1111, 9999);

        //verifie que l'utilisateur n'existe pas encore
        //dd($request['email'] );
        $user=User::where('email',$request['email'] )->first();
        if(!empty($user)){ return "existe deja";}
  
        try {
            DB::beginTransaction();
            $id = (new User)->store($request->only('name', 'email', 'activation_code', 'activation_otp', 'password', 'status'));
            
            //dd($id);
            
            if (!empty($id)) {
                if (!empty($role)) {
                    (new RoleUser())->store(['user_id' => $id, 'role_id' => $role->id]);
                }
                $defaultPackage = CreditService::defaultPackage();
                if ((preference('is_default_package') == 1) && !empty($defaultPackage)) {
                    $subscriptionService->storeFreeCredit($defaultPackage, $id);
                }
        
               DB::commit();
        
               return $this->createdResponse( [], __('Your account has been successfully registered.') );
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->badRequestResponse([], $e->getMessage());
        }

        

    }

}
