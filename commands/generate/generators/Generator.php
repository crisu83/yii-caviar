<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace console\commands\generate\generators;

use console\commands\generate\exceptions\Exception;
use console\commands\generate\File;

abstract class Generator extends \CModel
{
    /**
     * @var string
     */
    public $name = 'base';

    /**
     * @var string
     */
    public $description = 'Abstract base class for code generation.';

    /**
     * @var string
     */
    public $template = 'default';

    /**
     * @var array
     */
    public $templates = array();

    /**
     * @var \console\commands\generate\Command
     */
    public $command;

    /**
     * @param string $name
     */
    abstract public function generate($name);

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('template', 'required'),
        );
    }

    /**
     * @param File[] $files
     */
    public function save(array $files)
    {
        foreach ($files as $file) {
            $file->save();
        }
    }

    /**
     * Returns the list of attribute names of the model.
     *
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array();
    }

    /**
     * Converts a word to its plural form.
     *
     * @param string $name the word to be pluralized
     *
     * @return string the pluralized word
     */
    protected function pluralize($name)
    {
        $rules = array(
            '/(m)ove$/i' => '\1oves',
            '/(f)oot$/i' => '\1eet',
            '/(c)hild$/i' => '\1hildren',
            '/(h)uman$/i' => '\1umans',
            '/(m)an$/i' => '\1en',
            '/(s)taff$/i' => '\1taff',
            '/(t)ooth$/i' => '\1eeth',
            '/(p)erson$/i' => '\1eople',
            '/([m|l])ouse$/i' => '\1ice',
            '/(x|ch|ss|sh|us|as|is|os)$/i' => '\1es',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/(shea|lea|loa|thie)f$/i' => '\1ves',
            '/([ti])um$/i' => '\1a',
            '/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
            '/(bu)s$/i' => '\1ses',
            '/(ax|test)is$/i' => '\1es',
            '/s$/' => 's',
        );

        foreach ($rules as $rule => $replacement) {
            if (preg_match($rule, $name)) {
                return preg_replace($rule, $replacement, $name);
            }
        }

        return $name . 's';
    }

    /**
     * @param $value
     *
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

    /**
     * @param string $template
     * @param string $fileName
     * @param string $default
     *
     * @throws Exception
     *
     * @return string
     */
    protected function resolveTemplateFile($template, $fileName, $default)
    {
        $templatePath = "{$this->resolveTemplatePath($template)}/{$this->name}";

        if (is_file("$templatePath/$fileName")) {
            return "$templatePath/$fileName";
        }

        if (is_file("$templatePath/$default")) {
            return "$templatePath/$default";
        }

        throw new Exception("Unable to find view file '$fileName' in template path '$templatePath'.");
    }

    /**
     * @param $template
     *
     * @return string
     *
     * @throws Exception
     */
    protected function resolveTemplatePath($template)
    {
        if (!isset($this->templates[$template])) {
            throw new Exception("Unable to find template '$template'.");
        }

        return $this->templates[$template];
    }

    /**
     * @param string $fileName
     * @param array $_params_
     *
     * @return string
     *
     * @throws Exception
     */
    protected function renderFile($fileName, array $_params_ = array())
    {
        if (!is_file($fileName)) {
            throw new Exception("The template file '$fileName' does not exist.");
        }

        if (is_array($_params_)) {
            extract($_params_, EXTR_PREFIX_SAME, 'params');
        } else {
            $params = $_params_;
        }

        return preg_replace('/(<?php)/', "$1\n" . $this->renderBanner(), require($fileName), 1);
    }

    /**
     * @return string
     */
    protected function renderBanner()
    {
        return <<<EOD
/**
 * This file was generated by Caviar.
 * http://github.com/Crisu83/yii-caviar
 */
EOD;
    }

    /**
     * @param $name
     *
     * @return array
     *
     * @throws Exception
     */
    protected function parseAppAndName($name)
    {
        if (strpos($name, ':') === false) {
            throw new Exception("Malformed name '$name'.");
        }

        return explode(':', $name);
    }

    /**
     * @return string
     */
    protected function getBasePath()
    {
        return $this->command->basePath;
    }
}