<?php

/**
 * @package UseCaseTemplateService
 * @author TechVillage <support@techvill.org>
 * @contributor Soumik Datta <soumik.techvill@gmail.com>
 * @created 26-02-2023
 */

namespace Modules\OpenAI\Services;

class UseCaseTemplateService
{
    /**
     * Source
     *
     * @var string
     */
    protected $source;

    /**
     * Variables
     *
     * @var array
     */
    protected $variables;

    /**
     * Matches
     *
     * @var array
     */
    protected $matches = [];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(string $source)
    {
        $this->source = $source;
        $this->extractVariables();
    }

    /**
     * Set
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->variables['[['.strtoupper($key).']]'] = $value;
    }

    /**
     * To String
     *
     * @return string
     */
    public function __toString()
    {
        return str_replace(array_keys($this->variables), $this->variables, $this->source);
    }

    /**
     * Finds variables from the source.
     *
     * @return void
     */
    public function extractVariables()
    {
        preg_match_all('/\[{2}([a-zA-Z0-9_-]+)\]{2}/', $this->source, $this->matches, PREG_SET_ORDER);

        foreach ($this->matches as $match) {
            $this->variables['[['.strtoupper($match[1]).']]'] = '';
            $this->source = str_replace('[['.$match[1].']]', '[['.strtoupper($match[1]).']]', $this->source);
        }
    }

    /**
     * Sets the variables value. Provide key value pairs where key is variable name.
     *
     * @param array $variables
     */
    public function setVariables($variables)
    {
        foreach ($variables as $key => $value) {
            if (isset($this->variables['[['.strtoupper($key).']]'])) {
                $this->variables['[['.strtoupper($key).']]'] = filteringBadWords($value);
            }
        }
    }

    /**
     * Renders and returns the template input.
     *
     * @return string
     */
    public function render(): string
    {
        if (!is_null($this->variables)) {
            return str_replace(array_keys($this->variables), $this->variables, $this->source);
        }
        
        return $this->source;
        
    }
}
