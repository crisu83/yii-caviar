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

class ComponentGenerator extends Generator
{
    /**
     * @var string
     */
    public $name = 'component';

    /**
     * @var string
     */
    public $description = 'Component class generator.';

    /**
     * @var string
     */
    public $defaultFile = 'component.php';

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $baseClass = '\CComponent';

    /**
     * @var string
     */
    public $namespace = 'components';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->initComponent();
    }

    /**
     *
     */
    public function initComponent()
    {
        $this->namespace = "{$this->app}\\{$this->namespace}";
        $this->filePath = $this->namespaceToPath();
        $this->fileName = "{$this->className}.php";
    }

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
                $this->findTemplateFile("{$this->subject}.php"),
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                )
            )
        );

        $this->save($files);
    }

    /**
     * @return string
     */
    protected function namespaceToPath()
    {
        return str_replace('\\', '/', $this->namespace);
    }
}