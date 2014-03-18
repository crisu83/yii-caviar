<?php
/*
 * This file is part of Caviar.
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
     * @var string
     */
    private $_tempPath;

    /**
     * @var array
     */
    private static $_builtInGenerators = array(
        Generator::COMPONENT => array(
            'class' => 'crisu83\yii_caviar\generators\ComponentGenerator',
        ),
        Generator::CONFIG => array(
            'class' => 'crisu83\yii_caviar\generators\ConfigGenerator',
        ),
        Generator::CONTROLLER => array(
            'class' => 'crisu83\yii_caviar\generators\ControllerGenerator',
        ),
        Generator::LAYOUT => array(
            'class' => 'crisu83\yii_caviar\generators\LayoutGenerator',
        ),
        Generator::MODEL => array(
            'class' => 'crisu83\yii_caviar\generators\ModelGenerator',
        ),
        Generator::VIEW => array(
            'class' => 'crisu83\yii_caviar\generators\ViewGenerator',
        ),
        Generator::WEBAPP => array(
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

        $this->initGenerators();
        $this->initTemplates();
    }

    /**
     * @param array $args
     * @return int
     */
    public function run(array $args)
    {
        list($name, $config, $args) = $this->resolveRequest($args);

        if (!isset($name)) {
            $this->usageError("You must specify a generator name.");
        }

        if (!isset($args[0])) {
            $this->usageError("You must specify a subject.");
        }

        if (strpos($args[0], ':') !== false) {
            list ($config['context'], $config['subject']) = explode(':', $args[0]);
        } else {
            $config['subject'] = $args[0];
        }

        echo "\nPreparing generator ... ";

        Generator::setGenerators($this->generators);
        Generator::setTemplates($this->templates);
        Generator::setBasePath($this->getTempPath());

        echo "done\n";

        echo "\nRunning generator '$name'.\n";

        $files = Generator::run($name, $config);

        $this->save($files);

        return 0;
    }

    /**
     * @return string
     */
    public function getTempPath()
    {
        if (!isset($this->_tempPath)) {
            $hash = md5(microtime(true));
            $this->_tempPath = "{$this->basePath}/{$this->tempDir}/{$hash}";
        }

        return $this->_tempPath;
    }

    /**
     *
     */
    protected function initGenerators()
    {
        $this->generators = \CMap::mergeArray(self::$_builtInGenerators, $this->generators);
    }

    /**
     *
     */
    protected function initTemplates()
    {
        foreach ($this->templates as $template => $templatePath) {
            $this->templates[$template] = $this->normalizePath($templatePath);
        }

        $this->templates['default'] = __DIR__ . '/templates/default';
    }

    /**
     * @param $filePath
     * @return string
     */
    protected function normalizePath($filePath)
    {
        if (($path = \Yii::getPathOfAlias($filePath)) !== false) {
            $filePath = $path;
        }

        return $filePath;
    }

    /**
     * @param File[] $files
     */
    protected function save(array $files)
    {
        echo "\nSaving temporary files ... ";

        foreach ($files as $file) {
            $file->save();
        }

        echo "done\n";

        echo "\nCopying generated files ... \n";

        $fileList = $this->buildFileList($this->getTempPath(), $this->basePath);
        $this->copyFiles($fileList);

        echo "done\n";

        echo "\nRemoving temporary files ... ";

        $this->removeDirectory($this->getTempPath());

        echo "done\n\n";
    }

    /**
     * Flushes a directory recursively.
     * @param string $path the directory path.
     */
    protected function removeDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = $path . '/' . $entry;

            if (is_dir($entryPath)) {
                $this->removeDirectory($entryPath);
            } else {
                unlink($entryPath);
            }
        }

        rmdir($path);
    }
}