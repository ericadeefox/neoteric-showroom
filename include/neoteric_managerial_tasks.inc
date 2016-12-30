<?php
  
 /**
 * Neoteric Managerial Tasks
 *
 * This INC file contains functions that handle mangerial tasks.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 * @file   neoteric_managerial_tasks.inc
 * @date   26-Jun-2003
 */
  
 /********************* included files ****************************/
 include_once("neoteric_stock.inc");
  
 /****************** functions ********************************/
  
 /**
 * Convert Units
 *
 * The convert units function converts units from vendor/subcontractor units to the Neoteric units.
 * Function accesses the inventoryUnits table.
 *
 * @param integer $part Part number for the conversion
 * @param string $fromUnit Unit to convert from
 * @param string $toUnit Unit to convert to
 * @param double $Qty Quantity of the conversion
 * @return double Converted quantity if the conversion succeeds; -999 if the conversion fails
 */
 function convertUnits($part, $fromUnit, $toUnit, $qty) {
  //query the database to get the multiplier value for the conversion
  $qry = "SELECT dbMultiplier FROM inventoryUnits WHERE dbFromUnit='$fromUnit' AND dbToUnit='$toUnit'";
  $result = mysql_query($qry) or error("Unable to fetch multiplier from units table.");
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
   
  //calculate the converted quantity
  $convertedQty = $qty * $row['dbMultiplier'];
   
  //if the conversion is 0, it has failed; otherwise, we return the converted value
  if ($convertedQty == 0)
  $convertedQty = -999;
  return $convertedQty;
 }
  
 /**
 * Execute Transaction
 *
 * The execute transaction function executes a transaction by adding or deleting a
 * quantity to or from stock when $globalError is null.  If $error is not null,
 * this function will write the transaction info to the database with the error message passed in.
 *
 * @param array $transInfo Transaction information
 * @param int $neoQty Converted quantity
 * @param string $error Error message to write to the table, if any
 * @return int 1 if an error occurs, 0 otherwise
 */
 function executeTransaction($transInfo, $neoQty, $error = "") {
  //get the transaction key and date
  $trackingKey = getLastKey("inventoryTracking", "dbTransactionKey", "dbTransactionKey");
  $trackingKey++;
  $date = dateNow();
   
  //if $error is null, then we have some transaction to execute before writing to the table
  if (isnull($error)) {
   //get the quantities in bin1 and bin2 for quick recall
   $qtyInBin1 = qtyInBin1($transInfo['part']);
   $qtyInBin2 = qtyInBin2($transInfo['part']);
    
   //determine if the first bin is the bin we are to add to
   if (getValue("dbBin1", "inventoryStock", $transInfo['part'], "dbPartNumber") == $transInfo['binNumber']) {
    //check that we have enough in stock to complete the transaction
    if ($qtyInBin1 >= abs($neoQty) || $neoQty > 0) {
     //generate an update qty amount and update the stock
     $setQty = $qtyInBin1 + $neoQty;
     setField("dbBin1Qty", "inventoryStock", $setQty, "dbPartNumber", $transInfo['part']);
      
     //add an entry to inventory tracking showing what we did
     query("INSERT INTO inventoryTracking (dbTransactionKey, dbTransactionDate, dbBinNumber, dbPartNumber, dbQuantity, dbUnits, dbTransactionType, dbInformationNumber, dbPerson, dbNeotericQuantity, dbProjectNumber, dbSuccess) VALUES ($trackingKey, \"$date\", \"$transInfo[binNumber]\", \"$transInfo[part]\", \"$transInfo[qty]\", \"$transInfo[neoUnit]\", \"$transInfo[type]\", \"$transInfo[informationNumber]\", \"neonet\", $neoQty, $transInfo[projectNumber], 1)");
      
     //return that no error was generated
     return 0;
    }
    //if we don't have enough in the selected bin, we have an error
    else
     {
     $error = "Invalid stock transaction.  Check for valid bin numbers and quantities.";
    }
   }
   //we do the same thing here as the if branch above, except we look for bin 2
   else if(getValue("dbBin2", "inventoryStock", $transInfo['part'], "dbPartNumber") == $transInfo['binNumber']) {
    if ($qtyInBin2 >= abs($neoQty) || $neoQty > 0) {
     $setQty = $qtyInBin2 + $neoQty;
     setField("dbBin2Qty", "inventoryStock", $setQty, "dbPartNumber", $transInfo['part']);
     query("INSERT INTO inventoryTracking (dbTransactionKey, dbTransactionDate, dbBinNumber, dbPartNumber, dbQuantity, dbUnits, dbTransactionType, dbInformationNumber, dbPerson, dbNeotericQuantity, dbProjectNumber, dbSuccess) VALUES ($trackingKey, \"$date\", \"$transInfo[binNumber]\", \"$transInfo[part]\", \"$transInfo[qty]\", \"$transInfo[neoUnit]\", \"$transInfo[type]\", \"$transInfo[informationNumber]\", \"neonet\", \"$neoQty\", $transInfo[projectNumber], 1)");
     return 0;
    } else {
     $error = "Invalid stock transaction.  Check for valid bin numbers and quantities.";
    }
   }
   //else, we have an error in the bin number
   else
    {
    $error = "Invalid bin number specified.";
   }
  }
   
  //write to the inventory tracking table that an error has occured, with the specific error msg
  query("INSERT INTO inventoryTracking (dbTransactionKey, dbTransactionDate, dbBinNumber, dbPartNumber, dbQuantity, dbUnits, dbTransactionType, dbInformationNumber, dbPerson, dbNeotericQuantity, dbProjectNumber, dbSuccess, dbErrorMessage) VALUES ($trackingKey, \"$date\", \"$transInfo[binNumber]\", $transInfo[part], \"$transInfo[qty]\", \"$transInfo[neoUnit]\", \"$transInfo[type]\", \"$transInfo[informationNumber]\", \"neonet\", \"$neoQty\", $transInfo[projectNumber], 0, \"$error\")");
   
  //return that an error occurred
  return 1;
 }
  
 /**
 * Add Transaction
 *
 * The add transaction creates a record of the operation to be performed.
 * Function accesses the inventoryStock and inventoryTracking tables.
 *
 * @param int $part Part number for the transaction
 * @param double $qty Quantity of that part
 * @param string $type Type of transaction to perform [job/porder/sorder/invoice/blank]
 * @param string $informationNumber Information number to identify the part with
 * @param int $projectNumber Project number if the type is job
 * @param int $binNumber Bin number to use
 * the type of transaction
 */
 function addTransaction($part, $qty, $type, $informationNumber, $projectNumber = 0, $binNumber = 0) {
  //we start off by assuming no error occurs
  $globalError = 0;
   
  //get the neoteric unit and bin number [if not passed in] for the part number and generate the transaction information array
  $neoUnit = getValue("dbUnit", "inventoryStock", $part, "dbPartNumber");
  if ($binNumber == 0) {
   $binNumber = getValue("dbBin1", "inventoryStock", $part, "dbPartNumber");
  }
  $transInfo = array("part" => $part, "qty" => $qty, "type" => $type, "informationNumber" => $informationNumber, "projectNumber" => $projectNumber, "binNumber" => $binNumber, "neoUnit" => $neoUnit);
   
  //if the type or information is null or if qty=0 or part is less than 0, we have invalid info
  if (isnull($type) || isnull($informationNumber) || $qty == 0 || $part <= 0) {
   executeTransaction($transInfo, 0, "Invalid transaction information.  Check the type, information number, quantity, and/or part number.");
   return 1;
  }
   
  //we use neoQty instead of qty, so we assign neoQty the value of qty
  //neoQty may or may not change for the following if branch
  $neoQty = $qty;
  //if have a subcontractor/purchase order, we must calculate the neoteric quantity
  if (strcmp($type, "sorder") == 0 || strcmp($type, "porder") == 0) {
   //query the database for the subcontractor unit if the type is sorder
   if (strcmp($type, 'sorder') == 0) {
    $qry = "SELECT dbUnit FROM subcontractorOrderParts WHERE dbSubcontractOrderNumber=$informationNumber AND dbPartNumber=$part";
   }
   //query the database for the vendor unit if the type is porder
   else
    {
    $qry = "SELECT dbUnit FROM purchaseOrderParts WHERE dbPurchaseOrderNumber='$informationNumber' AND dbPartNumber=$part";
   }
   $result = mysql_query($qry) or error("Unable to retrieve units for part $part and subcontract order number $informationNumber.");
   $foreignUnits = $row = mysql_fetch_array($result, MYSQL_ASSOC);
    
   //calculate the neoteric quantity by converting the foreign units to the neoteric units
   $neoQty = convertUnits($part, $foreignUnits, $neoUnit, $qty);
    
   //if -999 is returned, we were unable to convert units for some reason
   if ($neoQty == -999) {
    $globalError = executeTransaction($transInfo, $neoQty, "Unable to convert units.");
    return 1;
   }
  }
   
  //if the type is job, we do project transactions
  if (strcmp($type, "job") == 0) {
   //check to see that the project number and job number combination is valid
   $qry = "SELECT * FROM projectSubJobs WHERE dbJobNumber='$informationNumber' AND dbProjectNumber=$projectNumber";
   $result = mysql_query($qry);
   //if at least 1 row is returned, we have a valid combination and we execute the transaction
   if (mysql_num_rows($result) < 0) {
    $globalError = executeTransaction($transInfo, $neoQty, "Invalid job number and project number combination.");
   } else {
    $globalError = executeTransaction($transInfo, $neoQty);
   }
  }
  //if the type is invoice, we do invoice transactions
  else if(strcmp($type, "invoice") == 0) {
    
  }
  //if the type if porder, we do purchase order transactions
  else if(strcmp($type, "porder") == 0) {
    
  }
  //if the type is sorder, we do subcontractor order transactions
  else if(strcmp($type, "sorder") == 0) {
    
  }
  //if the type is blank, we do general transactions
  else if(strcmp($type, "blank") == 0) {
    
  }
  //if there is no type, we have an error
  else
   {
   $globalError = executeTransaction($transInfo, $neoQty, "Invalid transaction type.");
  }
   
  //return whether or not an error has occured
  return $globalError;
 }
  
 /**
 * Delete Virtual Bin
 *
 * The delete virtual bin function empties the virtual bin given,
 * returning the inventory to stock.  Function accesses the
 * inventoryStock and inventoryVirtualBins tables.
 *
 * @param int $vbKey Primary key of the virtual bin entry
 */
 function deleteVirtualBin($vbKey) {
  //run the query
  $binInfo = getAllRows("inventoryVirtualBins", $vbKey, "dbVirtualBin", '', "deleteVirtualBin", "dbBin, dbQty, dbVirtualBin, dbPartNumber");
   
  for ($i = 0; $i < $max = sizeof($binInfo); $i ++) {
   $partNumber = $binInfo[$i]['dbPartNumber'];
   $stock = getRow("inventoryStock", $partNumber, "dbPartNumber", "deleteVirtualBin", "dbBin1, dbBin2Qty, dbBin1Qty");
   $bin = $binInfo[$i]['dbBin'];
   //qtyDiff is the amount of inventory not used in the product, i.e. how much goes back into inventory
   $binNumber = $stock['dbBin1'];
   if ($bin != $binNumber) {
    $newQty = $binInfo[$i]['dbQty'] + $stock['dbBin2Qty'];
    query("UPDATE inventoryStock SET dbBin2Qty = $newQty WHERE dbPartNumber = $partNumber", "completeProject|deleteVirtualBin");
   } else {
    $newQty = $binInfo[$i]['dbQty'] + $stock['dbBin1Qty'];
    query("UPDATE inventoryStock SET dbBin1Qty = $newQty WHERE dbPartNumber = $partNumber", "completeProject|deleteVirtualBin");
   }
   $vb = $binInfo[$i]['dbVirtualBin'];
   query("DELETE FROM inventoryVirtualBins WHERE dbVirtualBin = $vb", "completeProject|deleteVirtualBin");
  }
 }
  
?>
