<?php
  
 /**
 * Neoteric DB
 *
 * This file contains library functions used in database access.
 *
 * @package Includes
 * @company Neoteric Hovercraft, Inc
 * @author Collin Krepps
 */
  
 // Creates a connection to the database
  
 // Database access
 // Username to database server
 define('NEOUSER', "root");
 // Password to database server
 define('NEOPASSWD', 'N30va61!');
 // IP address of database server
 define('NEODBIP', "192.168.0.121");
 // Database name
 define('NEODBNAME', "neoteric");
 // Database type
 define('NEODBTYPE', "mysql");
  
 $link = neoDbConnect();
  
 /**
 * Connect to the database
 *
 * This function creates a link to the database with the default username, password, and database server as defined in neoteric_globals.inc.  Should this information change, these changes should be reflected in that include file, not this one.  This function is mysql-specific.
 *
 * @return resource Link identifier to the database
 */
 function neoDbConnect() {
  // FIXME add error checking here
  $link = mysql_connect(NEODBIP, NEOUSER, NEOPASSWD);
  mysql_select_db(NEODBNAME);
  return $link;
 }
  
 /**
 * Disconnect from the database
 *
 * This function destroys the link to the database and frees the associated resources.  This function is mysql-specific.
 *
 * @param resource $result The resource from the query last generated
 * @param resource $link The link identifier to the connection
 */
 function neoDbDisconnect($result, $link) {
  mysql_free_result($result);
  mysql_close($link);
  return;
 }
  
 /**
 * Get Last Key
 *
 * The get last key function executes a query on a given table, and yields the top row of the table when sorted in descending order.  The field in question is returned.
 *
 * @param string $tblName Name of table to query
 * @param mixed $primaryKeyName The primary key column of the table
 * @param mixed $fieldToReturn The field which is returned
 * @return mixed The field specified by $fieldToReturn, if a result is found
 */
 function getLastKey($tblName, $primaryKeyName, $fieldToReturn) {
  $qry = "SELECT $fieldToReturn FROM $tblName ORDER BY $primaryKeyName DESC LIMIT 1";
  // FIXME generate a proper error here
  $result = mysql_query($qry) or die("Error in getLastRow: Unable to perform MySQL query, $qry<br>" . mysql_error());
  if ($tmpRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
   return $tmpRow[$fieldToReturn];
  } else {
   return;
  }
 }
  
 /**
 * Get First Key
 *
 * The get first key function executes a query on a given table, and yields the top row of the table when sorted in descending order.  The field in question is returned.
 *
 * @param string $tblName Name of table to query
 * @param mixed $primaryKeyName The primary key column of the table
 * @param mixed $fieldToReturn The field which is returned
 * @return mixed The field specified by $fieldToReturn, if a result is found
 */
 function getFirstKey($tblName, $primaryKeyName, $fieldToReturn) {
  $qry = "SELECT $fieldToReturn FROM $tblName ORDER BY $primaryKeyName ASC LIMIT 1";
  $result = mysql_query($qry) or die("Error in getLastRow: Unable to perform MySQL query, $qry<br>" . mysql_error());
  if ($tmpRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
   return $tmpRow[$fieldToReturn];
  } else {
   return false;
  }
 }
  
 /**
 * Get a Single Row
 *
 * The get row function searches the given table on the searchterms,
 * and returns the first result to the query.
 *
 * @param string $cols Columns of table to return
 * @param string $tblName Name of table to query
 * @param mixed $searchTerm The value of interest
 * @param mixed $field The field which is returned
 * @param string $location Location of the function call
 * @return array An array of the row elements, if a result is found
 */
 function getRow($tblName, $searchTerm, $field, $location = 'neotericDB|getRow', $cols = '*') {
  $qry = "SELECT $cols FROM $tblName WHERE $field = '$searchTerm' LIMIT 1";
  $result = mysql_query($qry) or die("Error in $location: Unable to perform MySQL query, $qry<br>" . mysql_error());
  if ($tmpRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
   return $tmpRow;
  } else {
   return false;
  }
 }
  
 /**
 * Get All Rows
 *
 * The get all rows function searches the given table on the search terms,
 * and returns an array containing the results of the query.
 *
 * @param string $tblName Name of table to query
 * @param mixed $searchTerm Value of interest
 * @param mixed $field Field which is returned
 * @param string $orderBy Optional ordering choice
 * @param string $location Location of the function call
 * @param string $cols Columns of table to return
 * @return array Array of the row elements, if a result is found
 */
 function getAllRows($tblName, $searchTerm, $field, $orderBy = '', $location = 'neotericDB|getAllRows', $cols = '*') {
  if (strcmp($orderBy, '') == 0) {
   $qry = "SELECT $cols FROM $tblName WHERE $field = '$searchTerm'";
  } else {
   $qry = "SELECT $cols FROM $tblName WHERE $field = \"$searchTerm\" ORDER BY \"$orderBy\"";
  }
  $result = mysql_query($qry) or die("Error in $location: Unable to perform MySQL query, $qry<br>" . mysql_error());
  $count = 0;
  $toReturn = Array();
  while ($tmpRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
   $toReturn[$count] = $tmpRow;
   $count ++;
  }
  return $toReturn;
 }
  
 /**
 * Get Value
 *
 * The get value function returns the relevant item of the query
 * based on the given parameters.
 *
 * @param string $colName Name of column of interest
 * @param string $tblName Name of table to query
 * @param mixed $primaryKey The value of interest
 * @param string $primaryKeyName The name of the primary key column
 * @return mixed The value from the column specified, if a result is found
 */
 function getValue($colName, $tblName, $primaryKey, $primaryKeyName) {
  $qry = "SELECT $colName FROM $tblName WHERE $primaryKeyName = '$primaryKey'";
  $result = mysql_query($qry) or die("Error in getValue: Unable to perform MySQL query, $qry - $colName, $tblName, $primaryKey, $primaryKeyName<br>" . mysql_error());
  if ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
   return $tmpCol[$colName];
  } else {
   return false;
  }
 }
  
 /**
 * Query
 *
 * The query function executes a database query based on the
 * input string.  In the case of an error, the message displayed
 * gives information that is optionally passed in if possible.
 * Query may be of any type, but nothing is returned from this
 * function.
 *
 * @param string $qry Query to execute
 * @param string $location Where in the code the query was called
 */
 function query($qry, $location = 'neotericDB|query') {
  // FIXME add proper error checking
  mysql_query($qry) or die("Error in $location: $qry<br>" . mysql_error());
  return;
 }

 function dbQuery($queryString) {
  if(NEODBTYPE == "mysql") {
   // FIXME add error checking
   $link = mysql_connect(NEODBIP, NEOUSER, NEOPASSWD);
   // FIXME add error checking
   mysql_select_db(NEODBNAME);
   // FIXME add error checking
   $queryResult = mysql_query($queryString, $link);
   // FIXME add error checking
   while($row = mysql_fetch_row($queryResult)) {
    $allRows[] = $row;
   }
   mysql_free_result($queryResult);
   mysql_close($link);
   return $allRows;
  }
  else {
   // FIXME error message here
   return NULL;
  }
 }
  
 /**
 * Data
 *
 * The data function executes a database query based on the
 * input string.  In the case of an error, the message displayed
 * gives information that is optionally passed in if possible.
 * Query may be of any type, and the result of the query is returned.
 *
 * @param string $qry Query to execute
 * @param string $location Where in the code the query was called
 * @return resource Result of the query
 */
 function data($qry, $location = 'neotericDB|data') {
  $result = mysql_query($qry) or die("Error in $location: $qry<br>" . mysql_error());
  return $result;
 }
  
 /**
 * Set Field
 *
 * The set field function queries the table specified based
 * on matching the given key.
 *
 * @param string $field Name of table to query
 * @param string $table Name of table to query
 * @param mixed $value The value to which $field is set
 * @param mixed $keyName The name of the key
 * @param string $keyValue The value of the key
 */
 function setField($field, $table, $value, $keyName, $keyValue) {
  $qry = "UPDATE $table SET $field = '$value' WHERE $keyName = '$keyValue'";
  $result = mysql_query($qry) or die("Error in setField: Unable to perform MySQL query, $qry<br>" . mysql_error());
  return;
 }
?>
