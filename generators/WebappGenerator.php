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

class WebappGenerator extends Generator
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
    public $commands = array(
        // todo: change this to be done through configuration, this is just a temporary fix.
        'component {app}:controller --className="Controller" --baseClass="\CController"',
        'component {app}:userIdentity --className="UserIdentity" --baseClass="\CUserIdentity"',
        'controller {app}:site',
        'config {app}:main',
        'layout {app}:main',
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
        foreach ($this->commands as $command) {
            $args = explode(' ', $command);
            $args[1] = str_replace('{app}', $this->subject, $args[1]);
            $this->command->runGenerator($args);
        }

        $files = array();

        $files[] = $this->createGitKeepFile('runtime');
        $files[] = $this->createGitKeepFile('web/assets');

        $this->save($files);
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