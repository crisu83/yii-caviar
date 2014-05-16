<?php

namespace crisu83\yii_caviar\providers;

class ViewProvider extends Provider
{
    /**
     * @var string
     */
    public $name = 'view';

    /**
     * @var string
     */
    public $cssClass;

    /**
     * @var array
     */
    public $vars = array();

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'cssClass' => $this->cssClass,
            'docVars' => $this->generateDocVars(),
        );
    }

    /**
     * @return string
     */
    protected function generateDocVars()
    {
        $vars = array();

        foreach ($this->vars as $key => $value) {
            $vars[] = "@var \$$key $value";
        }

        return !empty($vars) ? implode("\n * ", $vars) : '';
    }
}