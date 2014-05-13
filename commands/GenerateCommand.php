<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\commands;

use crisu83\yii_caviar\generators\Generator;
use crisu83\yii_caviar\helpers\Line;

class GenerateCommand extends \CConsoleCommand
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
     * @var string name of the temporary directory.
     */
    public $tempDir = 'tmp';

    /**
     * @var string name of the default action.
     */
    public $defaultAction = 'help';

    /**
     * @var string name of the default template.
     */
    public $defaultTemplate = 'default';

    /**
     * @var bool whether to enable namespaces.
     */
    public $enableNamespaces = true;

    /**
     * @var string
     */
    protected $version = '1.0.0-beta';

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
    public function run($args)
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
        echo $this->renderVersion();

        // Usage
        echo Line::begin('Usage:', Line::YELLOW)->nl();
        echo Line::begin()
            ->indent(2)
            ->text('generator [context:]subject [options]')->nl(2);

        // Options
        echo Line::begin('Options:', Line::YELLOW)->nl();
        echo Line::begin()
            ->indent(2)
            ->text('--help', Line::MAGENTA)
            ->to(21)
            ->text('-h', Line::MAGENTA)
            ->text('Display this help message.')
            ->nl(2);

        // Generators
        echo Line::begin('Available generators:', Line::YELLOW)->nl();
        foreach ($this->generators as $name => $config) {
            echo Line::begin()
                ->indent(2)
                ->text($name, Line::MAGENTA)
                ->to(24)
                ->text(Generator::create($name, $config)->getDescription())
                ->nl();
        }
    }

    protected function renderVersion()
    {
        return Line::begin('Caviar', Line::MAGENTA)
            ->text('version')
            ->text($this->version, Line::YELLOW)
            ->nl(2);
    }

    /**
     * Displays the help for a specific generator.
     *
     * @param string $name name of the generator.
     */
    public function renderGeneratorHelp($name)
    {
        echo $this->renderVersion();

        Generator::help($name);
    }

    /**
     * @inheritDoc
     */
    public function usageError($message)
    {
        echo Line::begin('Error:', Line::RED)->nl();
        echo Line::begin()
            ->indent(2)
            ->text($message)
            ->nl();

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
     * Initializes the templates.
     */
    protected function initTemplates()
    {
        foreach ($this->templates as $template => $templatePath) {
            $this->templates[$template] = $this->normalizePath($templatePath);
        }

        if (!isset($this->templates['default'])) {
            $this->templates['default'] = dirname(__DIR__) . '/templates/default';
        }
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
     * Initializes the generators.
     */
    protected function initGenerators()
    {
        $this->generators = \CMap::mergeArray(self::$_builtInGenerators, $this->generators);

        Generator::setConfig(
            \Yii::createComponent(
                array(
                    'class' => '\crisu83\yii_caviar\components\Config',
                    'basePath' => $this->getTempPath(),
                    'generators' => $this->generators,
                    'templates' => $this->templates,
                    'attributes' => array(
                        'template' => $this->defaultTemplate,
                        'enableNamespaces' => $this->enableNamespaces,
                    ),
                )
            )
        );
    }

    /**
     * Runs a specific generator with the given configuration.
     *
     * @param string $name name of the generator.
     * @param array $config generator configuration.
     * @param array $args command line arguments.
     */
    protected function runGenerator($name, array $config, array $args)
    {
        echo $this->renderVersion();

        if (!isset($args[0])) {
            $this->usageError("You must specify a subject for what you are generating.");
        }

        list ($config['context'], $config['subject']) = strpos($args[0], ':') !== false
            ? explode(':', $args[0])
            : array('app', $args[0]);

        echo Line::begin('Running generator', Line::YELLOW)->nl();
        echo Line::begin()
            ->indent(2)
            ->text("Generating $name '{$args[0]}'.")
            ->nl(2);

        $generator = Generator::create($name, $config);

        if (!$generator->validate()) {
            $generator->renderErrors();
        }

        $files = $generator->generate();

        $this->save($files);
    }

    /**
     * Saves the given files in a temporary folder, copies them to the project root and deletes the temporary folder.
     *
     * @param File[] $files list of files to save.
     */
    protected function save(array $files)
    {
        echo Line::begin('Saving generated files ...', Line::GREEN)->end();

        foreach ($files as $file) {
            $file->save();
        }

        echo Line::begin()->nl();
        echo Line::begin('Copying generated files ...', Line::GREEN)->nl();

        $fileList = $this->buildFileList($this->getTempPath(), $this->basePath);
        $this->copyFiles($fileList);

        echo Line::begin('Removing temporary files ...', Line::GREEN)->nl();

        $this->removeDirectory($this->getTempPath());
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