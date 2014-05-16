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
use crisu83\yii_caviar\providers\Provider;

class ComponentGenerator extends FileGenerator
{
    /**
     * @var string
     */
    public $baseClass;

    /**
     * @var string
     */
    public $namespace = 'components';

    /**
     * @var string
     */
    public $filePath = 'components';

    /**
     * @var string
     */
    protected $name = 'component';

    /**
     * @var string
     */
    protected $description = 'Generates component classes.';

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $coreClass = '\CComponent';

    /**
     * @var string
     */
    protected $defaultTemplate = 'component.txt';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->initComponent();
    }

    /**
     *
     */
    public function initComponent()
    {
        if (!isset($this->baseClass)) {
            $this->baseClass = $this->coreClass;
        }

        if (!isset($this->className)) {
            $this->className = ucfirst($this->subject);
        }

        $this->namespace = !empty($this->namespace) ? "{$this->context}\\{$this->namespace}" : '';
        $this->fileName = "{$this->className}.php";
        $this->filePath = "{$this->context}/{$this->filePath}";
    }

    /**
     * @inheritDoc
     */
    public function attributeHelp()
    {
        return array_merge(
            parent::attributeHelp(),
            array(
                'baseClass' => "Name of the class to extend (defaults to {$this->baseClass}).",
                'namespace' => "Name of the namespace to use (defaults to '{$this->namespace}').",
                'subject' => "Name of the component that will be generated.",
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('baseClass, namespace', 'filter', 'filter' => 'trim'),
                array('baseClass', 'required'),
                array(
                    'baseClass, namespace',
                    'match',
                    'pattern' => '/^[a-zA-Z_\\\\]+$/',
                    'message' => '{attribute} should only contain word characters and backslashes.'
                ),
                array('baseClass', 'validateClass', 'extends' => '\CComponent', 'skipOnError' => true),
                array('baseClass', 'validateReservedKeyword', 'skipOnError' => true),
            )
        );
    }

    /**
     * Validates the base class to make sure that it exists and that it extends from the core class.
     *
     * @param string $attribute the attribute to validate.
     * @param array $params validation parameters.
     */
    public function validateClass($attribute, $params)
    {
        $className = @\Yii::import($this->$attribute, true);

        if (!is_string($className) || !$this->classExists($className)) {
            $this->addError($attribute, "Class '$className' does not exist or has syntax error.");
        } elseif (isset($params['extends'])
            && ltrim($className, '\\') !== ltrim($params['extends'], '\\')
            && !is_subclass_of($className, $params['extends'])) {
            $this->addError('baseClass', "Class '$className' must extend from {$params['extends']}.");
        }
    }

    /**
     * Validates an attribute to make sure it is not a reserved PHP keyword.
     *
     * @param string $attribute the attribute to validate.
     * @param array $params validation parameters.
     */
    public function validateReservedKeyword($attribute, $params)
    {
        if ($this->isReservedKeyword($this->$attribute)) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be a reserved PHP keyword.');
        }
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $files = array();

        $files[] = new File(
            $this->resolveFilePath(),
            $this->compile(
                $this->resolveTemplateFile(),
                $this->runProvider(
                    Provider::COMPONENT,
                    array(
                        'className' => $this->className,
                        'baseClass' => $this->baseClass,
                        'namespace' => $this->namespace,
                    )
                )
            )
        );

        return $files;
    }

    /**
     * Checks if the named class exists (in a case sensitive manner).
     *
     * @param string $name class name to be checked
     * @return boolean whether the class exists
     */
    protected function classExists($name)
    {
        return class_exists($name, false) && in_array(preg_replace('/^\\\\/', '', $name), get_declared_classes());
    }
}