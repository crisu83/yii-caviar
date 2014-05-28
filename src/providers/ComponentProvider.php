<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\providers;

class ComponentProvider extends FileProvider
{
    public $name = 'component';

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $baseClass = '\CComponent';

    /**
     * @var string
     */
    public $namespace = 'components';

    /**
     * @var array
     */
    public $use = array();

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'className' => $this->className,
            'baseClass' => $this->baseClass,
            'namespace' => !empty($this->namespace) ? "\nnamespace {$this->namespace};" : '',
            'use' => $this->renderUse(),
        );
    }

    protected function renderUse()
    {
        $use = array();

        foreach ($this->use as $className => $alias) {
            if (!is_string($className)) {
                $className = $alias;
                unset($alias);
            }

            $use[] = isset($alias) ? "$className as $alias" : $className;
        }

        return !empty($use) ? "\nuse " . implode(";\nuse ", $use) . ";\n" : '';
    }
}
