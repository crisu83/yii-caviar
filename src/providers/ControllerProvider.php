<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\providers;

class ControllerProvider extends ComponentProvider
{
    /**
     * @var string
     */
    public $name = 'controller';

    /**
     * @var string
     */
    public $baseClass = '\CController';

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
                'actions' => $this->actions,
            )
        );
    }
}