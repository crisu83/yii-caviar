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

use crisu83\yii_caviar\Compiler;
use crisu83\yii_caviar\Exception;

abstract class FileGenerator extends Generator
{
    /**
     * @var array
     */
    public $defaultTemplate;

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
            if (!isset($this->templates[$this->template])) {
                throw new Exception("Unable to find template '{$this->template}'.");
            }

            $this->templatePath = "{$this->templates[$this->template]}/{$this->name}";
        }

        return $this->templatePath;
    }

    /**
     * @param array $templates
     * @return string
     * @throws Exception
     */
    protected function resolveTemplateFile(array $templates = array())
    {
        if (empty($templates)) {
            $templates = $this->getDefaultTemplates();
        }

        $templatePath = $this->getTemplatePath();

        foreach ($templates as $templateFile) {
            $filePath = "$templatePath/$templateFile";

            if (file_exists($filePath) && is_file($filePath)) {
                return $filePath;
            }
        }

        throw new Exception("Unable to find template file.");
    }

    /**
     * @param string $templateFile
     * @param array $templateData
     * @return string
     */
    protected function compile($templateFile, array $templateData)
    {
        $compiler = new Compiler();
        return $compiler->compile(file_get_contents($templateFile), array_merge($this->templateData, $templateData));
    }

    /**
     * @return array
     */
    protected function getDefaultTemplates()
    {
        return array("{$this->subject}.txt", $this->defaultTemplate);
    }
}
