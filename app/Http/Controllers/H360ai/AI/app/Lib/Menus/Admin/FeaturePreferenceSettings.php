<?php

/**
 * @package Admin sidebar feature preference setting menu
 * @author TechVillage <mailto:support@techvill.org>
 * @contributor Md. Khayeruzzaman <[mailto:shakib.techvill@gmail.com]>
 * @created 37-07-2024
 */

namespace App\Lib\Menus\Admin;

class FeaturePreferenceSettings
{

    /**
     * Get menu items
     *
     * @return array
     */
    public static function get(): array
    {
        $items = [
            [
                'label' => __('Ai Doc Chat'),
                'name' => 'ai_doc_chat',
                'position' => '10',
                'visibility' => true,
            ],
            [
                'label' => __('Chatbot'),
                'name' => 'chatbot',
                'position' => '20',
                'visibility' => true,
            ],
            [
                'label' => __('Ai Detector'),
                'name' => 'ai_detector',
                'position' => '20',
                'visibility' => true,
            ],
        ];

        // Sort items based on position, placing items without a position at the beginning
        usort($items, function ($a, $b) {
            $positionA = isset($a['position']) ? $a['position'] : -1;
            $positionB = isset($b['position']) ? $b['position'] : -1;

            return $positionA <=> $positionB;
        });

        return $items;
    }
}
