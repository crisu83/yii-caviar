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

class ViewProvider extends Provider
{
    /**
     * @var string
     */
    public $name = 'view';

    /**
     * @var string
     */
    public $cssClass;

    /**
     * @var array
     */
    public $vars = array();

    /**
     * @inheritDoc
     */
    public function provide()
    {
        return array(
            'cssClass' => $this->cssClass,
            'docVars' => $this->generateDocVars(),
        );
    }

    /**
     * @return string
     */
    protected function generateDocVars()
    {
        $vars = array();

        foreach ($this->vars as $key => $value) {
            $vars[] = "@var \$$key $value";
        }

        return !empty($vars) ? "/**\n * " . implode("\n * ", $vars) . "\n */": '';
    }
}