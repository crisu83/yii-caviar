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

use crisu83\yii_caviar\File;

class WebAppGenerator extends Generator
{
    /**
     * @var string
     */
    protected $name = 'webapp';

    /**
     * @var string
     */
    protected $description = 'Generates a web application.';

    /**
     * @var array
     */
    protected $structure = array(
        'component' => array(
            array('controller', 'baseClass' => '\CController'),
            array('userIdentity', 'baseClass' => '\CUserIdentity'),
        ),
        'controller' => array(
            array('site'),
        ),
        'config' => array(
            array('main'),
        ),
        'layout' => array(
            array('main'),
        ),
    );

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            array(
                'subject' => "Name of the application to generate.",
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        // todo: add validation rules.
        return array_merge(
            parent::rules(),
            array()
        );
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        foreach ($this->structure as $name => $items) {
            foreach ($items as $config) {
                $config['subject'] = array_shift($config);
                $config['context'] = $this->subject;
                $files = array_merge(Generator::run($name, $config), $files);
            }
        }

        $files[] = $this->createGitKeepFile('runtime');
        $files[] = $this->createGitKeepFile('web/assets');

        return $files;
    }

    /**
     * @param $filePath
     * @return File
     */
    protected function createGitKeepFile($filePath)
    {
        return $this->createFile('.gitkeep', $filePath);
    }

    /**
     * @param $fileName
     * @param $filePath
     * @param string $content
     * @return File
     */
    protected function createFile($fileName, $filePath, $content = '')
    {
        return new File(self::$basePath . "/{$this->subject}/$filePath/$fileName", $content);
    }

    /**
     * @param array $structure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }
}