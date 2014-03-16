<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar;

use crisu83\yii_caviar\exceptions\Exception;
use crisu83\yii_caviar\generators\Generator;

class Command extends \CConsoleCommand
{
    /**
     * @var array
     */
    public $generators = array();

    /**
     * @var array
     */
    public $templates = array();

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var array
     */
    private static $_builtInGenerators = array(
        'component' => array(
            'class' => 'crisu83\yii_caviar\generators\ComponentGenerator',
        ),
        'config' => array(
            'class' => 'crisu83\yii_caviar\generators\ConfigGenerator',
        ),
        'controller' => array(
            'class' => 'crisu83\yii_caviar\generators\ControllerGenerator',
        ),
        'layout' => array(
            'class' => 'crisu83\yii_caviar\generators\LayoutGenerator',
        ),
        'model' => array(
            'class' => 'crisu83\yii_caviar\generators\ModelGenerator',
        ),
        'view' => array(
            'class' => 'crisu83\yii_caviar\generators\ViewGenerator',
        ),
        'webapp' => array(
            'class' => 'crisu83\yii_caviar\generators\WebappGenerator',
        ),
    );

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->generators = \CMap::mergeArray(self::$_builtInGenerators, $this->generators);

        foreach ($this->templates as $template => $templatePath) {
            if (($path = \Yii::getPathOfAlias($templatePath)) !== false) {
                $templatePath = $path;
            }

            $this->templates[$template] = $templatePath;
        }

        $this->templates['default'] = __DIR__ . '/templates/default';
    }

    /**
     * @param array $args
     * @return int
     */
    public function run(array $args)
    {
        if (!isset($args[0])) {
            $this->usageError("You must specify a generator id.");
        }

        if (!isset($args[1])) {
            $this->usageError("You must specify a name for what you are generating.");
        }

        if (!isset($this->generators[$args[0]])) {
            $this->usageError("Unknown generator '{$args[0]}'.");
        }

        $this->runGenerator($args);
    }

    /**
     * @param array $args
     * @throws Exception
     */
    public function runGenerator(array $args)
    {
        $config = \CMap::mergeArray($this->generators[$args[0]], $this->parseArguments(array_splice($args, 2)));

        if (strpos($args[1], ':') !== false) {
            list ($config['app'], $config['name']) = explode(':', $args[1]);
        } else {
            $config['name'] = $args[1];
        }

        /** @var Generator $generator */
        $generator = \Yii::createComponent($config);
        $generator->command = $this;
        $generator->init();

        $this->addTemplates($generator);

        if (!$generator->validate()) {
            throw new Exception("Generator validation failed.");
        }

        $generator->generate();
    }

    /**
     * @param array $args
     * @return array
     */
    protected function parseArguments(array $args)
    {
        $config = array();

        foreach ($args as $arg) {
            if (strpos($arg, '=') === false) {
                throw new Exception("Malformed argument '$arg' given.");
            }

            list ($key, $value) = explode('=', str_replace('"', '', substr($arg, 2)));
            $config[$key] = $value;
        }

        return $config;
    }

    /**
     * @param Generator $generator
     */
    protected function addTemplates(Generator $generator)
    {
        foreach ($this->templates as $template => $templatePath) {
            $path = "$templatePath/{$generator->name}";
            if (file_exists($path) && is_dir($path)) {
                $generator->templates[$template] = $templatePath;
            }
        }
    }
}