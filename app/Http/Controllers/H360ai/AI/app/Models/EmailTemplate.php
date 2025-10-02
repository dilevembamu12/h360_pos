<?php
/**
 * @package EmailTemplate
 * @author TechVillage <support@techvill.org>
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @created 20-05-2021
 */

namespace App\Models;
use App\Rules\CheckLanguage;
use App\Models\Model;
use Validator, DB;

class EmailTemplate extends Model
{

    /**
     * timestamps
     * @var boolean
     */
	public $timestamps = false;

    /**
     * Foreign key with Language model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'language_id');
    }

    /**
     * Foreign key with EmailTemplate model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo("App\Models\EmailTemplate", 'parent_id');
    }

    /**
     * Foreign key with EmailTemplate model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child()
    {
        return $this->hasMany("App\Models\EmailTemplate", 'parent_id');
    }

    /**
     * Store Validation
     * @param array $data
     * @return mixed
     */
    protected static function storeValidation($data = [])
    {
        $validator = Validator::make($data, [
            'name' => 'required|max:50',
            'slug' => 'required|unique:email_templates,slug',
            'subject' => 'required',
            'body' => 'required',
            'status' => 'required|in:Active,Inactive',
            'data' => ['nullable', new CheckLanguage]
        ]);

        return $validator;
    }

    /**
     * Update
     * @param array $data
     * @param int $id
     * @return mixed
     */
    protected static function updateValidation($data = [], $id = null)
    {
        $validator = Validator::make($data, [
            'name' => 'required|max:50',
            'slug' => [
                'required',
                'unique:email_templates,slug,' . $id
            ],
            'subject' => 'required',
            'body' => 'required',
            'status' => 'required|in:Active,Inactive',
            'data' => ['nullable', new CheckLanguage]
        ]);

        return $validator;
    }

    /**
     * Store
     * @param array $data
     * @return boolean
     */
    public function store($data = [])
    {
        $id = parent::insertGetId(array_intersect_key($data, array_flip((array) ['name', 'slug', 'subject', 'body', 'status', 'language_id', 'variables'])));

        if (isset($id) && !empty($id)) {
            if (isset($data['data']) && !empty($data['data'])) {
                foreach ($data['data'] as $key => $value) {
                    if (!empty($value['subject']) && !empty($value['body']) && !empty($value['language_id'])) {
                        $value['parent_id'] = $id;
                        parent::insert($value);
                    }
                }
            }
            self::forgetCache();
            return true;
        }
        return false;
    }

    /**
     * Delete
     * @param int $id
     * @return array
     */
    public function remove($id = null)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Email Template')])];
        $record = parent::find($id);
        if (!empty($record)) {
            DB::beginTransaction();
            try {
                parent::where('parent_id', $id)->delete();
                $record->delete();

                $data['status'] = 'success';
                $data['message'] = __('The :x has been successfully deleted.', ['x' => __('Email Template')]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                $data['message'] = $e->getMessage();
            }
        }

        return $data;
    }

    /**
     * Update
     * @param array $request
     * @param int $id
     * @param  object $exist
     * @return array
     */
    public function updateTemplate($request = [], $id = null, $exist = null)
    {
        DB::beginTransaction();
        try {
            $array = ['name', 'subject', 'body', 'status', 'language_id', 'variables'];
            if ($request['slug'] != $exist->slug) {
                $array[] = 'slug';
            }
            parent::where('id', $id)->update(array_intersect_key($request, array_flip((array) $array)));

            if (isset($request['data']) && !empty($request['data'])) {
                foreach ($request['data'] as $key => $value) {
                    if (!empty($value['subject']) && !empty($value['body']) && !empty($value['language_id'])) {
                        $value['parent_id'] = $id;
                        parent::updateOrInsert(['parent_id' => $id, 'language_id' => $value['language_id']], $value);
                    }
                }
            }
            self::forgetCache();
            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Email template')]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            $data = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return $data;
    }
}
