<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\generators;

use crisu83\yii_caviar\Exception;
use crisu83\yii_caviar\File;
use crisu83\yii_caviar\Renderer;

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
    public $subject;

    /**
     * @var string
     */
    public $context = 'protected';

    /**
     * @var string
     */
    public $template = 'default';

    /**
     * @var array
     */
    public $templates = array();

    /**
     * @var \crisu83\yii_caviar\Command
     */
    public $command;

    /**
     * @return File[]
     */
    abstract public function generate();

    /**
     *
     */
    public function init()
    {
    }

    /**
     * @return array
     */
    public function rules()
    {
        // todo: add the rest of the rules, including those in other generators.
        return array(
            array('subject, template', 'required'),
            array('description, context', 'safe'),
        );
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array();
    }

    /**
     * @param string $viewFile
     * @param array $data
     * @return string
     */
    protected function render($viewFile, array $data = array())
    {
        $renderer = new Renderer();
        return $renderer->renderFile($viewFile, array_merge($this->viewData, $data));
    }

    /**
     * @return string
     */
    protected function getViewPath()
    {
        if (!isset($this->viewPath)) {
            if (!isset($this->templates[$this->template])) {
                throw new Exception("Unable to find template '{$this->template}'.");
            }

            $this->viewPath = "{$this->templates[$this->template]}/{$this->name}";
        }

        return $this->viewPath;
    }

    /**
     * @param array $views
     * @return string
     * @throws Exception
     */
    protected function resolveViewFile(array $views = array())
    {
        if (empty($views)) {
            $views = $this->getDefaultViews();
        }

        $viewPath = $this->getViewPath();

        foreach ($views as $viewFile) {
            $filePath = "$viewPath/$viewFile";

            if (file_exists($filePath) && is_file($filePath)) {
                return $filePath;
            }
        }

        throw new Exception("Unable to find view file.");
    }

    /**
     * @return array
     */
    protected function getDefaultViews()
    {
        return array("{$this->subject}.php", $this->defaultView);
    }

    /**
     * @return string
     */
    protected function resolveFilePath()
    {
        return "{$this->getBasePath()}/{$this->filePath}/{$this->fileName}";
    }

    /**
     * @return string
     */
    protected function getBasePath()
    {
        return $this->command->resolveTempPath();
    }

    /**
     * @param $amount
     * @return string
     */
    protected function indent($amount = 1)
    {
        return str_repeat(' ', $amount * 4);
    }

    /**
     * Converts a word to its plural form.
     * @param string $name the word to be pluralized
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