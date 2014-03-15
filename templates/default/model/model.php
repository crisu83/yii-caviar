<?php
/**
 * @var string $namespace
 * @var string $className
 * @var string $baseClass
 * @var string $tableName
 * @var string $docProperties
 * @var string $docRelations
 * @var string $rules
 * @var string $relations
 * @var string $labels
 * @var string $searchConditions
 */
return <<<EOD
<?php

namespace $namespace;

/**
 * This is the model class for table $tableName".
 *
 * The followings are the available columns in table '$tableName':
 *
 * $docProperties
 *
 * The followings are the available model relations:
 *
 * $docRelations
 */
class $className extends $baseClass
{
    /**
	 * @return string the associated database table name.
	 */
	public function tableName()
	{
		return '$tableName';
	}

    /**
	 * @return array validation rules for model attributes.
	 */
    public function rules()
    {
	    return $rules;
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
	    return $relations;
	}

	/**
	 * @return array customized attribute labels (name=>label).
	 */
    public function attributeLabels()
    {
	    return $labels;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return \CActiveDataProvider the data provider that can return the models based on the search conditions.
	 */
	public function search()
	{
        \$criteria = new \CDbCriteria();

        $searchConditions

        return new \CActiveDataProvider(\$this, array('criteria' => \$criteria));
	}

    /**
	 * Returns the static model of this class.
	 *
	 * @param string \$className active record class name.
	 *
	 * @return $className the static model class.
	 */
	public static function model(\$className = __CLASS__)
	{
		return parent::model(\$className);
	}
}
EOD;
