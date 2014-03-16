<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\generators;

abstract class FileGenerator extends Generator
{
    /**
     * @var array
     */
    public $defaultView;

    /**
     * @var string
     */
    public $viewPath;

    /**
     * @var array
     */
    public $viewData = array();

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $filePath;

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('defaultView, fileName, filePath', 'required'),
            array('viewPath, viewData', 'safe'),
        );
    }
} 