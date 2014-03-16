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

class ComponentGenerator extends FileGenerator
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
    public $defaultView = 'component.php';

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
        if (!isset($this->className)) {
            $this->className = ucfirst($this->subject);
        }

        $this->namespace = "{$this->context}\\{$this->namespace}";
        $this->fileName = "{$this->className}.php";
        $this->filePath = $this->namespaceToPath();
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
            $this->render(
                $this->resolveViewFile(),
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                )
            )
        );

        return $files;
    }

    /**
     * @return string
     */
    protected function namespaceToPath()
    {
        return str_replace('\\', '/', $this->namespace);
    }
}