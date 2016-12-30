<?php
 /**
 * Neoteric Template
 *
 * This file contains functions to assist users in applying a template to their pages.  To use it, a file simply calls printTemplateHeader() with the appropriate parameters.  If the caller includes neoteric_globals.inc, then it will use ABSPREFIX/include/neoteric_template.html.  Otherwise, it will use ./include/neoteric_template.html.
 *
 * When a page is finished printing output, it should echo $footerText to close all appropriate table tags.
 *
 * @package Includes
 * @company Neoteric Hovercraft, Inc
 * @author Nik Reiman
 */
  
 /**
 * Prints out the template header
 *
 * Prints the header of a template.  The template should be a regular HTML file with special text in place of template data to be merged into the document.  In the case of each directive, it must be on its own line, since that entire line of HTML will be replaced by whatever content is in the merged document.  The following directives are parsed for replacement:
 *
 * !!title!! - The title of the document as shown in the web browser, which will appear as "NeoNet: $moduleName".
 *
 * !!header!! - The banner text in the page.  If $pageDescription is not null, it will print out "$moduleName: $pageDescription".  Otherwise, just "$moduleName" will be printed out.
 *
 * !!menu!! - This part is a bit special.  If the file "/code/$moduleName/menu.html" exists, it will simply print this menu out.  If not, the message "No submenu available" will be printed.  This was done because having the menu text inside of the individual module include files was somewhat silly and disorganized.
 *
 * !!break!! - Here is where the main text will be printed out.
 *
 * @param string $moduleName
 * @param string $pageDescription An optional description of the page contents.
 * @author Nik Reiman
 */
 function printTemplateHeader($moduleName, $pageDescription) {
  global $footerText;
   
  // Set the global working prefix
  if (defined('ABSPREFIX')) {
   $prefix = ABSPREFIX;
  } else {
   $prefix = ".";
  }
   
  $templateFile = "$prefix/include/neoteric_template.html";
  if ($filePointer = fopen($templateFile, 'r')) {
   while ($line = fgets($filePointer)) {
    // See if we print out the title
    if (ereg("!!title!!", $line)) {
     print "NeoNet: " . ucwords($moduleName);
    }
    // This is the banner text that is displayed in the page
    elseif(ereg("!!header!!", $line)) {
     if ($pageDescription != NULL) {
      print ucwords($moduleName) . ": $pageDescription";
     } else {
      print ucwords($moduleName);
     }
    }
    // Print out the menu for the module
    elseif(ereg("!!menu!!", $line)) {
     // See if there's a menu.html file in the module subdirectory.  If not,
     // print out a friendly message.
     $menuFile = "$prefix/$moduleName/menu.html";
     if (is_readable($menuFile)) {
      if (!($menuPointer = fopen("$menuFile", 'r'))) {
       // FIXME: Need to call the real error function
       print "Error; cannot open file";
      } else {
       $menuText = fread($menuPointer, filesize($menuFile));
       print $menuText;
       fclose($menuPointer);
      }
     } else {
      print "No submenu available";
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
   // FIXME: print out the real error function here
   print "Error: Template '$templateFile' could not be opened for loading.<br>";
   exit();
  }
 }
?>
