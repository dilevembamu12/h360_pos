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
    Credit,
    SubscriptionDetails,
    PackageSubscription
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


use Modules\OpenAI\Services\v2\FeaturePreferenceService;
use Spekulatius\PHPScraper\PHPScraper;


use ZipArchive;
use DOMDocument;
use SimpleXMLElement;
use Smalot\PdfParser\Parser;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

use AiProviderManager;


use Modules\OpenAI\Services\v2\ChatBotTrainingService;


use App\Http\Requests\Admin\AuthUserRequest;

use Modules\OpenAI\Http\Requests\v2\{
    ChatBotWidgetTrainingRequest,
    ChatBotWidgetMaterialRequest,
    ChatBotFetchRequest
};

class UserController extends Controller
{
    use ReportHelperTrait;

    public function __construct(){
        
    }

    public function create(Request $request, SubscriptionService $subscriptionService): JsonResponse
    {
        //je me connecte
        //dd(111);
        $headers = $request->header();

        $login = [
            'email' => $request->header('H360AI-ADMIN-EMAIL'),
            'password' => $request->header('H360AI-ADMIN-PASS'),
        ];
        if (!auth()->attempt($login)) {
            return $this->unprocessableResponse([], __('Invalid email or password'));
        }

        //$request['status'] = preference('user_default_signup_status') ?? 'Pending';

        //return $this->createdResponse($request->all(), __("zz"));

        $business_id = $request->business_id;
        $h360_user_id = $request->user_id;
        $business_name = $request->business_name;
        //return $this->createdResponse([], __(str_replace('{business_id}',$business_id,$request->header('H360AI_USER_SIGNATURE')) ));


        $request['status'] = 'Active';//-
        $request['first_name'] = "H360";
        $request['last_name'] = "USER";
        $request['email'] = "user_h360gpt_$business_id@h360.cd";
        $request['name'] = "H360GPT - $business_name";
        $request['password'] = \Hash::make("H360POS");
        $request['activation_code'] = Null;// \Str::random(10);
        $request['activation_otp'] = Null;//random_int(1111, 9999);

        //verifie que l'utilisateur n'existe pas encore
        //dd($request['email'] );
        $user = User::where('email', $request['email'])->first();
        if (!empty($user)) {
            return $this->createChatbot($request, $subscriptionService, $user);
            //  return $this->unprocessableResponse([], __('user existe deja'));
        }




        //si la connexion refuse je quite
        $role = Role::getAll()->where('slug', 'user')->first();
        $defaultPackage = CreditService::defaultPackage();




        try {
            DB::beginTransaction();
            $id = (new User)->store($request->only('name', 'email', 'activation_code', 'activation_otp', 'password', 'status'));
            if (!empty($id)) {
                if (!empty($role)) {
                    (new RoleUser())->store(['user_id' => $id, 'role_id' => $role->id]);
                }
                $defaultPackage = CreditService::defaultPackage();
                if (!empty($defaultPackage)) {
                    $subscriptionService->storeFreeCredit($defaultPackage, $id);
                }

                DB::commit();

                return $this->createdResponse([], __('Your account has been successfully registered.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->badRequestResponse([], $e->getMessage());
        }
        return $this->createdResponse($role, preference('user_default_signup_status'));
    }



    protected function createChatbot(Request $request, SubscriptionService $subscriptionService, $user): JsonResponse
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
        
        $headers = $request->header();

        $login = [
            'email' => $request->header('H360AI-ADMIN-EMAIL'),
            'password' => $request->header('H360AI-ADMIN-PASS'),
        ];
        if (!auth()->attempt($login)) {
            return $this->unprocessableResponse([], __('Invalid email or password2'));
        }


        $requestData = [];
        $subscription = null;
        //$userId = $this->contentService->getCurrentMemberUserId('meta', null);


        $business_id = $request->business_id;
        $h360_user_id = $request->user_id;
        $business_name = $request->business_name;

        $user_id = $user->id;
        $chatbot_signature_code = str_replace('{user_id}', $h360_user_id, str_replace('{business_id}', $business_id, $request->header('H360AI-CHATBOT-SIGNATURE')));


        //return $this->createdResponse([], $chatbot_signature_code);
        //je verifie d'abord que le chat bot de cette personne n'existe pas encore 
        //si il existe alors je fais le processus de training et et j'exite
        $chatbot = ChatBot::where('code', $chatbot_signature_code)->first();
        
        if (!empty($chatbot)) {
            //document H360 ( DOcumentations, conditions utilisations)
            //Ventes

            $urls = [
                "https://pos.h360.cd/h360ai/doc/",
                "https://pos.h360.cd/h360ai/posgpt"
            ];
            return $this->storeMaterials($request, $urls, $chatbot_signature_code, $user_id);
            //on entraine les chatsbotes
        }


        $lang = "French";
        $requestData['lang'] = $lang;
        $requestData['theme_color'] = "#9163dd";
        $requestData['name'] = $request->name;


        // Chat Provider & Model
        $requestData['provider'] = "openai";
        $requestData['model'] = "gpt-4o-mini";

        // Chat Provider & Model
        $requestData['embedding_provider'] = "openai";
        $requestData['embedding_model'] = "text-embedding-3-large";



        DB::beginTransaction();

        try {
            // Store New Chat Bot
            $newChatBot = new ChatBot();
            $newChatBot->user_id = $user_id;
            $newChatBot->name = $requestData['name'];
            //$newChatBot->code = substr(str_replace('-', '', (string) \Str::uuid()), 0, 15);
            $newChatBot->code = $chatbot_signature_code;
            $newChatBot->message = "Bonjour, Je suis votre assistant virtuel " . $requestData['name'] . ". Comment puis-je vous aider aujourd'hui?";
            $newChatBot->description = "Qu'est-ce qui vous amène ici aujourd'hui ? N'hésitez pas à poser des questions.";
            $newChatBot->role = "Ai Assistant";
            $newChatBot->status = "Active";
            $newChatBot->type = 'widgetChatBot';
            $newChatBot->is_default = 0;
            $newChatBot->theme_color = $requestData['theme_color'];
            $newChatBot->language = $requestData['lang'];
            $newChatBot->brand = (boolean) true;
           
            $newChatBot->prompt=preg_replace('/(<br\s*\/?>\s*){2,}/', '\n',"Tu dois agir comme un assistant expert en gestion des petites et moyennes entreprise dans une entreprise ayant des points de ventes , stock physique ou virtuel et la caisse

Tu assistes les employés des entreprise à retrouver les réponses rapides à leur question en inspectant leur document intégré à ce chabot. Votre objectif est de comprendre en profondeur l'intention de l'utilisateur, de poser des questions de clarification si nécessaire, de réfléchir étape par étape à des problèmes complexes, de fournir des réponses claires et précises et d'anticiper de manière proactive les informations de suivi utiles

analyser le contenu de l'entreprise , faire les calculs comptables et budgétaire , gérer l'entreprise comme un CEO expérimenter selon les la question qui t'es posée

Pour donner ta réponse, tu utiliseras un ton enthousiaste et passionné, et surtout chiffré.

Tu donneras ta réponse de manière longue et lisible en mettant en valeur les phrases ou les mots important pour attirer l'attention de l'utilisateur");


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

            //return $this->createdResponse($newChatBot, __($request->header('H360AI-CHATBOT-SIGNATURE')));

            $newChatBot->save();

            handleSubscriptionAndCredit(subscription('getUserSubscription', $user_id), 'chatbot', 1, $user_id);

            DB::commit();
            new ChatBotWidgetResource($newChatBot);
            return $this->createdResponse($request->all(), __("chatbot créer avec success"));

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }













        /******************************************************************************** */
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
    public function storeMaterials(Request $request, array $urls, $code, $user_id)
    {
        


        $business_id = $request->business_id;
        $h360_user_id = $request->user_id;
        $business_name = $request->business_name;

        $user_id = $user_id;
        $chatbot_signature_code = str_replace('{user_id}', $h360_user_id, str_replace('{business_id}', $business_id, $request->header('H360AI-CHATBOT-SIGNATURE')));



        //je fait d'abord une authentification en internet de user en cours pour que ses information soit stoquée dans la session
        $_request = new AuthUserRequest();
        $_request->setMethod('POST');
        $_request->replace([
            '_token' => csrf_token(),
            'email' => $request['email'],
            'password' => "H360POS",
        ]);
        $result = \App::makeWith(\App\Http\Controllers\Site\LoginController::class)->authenticate($_request);



        //suppression des materiels
        $materials = EmbededResource::with(['metas', 'user', 'childs'])->whereCategory('widgetChatBot')
            ->whereHas('metas', function ($q) use ($chatbot_signature_code) {
                $q->where('key', 'chatbot_code')->where('value', $chatbot_signature_code);
            })->get();
        foreach ($materials as $material) {
            foreach ($material->metas as $key => $meta) {
                # code...
                $meta->delete();

            }
            $material->delete();
        }




        $txt = "AA AA AA";


        //creation du materiel 
        $headers = [
            "user-id" => $h360_user_id,
        ];
        $postdata = [];
        $ids = [];
        foreach ($urls as $key => $url) {
            
            $client = new \GuzzleHttp\Client([
                \GuzzleHttp\RequestOptions::VERIFY => false
            ]);
            $_response = $client->get($url, [
                'headers' => $headers,
                'body' => json_encode($postdata)
            ]);

            $txt = $_response->getBody()->getContents();
            $txt = preg_replace('/(<br\s*\/?>\s*){2,}/', '\n', strip_tags($txt));
            
            $_request = new ChatBotWidgetTrainingRequest();
            $_request->setMethod('POST');
            $_request->replace([
                'text' => $txt,
                'type' => "type",
            ]);
            $result = \App::makeWith(\Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotTrainingController::class)->store($chatbot_signature_code, $_request);



            $ids["embeded_id"][] = $result->original['data'][0]->id;
        }

        //entrainement des materiels
        $_request = new ChatBotWidgetMaterialRequest();
        $_request->setMethod('POST');
        $_request->replace($ids);
        $result = \App::makeWith(\Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotTrainingController::class)->train($chatbot_signature_code, $_request);


        return response()->json($result);

    }


    /**
     * Retrieve the chatbot settings file URL based on the feature preference.
     *
     * @return string The URL of the chatbot settings file.
     */
    protected function chatbotSettings($option)
    {
        $keys = [
            'image' => 'default_avatar',
            'floating_image' => 'default_floating_image',
        ];

        $preference = FeaturePreference::whereSlug('chatbot')->first();

        if (!$preference || !isset($preference->general_options)) {
            return null;
        }

        $data = json_decode($preference->general_options, true);

        $keysToUpdate = [
            'default_avatar' => defaultImage('chatbots'),
            'default_floating_image' => defaultImage('chatbot_floating_image')
        ];

        $fileIds = array_intersect_key($data, $keysToUpdate);

        if (!empty($fileIds)) {
            $files = \App\Models\File::whereIn('id', $fileIds)->get()->keyBy('id');

            foreach ($keysToUpdate as $key => $defaultImage) {
                if (isset($data[$key])) {
                    $file = $files->get($data[$key]);

                    $fileUrl = $file ? $file->file_name : $defaultImage;
                    $data[$key] = $this->uploadPath() . '//' . $fileUrl;
                }
            }
        }

        return $data[$keys[$option]] ?? null;
    }



    /**
     * Create upload path
     * @return [type]
     */
    public function uploadPath()
    {
        return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads']));
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
}
