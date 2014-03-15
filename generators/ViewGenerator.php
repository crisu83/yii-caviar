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
    public $fileName;

    /**
     * @var string
     */
    public $filePath;

    /**
     * @inheritDoc
     */
    public function generate($name)
    {
        $files = array();

        list ($appName, $viewName) = $this->parseAppAndName($name);

        $files[] = new File(
            "{$this->command->basePath}/$appName/{$this->filePath}/$viewName.php",
            $this->renderFile(
                $this->resolveTemplateFile(
                    $this->template,
                    "$viewName.php",
                    "{$this->name}.php"
                )
            )
        );

        $this->save($files);
    }
}