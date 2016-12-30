<?php
  
 /**
 * Neoteric Conversion
 *
 * This INC file contains functions that convert stuff
 *
 * @package  neoteric_conversion.inc
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 */
  
 /****************** functions ********************************/
  
 /**
 * Convert Date
 *
 * The convert date function changes a string date format into a
 * Neoteric form for general use.
 *
 * @param string $date The date in MySQL formatting
 * @return string The date in dd-mm-yyyy [where mm is Jan, Feb, etc.] format
 */
 function convertDate($date) {
  return date("d-M-Y", strtotime($date));
 }
  
 /**
 * Revert Date
 *
 * The revert date function changes a string date format into MySQL
 * form for database storage.
 *
 * @param string $date Date in general form
 * @return string Date in yyyy-mm-dd hh:mm:ss format
 */
 function revertDate($date) {
  return date("Y-m-d H:i:s", strtotime($date));
 }
  
 /**
 * To Money
 *
 * The to money function converts a string number to currency to
 * two decimal places.
 *
 * @param string $str Number to convert
 * @return string String of the currency value to 2 decimal places
 */
 function toMoney($str) {
  //if str is null, return the zero dollar
  if (strcmp($str, "") == 0) {
   return "$0.00";
  }
  //explode the string into 2 parts:  the integer and the decimal parts
  $amt = explode(".", $str);
  $integer = $amt[0];
  //round the decimal part to 2 decimal places
  //get the first 2 decimal places
  $firstDec = substr($amt[1], 0, 2);
  //get the rest of the decimal places
  $secondDec = substr($amt[1], 2);
  //create a new number with the decimal point after the 100ths place
  $decimal = $firstDec . "." . $secondDec;
  //round that number to an even 'integer'
  $decimal = round($decimal, 0);
  //get the length of the integer part
  $length = strlen($integer);
  //for each 3 characters of the integer, we must add a comma
  for ($i = 3; $i < $length; $i = $i + 3) {
   //we split integer up into 2 parts [beginning of integer and the last 3*i characters]
   //each time we traverse through the for loop
   //the two parts are concatenated together with a comma
   $integer = substr($integer, 0, ($length - $i)) . "," . substr($integer, ($length - $i));
  }
  //if the decimal has only one character, we append an extra 0
  if (strlen($decimal) == 1) {
   $decimal .= "0";
  }
  //put string together and return
  $str = "$" . $integer . "." . $decimal;
  return $str;
 }
  
  
 /**
 * Modify Description
 *
 * The modify description function replaces all occurrances of a
 * double quote with two single quotes.  This eliminates problems
 * found by sending descriptions with quotes over quoted queries.
 *
 * @param string $description Description to modify
 * @return string Modified description
 */
 function modifyDescription($description) {
  return str_replace("\"", "''", $description);
 }
  
 /**
 * Generate Label
 *
 * The generate label function takes in a string that corresponds to
 * a database column name; this value should be of the form
 * db+"Word1"+"Word2"+...+"WordN" where there are N words
 * in the column name--besides the 'db.'  These words should be seperated
 * by capitilizing the first letter of each word; as such, the initial
 * 'db' will be ignored.
 *
 * @param string $column Column name to create a label for
 * @return string Generated label for the column name
 */
 function generateLabel($column) {
  $toReturn = "";
  $column = substr($column, 2);
  for($x = 0; $x < strlen($column); $x++) {
   $char = substr($column, $x, 1);
   if (strcmp($char, strtoupper($char)) == 0) {
    $toReturn = $toReturn . " ";
   }
   $toReturn = $toReturn . $char;
  }
  $toReturn = substr($toReturn, 1);
  return $toReturn;
 }
  
 /**
 * Wrap
 *
 * The wrap function determines if the string passed in has words
 * that are larger than the length given.  If so, it converts the
 * string to a fixed width set of strings.
 *
 * @param string $string String to wrap
 * @param int $length Max length of a word - default: 40 chars
 * @return string Converted string
 */
 function wrap ($string, $length = '40') {
  //check if we need to wordwrap the string
  //if the string is big enough, then we care
  $note = $string;
  if (strlen($string) > $length) {
   //check the first token
   if (strlen(strtok($string, ' ')) < $length) {
    //check each subsequent token
    while ($temp = strtok(' ')) {
     //if the length is too big, break everything at $length
     if (strlen($temp) >= $length) {
      $note = wordwrap($string, $length, '<br>', 1);
     }
    }
   } else {
    $note = wordwrap($string, $length, '<br>', 1);
   }
  }
  return $note;
 }
  
?>
