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

abstract class FileProvider extends Provider
{
    /**
     * @var string tab character.
     */
    public $tab = '    ';

    /**
     * Renders a "tab" character.
     *
     * @param int $amount number of indents.
     * @return string the rendered indent.
     */
    protected function indent($amount = 1)
    {
        return str_repeat($this->tab, $amount);
    }
}