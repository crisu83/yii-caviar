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
use crisu83\yii_caviar\helpers\ModelHelper;
use crisu83\yii_caviar\providers\Provider;

class ModelGenerator extends ComponentGenerator
{
    /**
     * @var string
     */
    public $baseClass;

    /**
     * @var string
     */
    public $namespace = 'models';

    /**
     * @var string
     */
    public $filePath = 'models';

    /**
     * @var string
     */
    public $connectionId = 'db';

    /**
     * @var string
     */
    public $tablePrefix;

    /**
     * @var bool
     */
    public $buildRelations = true;

    /**
     * @var bool
     */
    public $commentsAsLabels = true;

    /**
     * @var array
     */
    public $providers = array(
        array(Provider::MODEL),
    );

    /**
     * @var string
     */
    protected $name = 'model';

    /**
     * @var string
     */
    protected $description = 'Generates model classes.';

    /**
     * @var string
     */
    protected $defaultTemplate = 'model.txt';

    /**
     * @var string
     */
    protected $coreClass = '\CActiveRecord';

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->tableName = $this->subject;

        $this->initComponent();
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('connectionId, tablePrefix', 'filter', 'filter' => 'trim'),
                array('connectionId', 'required'),
                array(
                    'tablePrefix',
                    'match',
                    'pattern' => '/^(\w+[\w\.]*|\*?|\w+\.\*)$/',
                    'message' => '{attribute} should only contain word characters, dots, and an optional ending asterisk.'
                ),
                array(
                    'tablePrefix',
                    'match',
                    'pattern' => '/^[a-zA-Z_]\w*$/',
                    'message' => '{attribute} should only contain word characters.'
                ),
                array('connectionId', 'validateConnectionId', 'skipOnError' => true),
                array('tableName', 'validateTableName', 'skipOnError' => true),
                array('baseClass', 'validateClass', 'extends' => '\CActiveRecord', 'skipOnError' => true),
            )
        );
    }

    /**
     * Validates the connection id for this generator.
     *
     * @param string $attribute the attribute to validate.
     * @param array $params validation parameters.
     */
    public function validateConnectionId($attribute, $params)
    {
        $db = \Yii::app()->getComponent($this->connectionId);
        if ($db === null || !($db instanceof \CDbConnection)) {
            $this->addError('connectionId', 'A valid database connection is required to run this generator.');
        }
    }

    /**
     * Validates the table name for this generator.
     *
     * @param string $attribute the attribute to validate.
     * @param array $params validation parameters.
     */
    public function validateTableName($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $invalidTables = array();
        $invalidColumns = array();

        if ($this->tableName[strlen($this->tableName) - 1] === '*') {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
            } else {
                $schema = '';
            }

            $tables = $this->getDbConnection()->schema->getTables($schema);
            foreach ($tables as $table) {
                if ($this->tablePrefix == '' || strpos($table->name, $this->tablePrefix) === 0) {
                    if ($this->isReservedKeyword($table->name)) {
                        $invalidTables[] = $table->name;
                    }
                    if (($invalidColumn = $this->checkColumns($table)) !== null) {
                        $invalidColumns[] = $invalidColumn;
                    }
                }
            }
        } else {
            if (($table = $this->getTableSchema($this->tableName)) === null) {
                $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
            }

            if (!$this->hasErrors($attribute) && ($invalidColumn = $this->checkColumns($table)) !== null) {
                $invalidColumns[] = $invalidColumn;
            }
        }

        if ($invalidTables != array()) {
            $this->addError(
                'tableName',
                'Model class cannot take a reserved PHP keyword! Table name: ' . implode(', ', $invalidTables) . "."
            );
        }
        if ($invalidColumns != array()) {
            $this->addError(
                'tableName',
                'Column names that does not follow PHP variable naming convention: ' . implode(
                    ', ',
                    $invalidColumns
                ) . "."
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function attributeHelp()
    {
        return array_merge(
            parent::attributeHelp(),
            array(
                'connectionId' => "Name of the database connection to use (defaults to '{$this->connectionId}').",
                'tablePrefix' => "Prefix for table names (defaults to null).",
                'buildRelations' => "Whether to generate model relations (defaults to true).",
                'commentsAsLabels' => "Whether to generate model labels from comments (defaults to true).",
                'subject' => "Name for the model that will be generated.",
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $db = $this->getDbConnection();
        $this->tablePrefix = $db->tablePrefix;

        list ($schema, $tables) = $this->resolveSchemaAndTables();

        $files = array();

        foreach ($tables as $table) {
            $className = ModelHelper::generateClassName($db, $this->tablePrefix, $table->name);

            $files[] = new File(
                $this->resolveFilePathByClassName($className),
                $this->compile(
                    array(
                        'className' => $className,
                        'baseClass' => $this->baseClass,
                        'namespace' => $this->namespace,
                        'db' => $this->getDbConnection(),
                        'schema' => $schema,
                        'tableSchema' => $table,
                        'tablePrefix' => $this->tablePrefix,
                        'buildRelations' => $this->buildRelations,
                        'commentsAsLabels' => $this->commentsAsLabels,
                    )
                )
            );
        }

        return $files;
    }

    /**
     * @param string $className
     * @return string
     */
    protected function resolveFilePathByClassName($className)
    {
        return self::$config->basePath . "/{$this->filePath}/$className.php";
    }

    /**
     * @return array
     */
    protected function resolveSchemaAndTables()
    {
        if (($pos = strrpos($this->tableName, '.')) !== false) {
            $schema = substr($this->tableName, 0, $pos);
            $tableName = substr($this->tableName, $pos + 1);
        } else {
            $schema = '';
            $tableName = $this->tableName;
        }

        if ($tableName[strlen($tableName) - 1] === '*') {
            $tables = $this->getDbConnection()->schema->getTables($schema);
            if (!empty($this->tablePrefix)) {
                foreach ($tables as $i => $table) {
                    if (strpos($table->name, $this->tablePrefix) !== 0) {
                        unset($tables[$i]);
                    }
                }
            }
        } else {
            $tables = array($this->getTableSchema($this->tableName));
        }

        return array($schema, $tables);
    }

    /**
     * Returns the table schema for a specific table.
     *
     * @param string $tableName name of the table.
     * @return \CDbTableSchema table schema.
     */
    protected function getTableSchema($tableName)
    {
        /** @var \CDbConnection $connection */
        $connection = \Yii::app()->getComponent($this->connectionId);
        return $connection->schema->getTable($tableName, $connection->schemaCachingDuration !== 0);
    }

    /**
     * Check that all database field names conform to PHP variable naming rules
     * For example mysql allows field name like "2011aa", but PHP does not allow variable like "$model->2011aa"
     *
     * @param \CDbTableSchema $tableSchema table schema.
     * @return string the invalid table column name or null if no error.
     */
    protected function checkColumns($tableSchema)
    {
        foreach ($tableSchema->columns as $column) {
            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $column->name)) {
                return $tableSchema->name . '.' . $column->name;
            }
        }

        return null;
    }

    /**
     * @return \CDbConnection
     */
    protected function getDbConnection()
    {
        return \Yii::app()->getComponent($this->connectionId);
    }
}