<?php
/**
 * @package UserAccessController for User
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 10-06-2024
 */

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use App\Http\Controllers\Controller;
use Modules\OpenAI\Transformers\Api\v2\UserAccessResource;
use Illuminate\Http\{
    JsonResponse
};

class UserAccessController extends Controller
{
    /**
     * Retrieves the user access permissions and returns them as a UserAccessResource.
     *
     * @return UserAccessResource|JsonResponse The user access permissions as a UserAccessResource or a JSON response.
     */
    public function index():JsonResponse|UserAccessResource
    {
        return new UserAccessResource(json_decode(preference('user_permission')));
    }
}
