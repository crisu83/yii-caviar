<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\generators;

class LayoutGenerator extends ViewGenerator
{
    /**
     * @var string
     */
    public $name = 'layout';

    /**
     * @var string
     */
    public $description = 'Layout file generator.';

    /**
     * @var string
     */
    public $defaultTemplate = 'layout.txt';

    /**
     * @var string
     */
    public $fileName = 'layout.php';

    /**
     * @var string
     */
    public $filePath = 'views/layout';
}