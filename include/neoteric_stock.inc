<?php
  
 /**
 * Neoteric Stock
 *
 * This INC file contains functions used to query the
 * inventoryStock table
 *
 * @package  neoteric_stock.inc
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 */
  
 /****************** functions ********************************/
  
 /**
 * Qty in Bin 1
 *
 * The qty in bin 1 function queries the inventoryStock table for
 * the amount of a given part in that bin
 *
 * @param int $partNumber The part number
 * @return double The quantity of that part, if the part is found
 */
 function qtyInBin1 ($partNumber) {
  return getValue("dbBin1Qty", "inventoryStock", $partNumber, "dbPartNumber");
 }
  
 /**
 * Qty in Bin 2
 *
 * The qty in bin 2 function queries the inventoryStock table for
 * the amount of a given part in that bin
 *
 * @param int $partNumber The part number
 * @return double The quantity of that part, if the part is found
 */
 function qtyInBin2 ($partNumber) {
  return getValue("dbBin2Qty", "inventoryStock", $partNumber, "dbPartNumber");
 }
?>
