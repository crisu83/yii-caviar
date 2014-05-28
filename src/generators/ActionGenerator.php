<?php

namespace crisu83\yii_caviar\generators;

use crisu83\yii_caviar\components\File;
use crisu83\yii_caviar\providers\Provider;

class ActionGenerator extends ComponentGenerator
{
    /**
     * @var string
     */
    public $namespace = 'actions';

    /**
     * @var string
     */
    public $filePath = 'actions';

    /**
     * @var string
     */
    public $providers = array(
        Provider::ACTION,
    );

    /**
     * @var string
     */
    protected $name = 'action';

    /**
     * @var string
     */
    protected $description = 'Generates action classes.';

    /**
     * @var string
     */
    protected $defaultTemplate = 'action.txt';

    /**
     * @var string
     */
    protected $coreClass = '\CAction';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->className = ucfirst($this->subject) . 'Action';

        $this->initComponent();
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $files[] = new File(
            $this->resolveFilePath(),
            $this->compile(
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                )
            )
        );

        return $files;
    }
}