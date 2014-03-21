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

use crisu83\yii_caviar\Compiler;
use crisu83\yii_caviar\Exception;

abstract class FileGenerator extends Generator
{
    /**
     * @var string name of the template to use.
     */
    public $template;

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
     * @var string tab character.
     */
    protected $tab = '    ';

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
     * @return string path to the template file.
     * @throws \crisu83\yii_caviar\Exception if no template files are found.
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

        throw new Exception("Unable to find template file {$templates[0]}.");
    }

    /**
     * Compiles a template file with the given data.
     *
     * @param string $templateFile path to the template file.
     * @param array $templateData data to pass to the template.
     * @return string the compiled template.
     * @throws \crisu83\yii_caviar\Exception if the template file cannot be found.
     */
    protected function compile($templateFile, array $templateData)
    {
        if (!isset(self::$compiler)) {
            self::$compiler = new Compiler();
        }

        return self::$compiler->compile(
            file_get_contents($templateFile),
            array_merge($this->templateData, $templateData)
        );
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
     * Renders a "tab" character.
     *
     * @param int $amount number of indents.
     * @return string the rendered indent.
     */
    protected function indent($amount = 1)
    {
        return str_repeat($this->tab, $amount);
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
     * @param string $tab
     */
    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @param array $templateData
     */
    public function setTemplateData($templateData)
    {
        $this->templateData = $templateData;
    }
}
