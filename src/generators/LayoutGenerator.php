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
    protected $name = 'layout';

    /**
     * @var string
     */
    protected $description = 'Generates layout files.';

    /**
     * @var string
     */
    protected $defaultTemplate = 'layout.txt';

    /**
     * @var string
     */
    protected $fileName = 'layout.php';

    /**
     * @var string
     */
    protected $filePath = 'views/layouts';
}