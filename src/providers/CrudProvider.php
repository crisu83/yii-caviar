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

class CrudProvider extends ControllerProvider
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $modelNamespace;

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array_merge(
            parent::provide(),
            array(
                'modelClass' => $this->modelClass,
                'modelNamespace' => $this->modelNamespace,
            )
        );
    }
}
