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

class ViewGenerator extends Generator
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
    public $defaultFile = 'view.php';

    /**
     * @inheritDoc
     */
    public function rules()
    {
        // todo: add validation rules.
        return array_merge(
            parent::rules(),
            array()
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
            $this->renderFile(
                $this->findTemplateFile("{$this->subject}.php")
            )
        );

        $this->save($files);
    }
}