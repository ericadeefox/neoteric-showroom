<?php
  
 /**
 * Neoteric Subcontractor
 *
 * This INC file contains library functions used in the subcontractor
 * module.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 * @file   neoteric_subcontractor.inc
 * @date   7-Jul-2003
 */
  
  
 /*************************** Functions ****************************/
  
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $subconOrderNumber Order number for subcontractor shipment
 * @return string Data to be displayed in the menu
 */
 function menu($subconOrderNumber = '') {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../subcontractor/subcontractorOrder.php?subconOrderNumber=$subconOrderNumber">Orders</A><br>
			<A HREF="../subcontractor/subcontractorPartLookup.php?subconOrderNumber=$subconOrderNumber">Part Lookup</A><br>
			<A HREF="../subcontractor/subcontractorLookup.php?subconNumber=$subconOrderNumber">Lookup</A><br>
			<A HREF="../subcontractor/subcontractorSearch.php?subconOrderNumber=$subconOrderNumber">Search</A><br>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}

	/**
	 *	List Material
	 *
	 *	The list material function displays information about the parts 
	 *	we supply to the subcontractor to create our desired parts.  
	 * 
	 *	@param int $subconOrderNumber Subcontractor order number to edit
	 *	@param int $action Set if called from receiving algorithm
	 */
	function listMaterial ($subconOrderNumber, $action = '0') {
		/* action valid values:
		 *		0:	default		- returns to subcontractorOrder page
		 *		1:	receiving	- returns to order received page
		 *		2:	received	- returns to subcontractorOrder page, qty returned not editable
		 */
		//print all material required for this project
		//printing buttons
		if ($action == 1) {
			//Get the template and display the header
			$incDir = '../include';
			loadTemplate("$incDir/template.htm");
			printHeader("Subcontractor", "Subcontractor", menu($subconOrderNumber));
			print "<H4>Parts Information</H4>";
		}
		//print table headers
		echo <<< QQQ
			<FORM METHOD="get" ACTION="subcontractorMaterialUpdate.php">
				<TABLE BORDER="1" CELLSPACING="0">
					<TR>
						<TH>
							Part
						</TH>
						<TH>
							Job
						</TH>
						<TH>
							Qty
						</TH>
						<TH>
							Description
						</TH>
						<TH>
							Units
						</TH>
						<TH>
							Qty Returned
						</TH>
					</TR>
QQQ;
		$rows = getAllRows('projectMaterial', $subconOrderNumber, 'dbSubconOrder');
		//print row info for each item
		foreach ($rows as $row) {
			echo <<< QQQ
					<TR>
						<TD ALIGN="center">
							$row[dbPartNumber]
						</TD>
						<TD ALIGN="center">
							$row[dbJobNumber]
						</TD>
						<TD ALIGN="center">
							$row[dbQty]
						</TD>
						<TD ALIGN="center">
							$row[dbDescription]
						</TD>
						<TD ALIGN="center">
							$row[dbUnit]
						</TD>
						<TD ALIGN="center">
QQQ;
			//if the order has been received, no editing is allowed
			if ($action == 2) {
				print "$row[dbQtyReturned]";
			}
			//if not, the qty returned can be edited here
			else {
				print "<INPUT TYPE=\"text\" NAME=\"$row[dbPartNumber]-$row[dbJobNumber]\" VALUE=\"$row[dbQtyReturned]\" SIZE=\"6\">";
			}
				echo <<< QQQ
						</TD>
					</TR>
QQQ;
		}
		print "<TR><TD COLSPAN=\"5\"></TD><TD ALIGN=\"center\">\n";
		print "<INPUT TYPE=\"hidden\" NAME=\"subconOrderNumber\" VALUE=\"$subconOrderNumber\">\n";
		print "<INPUT TYPE=\"submit\" VALUE=\"Update\">\n";
		print "</TD></TR>\n";
		print "</TABLE>\n";
		if ($action) {
			print "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"receive\">";
		}
		print "</FORM>\n";
	}

	/**
	 *	Input Validation
	 *
	 *	The input validation function takes a column header and the column
	 *	value, then determines the validity of the value based on the 
	 *	header.  This function is specific to the subcontractor table.
	 *
	 *	@param string $columnName Column header for the value
	 *	@param string $columnValue Column value for the header
	 *	@return int 1 if the value is valid, 0 otherwise
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
