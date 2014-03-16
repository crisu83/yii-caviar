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

use crisu83\yii_caviar\File;

class ViewGenerator extends FileGenerator
{
    /**
     * @var string
     */
    public $name = 'view';

    /**
     * @var string
     */
    public $description = 'View file generator.';

    /**
     * @var string
     */
    public $defaultView = 'view.php';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fileName = "{$this->subject}.php";
        $this->filePath = "{$this->context}/$this->filePath";
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        // todo: add validation rules.
        return array_merge(
            parent::rules(),
            array(
                array('defaultView', 'required'),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $files[] = new File(
            $this->resolveFilePath(),
            $this->render(
                $this->resolveViewFile(),
                array(
                    "{$this->subject}.php",
                    $this->defaultView,
                )
            )
        );

        return $files;
    }
}