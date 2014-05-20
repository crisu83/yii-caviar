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

use crisu83\yii_caviar\helpers\ModelHelper;
use crisu83\yii_caviar\providers\Provider;

class CrudGenerator extends ModelGenerator
{
    /**
     * @var string
     */
    public $modelBaseClass;

    /**
     * @var string
     */
    public $modelNamespace = 'models';

    /**
     * @var string
     */
    public $modelTemplate = 'model.txt';

    /**
     * @var string
     */
    public $controllerBaseClass;

    /**
     * @var string
     */
    public $controllerNamespace = 'controllers';

    /**
     * @var string
     */
    public $controllerTemplate = 'controller.txt';

    /**
     * @var string
     */
    public $name = 'crud';

    /**
     * @var string
     */
    public $description = 'Generates model classes and associated controllers and views.';

    /**
     * @var string
     */
    public $actions = 'admin create delete index update view';

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $db = $this->getDbConnection();
        $modelClass = ModelHelper::generateClassName($db, $db->tablePrefix, $this->subject);

        $controllerUse = !empty($this->modelNamespace)
            ? array("{$this->context}\\{$this->modelNamespace}\\{$modelClass}")
            : array();

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
                    'baseClass' => $this->controllerBaseClass,
                    'namespace' => $this->controllerNamespace,
                    'actions' => $this->actions,
                    'providers' => array(
                        array(
                            Provider::CRUD,
                            'modelClass' => $modelClass,
                            'use' => $controllerUse,
                        ),
                    ),
                )
            )
        );

        return $files;
    }
}
