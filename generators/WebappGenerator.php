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

use crisu83\yii_caviar\File;

class WebAppGenerator extends Generator
{
    /**
     * @var string
     */
    public $name = 'webapp';

    /**
     * @var string
     */
    public $description = 'Web application generator.';

    /**
     * @var array
     */
    public $structure = array(
        'component' => array(
            array('controller', 'className' => 'Controller', 'baseClass' => '\CController'),
            array('userIdentity', 'className' => 'UserIdentity', 'baseClass' => '\CUserIdentity'),
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
                $files = array_merge($this->command->runGenerator($name, $config), $files);
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
        return new File("{$this->getBasePath()}/{$this->subject}/$filePath/$fileName", $content);
    }
}