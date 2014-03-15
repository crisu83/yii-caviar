<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace console\commands\generate\generators;

use console\commands\generate\File;

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
    public $baseClass = '\CController';

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

        $this->namespace = $appName . '\controllers';
        $this->className = ucfirst($controllerId) . 'Controller';
        $this->actions = explode(' ', $this->actions);

        $files[] = new File(
            "{$this->getBasePath()}/{$this->namespaceToPath()}/{$this->className}.php",
            $this->renderFile(
                $this->resolveTemplateFile(
                    $this->template,
                    "controllers/$controllerId.php",
                    "controllers/controller.php"
                ),
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
                "{$this->getBasePath()}/$appName/views/$controllerId/$actionId.php",
                $this->renderFile(
                    $this->resolveTemplateFile(
                        $this->template,
                        "views/$actionId.php",
                        "views/view.php"
                    ),
                    array(
                        'controllerClassName' => $this->namespace . '\\' . $this->className,
                        'cssClassName' => "$controllerId-controller $actionId-action",
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