<?php

namespace crisu83\yii_caviar\providers;

use crisu83\yii_caviar\generators\Generator;

abstract class Provider extends \CComponent
{
    const COMPONENT = 'component';
    const CONTROLLER = 'controller';
    const VIEW = 'view';

    /**
     * @var string
     */
    public $name = 'base';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @return array map of the data provided.
     */
    abstract public function provide();

    /**
     * @param Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }
}