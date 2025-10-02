<?php
/**
 * @package Language
 * @author TechVillage <support@techvill.org>
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 20-05-2021
 * @modified 07-09-2021
 */

namespace App\Models;
use App\Models\Model;
use Cache;
use Validator;
use Auth;

class Language extends Model
{
    /**
     * timestamps
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'flag',
        'status',
        'is_default',
        'direction'
    ];

    /**
     * @param array $data
     * @return mixed
     */
    protected static function updateValidation($data = [])
    {
        $validator = Validator::make($data, [
            'edit_direction'    => 'required|in:ltr,rtl',
            'edit_status'       => 'required|in:Active,Inactive',
        ]);

        return $validator;
    }

    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function updateLanguage($data, $id)
    {
        $updateLanguage = parent::where('id', $id);
        if ($updateLanguage->exists()) {
            $language = parent::where('is_default', 1)->first();
            $language->is_default = 0;
            $language->save();
            $updateLanguage->update($data);
            $updateLanguageInfo = Language::where('id', $id)->first();
            if (empty($updateLanguageInfo->is_default) && empty(Language::where('is_default', 1)->count())) {
                Language::where('short_name', 'en')->update(['is_default' => 1]);
                $updateLanguageInfo->short_name = 'en';
            }
            Preference::where('category', 'company')
                ->where('field', 'dflt_lang')
                ->update(['value' => $updateLanguageInfo->short_name]);
            self::forgetCache(['languages', 'preferences']);
            Cache::forget(config('cache.prefix') . '-admin-language-'. Auth::user()->id);
            return true;
        }
        return false;
    }
}
