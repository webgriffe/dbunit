<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit_Extensions_Database_DataSet_ITable;
use PHPUnit_Extensions_Database_DataSet_ITableIterator;
use PHPUnit_Extensions_Database_DataSet_ITableMetaData;

/**
 * Implements the basic functionality of data sets.
 */
abstract class AbstractDataSet implements IDataSet
{
    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param  bool $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected abstract function createIterator($reverse = false);

    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames()
    {
        $tableNames = [];

        foreach ($this->getIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            $tableNames[] = $table->getTableMetaData()->getTableName();
        }

        return $tableNames;
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param  string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData($tableName)
    {
        return $this->getTable($tableName)->getTableMetaData();
    }

    /**
     * Returns a table object for the given table.
     *
     * @param  string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITable
     */
    public function getTable($tableName)
    {
        foreach ($this->getIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            if ($table->getTableMetaData()->getTableName() == $tableName) {
                return $table;
            }
        }
    }

    /**
     * Returns an iterator for all table objects in the given dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    public function getIterator()
    {
        return $this->createIterator();
    }

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    public function getReverseIterator()
    {
        return $this->createIterator(true);
    }

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param IDataSet $other
     */
    public function matches(IDataSet $other)
    {
        $thisTableNames = $this->getTableNames();
        $otherTableNames = $other->getTableNames();

        sort($thisTableNames);
        sort($otherTableNames);

        if ($thisTableNames != $otherTableNames) {
            return false;
        }

        foreach ($thisTableNames as $tableName) {
            $table = $this->getTable($tableName);

            if (!$table->matches($other->getTable($tableName))) {
                return false;
            }
        }

        return true;
    }

    public function __toString()
    {
        $iterator = $this->getIterator();

        $dataSetString = '';
        foreach ($iterator as $table) {
            $dataSetString .= $table->__toString();
        }

        return $dataSetString;
    }
}
