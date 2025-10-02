<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\OpenAI\Entities\FeaturePreference;

class CheckPreferenceFile implements Rule
{
    /**
     * Stores the error message to be used for displaying errors
     */
    protected $errorMessage;

    /**
     * Default maximum file size set to 2 MB
     */
    private $size = 2;

    public function __construct($feature = null)
    {
        $feature = FeaturePreference::whereSlug($feature)->first();

        if ($feature && $feature->settings) {
            $restrictions = json_decode($feature->settings, true);
            $this->size = $restrictions['file_size'];
        }
    }

    public function passes($attribute, $value)
    {
        $fileSize = $value->getSize() / (1024 * 1024);
        // Check each file size
        if ($fileSize > $this->size) {
            $this->errorMessage = __("File must not exceed :x MB.", ['x' => $this->size]);
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage ?: __('The uploaded file does not meet the required standards. Please check the file format and size, and try again.');
    }
}
