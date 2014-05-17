<?php

namespace crisu83\yii_caviar\providers;

class ComponentProvider extends Provider
{
    public $name = 'component';

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $baseClass;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'className' => $this->className,
            'baseClass' => $this->baseClass,
            'namespace' => !empty($this->namespace) ? "namespace {$this->namespace};" : '',
        );
    }
}
