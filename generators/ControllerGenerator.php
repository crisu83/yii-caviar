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

class ControllerGenerator extends ComponentGenerator
{
    /**
     * @var string
     */
    public $name = 'controller';

    /**
     * @var string
     */
    public $description = 'Controller class generator.';

    /**
     * @var string
     */
    public $defaultFile = 'controller.php';

    /**
     * @var string
     */
    public $baseClass = '\CController';

    /**
     * @var string
     */
    public $namespace = 'controllers';

    /**
     * @var string|array
     */
    public $actions = 'index';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->className = ucfirst(strtolower($this->subject)) . 'Controller';

        if (is_string($this->actions)) {
            $this->actions = explode(' ', $this->actions);
        }

        $this->initComponent();
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
                    'actions' => $this->renderActions(),
                )
            )
        );

        foreach ($this->actions as $actionId) {
            $files[] = new File(
                "{$this->resolveViewPath()}/$actionId.php",
                $this->renderFile(
                    $this->findTemplateFile("views/$actionId.php", "views/view.php"),
                    array(
                        'controllerClass' => "{$this->namespace}\\{$this->className}",
                        'cssClass' => "{$this->subject}-controller $actionId-action",
                    )
                )
            );
        }

        $this->save($files);
    }

    /**
     * @return string
     */
    protected function renderActions()
    {
        $actions = array();

        foreach ($this->actions as $actionId) {
            $actions[] = $this->renderFile(
                $this->findTemplateFile("/actions/$actionId.php", "/actions/action.php"),
                array(
                    'methodName' => 'action' . ucfirst($actionId),
                    'viewName' => $actionId,
                )
            );
        }

        return implode("\n\n", str_replace("\n", "\n\t", $actions));
    }

    /**
     * @return string
     */
    protected function resolveViewPath()
    {
        return "{$this->getBasePath()}/{$this->app}/views/{$this->subject}";
    }
}