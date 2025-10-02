<?php

namespace App\Models;

use App\Models\Model;
use App\Rules\{
    CheckValidEmail,
    StrengthPassword
};
use Validator, DB;
use Illuminate\Validation\Rule;
class PasswordReset extends Model
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'password_resets';

    /**
     * Timestamp
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
    	'email', 'token', 'created_at'
    ];

    /**
     * Store Validation
     * @param array $data
     * @return mixed
     */
    protected static function storeValidation($data = [])
    {
        $validator = Validator::make($data, [
            'email' => [
                'required',
                'email',
                Rule::exists('users')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
                new CheckValidEmail,
            ],
        ], [
            'email.exists' => __('The selected email does not exist.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('Please provide a valid email address.'),
        ]);

        return $validator;
    }

    /**
     * Password Validation
     * @param array $data
     * @return mixed
     */
    protected static function passwordValidation($data = [])
    {
        $validator = Validator::make($data, [
            'password' => ['required', 'confirmed', new StrengthPassword]
        ]);

        return $validator;
    }

    /**
     * store
     * @param array $data
     * @return boolean
     */
    public function storeOrUpdate($data = [])
    {
        if (parent::updateOrInsert(['email' => $data['email']], $data)) {
            return true;
        }

        return false;
    }
    /**
     * Check token existence
     * @param array $data
     * @return boolean
     */
    public function tokenExist($data)
    {
        if (parent::where('token', $data)->orWhere('otp', $data)->first()) {
            return true;
        }

        return false;
    }

    /**
     * Update
     * @param array $request
     * @param int $id
     * @return array
     */
    public function updatePassword($request = [], $id = null)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('User')])];
        $result = User::where('id', $id);

        if ($result->exists()) {
            DB::beginTransaction();

            try {
                $result->update(array_intersect_key($request, array_flip((array) ['password', 'updated_at'])));

                parent::where('token', $request['token'])->orWhere('otp', $request['token'])->update(['token' => null, 'otp' => null]);

                $data['status'] = 'success';
                $data['message'] = __('Password reset successfully');
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                $data['message'] = $e->getMessage();
            }
        }

        return $data;
    }
}
