<?php

namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Subscription\Services\PackageSubscriptionService;
use App\Models\{
    User
};
use Db;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return [type]
     */
    public function update(Request $request)
    {
        $id = auth()->guard('api')->user()->id;
        $response = $this->checkExistence($id, 'users');

        if ($response['status'] === true) {

            $validator = User::siteUpdateValidation($request->only('name', 'image'), $id);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->messages());
            }

            try {
                DB::beginTransaction();

                $updated = (new User)->updateUser($request->only('name', 'image'), $id);
                $message =  $updated ? __('The :x has been successfully saved.', ['x' => __('User Info')]) : __('No changes found.');
                $user = User::with('avatarFile')->where('id', $id)->first();
                $this->updateStorageData($user);

                DB::commit();
                return $this->okResponse(new UserResource($user), $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse([], 500,  $e->getMessage());
            }
        }

        return $this->response([], 204, $response['message']);
    }

    /**
     * Return subscription's feature limits.
     *
     * @param PackageSubscriptionService $packageSubscriptionService
     * @return array
     */
    public function index(PackageSubscriptionService $packageSubscriptionService)
    {
        if (subscription('getUserSubscription', auth()->guard('api')->user()->id)) {
            
            $activeSubscription = $packageSubscriptionService->getUserSubscription();
            $activeFeatureLimits = $packageSubscriptionService->getActiveFeature($activeSubscription->id);

            // Convert all values to strings
            $convertedData = array_map(function($item) {
                return array_map('strval', $item);
            }, $activeFeatureLimits);
            return $this->response($convertedData);
            
        }

        $activeFeatureLimits =  $packageSubscriptionService->getDefaultFeature();
        return $this->response($activeFeatureLimits , 202, __('You don\'t have any subscription. Please subscribe a plan.'));
        
    }

    /**
     * Delete
     * @param Request $request
     * @return [type]
     */
    public function destroy(Request $request)
    {
        $id = auth()->guard('api')->user()->id;
        $role =  auth()->guard('api')->user()->role()->slug;

        $response = $this->checkExistence($id, 'users', ['getData' => true]);

        if ($response['status']) {
            if ($role == 'admin') {
                return $this->unprocessableResponse([], __("Admin account can't be deleted."));
            }
            if (!Hash::check($request->password, $response['data']->password)) {
                return $this->unprocessableResponse([], __('Password does not match'));
            }
            if (User::where('id', $id)->delete()) {
                \Auth::guard('api')->user()->token()->delete();
                return $this->okResponse([], __('Your :x has been successfully deleted.', ['x' => __('Account')]));
            }
        }

        return $this->notFoundResponse([], $response['message']);
    }

    /**
      * Update Storage Driver data
      *
      * @param array $requestData
      *
      * @throws \Exception
      *
      */
      private function updateStorageData($object) {

        $id = $object->objectFile()->value('file_id');

        if ($id) {
            $file = \App\Models\File::where([ 'id' => $id])->first();

            $currentValue = app()->make('all-image');
            $newValue = 'public/uploads/' . str_replace('\\', '/', $file->file_name);

            if (is_array($currentValue)) {
                $currentValue[] = $newValue;
            }
            app()->instance('all-image', $currentValue);
        }
    }

}
