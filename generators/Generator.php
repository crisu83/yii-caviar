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

use crisu83\yii_caviar\Exception;
use crisu83\yii_caviar\File;

abstract class Generator extends \CModel
{
    // constants
    const COMPONENT = 'component';
    const CONFIG = 'config';
    const CONTROLLER = 'controller';
    const CRUD = 'crud';
    const LAYOUT = 'layout';
    const MODEL = 'model';
    const VIEW = 'view';
    const WEBAPP = 'webapp';

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
    public $context = 'app';

    /**
     * @var string
     */
    public $template = 'default';

    /**
     * @var array
     */
    protected static $generators = array();

    /**
     * @var array
     */
    protected static $templates = array();

    /**
     * @var string
     */
    protected static $basePath;

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
     * @param $name
     * @param $config
     * @return Generator
     * @throws \crisu83\yii_caviar\Exception
     */
    public static function create($name, $config)
    {
        if (!isset(self::$generators[$name])) {
            throw new Exception("Unknown generator '$name'.");
        }

        $generator = \Yii::createComponent(\CMap::mergeArray(self::$generators[$name], $config));
        $generator->init();

        return $generator;
    }

    /**
     * @param string $name
     * @param array $config
     * @return \crisu83\yii_caviar\File[]
     * @throws \crisu83\yii_caviar\Exception
     */
    public static function run($name, array $config)
    {
        $generator = self::create($name, $config);

        // todo: is this the best place to run validation logic
        if (!$generator->validate()) {
            throw new Exception("Generator validation failed.");
        }

        return $generator->generate();
    }

    /**
     * @param array $generators
     */
    public static function setGenerators($generators)
    {
        self::$generators = $generators;
    }

    /**
     * @param array $templates
     */
    public static function setTemplates($templates)
    {
        self::$templates = $templates;
    }

    /**
     * @param string $basePath
     */
    public static function setBasePath($basePath)
    {
        self::$basePath = $basePath;
    }
}