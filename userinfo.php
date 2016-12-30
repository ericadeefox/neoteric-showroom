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

if ($_SESSION['id'] && !isset($_COOKIE['cookies']) && !$_SESSION['rememberMe']) {
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

            setcookie('tzRemember', $_POST['rememberMe']);
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

if ($_POST['submit']=='Submit') {
    $staffMessage = "A new message has been sent by ".$_SESSION['firstname']." ".$_SESSION['lastname']." via the Neoteric Hovercraft website.
	<br />Here are all the details of the message:
	<br />Company: ".$_SESSION['company']."
	<br />Email: ".$_SESSION['email']."
	<br />Subject: ".$_POST['subject']."
	<br />They've left the following message: <br />".$_POST['message']."
	<br />Thanks for using the Neoteric Hovercraft website!";

    $customerMessage = "Thank you, ".$_SESSION['firstname']." ".$_SESSION['lastname']."! Your message to Neoteric Hovercraft has been received. Please do not respond to this email; a Neoteric representative will contact you as soon as possible!
	<br />Here are all the details of the message:
	<br />Company: ".$_SESSION['company']."
	<br />Email: ".$_SESSION['email']."
	<br />Subject: ".$_POST['subject']."
	<br />You've left the following message: <br />".$_POST['message']."
	<br />Thanks for using the Neoteric Hovercraft website!";

    send_mail('webmaster@neoterichovercraft.com',
    'erica@neoterichovercraft.com',
    'A new message received from '.$_SESSION['firstname'].' '.$_SESSION['lastname'].'.',
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'guthrie@neoterichovercraft.com',
    'A new message received from '.$_SESSION['firstname'].' '.$_SESSION['lastname'].'.',
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'barb@neoterichovercraft.com',
    'A new message received from '.$_SESSION['firstname'].' '.$_SESSION['lastname'].'.',
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'chris@neoterichovercraft.com',
    'A new message received from '.$_SESSION['firstname'].' '.$_SESSION['lastname'].'.',
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    'filip@neoterichovercraft.com',
    'A new message received from '.$_SESSION['firstname'].' '.$_SESSION['lastname'].'.',
    $staffMessage);
    send_mail('webmaster@neoterichovercraft.com',
    $_SESSION['email'],
    'Your message to Neoteric Hovercraft has been received!',
    $customerMessage);

    header("Location: userinfo.php");
    exit;
}

$script = '';

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
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsiveslides.css">
    <link rel="stylesheet" href="css/unslider.css">
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />

    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->

    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <?php echo $script; ?>
    <script src="js/jquery-latest.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/jquery183.min.js"></script>
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
        <!--////////////////////////////////////Container-->
        <section id="container" style="overflow:hidden;">
            <div class="zerogrid">
                <div class="wrap-container clearfix">
                    <div id="main-content">
                        <div class="wrap-box">
                            <!--Start Box-->
                            <div class="header">
                                <h1></h1>
                                <p></p>
                            </div>
                            <div class="row">

                                <div class="row">
                                    <div class="col-1-1">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>My Account Information</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <center>
                                                        <h5>Welcome back, <?php echo $_SESSION['firstname'] ?>! You may review your user information here.</h5></center><br />
                                                    <center><a class="shop-buttons" href="edituserinfo.php">edit your account info</a></center><br />

                                                    <?php

                        if ($_SESSION['msg']['reg-err']) {
                            echo '<div class="err" >'.$_SESSION['msg']['reg-err'].'</div>';
                            unset($_SESSION['msg']['reg-err']);
                        }

                        if ($_SESSION['msg']['reg-success']) {
                            echo '<div class="success" >'.$_SESSION['msg']['reg-success'].'</div>';
                            unset($_SESSION['msg']['reg-success']);
                        }
                    ?>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Username:</b>
                                                                <?php echo $_SESSION['usr'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Email address:</b>
                                                                <?php echo $_SESSION['email'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>First name:</b>
                                                                <?php echo $_SESSION['firstname'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Last name:</b>
                                                                <?php echo $_SESSION['lastname'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Phone number:</b>
                                                                <?php echo $_SESSION['phone'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Company (if applicable):</b>
                                                                <?php echo $_SESSION['company'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Address:</b>
                                                                <?php echo $_SESSION['address'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>City:</b>
                                                                <?php echo $_SESSION['city'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>State, territory, or province:</b>
                                                                <?php echo $_SESSION['state'] ;?>
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>ZIP Code:</b>
                                                                <?php echo $_SESSION['zip'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-1">
                                                        <div class="wrap-col">
                                                            <p><b>Country:</b>
                                                                <?php echo $_SESSION['country'] ;?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-1-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>My Quote History</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="banner">
                                                        <ul>
                                                            <?php
                                                include_once("include/neoteric_conversion.inc");
                                                $query = mysql_query("SELECT * FROM neoteric.quote WHERE dbContactNumber = '" . $_SESSION['id'] . "' ORDER BY dbOrderDate DESC");
                                                while ($quote = mysql_fetch_array($query)) {
                                                    $totalCost = mysql_fetch_assoc(mysql_query("SELECT dbUnitCost, dbQuantity, SUM(dbUnitCost * dbQuantity) as dbTotalCost FROM neoteric.quoteParts WHERE dbQuoteNumber = '" . $quote['dbQuoteNumber'] . "'"));
                                                    $total = toMoney($totalCost['dbTotalCost']);
                                                    $totalPaid = toMoney($quote['dbTotalPayments']);
                                                    $originalDate = $quote['dbOrderDate'];
                                                    $orderDate = date("F j, Y", strtotime($originalDate));
                                                    echo "<li><p><b>Quote number:</b> " . $quote['dbQuoteNumber'] . "
													<br /><b>Order date:</b> " . $orderDate . "
													<br /><b>Total cost:</b> " . $total . "
													<br /><b>Total paid:</b> " . $totalPaid . "</p></li>";
                                                }
                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-2-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Active Invoices</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="banner">
                                                        <ul>
                                                            <li>
                                                                <p>These are invoices with payment pending for orders you've placed with Neoteric Hovercraft.</p>
                                                                <p>If you have any questions concerning any of these orders, please use the contact form at the bottom of this page, and reference your invoice number in the subject. Thank you!</li>
                                                            <?php
                                                include_once("include/neoteric_conversion.inc");
                                                $query = mysql_query("SELECT * FROM neoteric.quote WHERE dbContactNumber = '" . $_SESSION['id'] . "' ORDER BY dbOrderDate DESC");
                                                while ($quote = mysql_fetch_array($query)) {
                                                    if ($quote['dbPaid']==0 && $quote['dbInvoiceNumber'] != '0') {
                                                        $totalCost = mysql_fetch_assoc(mysql_query("SELECT dbUnitCost, dbQuantity, SUM(dbUnitCost * dbQuantity) as dbTotalCost FROM neoteric.quoteParts WHERE dbQuoteNumber = '" . $quote['dbQuoteNumber'] . "'"));
                                                        $total = toMoney($totalCost['dbTotalCost']);
                                                        $totalPaid = toMoney($quote['dbTotalPayments']);
                                                        $totalDue = toMoney($totalCost['dbTotalCost'] - $quote['dbTotalPayments']);
                                                        $originalDate = $quote['dbOrderDate'];
                                                        $orderDate = date("F j, Y", strtotime($originalDate));
                                                        echo "<li><p class='active-orders'><b>Invoice number:</b> " . $quote['dbInvoiceNumber'] . "
													<br /><b>Order date:</b> " . $orderDate . "
													<br /><b>Payment terms:</b> " . $quote['dbTerms'] . "
													<br /><b>Shipping method:</b> " . $quote['dbShippingMethod'] . "</p>";

                                                        echo "<p class='active-orders'><b>Total cost:</b> " . $total . "
													<br /><b>Total paid:</b> " . $totalPaid . "
													<br /><b>Total due:</b> " . $totalDue . "</p></li>";
                                                    }
                                                }
                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1-1">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>My Work-in-Progress</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="banner">
                                                        <ul>
                                                            <?php
                                                $dir = "/var/www/photos.neoterichovercraft.com/galleries/Building/". $_SESSION['usr'] . "/photos/";
                                                $prod = "http://photos.neoterichovercraft.com/galleries/Building/". $_SESSION['usr'] . "/photos/";
                                                $handle = opendir($dir);

                                                if ($handle) {
                                                    /* This is the correct way to loop over the directory. */
                                                   while (false !== ($entry = readdir($handle))) {
                                                       if (preg_match('/.jpg/', $entry)) {
                                                           $image = $prod . $entry;
                                                           mysql_select_db('webimages', $link);
                                                           $picInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM webimages.images WHERE filename = '$entry'"));
                                                           echo "<li><h4 style=\"color:#666;text-shadow:none;\">" . $picInfo['short_desc'] . "</h4><br /><img src=\"" . $image . "\"><br />" . $picInfo['long_desc'] . "</li>";
                                                       }
                                                   }

                                                    closedir($handle);
                                                } else {
                                                    echo "<br /><li>You have no current works-in-progress.</li>";
                                                }
                                                ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3-3">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Contact us!</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <form name="contact" method="post" action="" id="ff">
                                                        <p class="moreinfo">As a Neoteric customer, we are here to answer any questions or address any concerns you may have about your hovercraft. We can be reached by phone from 8:00 AM to 8:00 PM Eastern time, seven days
                                                            a week. If you would like to email us, or you are contacting after business hours, feel free to use this form to send us a customized message.</p>
                                                        <div class="col-1-3">
                                                            <div class="wrap-col">
                                                                <label>First name: </label><input type="text" name="firstname" value="<?php echo $_SESSION['firstname'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-1-3">
                                                            <div class="wrap-col">
                                                                <label>Last name: </label><input type="text" name="lastname" value="<?php echo $_SESSION['lastname'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-1-3">
                                                            <div class="wrap-col">
                                                                <label>Company: </label><input type="text" name="company" value="<?php echo $_SESSION['company'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-1-2">
                                                            <div class="wrap-col">
                                                                <label>Email address: </label><input type="text" name="email" value="<?php echo $_SESSION['email'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-1-2">
                                                            <div class="wrap-col">
                                                                <label>Subject: </label><input type="text" name="subject" placeholder="e.g. Order #22675: Change in seating arrangement">
                                                            </div>
                                                        </div>
                                                        <textarea name="message" rows="3" cols="20" placeholder="Please leave us a brief message"></textarea>
                                                        <span id="centerme"><p><input type="submit" name="submit" value="Submit" class="shop-buttons"></p></span>
                                                    </form>
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
        <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="js/unslider.js" type="text/javascript"></script>
        <script>
            $(function() {
                $('.banner').unslider()
            })
        </script>
</body>

</html>
