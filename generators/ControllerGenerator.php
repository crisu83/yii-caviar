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
    public $defaultFile = 'controllers/controller.php';

    /**
     * @var string
     */
    public $baseClass = '\CController';

    /**
     * @var string
     */
    public $namespace = 'controllers';

    /**
     * @var array
     */
    public $actions = 'index';

    /**
     * @inheritDoc
     */
    public function generate($name)
    {
        $files = array();

        list ($appName, $controllerId) = $this->parseAppAndName($name);

        $this->className = ucfirst($controllerId) . 'Controller';
        $this->namespace = "{$appName}\\{$this->namespace}";
        $this->actions = explode(' ', $this->actions);

        $files[] = new File(
            $this->resolveFilePath(),
            $this->renderFile(
                $this->findTemplateFile("controllers/$controllerId.php"),
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                    'actions' => $this->renderActions(),
                )
            )
        );

        foreach ($this->actions as $actionId) {
            // todo: change to generate these as a sub command.
            $files[] = new File(
                "{$this->getBasePath()}/$appName/views/$controllerId/$actionId.php",
                $this->renderFile(
                    $this->resolveTemplateFile(
                        $this->template,
                        "views/$actionId.php",
                        "views/view.php"
                    ),
                    array(
                        'controllerClass' => "{$this->namespace}\\{$this->className}",
                        'cssClass' => "$controllerId-controller $actionId-action",
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
            // todo: change to generate these as a sub command.
            $actions[] = $this->renderFile(
                $this->resolveTemplateFile(
                    $this->template,
                    "/actions/$actionId.php",
                    "/actions/action.php"
                ),
                array(
                    'methodName' => 'action' . ucfirst($actionId),
                    'viewName' => $actionId,
                )
            );
        }

        return implode("\n\n", $actions);
    }
}