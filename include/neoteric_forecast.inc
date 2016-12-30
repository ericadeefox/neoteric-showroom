<?php
  
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $subconOrderNumber Order number for subcontractor shipment
 * @return string Data to be displayed in the menu
 */
 function menu($forecastNumber = '') {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../forecasting/forecast.php?forecastNumber=$forecastNumber">Parts</A><br>
			<A HREF="../forecasting/forecast.php?forecastNumber=$forecastNumber&action=viewPicklist">Picklist</A><br>
			<A HREF="../forecasting/forecastOrder.php?forecastNumber=$forecastNumber">Order</A><br>
			<A HREF="../forecasting/forecastSearch.php?forecastNumber=$forecastNumber">Search</A><br>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}

	/**
	 *	Print Parts
	 *
	 *	The print parts function prints information for all parts stored 
	 *	in the database for the given project number.  
	 * 
	 *	@param int $projectNumber Project number for this project
	 */
	function printParts($forecastNumber){
		$qry = "SELECT * FROM forecastParts WHERE dbForecastNumber = $forecastNumber";
		$other = $result = mysql_query($qry) or die("Error in printParts: $qry " . mysql_error());
?>
		<FORM METHOD="get" ACTION="forecast.php">
		<INPUT TYPE="HIDDEN" NAME="action" VALUE="editPart">
		<INPUT TYPE="HIDDEN" NAME="forecastNumber" VALUE="<?php echo $forecastNumber;?>">
<?php
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$row[dbPartNumber]</TD>
				<TD>$row[dbQty]</TD>
				<TD>$row[dbPartDescription]</TD>
				<TD><INPUT TYPE="radio" VALUE="$row[dbPartKey]" NAME="partKey"></TD>
				</TR>
QQQ;
		}
		if (mysql_num_rows($other) > 0) {
			print "<TR><TD COLSPAN=\"4\" ALIGN=\"right\"><INPUT TYPE=\"submit\" VALUE=\"Edit\"></TD></TR>";
		}
?>
		</FORM>
		</TABLE>
<?php

		echo <<< QQQ
			<P>
			<FORM METHOD="get" ACTION="forecastAlgorithm.php">
				<INPUT TYPE="hidden" NAME="forecastNumber" VALUE="$forecastNumber">
				<INPUT TYPE="submit" VALUE="Execute Forecast">
			</FORM>
QQQ;
	}

	/**
	 *	Form to Add Part
	 *
	 *	The form to add part function displays the form that allows a 
	 *	manager to add a part to a project
	 * 
	 *	@param int $partNumber Part number - for the first form entry
	 *	@param double $qty Quantity - for the second form entry
	 *	@param int $projectNumber Project number for this project
	 */
	function formToAddPart($partNumber, $qty, $forecastNumber){
		global $action;
		//form to add a new part
		echo <<< QQQ
			<TR>
			<FORM ACTION="forecast.php">
			<TD><INPUT TYPE="text" NAME="partNumber" VALUE="$partNumber"></TD>
			<TD><INPUT TYPE="text" NAME="qty" VALUE="$qty"></TD>
			<INPUT TYPE="hidden" NAME="action" VALUE="addPart">
			<INPUT TYPE="hidden" NAME="forecastNumber" VALUE="$forecastNumber">
			<TD COLSPAN="2" ALIGN="right"><INPUT TYPE="submit" VALUE="Add Part"></TD>
			</TR>
			</FORM>
QQQ;
		printParts($forecastNumber);
		print "</TABLE>\n"; 
		return;
	}

	/**
	 *	Head
	 *
	 *	The head function prints title information for the main table 
	 *	of the editProject function.  This function then in turn calls
	 *	the formToAddPart function to continue building the table.
	 * 
	 *	@param int $partNumber Part number to be passed along
	 *	@param double $qty Quantity to be passed along
	 *	@param int $projectNumber Project number to be passed along
	 */
	function head($partNumber, $qty, $forecastNumber) {
?>
		<TABLE BORDER="1">
		<TR>
		<TD><STRONG>Part Number</STRONG></TD>
		<TD><STRONG>Quantity</STRONG></TD>
		<TD><STRONG>Description</STRONG></TD>
		<TD><STRONG>Edit</STRONG></TD>
		</TR>
<?php
		formToAddPart($partNumber, $qty, $forecastNumber);
	}

	/**
	 *	Edit Forecast
	 *
	 *	The edit project function continues an existing project by 
	 *	sending the appropriate project number to the head function.
	 * 
	 *	@param int $projectNumber Project number for this project
	 */
	function editForecast($forecastNumber) {
		//check that the project exists
		if (!($top = getValue('dbForecastNumber', 'forecastMaster', $forecastNumber, 'dbForecastNumber'))) {
			print "<STRONG>Error: Forecast $forecastNumber does not exist</STRONG><BR>";
			exit();
		}
		head('', '', $forecastNumber);
	}


	/**
	 *	Add Part
	 *
	 *	The add part function takes the given part and qty, parses them 
	 *	for errors, and adds the record to the database.  Function 
	 *	accesses the inventoryStock and projectParts tables.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param double $qty Quantity for this part
	 *	@param int $partNumber Part number to add to project
	 */
	function addPart($forecastNumber, $qty, $partNumber, $partDescription) {
		//check that we have valid amounts
		if( ($partNumber < 1) && ($qty <= 0)) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			head("", "", $forecastNumber);
			exit();
		}
		if($partNumber < 1) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			head("", $qty, $forecastNumber);
			exit();
		}
		if($qty <= 0) {
			print "<STRONG>Error: Quantity < 0</STRONG><br>\n";
			head($partNumber, "", $forecastNumber);
			exit();
		}
		//check that the part actually exists
		if ( !($tmpRow = getValue("dbPartNumber", "inventoryStock", $partNumber, "dbPartNumber"))) {
			print "<STRONG>Error: part not found</STRONG><br>\n";
			head("", $qty, $forecastNumber);
			exit();
		}
		
		//get the next part key
		$partKey = getLastKey("forecastParts", "dbPartKey", "dbPartKey");
		$partKey++;
		$description = modifyDescription($partDescription);
		query("INSERT INTO forecastParts VALUES (\"$partNumber\", \"$qty\", \"$forecastNumber\", \"$partKey\", \"$description\")", "neotericForecast|addPart");
		head("", "", $forecastNumber);
	} 

	/**
	 *	Edit Part
	 *
	 *	The edit part function places the project in "edit mode", 
	 *	allowing the user to change the part number, quantity, or remove 
	 *	a part altogether.  Note this option is only enabled for one 
	 *	part at a time.  Function accesses the projectParts table.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param int $partKey Primary key of the projectParts table
	 */
	function editPart($forecastNumber, $partKey) {
		//check that we have valid amounts
		if(strcmp($partKey, "") == 0) {
			print "<STRONG>Error:  Select part</STRONG><br>";
			head("", "", $forecastNumber);
			exit();
		}

		//get the entry to edit
		$query = getRow("forecastParts", "$partKey", "dbPartKey", "$forecastNumber");
		//display the row to edit
		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><b>Part Number</b></TD>
			<TD><b>Quantity</b></TD>
			<TD><b>Description</b></TD>
			</TR>

			<TR>
			<FORM ACTION="forecast.php">
			<INPUT TYPE="hidden" NAME="action" VALUE="editPartUpdate">
			<INPUT TYPE="hidden" NAME="forecastNumber" VALUE="$forecastNumber">
			<INPUT TYPE="hidden" NAME="partKey" VALUE="$partKey">
			<TD><INPUT TYPE="text" VALUE="$query[dbPartNumber]" NAME="partNumber"></TD>
			<TD><INPUT TYPE="text" VALUE="$query[dbQty]" NAME="qty"></TD>
			<TD>$query[dbPartDescription]</TD>
			</TR>
			<TR>
			<TD COLSPAN="4" ALIGN="right">
			<INPUT TYPE="submit" VALUE="Update">
			</TD>
			</TR>
			</FORM>
		
			<TR>
			<TD COLSPAN="4" ALIGN="left">
			<FORM action="forecast.php">
			<INPUT TYPE="hidden" NAME="action" VALUE="editPartRemove">
			<INPUT TYPE="hidden" NAME="forecastNumber" VALUE="$forecastNumber">
			<INPUT TYPE="hidden" NAME="partKey" VALUE="$partKey">
			<INPUT TYPE="submit" VALUE="Remove">
			</FORM>
			</TD>
			</TR>
			</TABLE>
QQQ;
	}

	/**
	 *	Edit Part Update
	 *
	 *	The edit part update function performs error checking on the data 
	 *	entered, then updates the appropriate table with the new information.
	 *	This function accesses the projectParts table.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param double $qty Quantity of this part
	 *	@param int $partKey primary key of the projectParts table
	 *	@param int $partNumber Part number to update
	 */
	function editPartUpdate($forecastNumber, $qty, $partKey, $partNumber) {
		//qty must be non-negative, and can be a double
		//part number must be a positive integer
		if( ($partNumber < 1) && ($qty <= 0)) {
			print "<STRONG>Error:  Part < 0</STRONG><br>";
			head("", "", $forecastNumber);
			exit();
		}
		if($partNumber < 1) {
			print "<STRONG>Error:  Part < 0</STRONG><br>";
			head("", "", $forecastNumber);
			exit();
		}
		if($qty <= 0) {
			print "<STRONG>Error: Quantity < 0</STRONG><br>";
			head("", "", $forecastNumber);
			exit();
		}
		//check that the part actually exists
		if ( !($tmpRow = getValue( "dbPartNumber", "inventoryStock", $partNumber, "dbPartNumber"))) {
			print "<STRONG>Error: part not found</STRONG><br>\n";
			head("", $qty, $forecastNumber);
			exit();
		}
		
		//get the description of the part
		$partDescription = getValue("dbDescription", "inventoryStock", $partNumber, "dbPartNumber");
		//add the record
		query("UPDATE forecastParts SET dbQty = $qty, dbPartNumber = $partNumber, dbPartDescription = \"$partDescription\" WHERE dbPartKey = $partKey", "forecast|editPartUpdate");
		head("", "", $forecastNumber);
	}

	/**
	 *	Edit Part Remove
	 *
	 *	The edit part remove function executes the query that removes the 
	 *	given part from the table.  This function accesses the projectParts 
	 *	table.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param int $partKey Primary key of the projectParts table
	 */
	function editPartRemove($forecastNumber, $partKey) {
		//remove the record
		query("DELETE FROM forecastParts WHERE dbPartKey = $partKey", "forecast|editPartRemove");
		head("", "", $forecastNumber);
	}

	/**
	 *	View Picklist
	 *
	 *	The view exploded picklist function queries the database to find
	 *	all parts that are needed to complete a given project.  This data 
	 *	is displayed and may be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewPicklist($sortBy, $forecastNumber) {
		/*	sortBy variable:
		 *
		 *	Default:	Part Number
		 *	1:			Qty
		 *	2:			QtyInStock
		 *	3:			QtyInVBs
		 */
		if ($sortBy == 1) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbQtyNeeded ASC";
		}
		else if ($sortBy == 2) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbUnit ASC";
		}
		else if ($sortBy == 3) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbQtyInStock ASC";
		}
		else if ($sortBy == 4) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY (dbQtyNeeded - dbQtyInStock) DESC";
		}
		else if ($sortBy == 5) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbPartDescription ASC";
		}
		else if ($sortBy == 6) {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbQtyInVBs DESC";
		}
		else {
			$qry = "SELECT * FROM forecastVirtualBins WHERE dbForecastNumber = $forecastNumber ORDER BY dbPartNumber ASC";
		}

		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, $qry<br>" . mysql_error());
		//print "adding part# $partNumber to project# $projectNumber with qty=$quantity and desc=$partDescription<br>";

		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber">Part #</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=1">Qty Needed</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=2">Unit</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=3">Qty in Stock</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=4">Difference</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=5">Description</A></TD>
			<TD><A HREF="../forecasting/forecast.php?action=viewPicklist&forecastNumber=$forecastNumber&sortBy=6">Qty in Virtual Bins</A></TD>
			</TR>
QQQ;
		$count = 0;
		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$diff = $tmpCol['dbQtyNeeded'] - $tmpCol['dbQtyInStock'];
			echo <<<QQQ
				<TR>
				<TD>$tmpCol[dbPartNumber]</TD>
				<TD>$tmpCol[dbQtyNeeded]</TD>
				<TD>$tmpCol[dbUnit]</TD>
				<TD>$tmpCol[dbQtyInStock]</TD>
				<TH>$diff</TH>
				<TD>$tmpCol[dbPartDescription]</TD>
				<TD>$tmpCol[dbQtyInVBs]</TD>
				</TR>
QQQ;
			$count++;
		}
		print "</TABLE>\n";
		print "Total: $count<p>";
	} 

	/**
	 *	Add Forecast
	 *
	 *	The add project function enters a new record in the master table 
	 *	for a project.  Project number is the next sequentially available, 
	 *	as passed in.  Function accesses the projectMaster table.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param string $projectDescription Description of the project
	 *	@param string $dateNeo Date of project creation
	 */
	function addForecast($forecastNumber, $forecastDescription, $dateNeo) {
		$forecastDescription = modifyDescription($forecastDescription);
		query("INSERT INTO forecastMaster VALUES(\"$forecastNumber\", \"$dateNeo\", \"\", \"$projectDescription\")", "addForecast");
		print "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/forecast.php?forecastNumber=$forecastNumber&action=editForecast\">";
	}

	/**
	 *	Edit Forecast Description
	 *	
	 *	The edit project description function allows the user to edit 
	 *	the description of the current project.
	 *
	 *	@param int $projectNumber Project number for this project
	 */
	function editForecastDescription ($forecastNumber) {
		if ($top = getRow('forecastMaster', $forecastNumber, 'dbForecastNumber', 'editForecastDescription')) {
			echo <<<QQQ
				<TABLE BORDER="1">
					<TR>
						<TD>
							<B>
								Forecast Number
							</B>
						</TD>
						<TD>
							<B>
								Description
							</B>
						</TD>
					</TR>

					<FORM ACTION="forecast.php">
						<TR>
							<INPUT TYPE="hidden" NAME="action" VALUE="editForecastDescUpdate">
							<INPUT TYPE="hidden" NAME="forecastNumber" VALUE="$forecastNumber">
							<TD>
								<STRONG>
									$top[dbForecastNumber]
								</STRONG>
							</TD>
							<TD>
							<TEXTAREA NAME="description" COLS=40 ROWS=2 MAXLENGTH=150>$top[dbDescription]</TEXTAREA>
							</TD>
						</TR>
						<TR>
							<TD COLSPAN="4" ALIGN="right">
								<INPUT TYPE="submit" VALUE="Edit">
							</TD>
						</TR>
					</FORM>
				</TABLE>
QQQ;
		}
	}

	/**
	 *	Edit Forecast Description Update
	 *
	 *	The edit project description update function writes a revised 
	 *	project description to the database for a given project number.  
	 *	Basic error checking and data validation is performed.
	 *
	 *	@param int $projectNumber Project number to modify
	 *	@param string $projectDescription Modified description
	 */
	function editForecastDescUpdate ($forecastNumber, $forecastDescription) {
		$forecastDecription = modifyDescription($forecastDescription);
		query("UPDATE forecastMaster SET dbDescription = \"$forecastDescription\" WHERE dbForecastNumber = $forecastNumber", "editForecastDescUpdate");
		print "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/forecast.php?forecastNumber=$forecastNumber&action=editForecast\">";
	  }






?>