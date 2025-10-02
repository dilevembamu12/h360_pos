<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\OpenAI\Entities\FeaturePreference;

class CheckChatbotFile implements Rule
{
    protected $errorMessage;
    private $size;
    private $limit;

    public function __construct()
    {
        $chatBot = FeaturePreference::whereSlug('chatbot')->first();

        if ($chatBot && $chatBot->settings) {
            $restrictions = json_decode($chatBot->settings, true);
            
            $this->size = $restrictions['file_size'];
            $this->limit = $restrictions['file_limit'];
        } else {
            // Set defaults or throw an exception if necessary
            $this->size = 0;
            $this->limit = 0;
        }
    }

    public function passes($attribute, $value)
    {
        // Check if value is an array of files and respect the limit
        if (is_array($value) && count($value) > $this->limit) {
            $this->errorMessage = __("The number of files must not exceed :x.", ['x' => $this->limit]);
            return false;
        }

        foreach ($value as $file) {
            $fileSize = $file->getSize() / (1024 * 1024);
            // Check each file size
            if ($fileSize > $this->size) {
                $this->errorMessage = __("Each file must not exceed :x MB.", ['x' => $this->size]);
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage ?: __('The uploaded file does not meet the required standards. Please check the file format and size, and try again.');
    }
}
