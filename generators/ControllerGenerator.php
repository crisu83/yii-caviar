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

use crisu83\yii_caviar\File;

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
        $this->className = ucfirst(strtolower($this->subject)) . 'Controller';

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

        if (is_string($this->actions)) {
            $this->actions = explode(' ', $this->actions);
        }

        $files[] = new File(
            $this->resolveFilePath(),
            $this->compile(
                $this->resolveTemplateFile(),
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                    'actions' => $this->renderActions(),
                )
            )
        );

        foreach ($this->actions as $actionId) {
            $files = array_merge(
                $files,
                Generator::run(
                    Generator::VIEW,
                    array(
                        'subject' => $actionId,
                        'context' => $this->context,
                        'template' => $this->template,
                        'templatePath' => "{$this->getTemplatePath()}/views",
                        'templateData' => array(
                            'controllerNamespace' => $this->className,
                            'controllerClass' => $this->className,
                            'cssClass' => "{$this->subject}-controller $actionId-action",
                        ),
                        'filePath' => "views/{$this->subject}",
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

        foreach ($this->actions as $actionId) {
            $actions[] = $this->compile(
                $this->resolveTemplateFile(
                    array(
                        "/actions/$actionId.txt",
                        "/actions/action.txt",
                    )
                ),
                array(
                    'methodName' => 'action' . ucfirst($actionId),
                    'viewName' => $actionId,
                )
            );
        }

        return implode("\n\n{$this->indent()}", str_replace("\n", "\n{$this->indent()}", $actions));
    }
}