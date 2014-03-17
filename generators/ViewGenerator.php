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
    public $defaultTemplate = 'view.txt';

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
            $this->compile(
                $this->resolveTemplateFile(),
                array(
                    "{$this->subject}.txt",
                    $this->defaultTemplate,
                )
            )
        );

        return $files;
    }
}