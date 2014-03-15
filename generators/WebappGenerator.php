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
        'component {app}:controller --className="Controller" --baseClass="\CController"',
        'component {app}:userIndentity --className="UserIdentity" --baseClass="\CUserIdentity"',
        'controller {app}:site',
        'config {app}:main',
        'layout {app}:main',
    );

    /**
     * @param string $name
     */
    public function generate($name)
    {
        foreach ($this->commands as $command) {
            $args = explode(' ', $command);
            $args[1] = str_replace('{app}', $name, $args[1]);
            $this->command->runGenerator($args);
        }
    }
}