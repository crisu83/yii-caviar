<?php

namespace crisu83\yii_caviar\providers;

class ControllerProvider extends Provider
{
    /**
     * @var string
     */
    public $name = 'controller';

    /**
     * @var array
     */
    public $actions;

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'actions' => $this->actions,
        );
    }
}