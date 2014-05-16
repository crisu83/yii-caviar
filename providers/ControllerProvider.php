<?php

namespace crisu83\yii_caviar\providers;

class ControllerProvider extends ComponentProvider
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
        return array_merge(
            parent::provide(),
            array(
                'actions' => '',
            )
        );
    }
}