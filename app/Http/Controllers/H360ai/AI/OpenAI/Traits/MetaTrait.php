<?php

/**
 * @package OrderTrait
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Al Mamun <[almamun.techvill@gmail.com]>
 * @created 05-09-2022
 */

 namespace Modules\OpenAI\Traits;

use Illuminate\Database\Eloquent\Collection;

trait MetaTrait
{
    /**
     * Access meta data directly from the model object
     *
     * OVERRIDING 'Model' default '__get()' method
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->attributes['id'])) {
            return parent::__get($name);
        }
        $val = parent::__get($name);

        if ($val <> null) {
            return $val;
        }
        if (!$this->metaFetched) {
            $this->getMeta();
        }

        if (isset($this->metaArray[$name])) {
            return $this->metaArray[$name];
        }

        return null;
    }

    /**
     * Get Meta
     *
     * @return array
     */
    public function getMeta()
    {
        if (!isset($this->relations['metadata'])) {
            $this->relations['metadata'] = $this->getMetaCollection();
        }
        $this->metaArray = $this->relations['metadata']->pluck('value', 'key')->toArray();
        $this->metaFetched = true;
        return $this->metaArray;
    }

    /**
     * Return metadata collection of the product
     * @return Collection
     */
    public function getMetaCollection()
    {
        if (!isset($this->relations['metadata'])) {
            $this->relations['metadata'] = $this->metadata()->get();
        }
        return $this->relations['metadata'];
    }
}
