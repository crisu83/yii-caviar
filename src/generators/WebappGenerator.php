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

use crisu83\yii_caviar\components\File;

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
    protected $generators = array(
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
     * @var array
     */
    protected $directories = array(
        'runtime',
        'web/assets',
    );

    /**
     * @inheritDoc
     */
    public function attributeHelp()
    {
        return array_merge(
            parent::attributeHelp(),
            array(
                'subject' => "Name of the application to generate.",
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getUsage()
    {
        return "{$this->name} subject [options]";
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        foreach ($this->generators as $name => $items) {
            foreach ($items as $config) {
                $config['subject'] = array_shift($config);
                $config['context'] = $this->subject;
                $files = array_merge(Generator::run($name, $config), $files);
            }
        }

        foreach ($this->directories as $dir) {
            $files[] = $this->createGitKeepFile($dir);
        }

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
        return new File(self::$config->basePath . "/{$this->subject}/$filePath/$fileName", $content);
    }
}