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

class ConfigGenerator extends ViewGenerator
{
    /**
     * @var string
     */
    public $fileName = 'config.php';

    /**
     * @var string
     */
    public $filePath = 'config';

    /**
     * @var string
     */
    protected $name = 'config';

    /**
     * @var string
     */
    protected $description = 'Generates configuration files.';

    /**
     * @var string
     */
    protected $defaultTemplate = 'config.txt';
}