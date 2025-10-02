<?php

/**
 * @package FolderTrait
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 24-10-2023
 */

namespace Modules\OpenAI\Traits;

trait FolderTrait
{
    /**
     * Get data
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $val = parent::__get($name);

        if ($val <> null) {
            return $val;
        }

        $data = $this->metaData()->where('key', $name)->first();

        if ($data) {
            return $data->value;
        }
    }
}
