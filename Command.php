<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar;

use crisu83\yii_caviar\generators\Generator;

class Command extends \CConsoleCommand
{
    /**
     * @var array
     */
    public $generators = array();

    /**
     * @var array
     */
    public $templates = array();

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var string
     */
    public $tempDir = 'tmp';

    /**
     * @var string
     */
    public $defaultAction = 'component';

    /**
     * @var array
     */
    private static $_builtInGenerators = array(
        'component' => array(
            'class' => 'crisu83\yii_caviar\generators\ComponentGenerator',
        ),
        'config' => array(
            'class' => 'crisu83\yii_caviar\generators\ConfigGenerator',
        ),
        'controller' => array(
            'class' => 'crisu83\yii_caviar\generators\ControllerGenerator',
        ),
        'layout' => array(
            'class' => 'crisu83\yii_caviar\generators\LayoutGenerator',
        ),
        'model' => array(
            'class' => 'crisu83\yii_caviar\generators\ModelGenerator',
        ),
        'view' => array(
            'class' => 'crisu83\yii_caviar\generators\ViewGenerator',
        ),
        'webapp' => array(
            'class' => 'crisu83\yii_caviar\generators\WebAppGenerator',
        ),
    );

    /**
     * Provides the command description.
     * @return string the command description.
     */
    public function getHelp()
    {
        return <<<EOD
USAGE
  yiic generate <name> [<context>:]<subject> [<args>]

DESCRIPTION
  Generates code using the available generators.

EXAMPLES
  * yiic generate component controller
    Generates a 'Controller' component under 'protected/components'.

  * yiic generate config main
    Generates a 'main' configuration under 'protected/main'.

  * yiic generate controller app:site
    Generates a 'SiteController' under 'app/controllers'.

  * yiic generate layout main
    Generates a 'main' layout under 'protected/controllers'.

  * yiic generate model api:user
    Generates an 'User' model 'api/models'.

  * yiic generate webapp app
    Generates an 'app' web application in the project root.

EOD;
    }

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->generators = \CMap::mergeArray(self::$_builtInGenerators, $this->generators);

        foreach ($this->templates as $template => $templatePath) {
            if (($path = \Yii::getPathOfAlias($templatePath)) !== false) {
                $templatePath = $path;
            }

            $this->templates[$template] = $templatePath;
        }

        $this->templates['default'] = __DIR__ . '/templates/default';
    }

    /**
     * @param array $args
     * @return int
     */
    public function run(array $args)
    {
        list($name, $config, $args) = $this->resolveRequest($args);

        if (isset($name)) {
            $this->usageError("You must specify a generator name.");
        }

        if (!isset($this->generators[$name])) {
            $this->usageError("Unknown generator '{$name}'.");
        }

        if (strpos($args[0], ':') !== false) {
            list ($config['context'], $config['subject']) = explode(':', $args[0]);
        } else {
            $config['subject'] = $args[0];
        }

        echo "Running generator '$name' ...\n";

        $files = $this->runGenerator($name, $config);

        $this->save($files);

        return 0;
    }

    /**
     * @param string $name
     * @param array $config
     * @return File[]
     * @throws Exception
     */
    public function runGenerator($name, array $config)
    {
        $config = \CMap::mergeArray($this->generators[$name], $config);

        /** @var Generator $generator */
        $generator = \Yii::createComponent($config);
        $generator->command = $this;

        $this->addTemplates($generator);

        $generator->init();

        if (!$generator->validate()) {
            throw new Exception("Generator validation failed.");
        }

        return $generator->generate();
    }

    /**
     * @return string
     */
    public function resolveTempPath()
    {
        return "{$this->basePath}/{$this->tempDir}";
    }

    /**
     * @param File[] $files
     */
    protected function save(array $files)
    {
        echo "Saving temporary files ...\n";

        foreach ($files as $file) {
            $file->save();
        }

        echo "Copying generated files ...\n";

        $fileList = $this->buildFileList($this->resolveTempPath(), $this->basePath);
        $this->copyFiles($fileList);

        @rmdir($this->resolveTempPath());
    }

    /**
     * @param Generator $generator
     */
    protected function addTemplates(Generator $generator)
    {
        foreach ($this->templates as $template => $templatePath) {
            $path = "$templatePath/{$generator->name}";
            if (file_exists($path) && is_dir($path)) {
                $generator->templates[$template] = $templatePath;
            }
        }
    }
}