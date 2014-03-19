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
     * @var array global generator configurations.
     */
    public $generators = array();

    /**
     * @var array list of templates (name => path).
     */
    public $templates = array();

    /**
     * @var string path to the project root.
     */
    public $basePath;

    /**
     * @var string name of the template to use as default.
     */
    public $defaultTemplate = 'default';

    /**
     * @var string name of the temporary directory.
     */
    public $tempDir = 'tmp';

    /**
     * @var string name of the default action.
     */
    public $defaultAction = 'help';

    /**
     * @var string path where the generated files are temporarily stored.
     */
    private $_tempPath;

    /**
     * @var array list of built in generators.
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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->initTemplates();
        $this->initGenerators();
    }

    /**
     * @inheritDoc
     */
    public function run(array $args)
    {
        list($action, $options, $args) = $this->resolveRequest($args);

        if ($action === 'help' || $action === '-h') {
            $this->renderHelp();
        } elseif (in_array('--help', $options) || in_array('-h', $args)) {
            $this->renderGeneratorHelp($action);
        } else {
            $this->runGenerator($action, $options, $args);
        }

        return 0;
    }

    /**
     * Displays the command help.
     */
    public function renderHelp()
    {
        echo "\nGENERATE COMMAND";
        echo "\n  Generates files using the available generators.\n";

        echo "\nUsage:";
        echo "\n  generator [context:]subject [--option=value ...]\n";

        echo "\nGenerators:";
        foreach ($this->generators as $name => $config) {
            $generator = Generator::create($name, $config);

            echo "\n  " . Generator::padHelpLabel($name) . $generator->getDescription();
        }

        echo "\n\n";
    }

    /**
     * Displays the help for a specific generator.
     *
     * @param string $name name of the generator.
     */
    public function renderGeneratorHelp($name)
    {
        Generator::help($name);
    }

    public function runGenerator($name, array $config, array $args)
    {
        if (!isset($args[0])) {
            $this->usageError("You must specify a subject.");
        }

        list ($config['context'], $config['subject']) = strpos($args[0], ':') !== false
            ? explode(':', $args[0])
            : array('app', $args[0]);

        if (!isset($config['template'])) {
            $config['template'] = $this->defaultTemplate;
        }

        echo "\nGENERATE COMMAND";
        echo "\n  Running '$name' generator.\n";

        $files = Generator::run($name, $config);

        $this->save($files);
    }

    /**
     * @inheritDoc
     */
    public function usageError($message)
    {
        echo "\nError: $message\n\n";

        exit(1);
    }

    /**
     * Returns the path for storing the generated files.
     *
     * @return string temporary path.
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
     * Initializes the generators.
     */
    protected function initGenerators()
    {
        $this->generators = \CMap::mergeArray(self::$_builtInGenerators, $this->generators);

        Generator::setGenerators($this->generators);
        Generator::setTemplates($this->templates);
        Generator::setBasePath($this->getTempPath());
    }

    /**
     * Initializes the templates.
     */
    protected function initTemplates()
    {
        foreach ($this->templates as $template => $templatePath) {
            $this->templates[$template] = $this->normalizePath($templatePath);
        }

        $this->templates['default'] = __DIR__ . '/templates/default';
    }

    /**
     * Normalizes the given file path by converting aliases to real paths.
     *
     * @param string $filePath file path.
     * @return string real path.
     */
    protected function normalizePath($filePath)
    {
        if (($path = \Yii::getPathOfAlias($filePath)) !== false) {
            $filePath = $path;
        }

        return $filePath;
    }

    /**
     * Saves the given files in a temporary folder, copies them to the project root and deletes the temporary folder.
     *
     * @param File[] $files list of files to save.
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
     * Removes a specific directory.
     *
     * @param string $path full path to the directory to remove.
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