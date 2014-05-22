<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\generators;

use crisu83\yii_caviar\components\File;
use crisu83\yii_caviar\providers\Provider;

class ControllerGenerator extends ComponentGenerator
{
    /**
     * @var string
     */
    public $baseClass;

    /**
     * @var string
     */
    public $namespace = 'controllers';

    /**
     * @var string
     */
    public $filePath = 'controllers';

    /**
     * @var string
     */
    public $providers = array(
        Provider::CONTROLLER,
    );

    /**
     * @var string|array
     */
    public $actions = 'index';

    /**
     * @var string
     */
    protected $name = 'controller';

    /**
     * @var string
     */
    protected $description = 'Generates controller classes.';

    /**
     * @var string
     */
    protected $defaultTemplate = 'controller.txt';

    /**
     * @var string
     */
    protected $coreClass = '\CController';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->className = ucfirst($this->subject) . 'Controller';

        $this->initComponent();
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('actions', 'filter', 'filter' => 'trim'),
                array(
                    'actions',
                    'match',
                    'pattern' => '/^\w+[\w\s,]*$/',
                    'message' => '{attribute} should only contain word characters, spaces and commas.'
                ),
                array('baseClass', 'validateClass', 'extends' => '\CController', 'skipOnError' => true),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function attributeHelp()
    {
        return array_merge(
            parent::attributeHelp(),
            array(
                'actions' => "Space separated actions to generate (defaults to '{$this->actions}').",
                'subject' => "Name of the controller that will be generated.",
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $this->actions = $this->normalizeActions($this->actions);

        $files[] = new File(
            $this->resolveFilePath(),
            $this->compile(
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                    'actions' => $this->renderActions(),
                )
            )
        );

        foreach ($this->actions as $actionId) {
            if ($this->resolveTemplateFile(array("views/$actionId.txt", "views/view.txt")) === null) {
                continue;
            }

            $files = array_merge(
                $files,
                Generator::run(
                    Generator::VIEW,
                    array(
                        'subject' => $actionId,
                        'context' => $this->context,
                        'template' => $this->template,
                        'filePath' => "views/{$this->subject}",
                        'templatePath' => "{$this->getTemplatePath()}/views",
                        'providers' => array(
                            array(
                                Provider::VIEW,
                                'cssClass' => "{$this->subject}-controller $actionId-action",
                                'vars' => array(
                                    'this' => $this->resolveControllerClass(),
                                ),
                            ),
                        ),
                    )
                )
            );
        }

        return $files;
    }

    /**
     * @return string
     */
    protected function renderActions()
    {
        $actions = array();

        // TODO change the structure of actions to support providers

        foreach ($this->actions as $actionId) {
            $actions[] = $this->compileInternal(
                $this->resolveTemplateFile(array("actions/$actionId.txt", "actions/action.txt")),
                array(
                    array(
                        Provider::ACTION,
                        'id' => $actionId,
                        'view' => $actionId,
                    ),
                )
            );
        }

        $indent = '    ';
        return implode("\n\n$indent", str_replace("\n", "\n$indent", $actions));
    }

    /**
     * @return string
     */
    protected function resolveControllerClass()
    {
        return !empty($this->namespace) ? "{$this->namespace}\\{$this->className}" : $this->className;
    }

    /**
     * Normalizes the given actions to an array.
     *
     * @param string|array $actions actions.
     * @return array normalized actions.
     */
    protected function normalizeActions($actions)
    {
        return is_string($actions) && !empty($actions) ? explode(' ', $actions) : array();
    }
}