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

use crisu83\yii_caviar\Exception;
use crisu83\yii_caviar\components\Compiler;
use crisu83\yii_caviar\providers\Provider;

abstract class FileGenerator extends Generator
{
    /**
     * @var string name of the template to use.
     */
    public $template;

    /**
     * @var array providers to use with this generator.
     */
    public $providers = array();

    /**
     * @var string name for the item that will be generated.
     */
    protected $name = 'file';

    /**
     * @var string short description of what this generator does.
     */
    protected $description = 'Abstract base class for file generation.';

    /**
     * @var string path to the templates for this generator.
     */
    protected $templatePath;

    /**
     * @var array additional data to pass to the template compiler.
     */
    protected $templateData = array();

    /**
     * @var string file name for the default template.
     */
    protected $defaultTemplate;

    /**
     * @var string file name for the generated file.
     */
    protected $fileName;

    /**
     * @var string file path to where the generated file should be saved.
     */
    protected $filePath;

    /**
     * @var Compiler
     */
    protected static $compiler;

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('template', 'required'),
            array('template', 'validateTemplate', 'skipOnError' => true),
        );
    }

    /**
     * Validates the template for this generator.
     *
     * @param string $attribute the attribute to validate.
     * @param array $params validation parameters.
     */
    public function validateTemplate($attribute, array $params)
    {
        if (!isset(self::$config->templates[$this->template])) {
            $this->addError('template', "Unable to find template '{$this->template}'.");
        }
    }

    /**
     * @inheritDoc
     */
    public function attributeHelp()
    {
        return array_merge(
            parent::attributeHelp(),
            array(
                'template' => "Name of the template to use (default to '{$this->template}')",
            )
        );
    }

    /**
     * Runs the providers for this generator.
     *
     * @param array $properties an array of properties to set for providers.
     * @return array an array with the provided data.
     */
    protected function runProviders(array $properties = array())
    {
        $data = array();

        foreach ($this->providers as $config) {
            $className = array_shift($config);

            if (isset(self::$config->providers[$className])) {
                $config = \CMap::mergeArray(self::$config->providers[$className], $config);
            } else {
                $config['class'] = $className;
            }

            if (!class_exists($config['class'])) {
                throw new Exception("Provider '{$config['class']}' does not exist.");
            }

            $class = new \ReflectionClass($config['class']);
            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $name = $property->getName();

                if (!isset($config[$name]) && isset($properties[$name])) {
                    $config[$name] = $properties[$name];
                }
            }

            $provider = \Yii::createComponent($config);

            $data = array_merge($data, $provider->provide());
        }

        return $data;
    }

    /**
     * Returns the template path for this generator.
     *
     * @return string template path.
     */
    protected function getTemplatePath()
    {
        if (!isset($this->templatePath)) {
            $this->templatePath = self::$config->templates[$this->template] . '/' . $this->name;
        }

        return $this->templatePath;
    }

    /**
     * Determines the template file to use for generating the file.
     *
     * @param array $templates list of candidate templates.
     * @return string path to the template file or null if no template is found.
     */
    protected function resolveTemplateFile(array $templates = array())
    {
        $templatePath = $this->getTemplatePath();

        $templates = array_merge($templates, $this->getDefaultTemplates());

        if (empty($templates)) {
            throw new Exception("No templates available.");
        }

        foreach ($templates as $templateFile) {
            $filePath = "$templatePath/$templateFile";

            if (is_file($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    /**
     * Compiles the template for this generator.
     *
     * @param array $properties properties to pass to the providers.
     * @return string the compiled template.
     * @throws Exception if the template file cannot be found.
     */
    protected function compile(array $properties = array())
    {
        return $this->compileInternal($this->resolveTemplateFile(), $this->runProviders($properties));
    }

    /**
     * Compiles a specific template file using the given data.
     *
     * @param string $templateFile path to the template file.
     * @param array $templateData an array of data to pass to the template.
     * @return string the compiled template.
     * @throws Exception if the template file cannot be found.
     */
    protected function compileInternal($templateFile, array $templateData)
    {
        if (!isset(self::$compiler)) {
            self::$compiler = new Compiler();
        }

        if (!is_file($templateFile)) {
            throw new Exception("Could not find template file '$templateFile'.");
        }

        return self::$compiler->compile(file_get_contents($templateFile), $templateData);
    }

    /**
     * Returns a list of the default templates.
     *
     * @return array list of templates.
     */
    protected function getDefaultTemplates()
    {
        return array("{$this->subject}.txt", $this->defaultTemplate);
    }

    /**
     * Returns the full path to where the generated files should be saved.
     *
     * @return string file path.
     */
    protected function resolveFilePath()
    {
        return self::$config->basePath . "/{$this->filePath}/{$this->fileName}";
    }

    /**
     * Returns whether the given value is a reserved keyword in php.
     *
     * @param string $value value to check.
     * @return bool whether the value is a reserved keyword.
     */
    protected function isReservedKeyword($value)
    {
        static $keywords = array(
            '__class__',
            '__dir__',
            '__file__',
            '__function__',
            '__line__',
            '__method__',
            '__namespace__',
            '__trait__',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'case',
            'catch',
            'callable',
            'cfunction',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'eval',
            'exception',
            'exit',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'namespace',
            'new',
            'old_function',
            'or',
            'parent',
            'php_user_filter',
            'print',
            'private',
            'protected',
            'public',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'this',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
        );
        return in_array(strtolower($value), $keywords, true);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }
}
