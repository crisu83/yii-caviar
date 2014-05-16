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

class CrudGenerator extends FileGenerator
{
    public $modelBaseClass;

    public $modelNamespace = 'models';

    public $modelTemplate = 'model.txt';

    public $controllerBaseClass;

    public $controllerNamespace = 'controllers';

    public $controllerTemplate = 'controller.txt';

    public $name = 'crud';

    public $description = 'Generates model classes and associated controllers and views.';

    public $actions = 'admin create delete index update view';

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $files = array_merge(
            $files,
            Generator::run(
                Generator::MODEL,
                array(
                    'subject' => $this->subject,
                    'context' => $this->context,
                    'template' => $this->modelTemplate,
                    'templatePath' => $this->getTemplatePath(),
                    'baseClass' => $this->modelBaseClass,
                    'namespace' => $this->modelNamespace,
                )
            ),
            Generator::run(
                Generator::CONTROLLER,
                array(
                    'subject' => $this->subject,
                    'context' => $this->context,
                    'template' => $this->controllerTemplate,
                    'templatePath' => $this->getTemplatePath(),
                    'templateData' => array(
                        'modelClass' => ucfirst($this->subject),
                        'modelNamespace' => !empty($this->modelNamespace) ? "{$this->context}\\{$this->modelNamespace}" : '',
                    ),
                    'baseClass' => $this->controllerBaseClass,
                    'namespace' => $this->controllerNamespace,
                    'actions' => $this->actions,
                )
            )
        );

        return $files;
    }
}
