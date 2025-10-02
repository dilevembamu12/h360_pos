<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\UseCase;
use Modules\OpenAI\Transformers\Api\V1\UseCaseResource;

class UseCaseController extends Controller
{

    /**
     * Display a paginated list of use cases with their related data.
     *
     * @param Request $request The HTTP request instance.
     * @return UseCaseResource A collection of use cases wrapped in a resource response.
     */
    public function index(Request $request): UseCaseResource | \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $useCases = UseCase::with(['option','option.metadata', 'objectImage', 'useCaseCategories'])
                    ->filter('Modules\\OpenAI\\Filters\\UseCaseFilter')->paginate(preference('row_per_page'));

        return UseCaseResource::collection($useCases);
    }
}
