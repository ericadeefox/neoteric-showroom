<?php
  
 /**
 * Neoteric Subcontractor
 *
 * This INC file contains library functions used in the subcontractor
 * module.
 *
 * @package Subcontractor
 * @company Neoteric Hovercraft, Inc
 * @author Todd R Windler
 */
  
 $contactFields = array("dbContactNumber" => "Contact Number",
  "dbDate" => "Date",
  "dbFirstName" => "First Name",
  "dbLastName" => "Last Name",
  "dbCompany" => "Company",
  "dbTitle" => "Title",
  "dbBusinessTitle" => "Business Title",
  "dbAddress" => "Address",
  "dbAddress2" => "Address 2",
  "dbCity" => "City",
  "dbState" => "State",
  "dbZip" => "Zip Code",
  "dbCountry" => "Country",
  "dbPrimaryPhone" => "Primary Phone",
  "dbSecondaryPhone" => "Secondary Phone",
  "dbPrimaryEmail" => "Primary Email",
  "dbSecondaryEmail" => "Secondary Email",
  "dbWebAddress" => "Web Address",
  "dbSource" => "Source",
  "dbCheck" => "Check",
  "dbAddNotes" => "Additional Notes");
  
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $contactNumber Number given to contact
 * @return string Data to be displayed in the menu
 */
 function menu($contactNumber = '') {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../contacts/contactLookup.php?contactNumber=$contactNumber">Lookup</A><br>
			<A HREF="../contacts/printRange.php">Mass Lookup</A><br>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}

/**
 * Input Validation
 *
 * The input validation function takes a column header and the column value, then determines the validity of the value based on the header.  This function is specific to the subcontractor table.
 *
 * @param string $columnName Column header for the value
 * @param string $columnValue Column value for the header
 * @return int 1 if the value is valid, 0 otherwise
 */
function inputValidation($columnName, $columnValue){
//null values are ok
if(isnull($columnValue)){
return 1;
}

		//any combination of spaces, alphanumerics, and miscellaneous symbols are allowed
		if(strcmp($columnName, "dbCompany")==0 || strcmp($columnName, "dbAddress")==0 || strcmp($columnName, "dbTerms")==0 || strcmp($columnName, "dbSupplies")==0){
			if (!ereg("^[ 0-9[:alpha:],-\.#\$%&()]+$", $columnValue)) { return 0; }
		}
		//any combination of spaces and letters are allowed
		else if(strcmp($columnName, "dbCity")==0 || strcmp($columnName, "dbCountry")==0 || strcmp($columnName, "dbAttention")==0){
			if (!ereg("^[ [:alpha:]]+$", $columnValue)) { return 0; }
		}
		//either 2 or 3 letters are allowed
		else if(strcmp($columnName, "dbState")==0){
			if (!ereg("^[[:alpha:]]{2,3}$", $columnValue)) { return 0; }
		}
		//any combination of numbers and dashes are allowed
		else if(strcmp($columnName, "dbPhone")==0 || strcmp($columnName, "dbFax")==0 || strcmp($columnName, "dbSecondPhone")==0 || strcmp($columnName, "dbSecondFax")==0 || strcmp($columnName, "dbZip")==0){
			if (!eregi("^[2-9]{1}[0-9]{2}\-[0-9]{3}\-[0-9]{4}[x]*[0-9]*$", $columnValue) && !eregi("^[0-9]{2}[ 0-9-]+$", $columnValue)) { return 0; }
		}
		/*alphanumerics, dots, underscores, and slasses are allowed for the username;
		multiple sections, each consisting of alphanumerics, dashes, and a dot between sections, are allowed
		for the host name;
		2 or 3 letters are allowed for the extension*/
		else if(strcmp($columnName, "dbEmail")==0){
			if (!ereg("^[_\.[:alnum:]-]+@([[:alnum:]][[:alnum:]-]+\.)+[[:alpha:]]{2,3}$", $columnValue)) { return 0; }
		}
		//anything is valid for the website that starts with http://
		else if(strcmp($columnName, "dbWebsite")==0){
			if (!ereg("^[http://.*]$", $columnValue)) { return 0; }
		}
		//any combination of alphanumerics and dashes are allowed
		else if(strcmp($columnName, "dbAccount")==0){
			if (!ereg("^[[:alnum:]-]+$", $columnValue)) { return 0; }
		}

		//if all tests pass, we have valid input
		return 1;
	}
?>
