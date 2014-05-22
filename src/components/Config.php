<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\components;

class Config extends \CComponent
{
    /**
     * @var string
     */
    public $basePath;

    /**
     * @var array
     */
    public $generators;

    /**
     * @var array
     */
    public $providers;

    /**
     * @var array
     */
    public $templates;

    /**
     * @var array
     */
    public $attributes;
}