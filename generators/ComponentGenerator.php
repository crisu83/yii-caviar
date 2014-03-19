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

        $this->namespace = "{$this->context}\\{$this->namespace}";
        $this->fileName = "{$this->className}.php";
        $this->filePath = $this->namespaceToPath();
    }

    /**
     * @inheritDoc
     */
    public function attributeDescriptions()
    {
        return array_merge(
            parent::attributeDescriptions(),
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
                array('baseClass, namespace', 'required'),
                array(
                    'baseClass, namespace',
                    'match',
                    'pattern' => '/^[a-zA-Z_\\\\]+$/',
                    'message' => '{attribute} should only contain word characters and backslashes.'
                ),
                array('baseClass', 'validateBaseClass', 'skipOnError' => true),
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
    public function validateBaseClass($attribute, $params)
    {
        $className = @\Yii::import($this->baseClass, true);

        if (!is_string($className) || !$this->classExists($className)) {
            $this->addError('baseClass', "Class '{$this->baseClass}' does not exist or has syntax error.");
        } elseif ($className !== $this->coreClass && !is_subclass_of($className, $this->coreClass)) {
            $this->addError('baseClass', "'{$this->className}' must extend from {$this->coreClass}.");
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
                array(
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
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

    /**
     * @return string
     */
    protected function namespaceToPath()
    {
        return str_replace('\\', '/', $this->namespace);
    }
}