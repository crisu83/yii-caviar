<?php

namespace crisu83\yii_caviar\helpers;

class ModelHelper
{
    /**
     * @param string $tableName
     * @param bool $addBrackets
     * @return string
     */
    public static function removePrefix(\CDbConnection $db, $tablePrefix, $tableName, $addBrackets = true)
    {
        if ($addBrackets && $db->tablePrefix === '') {
            return $tableName;
        }

        $prefix = $tablePrefix != '' ? $tablePrefix : $db->tablePrefix;

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
     * @param $tableName
     * @return string
     */
    public static function generateClassName(\CDbConnection $db, $tablePrefix,$tableName)
    {
        $tableName = self::removePrefix($db, $tablePrefix, $tableName, false);

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
}
