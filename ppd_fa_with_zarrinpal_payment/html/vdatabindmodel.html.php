<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

/**
This class is a container of the elements of the table that will be managed.
*/
class VisualDataBindModel
{
	var $dataBinds = null;
	var $keyField = null;
	var $tableName = null;
	
	/**
	Class constructor
	*/
	function __construct()
	{
		$this->dataBinds = array();
	}
	
	/**
	Sets the key field of the table
	*/
	function setKeyField($keyField)
	{
		$this->keyField = $keyField;
	}
	
	/**
	Adds a data bind element to the collection
	*/
	function addDataBind($dataBind)
	{
		$dataBind->setSourceTable($this->tableName);
		$this->dataBinds[] = $dataBind;
	}
	
	/**
	Sets the table name
	*/
	function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}
	
	/**
	Returns the key field of the table
	*/
	function getKeyField()
	{
		return $this->keyField;
	}
	
	/**
	Returns the table name
	*/
	function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	Returns the collection of elements
	*/
	function getDataBinds()
	{
		return $this->dataBinds;
	}
}

?>