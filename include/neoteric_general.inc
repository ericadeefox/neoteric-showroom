<?php
  
 /**
 * Neoteric General
 *
 * This INC file contains library functions used in general or
 * otherwise unspecified ways.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 * @file   neoteric_general.inc
 * @date   26-Jun-2003
 */
  
  
 /*************************** Functions ****************************/
  
 /**
 * Error
 *
 * The error function prints a given message to the screen
 *
 * @param string $msg Message to display
 * @param string $location Body of link to create
 */
 function error($msg, $location = '') {
  print "<STRONG>ERROR:</STRONG> $msg<br>";
  if (strlen($location) > 0) {
   print "<A HREF=\"" . $location . "\">Continue</A>";
  }
 }
  
 /**
 * Parameter
 *
 * Declares and retrieves a parameter to a PHP page
 *
 * Supported flags are as follows:
 *     required: parameter must be found. default
 * optional: parameter will default to $opt_val if not found
 * post: variable must be found in POST data
 * get: variable must be found in GET data
 *       if neither get nor post is specified, POST then GET will be checked
 * hard-error: exit on error. default
 * soft-error: return false when variable can't be found
 * ---formatting checks-------
 * numeric: any number, i.e. -3.141, 49, 0134134.231
 * integer: any integer, positive or negative
 * positive: any non-negative number
 *
 * @param reference $var    destination variable where the result will be stored
 * @param string    $name   name of the parameter expected
 * @param string    $flags  space-delimited list of flags
  
 function parameter(&$var, $name, $flags = 'required', $opt_val = '') {
 // Make string matching easier
 $flags = ' ' . $flags . ' ';
 $flags = strtolower($flags);
  
 // Should we hard-error (exit) or soft-error (return false and assign no value)
 // Default to hard-error
 if (strstr($flags, ' soft-error ')) {
 $soft_error = true;
 } else {
 $soft_error = false;
 }
  
 $found_data = false;
  
 // Step one: should we be looking for this in POST, GET, or either?
 if (strstr($flags, ' post ')) {
 // Check POST data
 if (array_key_exists($name, $_POST)) {
 $data = $_POST[$name];
 $found_data = true;
 }
 } else if(strstr($flags, ' get ')) {
 // Check GET data
 if (array_key_exists($name, $_GET)) {
 $data = $_GET[$name];
 $found_data = true;
 }
 } else {
 // Check POST then GET
 if (array_key_exists($name, $_POST)) {
 $data = $_POST[$name];
 $found_data = true;
 } else if(array_key_exists($name, $_GET)) {
 $data = $_GET[$name];
 $found_data = true;
 }
 }
  
 // Step 2: See if it was required
 // We default to required, so just check for !optional
 if (strstr($flags, ' optional ') == false) {
 if ($found_data == false) {
 if ($soft_error) {
 return false;
 } else {
 exit("Error! Expected parameter $name (flags: $flags) but didn't get it");
 }
 }
 }
  
 // Step 3: get data from optional input (if we're here, either we found it or it's optional)
 if ($found_data == false) {
 $data = $opt_val;
 }
  
 // Step 4: Check data for formatting
 if (strstr($flags, ' numeric ')) {
 if (is_numeric($data) == false) {
 if ($soft_error) {
 return false;
 } else {
 exit("Required: numeric expression. Found $name = $data, flags: $flags");
 }
 }
 }
  
 if (strstr($flags, ' integer ')) {
 if (ereg('^[0-9]+$', $data) == false) {
 if ($soft_error) {
 return false;
 } else {
 exit("Required: integer expression. Found $name = $data, flags: $flags");
 }
 }
 }
  
 if (strstr($flags, ' positive ')) {
 if ( ((float) $data) < 0) {
 if ($soft_error) {
 return false;
 } else {
 exit("Required: positive expression. Found $name = $data, flags: $flags");
 }
 }
 }
  
 $var = $data;
 // Return true since we actually did something
 return true;
 }
  
  
 /**
 * Date Now
 *
 * The date now function returns today's date formatted in
 * mysql style: yyyy-mm-dd.
 *
 * @return string formatted date
 */
 function dateNow() {
  return date("Y-m-d");
 }
  
 /**
 * Is Null
 *
 * The is null function checks to see whether a string is unset
 * or not.  Note that this function is different than the built
 * in is_null() php function, which tests the parameter for the
 * NULL reserved value.
 *
 * @param string $value Value to test
 * @return bool True if null string, false otherwise
 */
 function isnull($value) {
  if (strcmp($value, "") == 0) {
   return true;
  } else {
   return false;
  }
 }
  
 /**
 * Swap
 *
 * The swap function takes two strings, swaps them, and returns.
 * Variables are passed by reference.
 *
 * @param string $a First string
 * @param string $b Second string
 */
 function swap (&$a, &$b) {
  $temp = $a;
  $a = $b;
  $b = $temp;
 }
  
 /**
 * Redirect
 *
 * This function redirects the user to a particular page.
 *
 * @param string $page Page to redirect to
 */
 function redirect ($page, $parameters = "") {
  header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $page);
 }
?>
