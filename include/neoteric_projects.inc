<?php
  
 /**
 * Neoteric Projects
 *
 * This INC file contains library functions used in the projects
 * module.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 * @file   neoteric_projects.inc
 * @date   26-Jun-2003
 */
  
  
 /*************************** Functions ****************************/
  
 /**
 * Links
 *
 * The links function displays links to all areas of the projects module
 *
 * @param int $projectNumber Project number for this project
 */
 function links($projectNumber) {
  echo <<<QQQ
			<P>
			<table><tr><td>
			<FORM ACTION="generateJobs.php">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="submit" VALUE="Generate Jobs" WIDTH="40">
			</FORM>
			</td><td>
			<FORM ACTION="theoreticalTrial.php">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="submit" VALUE="Theoretical Trial" WIDTH="40">
			</FORM>
			</td><td>
			<FORM ACTION="sendToSubcontractor.php">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="submit" VALUE="Send to Subcontractor" SIZE="40">
			</FORM>
			</td><td>
			<FORM ACTION="voidProjectConfirm.php">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="submit" VALUE="Void Project" SIZE="40">
			</FORM>
			</td><td>
			<FORM ACTION="completeProjectPreprocess.php">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="submit" VALUE="Complete Project" SIZE="40">
			</FORM>
			</td></tr></table>
QQQ;
		//code for the generate jobs button
		return;
	}

	/**
	 *	Menu
	 *
	 *	The menu function generates a menu to use with the template
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@return string Data to be displayed in the menu
	 */

	function menu($projectNumber) {
		//Menu links go here
		$file = basename($_SERVER['PHP_SELF']);
		if ($projectNumber) {
			$output = <<<QQQ
				<A HREF="../project/editProject.php?action=editProject&projectNumber=$projectNumber">View Parts</A><BR>
				<A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber">View Jobs</A><br>
				<A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber">View Exploded Picklist</A><br>
				<A HREF="../project/editProject.php?action=viewOrderList&projectNumber=$projectNumber">View Order List</A><br>
				<A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber">View Material List</A><br>
				<A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber">View Subcontractor List</A><br>
				<A HREF="../project/editProject.php?action=batch&projectNumber=$projectNumber">Batch Job</A><br>
				<A HREF="../project/projectSearch.php?projectNumber=$projectNumber">Search</A><br>
				<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		}
		else {
			$output = <<<QQQ
				<A HREF="../project/projectSearch.php?projectNumber=$projectNumber">Search</A><br>
				<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		}
		return $output;
	}
	

	/**
	 *	Footer
	 *
	 *	The footer function displays links back to the main project page
	 *	and the page for the specified project.
	 * 
	 *	@param int $projectNumber Project number for this project
	 */
	function footer($projectNumber) {
/*		print "<A HREF=\"../project.php\">Back to Projects</A>&nbsp;&nbsp;&nbsp;";
		print "<A HREF=\"../project/editProject.php?action=editProject&projectNumber=$projectNumber\">Return to Project# $projectNumber</A>&nbsp;&nbsp;&nbsp;";
*/
		exit();
	}

	/**
	 *	Print Parts
	 *
	 *	The print parts function prints information for all parts stored 
	 *	in the database for the given project number.  
	 * 
	 *	@param int $projectNumber Project number for this project
	 */
	function printParts($projectNumber){
		$other = $result = mysql_query("SELECT * FROM projectParts WHERE dbProjectNumber = $projectNumber") or die("Error in printParts: " . mysql_error());
?>
		<FORM METHOD="get" ACTION="editProject.php">
		<INPUT TYPE="HIDDEN" NAME="action" VALUE="editPart">
		<INPUT TYPE="HIDDEN" NAME="projectNumber" VALUE="<?php echo $projectNumber;?>">
<?php
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$row[dbPartNumber]</TD>
				<TD>$row[dbQty]</TD>
				<TD>$row[dbDescription]</TD>
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
		links($projectNumber);
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
	function formToAddPart($partNumber, $qty, $projectNumber){
		global $action;
		//form to add a new part
		echo <<< QQQ
			<TR>
			<FORM ACTION="editProject.php">
			<TD><INPUT TYPE="text" NAME="partNumber" VALUE="$partNumber"></TD>
			<TD><INPUT TYPE="text" NAME="qty" VALUE="$qty"></TD>
			<INPUT TYPE="hidden" NAME="action" VALUE="addPart">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<TD COLSPAN="2" ALIGN="right"><INPUT TYPE="submit" VALUE="Add Part"></TD>
			</TR>
			</FORM>
QQQ;
		printParts($projectNumber);
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
	function head($partNumber, $qty, $projectNumber) {
?>
		<TABLE BORDER="1">
		<TR>
		<TD><STRONG>Part Number</STRONG></TD>
		<TD><STRONG>Quantity</STRONG></TD>
		<TD><STRONG>Description</STRONG></TD>
		<TD><STRONG>Edit</STRONG></TD>
		</TR>
<?php
		formToAddPart($partNumber, $qty, $projectNumber);
	}

	/**
	 *	View Subcontractor List
	 *
	 *	The view subcontractor list function queries the database to 
	 *	find all subcontractor information for a given project.  This 
	 *	data is displayed and may be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewSubcontractorList($sortBy, $projectNumber) {
		/*	sortBy variable:
		 *
		 *	Default:	Part Number
		 *	1:			Job Number
		 *	2:			Description
		 *	3:			Qty
		 *	4:			Unit
		 *	5:			Subcontractor
		 */
		if ($sortBy == 1) {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbJobNumber ASC";
		} else if ($sortBy == 2) {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbDescription ASC";
		} else if ($sortBy == 3) {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbQty ASC";
		} else if ($sortBy == 4) {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbUnit ASC";
		} else if ($sortBy == 5) {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbSubcontractor ASC";
		} else {
			$qry = "SELECT * FROM projectSubcon WHERE dbProjectNumber = $projectNumber ORDER BY dbPartNumber ASC";
		}
		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, ($qry)<br>" . mysql_error());
		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber&sortBy=1">Job #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber">Part #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber&sortBy=2">Description</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber&sortBy=3">Qty</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber&sortBy=4">Unit</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewSubcontractorList&projectNumber=$projectNumber&sortBy=5">Subcontractor</A></TD>
			</TR>
QQQ;
		$count = 0;
		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$tmpCol[dbJobNumber]</TD>
				<TD>$tmpCol[dbPartNumber]</TD>
				<TD>$tmpCol[dbDescription]</TD>
				<TD>$tmpCol[dbQty]</TD>
				<TD>$tmpCol[dbUnit]</TD>
				<TD>$tmpCol[dbSubcontractor]</TD>
				</TR>
QQQ;
			if (!isset($subcontractor)) {
				$subcontractor = $tmpCol['dbSubcontractor'];
			}
			$count++;
		}
		print '</TABLE>';
		print "Total: $count<p>";
		//if a subcontractor has been selected, print the subcontractor info below the table
		if ($subcontractor) {
			$row = getRow('subcontractor', $subcontractor, 'dbSubcontractor', 'viewSubcontractorList');
			echo <<< QQQ
				<TABLE BORDER="2" CELLSPACING="0">
					<TR>
						<TH>
							Subcontractor Number
						</TH>
						<TH>
							Company
						</TH>
						<TH>
							Address
						</TH>
						<TH>
							City
						</TH>
						<TH>
							State
						</TH>
						<TH>
							Zip
						</TH>
						<TH>
							Country
						</TH>
					</TR>

					<TR>
						<TD>
							$row[dbSubcontractor]&nbsp;
						</TD>
						<TD>
							$row[dbCompany]&nbsp;
						</TD>
						<TD>
							$row[dbAddress]&nbsp;
						</TD>
						<TD>
							$row[dbCity]&nbsp;
						</TD>
						<TD>
							$row[dbState]&nbsp;
						</TD>
						<TD>
							$row[dbZip]&nbsp;
						</TD>
						<TD>
							$row[dbCountry]&nbsp;
						</TD>
					</TR>

					<TR>
						<TH>
							Attention
						</TH>
						<TH>
							Account
						</TH>
						<TH>
							Terms
						</TH>
						<TH>
							Phone
						</TH>
						<TH>
							Fax
						</TH>
						<TH>
							Supplies
						</TH>
						<TH>
							Email
						</TH>
					</TR>

					</TR>
						<TD>
							$row[dbAttention]&nbsp;
						</TD>
						<TD>
							$row[dbAccount]&nbsp;
						</TD>
						<TD>
							$row[dbTerms]&nbsp;
						</TD>
						<TD>
							$row[dbPhone]&nbsp;
						</TD>
						<TD>
							$row[dbFax]&nbsp;
						</TD>
						<TD>
							$row[dbSupplies]&nbsp;
						</TD>
						<TD>
							$row[dbEmail]&nbsp;
						</TD>
					</TR>

					<TR>
						<TH>
							Second Phone
						</TH>
						<TH>
							Second Fax
						</TH>
						<TH>
							Website
						</TH>
					</TR>
					
					<TR>
						<TD>
							$row[dbSecondPhone]&nbsp;
						</TD>
						<TD>
							$row[dbSecondFax]&nbsp;
						</TD>
						<TD>
							$row[dbWebsite]&nbsp;
						</TD>
					</TR>
				</TABLE>
QQQ;
		}
		footer($projectNumber);
	}

	/**
	 *	View Material List
	 *
	 *	The view material list function queries the database to find 
	 *	all material information for a given project.  This data is 
	 *	displayed and may be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewMaterialList($sortBy, $projectNumber) {
		/*	sortBy variable:
		 *
		 *	Default:	Part Number
		 *	1:			Qty
		 *	2:			Unit
		 *	3:			Description
		 *	4:			Job Number
		 *	5:			Subcon Order
		 */
		if ($sortBy == 1) {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbQty ASC";
		} else if ($sortBy == 2) {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbUnit ASC";
		} else if ($sortBy == 3) {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbDescription ASC";
		} else if ($sortBy == 4) {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbJobNumber ASC";
		} else if ($sortBy == 5) {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbSubconOrder ASC";
		} else {
			$qry = "SELECT * FROM projectMaterial WHERE dbProjectNumber = $projectNumber ORDER BY dbPartNumber ASC";
		}

		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, $qry<br>" . mysql_error());

		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber">Part #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber&sortBy=1">Qty</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber&sortBy=2">Unit</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber&sortBy=3">Description</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber&sortBy=4">Job #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewMaterialList&projectNumber=$projectNumber&sortBy=5">Subcontractor</A></TD>
			</TR>
QQQ;
		$count = 0;
		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$tmpCol[dbPartNumber]</TD>
				<TD>$tmpCol[dbQty]</TD>
				<TD>$tmpCol[dbUnit]</TD>
				<TD>$tmpCol[dbDescription]</TD>
				<TD>$tmpCol[dbJobNumber]</TD>
				<TD>$tmpCol[dbSubconOrder]</TD>
				</TR>
QQQ;
			$count++;
		}
		print "</TABLE>\n";
		print "Total: $count<p>";
		footer($projectNumber);
	} 

	/**
	 *	View Order List
	 *
	 *	The view order list function queries the database to find all 
	 *	parts that need to be ordered for a given project.  This data 
	 *	is displayed and may be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewOrderList($sortBy, $projectNumber) {		
		/*	sortBy variable:
		 *
		 *	Default:	Part Number
		 *	1:			Job Number
		 *	2:			Qty
		 *	3:			Description
		 */
		if ($sortBy == 1) {
			$qry = "SELECT * FROM projectOrderList WHERE dbProjectNumber = $projectNumber ORDER BY dbJobNumber ASC";
		} else if ($sortBy == 2) {
			$qry = "SELECT * FROM projectOrderList WHERE dbProjectNumber = $projectNumber ORDER BY dbQty ASC";
		} else if ($sortBy == 3) {
			$qry = "SELECT * FROM projectOrderList WHERE dbProjectNumber = $projectNumber ORDER BY dbDescription ASC";
		} else {
			$qry = "SELECT * FROM projectOrderList WHERE dbProjectNumber = $projectNumber ORDER BY dbPartNumber ASC";
		}

		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, $qry<br>" . mysql_error());

		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><A HREF="../project/editProject.php?action=viewOrderList&projectNumber=$projectNumber&sortBy=1">Job #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewOrderList&projectNumber=$projectNumber&sortBy=2">Qty</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewOrderList&projectNumber=$projectNumber">Part #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewOrderList&projectNumber=$projectNumber&sortBy=3">Description</A></TD>
			</TR>
QQQ;
		$count = 0;
		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$tmpCol[dbJobNumber]</TD>
				<TD>$tmpCol[dbQty]</TD>
				<TD>$tmpCol[dbPartNumber]</TD>
				<TD>$tmpCol[dbDescription]</TD>
				<TD>$tmpCol[dbBin]</TD>
				</TR>
QQQ;
			$count++;
		}
		print "</TABLE>\n";
		print "Total: $count<p>";
		footer($projectNumber);
	} 

	/**
	 *	View Exploded Picklist
	 *
	 *	The view exploded picklist function queries the database to find
	 *	all parts that are needed to complete a given project.  This data 
	 *	is displayed and may be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewExplodedPicklist($sortBy, $projectNumber) {
		/*	sortBy variable:
		 *
		 *	Default:	Part Number
		 *	1:			Job Number
		 *	2:			Qty
		 *	3:			Unit
		 *	4:			Description
		 *	5:			Bin
		 *	6:			Location
		 */
		if ($sortBy == 1) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbJobNumber ASC";
		} else if ($sortBy == 2) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbQty ASC";
		} else if ($sortBy == 3) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbUnit ASC";
		} else if ($sortBy == 4) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbDescription ASC";
		} else if ($sortBy == 5) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbBin ASC";
		} else if ($sortBy == 6) {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbLocation ASC";
		} else {
			$qry = "SELECT * FROM inventoryVirtualBins WHERE dbProjectNumber = $projectNumber ORDER BY dbPartNumber ASC";
		}

		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, $qry<br>" . mysql_error());
		//print "adding part# $partNumber to project# $projectNumber with qty=$quantity and desc=$partDescription<br>";

		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=1">Job #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber">Part #</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=2">Qty</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=3">Unit</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=4">Description</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=5">Bin</A></TD>
			<TD><A HREF="../project/editProject.php?action=viewExplodedPicklist&projectNumber=$projectNumber&sortBy=6">Location</A></TD>
			</TR>
QQQ;
		$count = 0;
		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo <<<QQQ
				<TR>
				<TD>$tmpCol[dbJobNumber]</TD>
				<TD>$tmpCol[dbPartNumber]</TD>
				<TD>$tmpCol[dbQty]</TD>
				<TD>$tmpCol[dbUnit]</TD>
				<TD>$tmpCol[dbDescription]</TD>
				<TD>$tmpCol[dbBin]</TD>
				<TD>$tmpCol[dbLocation]</TD>
				</TR>
QQQ;
			$count++;
		}
		print "</TABLE>\n";
		print "Total: $count<p>";
		footer($projectNumber);
	} 

	/**
	 *	View Jobs
	 *
	 *	The view jobs function queries the database to find all jobs 
	 *	associated a given project.  This data is displayed and may 
	 *	be sorted by the user.
	 * 
	 *	@param int $sortBy Flag to determine on which column to sort
	 *	@param int $projectNumber Project number for this project
	 */
	function viewJobs($sortBy, $projectNumber) {
//		/*	sortBy variable:
//		 *
//		 *	Default:	Part Number
//		 *	1:			Job Number
//		 *	2:			Parent Job Number
//		 *	3:			Qty
//		 *	4:			Description
//		 *	5:			Assy Hours
//		 *	6:			Fab Hours
//		 *	7:			Sub Cost
//		 */
//		if ($sortBy == 1) {
//			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbJobNumber ASC";
//		} else if ($sortBy == 2) {
//			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbParentJobNumber ASC";
//		} else if ($sortBy == 3) {
//			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbQty ASC";
//		} else if ($sortBy == 4) {
//			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbDescription ASC";
////		} else if ($sortBy == 5) {
////			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbAssyHours ASC";
////		} else if ($sortBy == 6) {
////			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbFabHours ASC";
////		} else if ($sortBy == 7) {
////			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbSubCost ASC";
//		} else {
//			$qry = "SELECT * FROM projectSubJobs WHERE dbProjectNumber = $projectNumber ORDER BY dbPartNumber ASC";
//		}
//		$result = mysql_query($qry) or die("ERROR: Unable to perform MySQL query, ($qry)<br>" . mysql_error());
//
//		echo <<<QQQ
//			<TABLE BORDER="1">
//			<TR>
//			<TD>&nbsp;</TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=1">Job #</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=2">Parent Job #</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber">Part #</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=3">Qty</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=4">Description</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=5">Assembly Hours</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=6">Fabrication Hours</A></TD>
//			<TD><A HREF="../project/editProject.php?action=viewJobs&projectNumber=$projectNumber&sortBy=7">Subcontractor Cost</A></TD>
//			</TR>
//QQQ;
//		$count = 0;
//		while ($tmpCol = mysql_fetch_array($result, MYSQL_ASSOC)) {
//			print "<TR>";
//			if ($tmpCol['dbQtyCompleted'] == 0) {
//				print "<TD><A HREF=\"completeJobPreprocess.php?projectNumber=$projectNumber&jobNumber=$tmpCol[dbJobNumber]\">Complete</A></TD>";
//			}
//			else {
//				print "<TD>&nbsp;</TD>";
//			}
//			echo <<<QQQ
//				<TD>$tmpCol[dbJobNumber]</TD>
//				<TD>$tmpCol[dbParentJobNumber]</TD>
//				<TD>$tmpCol[dbPartNumber]</TD>
//				<TD>$tmpCol[dbQty]</TD>
//				<TD>$tmpCol[dbDescription]</TD>
//				<TD>$tmpCol[dbAssyHours]</TD>
//				<TD>$tmpCol[dbFabHours]</TD>
//				<TD>$tmpCol[dbSubCost]</TD>
//				</TR>
//QQQ;
//			$count++;
//		}
//		print "</TABLE>\n";
//		print "Total: $count<p>";
//		footer($projectNumber);
//	}
//
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
	function editPartRemove($projectNumber, $partKey) {
		//remove the record
		query("DELETE FROM projectParts WHERE dbPartKey = $partKey", "neotericProject|editPartRemove");
		head("", "", $projectNumber);
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
	function editPartUpdate($projectNumber, $qty, $partKey, $partNumber) {
		//qty must be non-negative, and can be a double
		//part number must be a positive integer
		if( ($partNumber < 1) && ($qty <= 0)) {
			print "<STRONG>Error:  Part < 0</STRONG><br>";
			head("", "", $projectNumber);
			exit();
		}
		if($partNumber < 1) {
			print "<STRONG>Error:  Part < 0</STRONG><br>";
			head("", "", $projectNumber);
			exit();
		}
		if($qty <= 0) {
			print "<STRONG>Error: Quantity < 0</STRONG><br>";
			head("", "", $projectNumber);
			exit();
		}
		//check that the part actually exists
		if ( !($tmpRow = getValue( "dbPartNumber", "inventoryStock", $partNumber,"dbPartNumber"))) {
			print "<STRONG>Error: part not found</STRONG><br>\n";
			head("", $qty, $projectNumber);
			exit();
		}
		
		//get the description of the part
		$partDescription = getValue("dbDescription", "inventoryStock", $partNumber, "dbPartNumber");
		//add the record
		query("UPDATE projectParts SET dbQty = $qty, dbPartNumber = $partNumber, dbDescription = \"$partDescription\" WHERE dbPartKey = $partKey", "neotericProject|editPartUpdate");
		head("", "", $projectNumber);
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
	function editPart($projectNumber, $partKey) {
		//check that we have valid amounts
		if(strcmp($partKey, "") == 0) {
			print "<STRONG>Error:  Select part</STRONG><br>";
			head("", "", $projectNumber);
			exit();
		}
		//check that the project is not voided or completed
		if (getValue("dbCompleted","projectMaster", $projectNumber, "dbProjectNumber") || 								getValue("dbVoided","projectMaster", $projectNumber, "dbProjectNumber")) {
			print "<STRONG>Error:  project completed or voided</STRONG><BR>";
			head("", "", $projectNumber);
			exit();
		}

		//get the entry to edit
		$query = getRow("projectParts", "$partKey", "dbPartKey", "$projectNumber");
		//display the row to edit
		echo <<<QQQ
			<TABLE BORDER="1">
			<TR>
			<TD><b>Part Number</b></TD>
			<TD><b>Quantity</b></TD>
			<TD><b>Description</b></TD>
			</TR>

			<TR>
			<FORM ACTION="editProject.php">
			<INPUT TYPE="hidden" NAME="action" VALUE="editPartUpdate">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="hidden" NAME="partKey" VALUE="$partKey">
			<TD><INPUT TYPE="text" VALUE="$query[dbPartNumber]" NAME="partNumber"></TD>
			<TD><INPUT TYPE="text" VALUE="$query[dbQty]" NAME="qty"></TD>
			<TD>$query[dbDescription]</TD>
			</TR>
			<TR>
			<TD COLSPAN="4" ALIGN="right">
			<INPUT TYPE="submit" VALUE="Update">
			</TD>
			</TR>
			</FORM>
		
			<TR>
			<TD COLSPAN="4" ALIGN="left">
			<FORM action="editProject.php">
			<INPUT TYPE="hidden" NAME="action" VALUE="editPartRemove">
			<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
			<INPUT TYPE="hidden" NAME="partKey" VALUE="$partKey">
			<INPUT TYPE="submit" VALUE="Remove">
			</FORM>
			</TD>
			</TR>
			</TABLE>
QQQ;
		footer($projectNumber);
	}

	/**
	 *	Batch Submit
	 *
	 *	The batch submit function adds any number of parts to the 
	 *	project simultaneously, provided they are identical.  Function 
	 *	accesses the inventoryStock and projectParts tables.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param double $qty Amount to be added to each line
	 *	@param int $partKey Primary key of the projectParts table
	 *	@param int $count Number of times to add this entry to project
	 *	@param int $partNumber Part number to add to this project
	 */
	function batchSubmit($projectNumber, $qty, $partKey, $count, $partNumber) {
		//check that we have valid amounts
		if( ($partNumber < 1) && ($qty <= 0)) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			batch($projectNumber, '', '', '');
			exit();
		}
		if($partNumber < 1) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			batch($projectNumber, '', $qty, $count);
			exit();
		}
		if($qty <= 0) {
			print "<STRONG>Error: Quantity < 0</STRONG><br>\n";
			batch($projectNumber, $partNumber, '', $count);
			exit();
		}
		if($count < 1) {
			print "<STRONG>Error: Jobs to add < 1</STRONG><br>\n";
			batch($projectNumber, $partNumber, $qty, '');
			exit();
		}
		if(!ereg("^[0-9]+$",$count) || !ereg("^[0-9]+$", $partNumber)) {
			print "<STRONG>Error: Part, jobs not integers</STRONG><br>\n";
			batch($projectNumber, $partNumber, $qty, '');
			exit();
		}
		for ($i = 0; $i < $count; $i ++) {		
			//get the next part key
			$partKey = getLastKey("projectParts", "dbPartKey", "dbPartKey");
			$partKey++;
			//get the description of the part
			$partDescription = getValue("dbDescription", "inventoryStock", $partNumber, "dbPartNumber");
			//add the record
			query("INSERT INTO projectParts VALUES(\"$partKey\", \"$projectNumber\", \"$partNumber\", 	\"$qty\", \"0\", \"0\", \"$partDescription\")", "neotericProject|batchSubmit");
		}
		head("", "", $projectNumber);
	} 

	/**
	 *	Batch
	 *
	 *	The batch function accepts a part number, quantity, and iteration 
	 *	number to speed up repetitive entries to a project.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param double $qty Amount to be added to each line
	 *	@param int $count Number of times to add this entry to project
	 *	@param int $partNumber Part number to add to this project
	 */
	function batch($projectNumber, $partNumber = '', $qty = '', $count = '') {
		//check that the project is not voided or completed
		if (getValue("dbCompleted","projectMaster", $projectNumber, "dbProjectNumber") || 								getValue("dbVoided","projectMaster", $projectNumber, "dbProjectNumber")) {
			print "<STRONG>Error:  Project completed or voided</STRONG><BR>";
			head("", "", $projectNumber);
			exit();
		}
		//get the part number and quantity
		echo <<<QQQ
			<TABLE>
				<FORM action="editProject.php">
					<INPUT TYPE="hidden" NAME="action" VALUE="batchSubmit">
					<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
					<TR>
						<TD>
							Part Number
						</TD>
						<TD>
							<INPUT TYPE="text" NAME="partNumber" VALUE=$partNumber>
						</TD>
					</TR>
					<TR>
						<TD>
							Quantity
						</TD>
						<TD>
							<INPUT TYPE="text" NAME="qty" VALUE=$qty>
						</TD>
					</TR>
					<TR>
						<TD>
							Total Jobs
						</TD>
						<TD>
							<INPUT TYPE="text" NAME="count" VALUE=$count>
						</TD>
					</TR>
					<TR>
						<TD COLSPAN=2>
							<INPUT TYPE="submit" VALUE="Add Parts">
						</TD>
					</TR>
				</FORM>
			</TABLE>
QQQ;
		footer($projectNumber);
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
	function addPart($projectNumber, $qty, $partNumber, $partDescription) {
		//check that we have valid amounts
		if( ($partNumber < 1) && ($qty <= 0)) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			head("", "", $projectNumber);
			exit();
		}
		if($partNumber < 1) {
			print "<STRONG>Error:  Part < 0</STRONG><br>\n";
			head("", $qty, $projectNumber);
			exit();
		}
		if($qty <= 0) {
			print "<STRONG>Error: Quantity < 0</STRONG><br>\n";
			head($partNumber, "", $projectNumber);
			exit();
		}
		//check that the project is not voided or completed
		if (getValue("dbCompleted","projectMaster", $projectNumber, "dbProjectNumber") || 								getValue("dbVoided","projectMaster", $projectNumber, "dbProjectNumber")) {
			print "<STRONG>Error:  Project completed or voided</STRONG><BR>";
			head("", "", $projectNumber);
			exit();
		}
		//check that the part actually exists
		if ( !($tmpRow = getValue("dbPartNumber", "inventoryStock", $partNumber, "dbPartNumber"))) {
			print "<STRONG>Error: part not found</STRONG><br>\n";
			head("", $qty, $projectNumber);
			exit();
		}
		
		//get the next part key
		$partKey = getLastKey("projectParts", "dbPartKey", "dbPartKey");
		$partKey++;
		$description = modifyDescription($partDescription);
		query("INSERT INTO projectParts VALUES(\"$partKey\", \"$projectNumber\", \"$partNumber\", \"$qty\", \"0\", \"0\", \"$description\")", "neotericProject|addPart");
		head("", "", $projectNumber);
	} 

	/**
	 *	Add Project
	 *
	 *	The add project function enters a new record in the master table 
	 *	for a project.  Project number is the next sequentially available, 
	 *	as passed in.  Function accesses the projectMaster table.
	 * 
	 *	@param int $projectNumber Project number for this project
	 *	@param string $projectDescription Description of the project
	 *	@param string $dateNeo Date of project creation
	 */
	function addProject($projectNumber, $projectDescription, $dateNeo) {
		$projectDescription = modifyDescription($projectDescription);
		query("INSERT INTO projectMaster VALUES(\"$projectNumber\",\"$projectDescription\",\"$dateNeo\",\"0\",\"0\",\"0\")", "neotericProject|addProject");
		head("", "", $projectNumber);
	}

	/**
	 *	Edit Project
	 *
	 *	The edit project function continues an existing project by 
	 *	sending the appropriate project number to the head function.
	 * 
	 *	@param int $projectNumber Project number for this project
	 */
	function editProject($projectNumber) {
		//check that the project exists
		if (!($top = getValue('dbProjectNumber', 'projectMaster', $projectNumber, 'dbProjectNumber'))) {
			print '<STRONG>Error: Project does not exist</STRONG><BR>';
			print '<A HREF="../project/projectSearch.php">Search</A>&nbsp;&nbsp;&nbsp;';
			exit();
		}
		head('', '', $projectNumber);
	}

	/**
	 *	Edit Project Description
	 *	
	 *	The edit project description function allows the user to edit 
	 *	the description of the current project.
	 *
	 *	@param int $projectNumber Project number for this project
	 */
	 function editProjectDescription ($projectNumber) {
		if ($top = getRow('projectMaster', $projectNumber, 'dbProjectNumber', 'editProjectDescription')) {
			echo <<<QQQ
				<TABLE BORDER="1">
					<TR>
						<TD>
							<B>
								Project Number
							</B>
						</TD>
						<TD>
							<B>
								Description
							</B>
						</TD>
					</TR>

					<FORM ACTION="editProject.php">
						<TR>
							<INPUT TYPE="hidden" NAME="action" VALUE="editProjDescUpdate">
							<INPUT TYPE="hidden" NAME="projectNumber" VALUE="$projectNumber">
							<TD>
								<STRONG>
									$top[dbProjectNumber]
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
		footer($projectNumber);
	 }

	 /**
	  *	Edit Project Description Update
	  *
	  *	The edit project description update function writes a revised 
	  *	project description to the database for a given project number.  
	  *	Basic error checking and data validation is performed.
	  *
	  *	@param int $projectNumber Project number to modify
	  *	@param string $projectDescription Modified description
	  */
	  function editProjDescUpdate ($projectNumber, $projectDescription) {
		  $projectDecription = modifyDescription($projectDescription);
		  query("UPDATE projectMaster SET dbDescription = \"$projectDescription\" WHERE dbProjectNumber = $projectNumber", "editProjDescUpdate");
		  head('', '', $projectNumber);
	  }
?>