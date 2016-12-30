<?php
  
 /**
 * Neoteric Costing
 *
 * This INC file contains library functions used in the costing
 * module.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Ryan Dagey
 * @file   neoteric_costing.inc
 * @date   26-Jun-2003
 */
  
  
 /*************************** Functions ****************************/
  
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $partNumber Part number currently being analyzed
 * @return string Data to be displayed in the menu
 */
  
 function menu($partNumber) {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../costing/getPart.php?partNumber=$partNumber">View Parts</A><BR>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}
?>