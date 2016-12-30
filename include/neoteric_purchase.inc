<?php
  
 /**
 * Neoteric Purchase Orders
 *
 * This INC file contains library functions used in the purchase orders
 * module.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Ryan Dagey
 * @file   neoteric_purchase.inc
 * @date   12-Nov-2003
 */
  
  
 /*************************** Functions ****************************/
 /**
 * Menu
 *
 * The menu function generates a menu to use with the template
 *
 * @param int $purchaseOrders Purchase order number for the order
 * @return string Data to be displayed in the menu
 */
  
 function menu($purchaseOrderNumber) {
  //Menu links go here
  $file = basename($_SERVER['PHP_SELF']);
  $output = <<<QQQ
			<A HREF="../purchase_orders/purchaseOrder.php?purchaseOrderNumber=$purchaseOrderNumber">View Orders</A><BR>
			<A HREF="../purchase_orders/purchaseOrderAddPart.php?purchaseOrderNumber=$purchaseOrderNumber">Add Part</A><BR>
			<A HREF="../purchase_orders/purchaseorderVendorBrowse.php?purchaseOrderNumber=$purchaseOrderNumber">Browse Vendors</A><BR>
			<A HREF="../purchase_orders/purchaseorderVendorLookup.php?purchaseOrderNumber=$purchaseOrderNumber">Vendor Lookup</A><BR>
			<A HREF="../purchase_orders/purchaseorderVendorTable.php?purchaseOrderNumber=$purchaseOrderNumber">Vendor Table</A><BR>
			<A HREF="../purchase_orders/poSearch.php?purchaseOrder=$purchaseOrderNumber">Search</A><BR>
			<A HREF="#" OnClick="window.open('../help.php?file=$file&title=NeoNET Help', '', 'width=400, height=400, scrollbars=1, resizeable=1, top=100, left=400');">Help</A>
QQQ;
		return $output;
	}
?>