<?php

define('INCLUDE_CHECK', true);

require 'neocon.php';
require 'functions.php';
// Those two files can be included only if INCLUDE_CHECK is defined

session_name('neoLogin');
// Starting the session

session_set_cookie_params(2*7*24*60*60);
// Making the cookie live for 2 weeks

session_start();

if ($_SESSION['id'] && !isset($_COOKIE['contacts']) && !$_SESSION['rememberMe']) {
    // If you are logged in, but you don't have the tzRemember cookie (browser restart)
    // and you have not checked the rememberMe checkbox:
    $_SESSION = array();
    session_destroy();
    // Destroy the session
}

if (isset($_GET['logoff'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_POST['submit']=='Login') {
    // Checking whether the Login form has been submitted
    $err = array();
    // Will hold our errors

    if (!$_POST['username'] || !$_POST['password']) {
        $err[] = '   &nbsp All the fields must be filled in!';
    }

    if (!count($err)) {
        $_POST['username'] = mysql_real_escape_string($_POST['username']);
        $_POST['password'] = mysql_real_escape_string($_POST['password']);
        $_POST['rememberMe'] = (int)$_POST['rememberMe'];

        // Escaping all input data
        $row = mysql_fetch_assoc(mysql_query("SELECT dbContactNumber,dbUsr,dbPassword,dbPrimaryEmail,dbIP,dbDate,dbFirstName,dbLastName,dbPhone,dbCompany,dbAddress,dbCity,dbState,dbCountry,dbZip,dbType,dbHowFind,dbWhereUse,dbVehicles,dbOtherComments FROM contacts WHERE dbUsr='{$_POST['username']}' AND dbPassword='".md5($_POST['password'])."'"));

        if ($row['dbUsr']) {
            // If everything is OK login

            $_SESSION['usr']=$row['dbUsr'];
            $_SESSION['id'] = $row['dbContactNumber'];
            $_SESSION['email'] = $row['dbPrimaryEmail'];
            $_SESSION['firstname']=$row['dbFirstName'];
            $_SESSION['lastname'] = $row['dbLastName'];
            $_SESSION['phone'] = $row['dbPhone'];
            $_SESSION['company']=$row['dbCompany'];
            $_SESSION['address'] = $row['dbAddress'];
            $_SESSION['city'] = $row['dbCity'];
            $_SESSION['state']=$row['dbState'];
            $_SESSION['country'] = $row['dbCounty'];
            $_SESSION['zip'] = $row['dbZip'];
            $_SESSION['find']=$row['dbHowFind'];
            $_SESSION['kind'] = $row['dbType'];
            $_SESSION['land'] = $row['dbWhereUse'];
            $_SESSION['othercraft']=$row['dbVehicles'];
            $_SESSION['message'] = $row['dbOtherComments'];
            $_SESSION['rememberMe'] = $_POST['rememberMe'];

            // Store some data in the session

            setcookie('contacts', $_POST['rememberMe']);
        } else {
            $err[]='&nbsp Wrong username and/or password!';
        }
    }

    if ($err) {
        $_SESSION['msg']['login-err'] = implode('<br />', $err);
    }
    // Save the error messages in the session

    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

if ($_POST['submit']=='Update') {
    $_SESSION['quantity'] = $_POST['quantity'];
    $_SESSION['notes'] = $_POST['notes'];
    header("Location: cart.php");
    exit;
}

if ($_POST['submit']=='Add to Cart?') {
    $_SESSION['quantity'] = $_POST['quantity'];
    $_SESSION['notes'] = $_POST['notes'];
    header("Location: cart.php");
    exit;
}

if ($_POST['submit']=='Add Craft') {
    $_SESSION['quantity'] = $_POST['quantity'];
    $_SESSION['notes'] = $_POST['notes'];
    header("Location: shopping.php");
    exit;
}

if ($_POST['submit']=='To Checkout') {
    $_SESSION['quantity'] = $_POST['quantity'];
    $_SESSION['notes'] = $_POST['notes'];
    header("Location: cart.php");
    exit;
}

if ($_POST['submit']=='Submit') {
    $_SESSION['hovercraft'] = $_POST['hovercraft'];
    $_SESSION['seating'] = $_POST['seating'];
    $_SESSION['engine'] = $_POST['engine'];
    $_SESSION['injection'] = $_POST['injection'];
    $_SESSION['color'] = $_POST['color'];
    $_SESSION['ass'] = $_POST['ass'];
    $_SESSION['notes'] = $_POST['notes'];

    $list = '';
    for ($i = 0; $i < 429; $i++) {
        if ($_SESSION['quantity'][$i] > 0) {
            $list .= 'Part: ' . $_SESSION['parts-order'][$i] . '<br />Quantity: ' . $_SESSION['quantity'][$i] . '<br/><br/> ';
        }
    };
    if (!$_SESSION['quantity']) {
        $list = 'No parts ordered at this time.';
    };

    $staffMessage = "A quote has been requested by user ".$_SESSION['usr']." via the Neoteric Showroom system!
	<br />Here are all the details of the quote:
	<br />Type of hovercraft: ".$_SESSION['hovercraft']."
	<br />Seating arrangement: ".$_SESSION['seating']."
	<br />Engine & performance specs: ".$_SESSION['engine']." & ".$_SESSION['injection']."
	<br />Color of hovercraft: ".$_SESSION['color']." Go to http://www.color-hex.com/color/".$_SESSION['color']." to see what this color is!
	<br />Assembly plan: ".$_SESSION['ass']."
	<br />Parts ordered & quantity: ".$list."
	<br />They've left the following notes, custom specs, and instructions: <br />".$_SESSION['notes']."
	<br />Thanks for using the Neoteric Showroom instant response system!";

    $customerMessage = "Thanks, ".$_SESSION['usr'].", for requesting a free quote via the Neoteric Showroom system! Please do not respond to this email. A Neoteric representative will contact you as soon as possible to discuss your hovercraft!
	<br />Here are all the details of the quote:
	<br />Type of hovercraft: ".$_SESSION['hovercraft']."
	<br />Seating arrangement: ".$_SESSION['seating']."
	<br />Engine & performance specs: ".$_SESSION['engine']." & ".$_SESSION['injection']."
	<br />Color of hovercraft: ".$_SESSION['color']." Go to http://www.color-hex.com/color/".$_SESSION['color']." to see what this color is!
	<br />Assembly plan: ".$_SESSION['ass']."
	<br />Parts ordered & quantity: ".$list."
	<br />You've left the following notes, custom specs, and instructions: <br />".$_SESSION['notes']."
	<br />Thanks for using the Neoteric Showroom instant response system!";

    send_mail('webmaster@neoterichovercraft.com',
    'erica@neoterichovercraft.com',
    'A new quote received from '.$_SESSION['usr'],
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'guthrie@neoterichovercraft.com',
    'A new quote received from '.$_SESSION['usr'],
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'chris@neoterichovercraft.com',
    'A new quote received from '.$_SESSION['usr'],
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'barb@neoterichovercraft.com',
    'A new quote received from '.$_SESSION['usr'],
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'filip@neoterichovercraft.com',
    'A new quote received from '.$_SESSION['usr'],
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    $_SESSION['email'],
    'Thank you for requesting a free quote from Neoteric Hovercraft!',
    $customerMessage);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <link rel="icon" type="image/ico" href="favicon.ico">

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>Neoteric Hovercraft - Hovercraft Showroom</title>
    <meta name="description" content="The official website for Neoteric Hovercraft: the world's original light hovercraft manufacturer.">
    <meta name="author" content="Ryan Guthrie and Erica Dee Fox.">
    <meta name="keywords" content="hovercraft, light hovercraft, shipbuilding, aircraft, manufacturing, hovercraft manufacturing, neoteric, neoteric hovercraft">

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
  ================================================== -->
    <link rel="stylesheet" href="css/zerogrid.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsiveslides.css">
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>

    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->

    <script src="login_panel/js/slide.js" type="text/javascript"></script>

    <?php echo $script; ?>
    <script src="js/jquery-latest.min.js"></script>
    <script src="js/jscolor.js"></script>
    <script src="js/script.js"></script>
    <script src="js/smoothscroll.js"></script>
    <script src="js/mousescroll.js"></script>
    <script src="js/wow.min.js"></script>
    <script type="text/javascript" src="js/scrolloverflow.js"></script>
    <script src="js/responsiveslides.min.js"></script>
    <script>
        // You can also use "$(window).load(function() {"
        $(function() {
            // Slideshow
            $("#slider").responsiveSlides({
                auto: true,
                pager: false,
                nav: true,
                speed: 500,
                namespace: "callbacks",
                before: function() {
                    $('.events').append("<li>before event fired.</li>");
                },
                after: function() {
                    $('.events').append("<li>after event fired.</li>");
                }
            });
        });
    </script>


    <!--[if lt IE 8]>
       <div style=' clear: both; text-align:center; position: relative;'>
         <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
           <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
        </a>
      </div>
    <![endif]-->
    <!--[if lt IE 9]>
		<script src="js/html5.js"></script>
		<script src="js/css3-mediaqueries.js"></script>
	<![endif]-->

</head>

<body>
    <div class="wrap-body">
        <!--////////////////////////////////////Header-->
        <header class="bg-theme animated slideInDown">
            <div class="wrap-header zerogrid">
                <div class="row" style="width: 100vw;">
                    <div id="cssmenu">
                        <ul>
                            <li class='active'><a href="shopping.php">Build Your Own Hovercraft</a>
                                <ul class="craft-drop">
                                    <li><a href="../recconf.php">Recreational </a></li>
                                    <li><a href="../rescconf.php">Rescue</a></li>
                                    <li><a href="../comconf.php">Commercial</a></li>
                                    <li><a href="../milconf.php">Military</a></li>
                                    <li><a href="shopping.php">
                                        <?php echo $_SESSION['hovercraft'] ?
                                        '<i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        My Cart
                                        <br /><b>'. $_SESSION['hovercraft'] .'</b>'
                                        : '<b>Start Building!</b>';?>
                                        </a>
                                    </li>
                                </ul>
                                <li>
                                    <a href="cart.php">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <?php echo $_SESSION['hovercraft'] ?
                                      '<b>' . $_SESSION['hovercraft'] . '</b>'
                                      : 'My Cart';?>
                                    </a>
                                </li>
                            </li>
                            <li><a href="used.php">Buy Used Craft</a></li>
                            <li><a href="#">Buy Parts</a>
                                <ul>
                                    <li><a href="accessories.php">Accessories</a></li>
                                    <li><a href="bodymarine.php">Body & Marine</a></li>
                                    <li><a href="drivetrain.php">Drivetrain</a></li>
                                    <li><a href="electrical.php">Electrical & Radio</a></li>
                                    <li><a href="trainingdocs.php">Training & Documents</a></li>
                                </ul>
                            </li>
                            <?php echo $_SESSION['usr'] ? '<li><a href="userinfo.php">'.$_SESSION["usr"].'</a><ul><li><a href="?logoff">Log Out</a></li></ul></li>': '<li class="active"><a href="login.php">Log In or Register</a></ul>';?>
                        </ul>
                    </div>
                    <a class="logo" href="index.php"><img src="images/logo.png" /></a>
                </div>
            </div>
        </header>
        <form name="form1" id="ff" method="post" action="">
            <div class="section " id="section1">
                <a name="parts"></a>
                <section id="container" style="margin-top: 12vh;">
                    <div class="zerogrid">
                        <div class="wrap-container clearfix">
                            <div id="main-content" class="col-3-3">
                                <?php
                                //set the directory where includes are stored
                                //include the neoteric files
                                include_once("include/neoteric_dbase.inc");
                                include_once("include/neoteric_conversion.inc");

                                $_SESSION['parts-order'] = array();

                                $count = 0;

                                //set the options array, which contains the database field name as the key and the title of each option section as the values
                                $options = array('dbAccessoriesClothing'=>"<b>Accessories & Clothing</b></br> <span class=\"tabinner\">Accessories for hovercraft use, including clothing, safety equipment, and shipping equipment.</span>");

                                //for each of the options...
                                foreach ($options as $key=>$option) {
                                    $query = "SELECT * FROM inventoryStock WHERE dbWebCategory = \"$key\" ORDER BY dbDescription ASC"; //You don't need a ; like you do in SQL
                                  $resultOptions = mysql_query($query);

                                  //print the table header information
                                  print "<div class=\"parts-table\" id=\"$key-section\">
                                    <div class=\"wrap-col\" align=\"center\" style=\"margin: 0px;\">
                                      <div class=\"col-3-3\">
                                        <p style=\"background-color:#3b3b3b; color:#fff; margin:0px; padding:10px; \">$option</p>
                                      </div>
                                    </div>";
                                    print "<ul class=\"paginator\">\n";
                                    print "<li><h3>Accessories & Clothing</h3></li>";

                                    while ($row = mysql_fetch_array($resultOptions)) {
                                        $rows[] = $row;
                                        if ($row['dbOnline'] == '1') {
                                            print "<li><a href=\"#" . $row['dbPartNumber'] . "\" class=\"scroll\" style=\"color:#fff;cursor:pointer;\">" . $row['dbDescription'] . "</a></li>";
                                        }
                                    }

                                    print "</ul>";

                                    //for each record returned, print out part information
                                    foreach ($rows as $row) {   //Creates a loop to loop through results
                                      $fprice = toMoney($row['dbFullAssemblyCost']);
                                        $pprice = toMoney($row['dbPartialAssemblyCost']);
                                        if (($pprice == '$0.00') || (strcmp($fprice, $pprice) == 0)) {
                                            $pprice = '';
                                        } else {
                                            $pprice = '<b>Price (partially-assembled): </b><i>(' . $pprice . ')</i>';
                                        }
                                        $pictureNumber=ltrim($row[dbPartNumber], '0');
                                        if (file_exists('/usr/local/apache/sites/photogallery/partImages/large/' . $pictureNumber . '.jpg')) {
                                            $photo = '<br /><img class="thumb-' . $key . '" src="http://photos.neoterichovercraft.com/partImages/large/' . $pictureNumber . '.jpg" border="0" alt="'. $row['dbDescription'] .'"/>';
                                        } else {
                                            $photo = '';
                                        }

                                        array_push($_SESSION['parts-order'], $row['dbDescription']);
                                        echo "<div class=\"part-page\" id=\"" . $row['dbPartNumber'] . "\">
                                        <p>" . $row['dbDescription'] . "" . $photo . "<br />
                                        <b>Part number: </b>" . $row['dbPartNumber'] . "</p>
                                        <p>" . $row['dbCustomerDescription'] . "</p>
                                        <p>" . $pprice . "</p>
                                        <p><b>Price: </b>" . $fprice . "<br />
                                        <p style=\"font-size:30px;\">
                                          <div style=\"margin:10px;\" id=\"quant" . $row['dbPartNumber'] . "\">Quantity?
                                            <i class=\"fa fa-shopping-cart\" aria-hidden=\"true\" style=\"display:inline;\"></i>
                                            <input type=\"number\" name=\"quantity[]\" min=\"0\" max=\"100\" placeholder=\"0\" id=\"num" . $row['dbPartNumber'] . "\" style=\"transform:scale(1.5,1.5);height:25px;\" value=\"" . $_SESSION['quantity'][$count] . "\">
                                            <input class=\"sendButton\" style=\"padding:5px;margin:10px;color:#000;\" type='submit' name='submit' value='Add to Cart?' />
                                          </div>
                                        </p>
                                      </div>";

                                        $count = $count + 1;
                                    }

                                  //advance the pointer to the array for the key() call
                                  //next($options);

                                  print "</div>";
                                }
                                print '</div>
                                </div>';
                                ?>
                            </div>
                        </div>
                        <div class="section " id="section2">
                            <a name="checkout"></a>
                            <div class="zerogrid">
                                <div class="wrap-container clearfix">
                                    <div id="main-content">
                                        <div class="row" <?php if ($_SESSION[ 'hovercraft']) { ; } else { echo 'style="display:none;"'; } ?> >
                                            <div class="col-1-1">
                                                <div class="wrap-col">
                                                    <div class="contact-header">
                                                        <h5>Craft Info</h5>
                                                    </div>
                                                    <div id="ff" style="background: #fff; padding: 20px; box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3);float:left;width:100%;">
                                                        <ul class="conf-list-item">
                                                            <div class="col-1-5">
                                                                <li>
                                                                    <p>&nbsp;Hovercraft: <textarea class="craft-info" readonly><?php echo $_SESSION['hovercraft']; ?></textarea></p>
                                                                </li>
                                                            </div>
                                                            <div class="col-1-5">
                                                                <li>
                                                                    <p>&nbsp;Seating: <textarea class="craft-info" readonly><?php echo $_SESSION['seating']; ?></textarea></p>
                                                                </li>
                                                            </div>
                                                            <div class="col-1-5">
                                                                <li>
                                                                    <p>&nbsp;Performance: <textarea class="craft-info" readonly><?php echo $_SESSION['engine']; ?></textarea></p>
                                                                </li>
                                                            </div>
                                                            <div class="col-1-5">
                                                                <li>
                                                                    <p>&nbsp;Color: <textarea class="craft-info" id="color-info" style="background-color:#<?php echo $_SESSION['color']; ?>;" readonly></textarea></p>
                                                                </li>
                                                            </div>
                                                            <div class="col-1-5">
                                                                <li>
                                                                    <p>&nbsp;Assembly: <textarea class="craft-info" readonly><?php echo $_SESSION['ass']; ?></textarea></p>
                                                                </li>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-1-1">
                                                <div class="col-1-2">
                                                    <div class="wrap-col">
                                                        <div class="contact-header">
                                                            <h5>Custom Options & Notes</h5>
                                                        </div>
                                                        <div id="ff" style="background: #fff; padding: 20px; box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3); ">
                                                            <textarea name="notes" id="notes"><?php echo $_SESSION['notes']; ?></textarea>
        </form>
        </div>
        </div>
        </div>
        <div class="col-1-2">
            <div class="wrap-col">
                <div class="contact-header">
                    <h5>Parts Info</h5>
                </div>
                <div id="ff" style="background: #fff; padding: 20px; box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3); ">
                    <textarea readonly>
                                                        <?php
                                                        for ($i = 0; $i < 1000; $i++) {
                                                            if ($_SESSION['quantity'][$i] > 0) {
                                                                print 'Part: ' . $_SESSION['parts-order'][$i] . '
                                                            Quantity: ' . $_SESSION['quantity'][$i] . '
                                                            ';
                                                            };
                                                        };
                                                        ?>
                                                      </textarea>
                    </form>
                </div>
            </div>
        </div>
        </div>
        </div>
        <div class="row">
            <div class="col-1-1">
                <div class="wrap-col" style="margin: 0px 10px;">
                    <div class="contact-header">
                        <h5>Review and Submit </h5>
                    </div>
                    <div id="contact_form">
                        <p>Please review your selections, and make any comments as necessary. If you are happy with your selections, click the "Submit" button at the bottom of this form. <b>Please note:</b> this form is not a legally-binding contract, nor
                            are you required to make payment at this time. When you submit the form, a Neoteric representative will receive it and contact you as soon as possible to discuss final arrangements and payment. Thank you for choosing Neoteric!</p>
                        <div id="ff" class="buttons">
                            <input class="sendButton" type="submit" name="submit" value="Update">
                            <input class="sendButton" type="submit" name="submit" value="Add Craft">
                            <input class="sendButton" type="submit" name="submit" value="To Checkout">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </section>
        <!--////////////////////////////////////Footer-->
        <footer class="bg-theme animated slideInUp">
            <div class="wrap-header zerogrid">
                <div class="row">
                    <div id="cssmenu">
                        <ul class="fa-ul">
                            <li><a href="http://neoterichovercraft.blogspot.com/" target="_blank"><i class="fa fa-bold fa-lg"></i></a></li>
                            <li><a href="../../aboutus.php#contact"><i class="fa fa-envelope fa-lg"></i></a></li>
                            <li><a href="https://www.facebook.com/Neoteric.Hovercraft.Inc/" target="_blank"><i class="fa fa-facebook fa-lg"></i></a></li>
                            <li><a href="https://www.linkedin.com/company/neoteric-hovercraft-inc" target="_blank"><i class="fa fa-linkedin fa-lg"></i></a></li>
                            <li><a href="https://twitter.com/neohovercraft" target="_blank"><i class="fa fa-twitter fa-lg"></i></a></li>
                            <li><a href="https://www.youtube.com/user/NeotericHovercraft" target="_blank"><i class="fa fa-youtube fa-lg"></i></a></li>
                        </ul>
                    </div>
                    <a class="logo" href="http://www.NeotericHovercraft.com">
                        <p>&copy; 2016 Neoteric Hovercraft, Inc.</p>
                    </a>
                    <a href="privacypolicy.php" class="logo">
                        <p><i>Privacy Policy</i></p>
                    </a>
                </div>
                <div id='ss_menu'>
                    <div><a href="../../aboutus.php#contact"><i class="fa fa-envelope"></i></a></div>
                    <div><a href="https://www.facebook.com/Neoteric.Hovercraft.Inc/" target="_blank"><i class="fa fa-facebook"></i></a></div>
                    <div><a href="https://twitter.com/neohovercraft" target="_blank"><i class="fa fa-twitter"></i></a></div>
                    <div><a href="https://www.youtube.com/user/NeotericHovercraft" target="_blank"><i class="fa fa-youtube"></i></a></div>
                    <div class='menu'>
                        <div class='share' id='ss_toggle' data-rot='180'>
                            <div class='circle'></div>
                            <div class='bar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script>
        $(document).ready(function(ev) {
            var toggle = $('#ss_toggle');
            var menu = $('#ss_menu');
            var rot;

            $('#ss_toggle').on('click', function(ev) {
                rot = parseInt($(this).data('rot')) - 180;
                menu.css('transform', 'rotate(' + rot + 'deg)');
                menu.css('webkitTransform', 'rotate(' + rot + 'deg)');
                if ((rot / 180) % 2 == 0) {
                    //Moving in
                    toggle.parent().addClass('ss_active');
                    toggle.addClass('close');
                } else {
                    //Moving Out
                    toggle.parent().removeClass('ss_active');
                    toggle.removeClass('close');
                }
                $(this).data('rot', rot);
            });

            menu.on('transitionend webkitTransitionEnd oTransitionEnd', function() {
                if ((rot / 180) % 2 == 0) {
                    $('#ss_menu div i').addClass('ss_animate');
                } else {
                    $('#ss_menu div i').removeClass('ss_animate');
                }
            });
        });
    </script>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-36251023-1']);
        _gaq.push(['_setDomainName', 'jqueryscript.net']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <script>
        $(document).ready(function() {
            $('.thumb').toggle(function() {
                $(this).addClass('transition').not($(this)).removeClass('transition');
                $('#darken').css("display", "block");

            }, function() {
                $(this).removeClass('transition');
                $('#darken').css("display", "none");
            });
            $('#close').click(function() {
                $('.thumb').removeClass('transition');
                $('#darken').css("display", "none");
            });
        });
    </script>
    <script>
        $('a.checkout-tab').click(function(e) {
            // Special stuff to do when this link is clicked...

            // Cancel the default action
            e.preventDefault();
        });
    </script>
</body>
</html>
