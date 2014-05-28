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

class InlineActionProvider extends Provider
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $view = 'action';

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'methodName' => 'action' . ucfirst($this->id),
            'viewName' => $this->view,
        );
    }
}