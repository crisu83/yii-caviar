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
     * @var array
     */
    public $defaultTemplate = 'file.txt';

    /**
     * @var string
     */
    public $templatePath;

    /**
     * @var array
     */
    public $templateData = array();

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $filePath;

    /**
     * @var string
     */
    public $tab = '    ';

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
            array('defaultTemplate, fileName, filePath', 'required'),
            array('templatePath, data', 'safe'),
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePath()
    {
        if (!isset($this->templatePath)) {
            $this->templatePath = $this->resolveTemplatePath($this->template);
        }

        return $this->templatePath;
    }

    /**
     * @param $template
     * @return string
     * @throws \crisu83\yii_caviar\Exception
     */
    protected function resolveTemplatePath($template)
    {
        if (!isset(self::$templates[$template])) {
            throw new Exception("Unable to find template '$template'.");
        }

        return self::$templates[$template] . '/' . $this->name;
    }

    /**
     * @param array $templates
     * @return string
     * @throws \crisu83\yii_caviar\Exception
     */
    protected function resolveTemplateFile(array $templates = array())
    {
        $templatePath = $this->getTemplatePath();

        foreach (array_merge($templates, $this->getDefaultTemplates()) as $templateFile) {
            $filePath = "$templatePath/$templateFile";

            if (is_file($filePath)) {
                return $filePath;
            }
        }

        throw new Exception("Unable to find template file.");
    }

    /**
     * @param string $templateFile
     * @param array $templateData
     * @return string
     * @throws \crisu83\yii_caviar\Exception
     */
    protected function compile($templateFile, array $templateData)
    {
        if (!is_file($templateFile)) {
            throw new Exception("Unable to find template file '$templateFile'.");
        }

        if (!isset(self::$compiler)) {
            self::$compiler = new Compiler();
        }

        return self::$compiler->compile(
            file_get_contents($templateFile),
            array_merge($this->templateData, $templateData)
        );
    }

    /**
     * @return array
     */
    protected function getDefaultTemplates()
    {
        return array("{$this->subject}.txt", $this->defaultTemplate);
    }

    /**
     * @return string
     */
    protected function resolveFilePath()
    {
        return self::$basePath . "/{$this->filePath}/{$this->fileName}";
    }

    /**
     * @param $amount
     * @return string
     */
    protected function indent($amount = 1)
    {
        return str_repeat($this->tab, $amount);
    }

    /**
     * @param $value
     * @return bool
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
}
