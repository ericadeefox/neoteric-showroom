<?php
  
 /**
 * Template Loader
 *
 * This file contains functions to assist users in applying
 * a template to their pages.  To use it, a file simply calls
 * printTemplateHeader() with the appropriate parameters.  If
 * the caller includes neoteric_globals.inc, then it will use
 * ABSPREFIX/include/neoteric_template.html.  Otherwise, it will use
 * ./include/neoteric_template.html.
 * When a page is finished printing output, it should echo $footerText
 * to close all appropriate table tags.
 *
 * @package Neoteric Includes
 * @company Neoteric Hovercraft, Inc
 * @author Nik Reiman
 * @file neoteric_template.inc
 */
  
 /**
 * printTemplateHeader
 *
 * Prints out the template header
 *
 * Prints the header of a template.  The template should be a regular
 * HTML file with special text in place of template data to be merged
 * into the document.  In the case of each directive, it must be on
 * its own line, since that entire line of HTML will be replaced by
 * whatever content is in the merged document.  The following directives
 * are parsed for replacement:
 * !!title!! - The title of the document as shown in the web browser.
 * !!header!! - The banner text in the page.
 *
 * @company Neoteric Hovercraft, Inc.
 * @author Nik Reiman
 * @param string $moduleName The name of the module being printed out
 * @param string $pageDescription An optional description of the page contents.
 **/
 function printTemplateHeader($moduleName, $pageDescription) {
  global $footerText;
   
  // Set the global working prefix
  if (defined(ABSPREFIX)) {
   $prefix = ABSPREFIX;
  } else {
   $prefix = ".";
  }
   
  $templateFile = "$prefix/include/neoTemplate.html";
  if ($filePointer = fopen($templateFile, 'r')) {
   while ($line = fgets($filePointer)) {
    // See if we print out the title
    if (ereg("!!title!!", $line)) {
     print "Neonet: $moduleName";
    }
    // This is the banner text that is displayed in the page
    elseif(ereg("!!header!!", $line)) {
     if ($pageDescription != NULL) {
      print "$moduleName: $pageDescription";
     } else {
      print "$moduleName";
     }
    }
    // Print out the menu for the module
    elseif(ereg("!!menu!!", $line)) {
     $menuFile = "$prefix/$moduleName/menu.html";
     if (is_readable($menuFile)) {
      if (!($menuPointer = fopen("$menuFile", 'r'))) {
       print "Error; cannot open file";
      }
      $menuText = fread($menuPointer, filesize($menuFile));
      print $menuText;
      fclose($menuPointer);
     } else {
      print "No menu available";
     }
    }
    // This is where the body of the document should go, so toss the rest
    // of the text into the $footerText variable, and make sure the calling
    // page echos that out.
    elseif(ereg("!!break!!", $line)) {
     $footerText = "";
     while ($line = fgets($filePointer)) {
      $footerText .= $line;
     }
     break;
    } else {
     print "$line";
    }
   }
   fclose($filePointer);
  } else {
   print "Error: Template '$templateFile' could not be opened for loading.<br>";
   exit();
  }
 }
  
 function printTemplateFooter() {
  print $footerText;
 }
  
 function loadTemplate($templateFile) {
  global $headerText, $footerText;
  static $count = 0;
  if ($count == 0) {
   if ($theFile = fopen($templateFile, 'r')) {
    $theTemplate = fread($theFile, filesize($templateFile));
    fclose($theFile);
     
    //Split the file into the header and footer
    $split = split('!!break!!', $theTemplate);
    $headerText .= $split[0];
    $footerText .= $split[1];
   }
   $count ++;
  }
 }
  
 /**
 *              Print Header
 *
 *              printHeader prints out the header created from loadTemplate.  The user
 *              must declare a page title, menu, and header to display.
 *
 *              @param string $title    title to display
 *              @param string $header   header to display
 *              @param string $menu             menu to display
 */
  
 function printHeader($userTitle, $userHeader, $userMenu) {
  global $headerText;
   
  //replace the !!header!! marker with the user-defined header
  $headerText = eregi_replace('!!header!!', $userHeader, $headerText);
   
  //replace the !!title!! marker with the user-defined header
  $headerText = eregi_replace('!!title!!', $userTitle, $headerText);
   
  //replace the !!menu!! marker with the user-defined menu
  $headerText = eregi_replace('!!menu!!', $userMenu, $headerText);
   
  //print the header
  print $headerText;
 }
  
 /**
 *              Print Header
 *
 *              printFooter prints out the footer created from loadTemplate.
 *              There are no options.
 *
 */
  
 function printFooter() {
  global $footerText;
  print $footerText;
 }
?>
