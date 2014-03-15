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
    public $className;

    /**
     * @var string
     */
    public $baseClass = '\CComponent';

    /**
     * @var string
     */
    public $namespace;

    /**
     * @inheritDoc
     */
    public function generate($name)
    {
        $files = array();

        list ($appName, $component) = $this->parseAppAndName($name);

        $files[] = new File(
            "{$this->getBasePath()}/{$this->namespaceToPath()}/{$this->className}.php",
            $this->renderFile(
                $this->resolveTemplateFile(
                    $this->template,
                    "$component.php",
                    "component.php"
                ),
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