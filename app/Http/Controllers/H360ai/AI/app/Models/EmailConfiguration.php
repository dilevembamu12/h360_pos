<?php
/**
 * @package EmailConfiguration
 * @author TechVillage <support@techvill.org>
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @created 20-05-2021
 */

namespace App\Models;

use App\Models\Model;
use App\Rules\{
  CheckValidEmail
};
use Validator;

class EmailConfiguration extends Model
{
    /**
     * timestamps
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Store
     * @param array $request
     * @return boolean
     */
    public function store($request = [])
    {
        if (parent::updateOrInsert(['id' => 1], $request)) {
            self::forgetCache();
            return true;
        }

       return false;
    }

}
