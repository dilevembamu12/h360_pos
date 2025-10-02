<?php

use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Services\v2\TeamMemberService;
use Modules\OpenAI\Services\ChatService;

if (!function_exists('activeMenu')) {
    /**
     * menu activated
     *
     * @param string $routedName
     * @return mixed
     */
    function activeMenu(...$routeName )
    {
        if (in_array(url()->current(), $routeName)) {
            return ['color1' => '#E60C84', 'color2' => '#FFCF4B', 'class' => 'bg-color-F6 dark:bg-color-47 border-design-1', 'collapse' => 'show', 'parent-border' => 'border-design-1'];
        }

       return ['color1' => '#141414', 'color2' => '#141414', 'class' => '', 'collapse' => '', 'parent-border' => ''];
    }
}

if (!function_exists('accountSidebarActiveMenu')) {
    /**
     * menu activated
     *
     * @param string $routedName
     * @return mixed
     */
    function accountSidebarActiveMenu(...$routeName )
    {
        if (in_array(url()->current(), $routeName)) {
            return ['class' => 'border-design-3-active', 'color1' => '#E60C84', 'color2' => '#FFCF4B' ];
        }

       return ['class' => 'border-color-DF dark:border-[#474746]', 'color1' => '#898989', 'color2' => '#898989'];
    }
}
 
if (!function_exists('temperature')) {
    /**
     * content level
     *
     * @param string $temperature
     * @return mixed
     */
    function temperature($temperature)
    {
        $value = 0;

        switch($temperature) {

            case "Optimal" :
                $value = 0.5;
                break;
            case "Low" :
                $value = 0.8;
                break;
            case "Medium" :
                $value = 0.9;
                break;
            case "High" :
                $value = 1;
                break;
        }

        return $value;
    }
}

if (!function_exists('codeLabel')) {
    /**
     * Code label
     *
     * @param string $label
     * @return mixed
     */
    function codeLabel($label, $swap=false)
    {
        
        if ($swap) {
            $codeLabel = ['Easy' => 'Noob', 'Medium' => 'Moderate', 'High' => 'High'];
        } else {
            $codeLabel = ['Noob' => 'Easy', 'Moderate' => 'Medium', 'High' => 'High'];
        }

        return array_key_exists($label, $codeLabel) ? $codeLabel[$label] : '';  
    }

}

if (!function_exists('variant')) {
    /**
     * Variant
     *
     * @param string $variant
     * @return mixed
     */
    function variant($variant)
    {
        
        $variantLabel = [
            '1' => 'one', 
            '2' => 'two', 
            '3' => 'three'
        ];

        return array_key_exists($variant, $variantLabel) ? $variantLabel[$variant] : '';  
    }

}

if (!function_exists('providerClassName')) {
    /**
     * Provider's Class Name
     *
     * @param string $data
     * @return mixed
     */
    function providerClassName($data)
    {
        return ucFirst(str_replace('_', '', $data));
    }

}

if (!function_exists('processApiPreferenceData')) {
    /**
     * Process API Preference Data
     *
     * @param string $key
     * @param array $array
     * @return array
     */
    function processApiPreferenceData($key, $array)
    {
        $data = [];
        foreach ($array as $value) {
            switch($key) {
                case "codeLabel":
                    $data[] = [codeLabel($value) => $value];
                    break;
                case "temperature":
                    $data[] = [$value => temperature($value)];
                    break;
                case "openai_variant":
                    $data[] = [variant($value) => $value];
                    break;
                case "stable_diffusion_variant":
                    $data[] = [variant($value) => $value];
                    break;
                default:
                    $data[] = [$value => $value];
                    break;
            }
        }

        return $data;
    }
}

if (!function_exists('creativityLabel')) {
    /**
     * Converts integer to text.
     *
     * Example output: 'High', 'Low', 'Medium'.
     *
     * @param string $bytes
     * @param string $unit
     * @return string
     */
    function creativityLabel($label)
    {
        $creativitylabel = [
            0.5 => "Optimal",
            0.8 => "Low",
            0.9 => "Medium",
            1 => "High"
        ];  
        return array_key_exists($label,$creativitylabel) ? $creativitylabel[$label] : null;  
    }
}

if (!function_exists('processPreferenceData')) {
    /**
     * Process Preference Data
     *
     * @param string $value
     * @return array
     */
    function processPreferenceData($value)
    {
        return $value != NULL ? json_decode($value, true) : []; 
    }
}

if (!function_exists('apiKey')) {
    /**
     * Get API Key
     *
     * @param string $value
     * @return string
     */
    function apiKey($value): string
    {
        $key = new ChatService();
        return $key->aiKey($value);
    }
}

if (!function_exists('volume')) {
    /**
     * Code label
     *
     * @param string $data
     * @return mixed
     */
    function volume($data, $swap=false)
    {

        if ($swap) {
            $volume = ['-6.00' => 'Low', '0.00' => 'Default', '6.00' => 'High'];
        } else {
            $volume = ['Low' => '-6.00', 'Default' => '0.00', 'High' => '6.00'];
        }

        return array_key_exists($data, $volume) ? $volume[$data] : '';
    }

}

if (!function_exists('pitch')) {
    /**
     * Code label
     *
     * @param string $data
     * @return mixed
     */
    function pitch($data, $swap=false)
    {
        
        if ($swap) {
            $pitch = ['-20.00' => 'Low', '0.00' => 'Default', '20.00' => 'High'];
        } else {
            $pitch = ['Low' => '-20.00', 'Default' => '0.00', 'High' => '20.00'];
        }

        return array_key_exists($data, $pitch) ? $pitch[$data] : '';
    }

}

if (!function_exists('speed')) {
    /**
     * Code label
     *
     * @param string $data
     * @return mixed
     */
    function speed($data, $swap=false)
    {
        
        if ($swap) {
            $speed = ['0.25' => 'Super Slow', '0.50' => 'Slow', '1.00' => 'Default', '2.00' => 'Fast', '4.00' => 'Super Fast',];
        } else {
            $speed = ['Super Slow' => '0.25', 'Slow' => '0.50', 'Default' => '1.00', 'Fast' => '2.00', 'Super Fast' => '4.00'];
        }

        return array_key_exists($data, $speed) ? $speed[$data] : '';
    }

}

if (!function_exists('audioEffect')) {
    /**
     * Code label
     *
     * @param string $data
     * @return mixed
     */
    function audioEffect($data, $swap=false)
    {
        
        if ($swap) {
            $audioEffect = ['wearable-class-device' => 'Smart Watch', 'handset-class-device' => 'Smartphone', 'headphone-class-device' => 'Headphone', 'small-bluetooth-speaker-class-device' => 'Bluetooth', 'medium-bluetooth-speaker-class-device' => 'Smart Bluetooth', 'large-home-entertainment-class-device' => 'Smart TV', 'large-automotive-class-device' => 'Car Speaker', 'telephony-class-application' => 'Telephone'];
        } else {
            $audioEffect = ['Smart Watch' => 'wearable-class-device', 'Smartphone' => 'handset-class-device', 'Headphone' => 'headphone-class-device', 'Bluetooth' => 'small-bluetooth-speaker-class-device', 'Smart Bluetooth' => 'medium-bluetooth-speaker-class-device', 'Smart TV' => 'large-home-entertainment-class-device', 'Car Speaker' => 'large-automotive-class-device', 'Telephone' => 'telephony-class-application'];
        }

        return array_key_exists($data, $audioEffect) ? $audioEffect[$data] : '';
    }

}
if (!function_exists('imageResulation')) {
    /**
     * Get Image Resulation
     *
     * @param string $value
     * @return [type]
     */
    function imageResulation($value)
    {
       return config('openAI.size')[$value];
    }
}

if (!function_exists('filteringBadWords')) {
     /**
     * Filtering Bad words.
     *
     * @return string
     */
    function filteringBadWords($value)
    {
        $badWords = preference('bad_words');
        $words = explode(',', $badWords);
       
        foreach ($words as $word) {
            $value = str_ireplace(trim($word),'', $value);
        }

        return $value;
    }
}

if (!function_exists('sortResolution')) {
    /**
     * Sort Image Resolution
     *
     * @param array $data
     * @return array
     */
    function sortResolution($data, $sort = 'asc')
    {
        $arr = [];
        
        foreach ($data as $value) {
            $exp = explode('x', $value);
            
            if (isset($exp[0]) && isset($exp[1])) {
                $arr[$value] = $exp[0] * $exp[1];
            }
        }
        
        if ($sort == 'asc') {
            asort($arr);
        } else {
            arsort($arr);
        }
        
        return array_keys($arr);
    }
}

if (!function_exists('countWords')) {
    /**
     * @param mixed $text
     * 
     * @return integer
     */
    function countWords($text){

        $words = preg_match_all('/\b\w+\b/u', $text);
        return (int) $words;
    }
}

if (!function_exists('checkUserSubscription')) {
    /**
     * @param mixed $type
     * @param array $data
     * 
     * @return array
     */
    function checkUserSubscription($type, $data = []) : array
    {
        $contentService = new ContentService();
        $userId = $contentService->getCurrentMemberUserId('meta', null);
        if (!subscription('isAdminSubscribed', $userId)) {
            $userStatus = $contentService->checkUserStatus($userId, 'meta');
            
            if ($userStatus['status'] == 'fail') {
                return [
                    'response' => $userStatus['message'],
                    'status' => 'failed',
                ];
            }
        
            $validation = subscription('isValidSubscription', $userId, $type);

            if ($validation['status'] == 'fail' && !auth()->user()?->hasCredit($type)) {
                return [
                    'response' => $validation['message'],
                    'status' => 'failed',
                ];
            }
        }

        return [
            'status' => 'success',
        ];
    }

    if (!function_exists('handleSubscriptionAndCredit')) {
        /**
         * @param mixed $subscription
         * @param mixed $type
         * @param mixed $usages
         * @param mixed $userId
         * @param array $data Optional
         * 
         * @return bool
         */
        function handleSubscriptionAndCredit($subscription, $type, $usages, $userId, $data = []) : bool
        {
            if (!subscription('isAdminSubscribed', $userId) || auth()?->user()?->hasCredit($type)) {
                $increment = subscription('usageIncrement', optional($subscription)->id, $type, $usages, $userId);
                if ($increment && $userId != auth()->user()?->id) {
                    (new TeamMemberService())->updateTeamMeta($type, $usages, $data);
                }
            }
            
            return true;
        }
    }
}

if (!function_exists('filterDownloadName')) {
    /**
     * Filter Download Name
     *
     * @param string $content
     * @param string $name
     *
     * @return string
     */
    function filterDownloadName($content, $fileName) {
        $name = cleanedUrl(trimWords($content, 45, ''));
        [$defaultName, $extension] = explode('.', $fileName, 2);

        $filteredName = $name . '.' . $extension;

        return $filteredName;
    }
}

if (!function_exists('getMaxTokenKey')) {
        /**
         * Returns the token key for a given model
         * 
         * This function is used to map a given model to its respective token key.
         * The token key is used to limit the number of tokens that can be generated
         * for a given request.
         * 
         * If no match is found, the function defaults to 'max_tokens'.
         * 
         * @param string $model The model to map to a token key
         * 
         * @return string The token key for the given model
         */
function getMaxTokenKey($model)
    {
        $modelToTokenKeyMap = [
            'gpt-4' => 'max_tokens',  // For models like gpt-4, use max_tokens
            'gpt-3.5-turbo' => 'max_tokens', // For other models, use max_tokens
            'gpt-3.5' => 'max_tokens', // For other models, use max_tokens
            'gpt-4o' => 'max_tokens', // For other models, use max_tokens
            'gpt-4o-mini' => 'max_tokens', // For other models, use max_tokens
            'o1-preview' => 'max_completion_tokens',  // For '01' model, use max_completion_tokens
            'o1-mini' => 'max_completion_tokens',  // For '01' model, use max_completion_tokens
            'o1' => 'max_completion_tokens',  // For '01' model, use max_completion_tokens
            'o3-mini' => 'max_completion_tokens',  // For '01' model, use max_completion_tokens
            // Add more models and their respective token key mappings if needed
        ];
  
        return $modelToTokenKeyMap[$model] ?? 'max_tokens';  // Default to max_tokens if no match
    }
}
