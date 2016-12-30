<?php
  
 /**
 * Neoteric Inventory
 *
 * This INC file contains library functions used in the inventory
 * module.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Ryan Dagey
 * @file   neoteric_inventory.inc
 * @date   11-Nov-2003
 */
  
  
 /*************************** Functions ****************************/
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $partNumber Part number for this part
 * @return string Data to be displayed in the menu
 */
  
 function menu($partNumber) {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../inventory/editPart.php?partNumber=$partNumber">View Parts</A><BR>
			<A HREF="../inventory/partSearch.php?partNumber=$partNumber">Part Search</A><BR>
			<A HREF="../inventory/printRange.php?partNumber=$partNumber">Print Range</A><BR>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}
?>