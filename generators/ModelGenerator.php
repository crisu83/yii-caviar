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

class ModelGenerator extends ComponentGenerator
{
    /**
     * @var string
     */
    public $baseClass = '\CActiveRecord';

    /**
     * @var string
     */
    public $namespace = 'models';

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
    protected $tableName;

    /**
     * @var array
     */
    protected $relations;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->className = $this->generateClassName($this->subject); // must be done first
        $this->tableName = $this->subject;

        $this->initComponent();
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
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
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
        $files = array();

        $db = $this->getDbConnection();
        $tableSchema = $db->getSchema()->getTable($this->tableName);

        $files[] = new File(
            $this->resolveFilePath(),
            $this->compile(
                $this->resolveTemplateFile(),
                array(
                    'tableName' => $this->tableName,
                    'className' => $this->className,
                    'baseClass' => $this->baseClass,
                    'namespace' => $this->namespace,
                    'docProperties' => $this->renderDocProperties($tableSchema),
                    'docRelations' => $this->renderDocRelations(),
                    'relations' => $this->renderRelations(),
                    'rules' => $this->renderRules($tableSchema),
                    'labels' => $this->renderLabels($tableSchema),
                    'searchConditions' => $this->renderSearchConditions($tableSchema),
                )
            )
        );

        return $files;
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return string
     */
    protected function renderDocProperties(\CDbTableSchema $tableSchema)
    {
        $properties = array();

        foreach ($tableSchema->columns as $column) {
            $properties[] = "@property {$column->type} \${$column->name}";
        }

        return implode("\n * ", $properties);
    }

    /**
     * @return string
     */
    protected function renderDocRelations()
    {
        $properties = array();

        foreach ($this->getRelations() as $name => $relation) {
            if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches)) {
                $type = $matches[1];
                $model = $matches[2];

                switch ($type) {
                    case 'HAS_ONE':
                        $properties[] = "@property $model \$$name";
                        break;
                    case 'BELONGS_TO':
                        $properties[] = "@property $model \$$name";
                        break;
                    case 'HAS_MANY':
                        $properties[] = "@property {$model}[] \$$name";
                        break;
                    case 'MANY_MANY':
                        $properties[] = "@property {$model}[] \$$name";
                        break;
                    default:
                        echo "@property mixed \$$name";
                }
            }
        }

        return !empty($properties) ? implode("\n * ", $properties) : '';
    }

    /**
     * @return string
     */
    protected function renderRelations()
    {
        $relations = array();

        foreach ($this->getRelations() as $name => $relation) {
            $relations[] = "'$name' => $relation";
        }

        return $this->renderArray($relations);
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return string
     */
    protected function renderRules(\CDbTableSchema $tableSchema)
    {
        $rules = array();

        foreach ($this->generateRules($tableSchema) as $rule) {
            $rules[] = $rule;
        }

        $rules[] = "array('" . implode(', ', array_keys($tableSchema->columns)) . "', 'safe', 'on' => 'search'),";

        return $this->renderArray($rules);
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return string
     */
    protected function renderLabels(\CDbTableSchema $tableSchema)
    {
        $labels = array();

        foreach ($this->generateLabels($tableSchema) as $name => $label) {
            $labels[] = "'$name' => \Yii::t('{$this->tableName}', '$label')";
        }

        return $this->renderArray($labels);
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return string
     */
    protected function renderSearchConditions(\CDbTableSchema $tableSchema)
    {
        $conditions = array();

        foreach ($tableSchema->columns as $name => $column) {
            if ($column->type === 'string') {
                $conditions[] = "\$criteria->compare('$name', \$this->$name, true);";
            } else {
                $conditions[] = "\$criteria->compare('$name', \$this->$name);";
            }
        }

        return implode("\n{$this->indent(2)}", $conditions);
    }

    /**
     * @param array $array
     *
     * @return string
     */
    protected function renderArray(array $array)
    {
        return "array(\n{$this->indent(3)}" . implode(",\n{$this->indent(3)}", $array) . "\n{$this->indent(2)})";
    }

    /**
     * @return array
     */
    protected function getRelations()
    {
        return isset($this->relations[$this->className]) ? $this->relations[$this->className] : array();
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return array
     */
    public function generateLabels(\CDbTableSchema $tableSchema)
    {
        $labels = array();

        foreach ($tableSchema->columns as $column) {
            if ($this->commentsAsLabels && $column->comment) {
                $labels[$column->name] = $column->comment;
            } else {
                $label = ucwords(
                    trim(
                        strtolower(
                            str_replace(array('-', '_'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name))
                        )
                    )
                );
                $label = preg_replace('/\s+/', ' ', $label);
                if (strcasecmp(substr($label, -3), ' id') === 0) {
                    $label = substr($label, 0, -3);
                }
                if ($label === 'Id') {
                    $label = 'ID';
                }
                $label = str_replace("'", "\\'", $label);
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * @param \CDbTableSchema $tableSchema
     *
     * @return array
     */
    public function generateRules(\CDbTableSchema $tableSchema)
    {
        $rules = array();
        $required = array();
        $integers = array();
        $numerical = array();
        $length = array();
        $safe = array();

        foreach ($tableSchema->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }

            $r = !$column->allowNull && $column->defaultValue === null;

            if ($r) {
                $required[] = $column->name;
            }
            if ($column->type === 'integer') {
                $integers[] = $column->name;
            } elseif ($column->type === 'double') {
                $numerical[] = $column->name;
            } elseif ($column->type === 'string' && $column->size > 0) {
                $length[$column->size][] = $column->name;
            } elseif (!$column->isPrimaryKey && !$r) {
                $safe[] = $column->name;
            }
        }
        if ($required !== array()) {
            $rules[] = "array('" . implode(', ', $required) . "', 'required')";
        }
        if ($integers !== array()) {
            $rules[] = "array('" . implode(', ', $integers) . "', 'numerical', 'integerOnly' => true)";
        }
        if ($numerical !== array()) {
            $rules[] = "array('" . implode(', ', $numerical) . "', 'numerical')";
        }
        if ($length !== array()) {
            foreach ($length as $len => $cols) {
                $rules[] = "array('" . implode(', ', $cols) . "', 'length', 'max' => $len)";
            }
        }
        if ($safe !== array()) {
            $rules[] = "array('" . implode(', ', $safe) . "', 'safe')";
        }

        return $rules;
    }

    /**
     * @return \CDbConnection
     */
    protected function getDbConnection()
    {
        return \Yii::app()->{$this->connectionId};
    }

    /**
     * @param string $tableName
     * @param bool $addBrackets
     * @return string
     */
    protected function removePrefix($tableName, $addBrackets = true)
    {
        $db = $this->getDbConnection();

        if ($addBrackets && $db->tablePrefix === '') {
            return $tableName;
        }

        $prefix = $this->tablePrefix != '' ? $this->tablePrefix : $db->tablePrefix;

        if ($prefix != '') {
            if ($addBrackets && $db->tablePrefix != '') {
                $prefix = $db->tablePrefix;
                $lb = '{{';
                $rb = '}}';
            } else {
                $lb = $rb = '';
            }

            if (($pos = strrpos($tableName, '.')) !== false) {
                $schema = substr($tableName, 0, $pos);
                $name = substr($tableName, $pos + 1);
                if (strpos($name, $prefix) === 0) {
                    return $schema . '.' . $lb . substr($name, strlen($prefix)) . $rb;
                }
            } elseif (strpos($tableName, $prefix) === 0) {
                return $lb . substr($tableName, strlen($prefix)) . $rb;
            }
        }

        return $tableName;
    }

    /**
     * @return array
     */
    protected function generateRelations()
    {
        if (!$this->buildRelations) {
            return array();
        }

        $schemaName = '';
        if (($pos = strpos($this->tableName, '.')) !== false) {
            $schemaName = substr($this->tableName, 0, $pos);
        }

        $relations = array();
        $db = $this->getDbConnection();
        foreach ($db->schema->getTables($schemaName) as $table) {
            if ($this->tablePrefix != '' && strpos($table->name, $this->tablePrefix) !== 0) {
                continue;
            }
            $tableName = $table->name;

            if ($this->isRelationTable($table)) {
                $pks = $table->primaryKey;
                $fks = $table->foreignKeys;

                $table0 = $fks[$pks[0]][0];
                $table1 = $fks[$pks[1]][0];
                $className0 = $this->generateClassName($table0);
                $className1 = $this->generateClassName($table1);

                $unprefixedTableName = $this->removePrefix($tableName);

                $relationName = $this->generateRelationName($table0, $table1, true);
                $relations[$className0][$relationName] = "array(self::MANY_MANY, '$className1', '$unprefixedTableName($pks[0], $pks[1])')";

                $relationName = $this->generateRelationName($table1, $table0, true);

                $i = 1;
                $rawName = $relationName;
                while (isset($relations[$className1][$relationName])) {
                    $relationName = $rawName . $i++;
                }

                $relations[$className1][$relationName] = "array(self::MANY_MANY, '$className0', '$unprefixedTableName($pks[1], $pks[0])')";
            } else {
                $className = $this->generateClassName($tableName);
                foreach ($table->foreignKeys as $fkName => $fkEntry) {
                    // Put table and key name in variables for easier reading
                    $refTable = $fkEntry[0]; // Table name that current fk references to
                    $refKey = $fkEntry[1]; // Key in that table being referenced
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $relationName = $this->generateRelationName($tableName, $fkName, false);
                    $relations[$className][$relationName] = "array(self::BELONGS_TO, '$refClassName', '$fkName')";

                    // Add relation for the referenced table
                    $relationType = $table->primaryKey === $fkName ? 'HAS_ONE' : 'HAS_MANY';
                    $relationName = $this->generateRelationName(
                        $refTable,
                        $this->removePrefix($tableName, false),
                        $relationType === 'HAS_MANY'
                    );
                    $i = 1;
                    $rawName = $relationName;
                    while (isset($relations[$refClassName][$relationName])) {
                        $relationName = $rawName . ($i++);
                    }
                    $relations[$refClassName][$relationName] = "array(self::$relationType, '$className', '$fkName')";
                }
            }
        }
        return $relations;
    }

    /**
     * Checks if the given table is a "many to many" pivot table.
     * Their PK has 2 fields, and both of those fields are also FK to other separate tables.
     * @param \CDbTableSchema $table to inspect
     * @return boolean true if table matches description of helper table.
     */
    protected function isRelationTable($table)
    {
        $pk = $table->primaryKey;
        return (count($pk) === 2 // we want 2 columns
            && isset($table->foreignKeys[$pk[0]]) // pk column 1 is also a foreign key
            && isset($table->foreignKeys[$pk[1]]) // pk column 2 is also a foriegn key
            && $table->foreignKeys[$pk[0]][0] !== $table->foreignKeys[$pk[1]][0]); // and the foreign keys point different tables
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function generateClassName($tableName)
    {
        if ($this->tableName === $tableName || ($pos = strrpos($this->tableName, '.')) !== false && substr($this->tableName, $pos + 1) === $tableName) {
            return $this->className;
        }

        $tableName = $this->removePrefix($tableName, false);
        // remove schema part (e.g. remove 'public2.' from 'public2.post')
        if (($pos = strpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $className = '';

        foreach (explode('_', $tableName) as $name) {
            if ($name !== '') {
                $className .= ucfirst($name);
            }
        }

        return $className;
    }

    /**
     * Generate a name for use as a relation name (inside relations() function in a model).
     * @param string $tableName
     * @param string $fkName
     * @param boolean $multiple
     * @return string the relation name
     */
    protected function generateRelationName($tableName, $fkName, $multiple)
    {
        if (strcasecmp(substr($fkName, -2), 'id') === 0 && strcasecmp($fkName, 'id')) {
            $relationName = rtrim(substr($fkName, 0, -2), '_');
        } else {
            $relationName = $fkName;
        }

        $relationName[0] = strtolower($relationName);

        if ($multiple) {
            $relationName = $this->pluralize($relationName);
        }

        $names = preg_split('/_+/', $relationName, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($names)) {
            return $relationName;
        } // unlikely

        for ($name = $names[0], $i = 1; $i < count($names); ++$i) {
            $name .= ucfirst($names[$i]);
        }

        $rawName = $name;
        $table = $this->getDbConnection()->schema->getTable($tableName);
        $i = 0;
        while (isset($table->columns[$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }

    /**
     * Converts a word to its plural form.
     * @param string $name the word to be pluralized
     * @return string the pluralized word
     */
    protected function pluralize($name)
    {
        $rules = array(
            '/(m)ove$/i' => '\1oves',
            '/(f)oot$/i' => '\1eet',
            '/(c)hild$/i' => '\1hildren',
            '/(h)uman$/i' => '\1umans',
            '/(m)an$/i' => '\1en',
            '/(s)taff$/i' => '\1taff',
            '/(t)ooth$/i' => '\1eeth',
            '/(p)erson$/i' => '\1eople',
            '/([m|l])ouse$/i' => '\1ice',
            '/(x|ch|ss|sh|us|as|is|os)$/i' => '\1es',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/(shea|lea|loa|thie)f$/i' => '\1ves',
            '/([ti])um$/i' => '\1a',
            '/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
            '/(bu)s$/i' => '\1ses',
            '/(ax|test)is$/i' => '\1es',
            '/s$/' => 's',
        );

        foreach ($rules as $rule => $replacement) {
            if (preg_match($rule, $name)) {
                return preg_replace($rule, $replacement, $name);
            }
        }

        return $name . 's';
    }
}