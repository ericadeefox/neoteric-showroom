<?php
  
 /**
 * User Prompt
 *
 * This PHP file contains the user prompt routine, which builds a
 * table of the user's choice for a variety of parts and jobs.
 * If an existing job is found, the user is prompted as to whether
 * to use the existing job or to create a new job.  There are five
 * constants defined in neoteric_general.inc that are used in this
 * file.
 *
 * @package  Include
 * @company  Neoteric Hovercraft, Inc
 * @author   Collin Krepps
 * @file   userPrompt.inc
 */
  
 /************* functions **************************/
  
 /**
 * Trickle Down
 *
 * The trickle down function recursively propagates a "yes" answer
 * through all levels of children for any parent part.  This allows
 * the data in the table to be correct, even though the generate
 * jobs function should not look below the first "yes" answer.
 *
 * @param int $partNumber Part number of the parent
 * @param string $tableName Name of the table containing the
 * @param int $key Primary key for table entry
 * @return int Largest primary key set
 * user's choices
 */
 function trickleDown($partNumber, $tableName, $key) {
  //make the query
  $qry = "SELECT dbKey, dbParentPartNumber, dbChildPartNumber from $tableName WHERE dbParentPartNumber = $partNumber";
  $result = mysql_query($qry) or die("error in trickleDown: $qry " . mysql_error());
  //continue until you have exhausted all children
  while ($top = mysql_fetch_array($result, MYSQL_ASSOC)) {
   $qry = "UPDATE $tableName SET dbNewJob = " . YES . " WHERE dbKey = $top[dbKey]";
   mysql_query($qry) or die("error in trickleDown: $qry " . mysql_error());
   //find the largest key value to use...
   if ($top['dbParentPartNumber'] != $top['dbChildPartNumber']) {
    //check that we are not in a loop here...
    if ($key > $top['dbKey']) {
     $key = trickleDown($top['dbChildPartNumber'], $tableName, $key);
    } else {
     $key = trickleDown($top['dbChildPartNumber'], $tableName, $top['dbKey']);
    }
   }
  }
  //need to return from this call
  return $key;
 }
  
 /**
 * Ask User
 *
 * The ask user function displays all available choices to the
 * screen, as well as information about the part in question.
 * Each link contains unique values that allow another function
 * to commit the data to a table and continue to the next part.
 *
 * @param int $partNumber Part number of the parent
 * @param string $tableName Name of the table containing the
 * user's choices
 * @param int $projectNumber Project number for this project
 * @param double $qty Amount of this part to create
 * @param int $key Primary key for table entry
 */
 function askUser ($partNumber, $tableName, $projectNumber, $qty, $key) {
  //action determines where to go next
  $action = "set";
  $description = getValue("dbDescription", "inventoryStock", $partNumber, "dbPartNumber");
  //print the links so the use can choose
  print "Job exists for part # <STRONG>$partNumber</STRONG> -- <STRONG>$description</STRONG>.  Use existing job?<P>";
  print "<TABLE><TR><TD WIDTH=\"100\">";
  print "<A HREF=\"userPrompt.php?action=$action&partNumber=$partNumber&userPrompt=" . NO . "&tableName=$tableName&projectNumber=$projectNumber&qty=$qty&key=$key&template=0\">No</A>";
  print "</TD><TD>";
  print "-- New job number";
  print "</TD></TR><TR><TD>";
  print "<A HREF=\"userPrompt.php?action=$action&partNumber=$partNumber&userPrompt=" . NOTOALL . "&tableName=$tableName&projectNumber=$projectNumber&qty=$qty&key=$key&template=0\">No to All</A>";
  print "</TD><TD>";
  print "-- New job number for all remaining jobs";
  print "</TD></TR><TR><TD>";
  print "<A HREF=\"userPrompt.php?action=$action&partNumber=$partNumber&userPrompt=" . CANCEL . "&tableName=$tableName&projectNumber=$projectNumber&qty=$qty&key=$key&template=0\">Cancel</A>";
  print "</TD><TD>";
  print "-- Stop generating jobs";
  print "</TD></TR>";
  print "</TABLE>";
  print "<P>";
   
  //print some job-specific info
  $qry = "SELECT DISTINCT dbJobNumber, dbQty FROM projectSubJobs WHERE dbPartNumber = $partNumber AND dbComplete = 0 AND LOCATE('-', dbJobNumber)>0 ORDER BY dbSubJobsKey ASC";
  $result = data($qry, 'userPrompt|askUser');
  print "<TABLE><TR>";
  print "<TH>Job Number</TH><TH>Description</TH><TH>Quantity</TH></TR>";
  //get all jobs outstanding
  while ($top = mysql_fetch_array($result, MYSQL_ASSOC)) {
   $trim = explode('-', $top['dbJobNumber']);
   //make sure its not a theoretical trial
   if (strcmp(substr($trim[1], 0, 1), 'x') != 0) {
    $qry = "SELECT dbProjectNumber FROM projectPartList WHERE dbJobNumber = \"$top[dbJobNumber]\" ORDER BY dbProjectNumber ASC LIMIT 1";
    $projectResult = data($qry, 'userPrompt|askUser');
    print "<TR><TD><A HREF=\"userPrompt.php?action=$action&partNumber=$partNumber&userPrompt=5&tableName=$tableName&projectNumber=$projectNumber&qty=$qty&key=$key&template=0&jobNumber=$top[dbJobNumber]\">$top[dbJobNumber]</A></TD>";
    //try to get the project number
    if ($middle = mysql_fetch_array($projectResult, MYSQL_ASSOC)) {
     $qry = "SELECT dbDescription FROM projectMaster WHERE dbProjectNumber = \"$middle[dbProjectNumber]\"";
     $descResult = data($qry, 'userPrompt|askUser');
     //try to get the description
     if ($bottom = mysql_fetch_array($descResult, MYSQL_ASSOC)) {
      print "<TD>$bottom[dbDescription]</TD>";
     } else {
      //if there is no result found, print a table cell anyway
      print "<TD></TD>";
     }
    } else {
     //if there is no result found, print a table cell anyway
     print "<TD></TD>";
    }
    print "<TD>$top[dbQty]</TD>";
    print "</TR>";
   }
  }
  print "</TABLE>";
  exit();
 }
  
 /**
 * Get Children
 *
 * The get children function writes all children for a given part
 * number to a table then recursively checks each child's children,
 * until no children are found.  The only children expanded are
 * those for which a job is made.
 *
 * @param int $partNumber Part number of the parent
 * @param string $tableName Name of the table containing the
 * user's choices
 * @param int $projectNumber project number for this project
 */
 function getChildren($partNumber, $tableName, $projectNumber) {
  $result = mysql_query("SELECT dbQuantity, dbChildNumber FROM inventoryChild WHERE dbPartNumber = $partNumber") or die("error in getChildren " . mysql_error());
  if (mysql_num_rows($result) == 0) {
   //the part has no children, return
   return;
  } else {
   //do this as long as we have children
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    //check that the quantity is not zero
    if ($row['dbQuantity'] != 0) {
     //check that a job is created for this part
     if (getValue('dbCreateJob', 'inventoryStock', $row['dbChildNumber'], 'dbPartNumber')) {
      $key = getLastKey($tableName, "dbKey", "dbKey");
      $key++;
      query("INSERT INTO $tableName VALUES ($key, $partNumber, $row[dbChildNumber], $row[dbQuantity], '9', '')", 'getChildren');
      //call recursively with the child part
      getChildren($row['dbChildNumber'], $tableName, $projectNumber);
     }
     //if a job is not created for the child, we do not need to explore further
    }
   }
  }
 }
  
 /**
 * Job in Progress
 *
 * The job in progress function returns a boolean value, stating
 * whether or not a job exists for that part number.
 *
 * @param int $partNumber Part number to check
 * @return int 1 if a job exists, 0 otherwise
 */
 function jobInProgress($partNumber) {
  $result = getAllRows('projectSubJobs', $partNumber, 'dbPartNumber', '', 'userPrompt|jobInProgress');
  while ($top = array_pop($result)) {
   if (($top['dbComplete'] == 0)) {
    return 1;
   }
  }
  return 0;
 }
  
 /**
 * User Prompt
 *
 * The user prompt function builds a list of all jobs and subjobs,
 * then, if a job exists, asks the user to either use an existing
 * job number or to create a new job number.  Whenever the user
 * chooses an existing job number that decision is carried
 * recursively through that part's children.  This function
 * continues to prompt the user until all potential jobs have been
 * prompted.
 *
 * @param int $partNumber Part number to check
 * @param string $action Determines which section(s) of code get executed
 * @param int $userPrompt Defined constant value that is written
 * to the table
 * @param string $rand Table that holds the user's answers
 * @param int $projectNumber Project number for this project
 * @param double $qty Amount of each part that is needed
 * @param int $key Primary key for the table entry
 */
 function userPrompt($partNumber, $action, $userPrompt, $rand, $projectNumber, $qty, $key, $template, $jobNumber) {
  if (isnull($template)) {
   $template = 0;
  }
  //initially we want to do this stuff
  //valid values of action: NULL, ask, set
  if (isnull($action)) {
   //choose a random number to name our table
   $rand = rand(10000, 90000);
   $rand = "a" . $rand;
   //create our temporary table
   $qry = "CREATE TABLE $rand (dbKey integer primary key not null, dbParentPartNumber integer, dbChildPartNumber integer, dbQuantity double, dbNewJob char(1), dbJobNumber text)";
   while (!(mysql_query($qry) or die("error in table creation: $qry " . mysql_error()))) {
    $rand = rand(10000, 90000);
    $rand = "a" . $rand;
   }
   //insert the main part into our table
   query("INSERT INTO $rand VALUES (1, $partNumber, $partNumber, 1, 9, '')", 'userPrompt');
    
   getChildren($partNumber, $rand, $projectNumber);
   //change action, move to ask portion of code
   $key = 0;
   $action = "ask";
  }
  //set the preference for the specified part
  if (strcmp($action, "set") == 0) {
   //user chose not to use an existing job number
   if ($userPrompt == NO) {
    $qry = "UPDATE $rand SET dbNewJob = " . NO . " WHERE dbKey = $key";
    $result = mysql_query($qry) or die("error in userPrompt: $qry " . mysql_error());
    $action = "ask";
   }
   //user chose not to use an existing job number for all choices
   else if ($userPrompt == NOTOALL) {
    $qry = "UPDATE $rand SET dbNewJob = " . NO . " WHERE dbKey >= $key";
    $result = mysql_query($qry) or die("error in userPrompt: $qry " . mysql_error());
    $action = "done";
   }
   //user chose to cancel
   else if ($userPrompt == CANCEL) {
    query("DROP TABLE $rand", 'userPrompt');
    header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/editProject.php?action=editProject&projectNumber=$projectNumber");
    exit();
   }
   //user chose a specific job number to use
   else if ($userPrompt == 5) {
    $qry = "UPDATE $rand SET dbNewJob = 5, dbJobNumber = \"$jobNumber\" WHERE dbKey >= $key";
    $result = mysql_query($qry) or die("error in userPrompt: $qry " . mysql_error());
    $key = trickleDown($partNumber, $rand, $key);
    $action = "ask";
   }
    
  }
  //select the next available part and ask
  if (strcmp($action, "ask") == 0) {
   $key++;
   $qry = "SELECT dbChildPartNumber from $rand WHERE dbKey = $key";
   $result = mysql_query($qry) or die("error in userPrompt " . mysql_error());
   //this test should always be true, due to earlier check for headers
   if ($tmpRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
    //get the part number selected
    //check added to please dagey.  if it breaks, reexamine here
    $partNumber = $tmpRow['dbChildPartNumber'];
    $bool = jobInProgress($partNumber);
    //if a job is in progress for this part, ask the user
    if ($bool) {
     //Get the template and display the header
     if ($template == 0) {
      $template = 99999999;
      $incDir = '../include';
      if (defined('LOADED')) {
      } else {
       loadTemplate("$incDir/template.htm");
      }
      printHeader("Projects", "Project $projectNumber", menu($projectNumber));
     }
     askUser($tmpRow['dbChildPartNumber'], $rand, $projectNumber, $qty, $key);
    }
    //otherwise, there are no jobs, so choose to make a new job number and continue
    else
    {
     $qry = "UPDATE $rand SET dbNewJob = " . NO . " WHERE dbkey = $key";
     $result = mysql_query($qry) or die("error in userPrompt " . mysql_error());
     userPrompt(NULL, "ask", NULL, $rand, $projectNumber, $qty, $key, $template, NULL);
    }
   } else {
    //call userPrompt again, this time it should redirect
    userPrompt(NULL, "done", NULL, $rand, $projectNumber, $qty, $key, $template, NULL);
   }
  }
  //  if (strcmp($action, 'done') == 0) {
  //   print("<A HREF=\"http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . /generateJobs.php?action=continue&projectNumber=$projectNumber&qty=$qty&tableName=$rand&partNumber=$partNumber\">Press here to continue this process</A>");
  header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/generateJobs.php?action=continue&projectNumber=$projectNumber&qty=$qty&tableName=$rand&partNumber=$partNumber");
  exit();
  //  }
 }
?>
