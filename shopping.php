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
    $_SESSION['hovercraft'] = $_POST['hovercraft'];
    $_SESSION['seating'] = $_POST['seating'];
    $_SESSION['engine'] = $_POST['engine'];
    $_SESSION['injection'] = $_POST['injection'];
    $_SESSION['color'] = $_POST['color'];
    $_SESSION['ass'] = $_POST['ass'];
    $_SESSION['notes'] = $_POST['notes'];

    header("Location: shopping.php");
    exit;
}

if ($_POST['submit']=='Add Options') {
    $_SESSION['hovercraft'] = $_POST['hovercraft'];
    $_SESSION['seating'] = $_POST['seating'];
    $_SESSION['engine'] = $_POST['engine'];
    $_SESSION['injection'] = $_POST['injection'];
    $_SESSION['color'] = $_POST['color'];
    $_SESSION['ass'] = $_POST['ass'];
    $_SESSION['notes'] = $_POST['notes'];

    header("Location: accessories.php");
    exit;
}

if ($_POST['submit']=='Make Quote') {
    $_SESSION['hovercraft'] = $_POST['hovercraft'];
    $_SESSION['seating'] = $_POST['seating'];
    $_SESSION['engine'] = $_POST['engine'];
    $_SESSION['injection'] = $_POST['injection'];
    $_SESSION['color'] = $_POST['color'];
    $_SESSION['ass'] = $_POST['ass'];
    $_SESSION['notes'] = $_POST['notes'];

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
    <div class="wrap-body" style="overflow-x:hidden;">
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
            <a name="craft"></a>
            <div class="section " id="section0">
                <!--////////////////////////////////////Container-->
                <div class="zerogrid">
                    <div class="wrap-container clearfix">
                        <div id="main-content">
                            <div class="wrap-box">
                                <!--Start Box-->
                                <div class="row">
                                    <div class="col-3-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Hovercraft Categories</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <label class="row border1">
                                                      <div class="col-1-4">
                                                        <div class="wrap-col">
                                                          <input type="radio" class="radio" name="hovercraft" id="Commercial" value="Commercial" onclick="hoverselect()" <?php if ($_SESSION["hovercraft"] == "Commercial") {
                                                            echo "checked";
                                                          }?> />
                                                          <label for="Commercial" class="label">
                                                            <img src="images/icons/commercial.png" alt="Commercial craft"/></label>
                                                    <label class="hoverselect">Commercial</label>
                                                </div>
                                            </div>
                                            <div class="col-1-4">
                                                <div class="wrap-col">
                                                    <input type="radio" class="radio" name="hovercraft" id="Military" value="Military" onclick="hoverselect()" <?php if ($_SESSION[ "hovercraft"]=="Military" ) { echo "checked"; }?> />
                                                    <label for="Military" class="label"><img src="images/icons/military.png" alt="Military craft"/></label>
                                                    <label class="hoverselect">Military</label>
                                                </div>
                                            </div>
                                            <div class="col-1-4">
                                                <div class="wrap-col">
                                                    <input type="radio" class="radio" name="hovercraft" id="Recreational" value="Recreational" onclick="hoverselect()" <?php if ($_SESSION[ "hovercraft"]=="Recreational" ) { echo "checked"; }?> />
                                                    <label for="Recreational" class="label"><img src="images/icons/recreational.png" alt="Recreational craft"/></label>
                                                    <label class="hoverselect">Recreation</label>
                                                </div>
                                            </div>
                                            <div class="col-1-4">
                                                <div class="wrap-col">
                                                    <input type="radio" class="radio" name="hovercraft" id="Rescue" value="Rescue" onclick="hoverselect()" <?php if ($_SESSION[ "hovercraft"]=="Rescue" ) { echo "checked"; }?> />
                                                    <label for="Rescue" class="label"><img src="images/icons/rescue.png" alt="Rescue craft"/></label>
                                                    <label class="hoverselect">Rescue</label>
                                                </div>
                                            </div>
                                            </label>
                                            <label class="row border1">
												<div class="col-1-1">
													<div class="wrap-col">
						<a class="scroll" href="#seating" id="hovercraft-desc">A description of what your hovercraft will be.</a>
													</div>
												</div>
											</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3-3">
                                <div class="wrap-col">
                                    <div class="contact">
                                        <div class="contact-header">
                                            <h5>Seating Options</h5>
                                        </div>
                                        <div id="contact_form">
                                            <label class="row border1" id="seating">

												<div class="col-1-4">
													<div class="wrap-col">
													<input type="radio" class="radio" name="seating" id="1x4sidebyside" value="1x4-sidebyside" <?php if ($_SESSION["seating"] == "1x4-sidebyside") {
                    echo "checked";
                }?> onclick="seatingselect()"/>
													<label for="1x4sidebyside" class="label"><img src="images/icons/1x4-sidebyside.png" alt="4-seat side-by-side"/></label>
                                            <label class="hoverselect">1x4 Side by Side</label>
                                        </div>
                                    </div>
                                    <div class="col-1-4">
                                        <div class="wrap-col">
                                            <input type="radio" class="radio" name="seating" id="1x6sidebyside" value="1x6-sidebyside" <?php if ($_SESSION[ "seating"]=="1x6-sidebyside" ) { echo "checked"; }?> onclick="seatingselect()"/>
                                            <label for="1x6sidebyside" class="label"><img src="images/icons/1x6-sidebyside.png" alt="6-seat side-by-side"/></label>
                                            <label class="hoverselect">1x6 Side by Side</label>
                                        </div>
                                    </div>
                                    <div class="col-1-4">
                                        <div class="wrap-col">
                                            <input type="radio" class="radio" name="seating" id="1x4inline" value="1x4-inline" <?php if ($_SESSION[ "seating"]=="1x4-inline" ) { echo "checked"; }?> onclick="seatingselect()"/>
                                            <label for="1x4inline" class="label"><img src="images/icons/1x4-inline.png" alt="4-seat inline"/></label>
                                            <label class="hoverselect">1x4 Inline</label>
                                        </div>
                                    </div>
                                    <div class="col-1-4">
                                        <div class="wrap-col">
                                            <input type="radio" class="radio" name="seating" id="1x6inline" value="1x6-inline" <?php if ($_SESSION[ "seating"]=="1x6-inline" ) { echo "checked"; }?> onclick="seatingselect()"/>
                                            <label for="1x6inline" class="label"><img src="images/icons/1x6-inline.png" alt="6-seat inline"/></label>
                                            <label class="hoverselect">1x6 Inline</label>
                                        </div>
                                    </div>
                                    </label>
                                    <label class="row border1">
												<div class="col-1-1">
													<div class="wrap-col">
						<a class="scroll" href="#performance" id="seating-desc">A description of how your seating will be.</a>
													</div>
												</div>
											</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3-3">
                        <div class="wrap-col">
                            <div class="contact">
                                <div class="contact-header">
                                    <h5>Engine Options</h5>
                                </div>
                                <div id="contact_form">
                                    <label class="row border1" id="performance">
											<div class="row">
												<div class="col-1-5">
													<div class="wrap-col">
													<input type="radio" class="radio" name="engine" id="55hp" value="55hp Carbureted" <?php if ($_SESSION["engine"] == "55hp Carbureted") {
                    echo "checked";
                }?> onclick="performanceselect()"/>
													<label for="55hp" class="label"><img src="images/icons/55hpcarb.png" alt="55hp carbureted"/></label>
                                    <label class="hoverselect">55hp Carbureted</label>
                                </div>
                            </div>
                            <div class="col-1-5">
                                <div class="wrap-col">
                                    <input type="radio" class="radio" name="engine" id="65hp" value="65hp Carbureted" <?php if ($_SESSION[ "engine"]=="65hp Carbureted" ) { echo "checked"; }?> onclick="performanceselect()"/>
                                    <label for="65hp" class="label"><img src="images/icons/65hpcarb.png" alt="65hp carbureted"/></label>
                                    <label class="hoverselect">65hp Carbureted</label>
                                </div>
                            </div>
                            <div class="col-1-5">
                                <div class="wrap-col">
                                    <input type="radio" class="radio" name="engine" id="100hp" value="100hp Carbureted" <?php if ($_SESSION[ "engine"]=="100hp Carbureted" ) { echo "checked"; }?> onclick="performanceselect()"/>
                                    <label for="100hp" class="label"><img src="images/icons/100hpcarb.png" alt="100hp carbureted"/></label>
                                    <label class="hoverselect">100hp Carbureted</label>
                                </div>
                            </div>
                            <div class="col-1-5">
                                <div class="wrap-col">
                                    <input type="checkbox" class="checkbox" name="injection[]" id="fuelinjection" value="Fuel Injection" <?php if (in_array( "Fuel Injection", $_SESSION[ 'injection'])) { echo "checked"; }?> onclick="performanceselect()"/>
                                    <label for="fuelinjection" class="label"><img src="images/icons/fuelinjected.png" alt="Fuel injection"/></label>
                                    <label class="hoverselect">Fuel Injection</label>
                                </div>
                            </div>
                            <div class="col-1-5">
                                <div class="wrap-col">
                                    <input type="checkbox" class="checkbox" name="injection[]" id="oilinjection" value="Oil Injection" <?php if (in_array( "Oil Injection", $_SESSION[ 'injection'])) { echo "checked"; }?> onclick="performanceselect()"/>
                                    <label for="oilinjection" class="label"><img src="images/icons/oilinjected.png" alt="Oil injection"/></label>
                                    <label class="hoverselect">Oil Injection</label>
                                </div>
                            </div>
                        </div>
                        </label>
                        <label class="row border1">
												<div class="col-1-1">
													<div class="wrap-col">
						<p id="performance-desc">A description of what your engine & performance will be.</p>
													</div>
												</div>
											</label>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-3-3">
        <div class="wrap-col">
            <div class="contact">
                <div class="contact-header">
                    <h5>Color Options</h5>
                </div>
                <div id="contact_form">
                    <label class="row border1" id="color">
												<div class="col-1-2">
													<div class="wrap-col">
						<p>Selecting a color other than rescue red or white will require color-matching a gel coat and therefore extra charges will apply.</p>
													</div>
												</div>
												<div class="col-1-3">
													<div class="wrap-col">
														<input name="color" id="color" class="jscolor" style="padding:20px;" value="<?php echo $_SESSION['color']; ?>">
													</div>

												</div>
											</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-3-3">
        <div class="wrap-col">
            <div class="contact">
                <div class="contact-header">
                    <h5>Assembly Options</h5>
                </div>
                <div id="contact_form">
                    <label class="row border1">
											<div class="row">
												<div class="col-1-5">
													<div class="wrap-col">
													</div>
												</div>
												<div class="col-1-5">
													<div class="wrap-col">
													<input type="radio" class="radio" name="ass" id="full" value="Fully assembled" <?php if ($_SESSION["ass"] == "Fully assembled") {
                    echo "checked";
                }?> onclick="assselection()"/>
													<label for="full" class="label"><img src="images/icons/full.png" alt="Fully assembled"/></label>
                    <label class="hoverselect">Fully assembled</label>
                </div>
            </div>
            <div class="col-1-5">
                <div class="wrap-col">
                    <input type="radio" class="radio" name="ass" id="partial" value="Partially assembled" <?php if ($_SESSION[ "ass"]=="Partially assembled" ) { echo "checked"; }?> onclick="assselection()"/>
                    <label for="partial" class="label"><img src="images/icons/partial.png" alt="Partially assembled"/></label>
                    <label class="hoverselect">Partially assembled</label>
                </div>
            </div>
            <div class="col-1-5">
                <div class="wrap-col">
                    <input type="radio" class="radio" name="ass" id="self" value="I will assemble it" <?php if ($_SESSION[ "ass"]=="I will assemble it" ) { echo "checked"; }?> onclick="assselection()"/>
                    <label for="self" class="label"><img src="images/icons/self.png" alt="Self-assembled"/></label>
                    <label class="hoverselect">I will assemble it</label>
                </div>
            </div>
            <div class="col-1-5">
                <div class="wrap-col">
                </div>
            </div>
        </div>
        <label class="row border1">
												<div class="col-1-1">
													<div class="wrap-col">
						<a class="scroll" href="#section2" id="ass-desc">How would you like your craft assembled, if at all?</a>
													</div>
												</div>
											</label>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="section " id="section2" style="margin-top:0px;">
        <a name="checkout"></a>
        <div class="zerogrid">
            <div class="wrap-container clearfix">
                <div id="main-content">
                    <div class="row">
                        <div class="col-1-1">
                            <div class="wrap-col">
                                <div class="contact-header">
                                    <h5>Additional Options?</h5>
                                </div>
                                <div id="ff" style="background: #fff; padding: 20px; box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3); ">
                                    <textarea readonly><?php
                                        for ($i = 0; $i < 1000; $i++) {
                                            if ($_SESSION['quantity'][$i] > 0) {
                                                print 'Part: ' . $_SESSION['parts-order'][$i] . '
												Quantity: ' . $_SESSION['quantity'][$i] . '
												';
                                            };
                                        };
                                        if (!$_SESSION['quantity']) {
                                            print 'You have not requested any additional options. If you\'d like to request additional options, visit the parts catalog by clicking "Add Options" below.';
                                        };
                                    ?></textarea></form>
                                </div>
                            </div>


                            <div class="col-1-1">
                                <div class="col-1-1">
                                    <div class="wrap-col">
                                        <div class="contact-header">
                                            <h5>Any Special Requests?</h5>
                                        </div>
                                        <div id="ff" style="background: #fff; padding: 20px; box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3); ">
                                            <textarea name="notes" id="notes"><?php echo $_SESSION['notes']; ?></textarea></form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin: 0px 10px;">
                        <div class="col-1-1">
                            <div class="contact-header">
                                <h5>Review and Submit </h5>
                            </div>
                            <div id="contact_form">
                                <p>Please review your selections, and make any comments as necessary. If you are happy with your selections, click the "Submit" button at the bottom of this form. <b>Please note:</b> this form is not a legally-binding contract,
                                    nor are you required to make payment at this time. When you submit the form, a Neoteric representative will receive it and contact you as soon as possible to discuss final arrangements and payment. Thank you for choosing
                                    Neoteric!</p>
                                <div id="ff" class="buttons">
                                    <input class="sendButton" type="submit" name="submit" value="Update">
                                    <input class="sendButton" type="submit" name="submit" value="Add Options">
                                    <input class="sendButton" type="submit" name="submit" value="Make Quote">
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
        var myHoverdrop = document.getElementsByName('hovercraft');
        var myHoverdesc = document.getElementById('hovercraft-desc');
        var mySeatdrop = document.getElementsByName('seating');
        var mySeatdesc = document.getElementById('seating-desc');
        var myEngdrop = document.getElementsByName('engine');
        var myInjdrop = document.getElementsByName('injection');
        var myPerfdesc = document.getElementById('performance-desc');
        var myColordrop = document.getElementById('color');
        var myAssdrop = document.getElementsByName('ass');
        var myAssdesc = document.getElementById('ass-desc');

        function hoverselect() {
            if (myHoverdrop.checked = true) {
                if (document.getElementById("Recreational").checked) {
                    myHoverdesc.innerHTML =
                        "Why own a boat, a kayak, canoe, jet boat, jet ski, and a snowmobile, when all you need is a hovercraft – the all-in-one, all-season recreational vehicle. A hovercraft flies you smoothly over land, water, sand, mud, swamp, snow, and ice, letting you explore areas that boats and other recreational vehicles can't reach. <p class='sendButton'>Continue to Seating!</p>";
                } else if (document.getElementById("Commercial").checked) {
                    myHoverdesc.innerHTML =
                        "Neoteric hovercraft are utilized throughout the world for a wide variety of commercial applications, including mosquito control, oil spill cleanup, gold mining, wildlife conservation, commercial fishing, forestry. <p class='sendButton'>Continue to Seating!</p>";
                } else if (document.getElementById("Rescue").checked) {
                    myHoverdesc.innerHTML =
                        "First responders worldwide have recognized that the hovercraft is the only rescue vehicle able to perform fast, safe rescue operations on swift water, thin or broken ice, flood waters and snow. Because it safely hovers 9 inches above the terrain, a hovercraft keeps the rescue team above the danger – not in it – and gives rescuers access to areas a boat or helicopter cannot reach. <p class='sendButton'>Continue to Seating!</p>";
                } else if (document.getElementById("Military").checked) {
                    myHoverdesc.innerHTML =
                        "The hovercraft is the only vehicle able to perform fast, safe operations on swift water, thin or broken ice, snow, mud, swampland and flood waters. Because it safely hovers 9 inches above the terrain, a hovercraft keeps military personnel above the danger - not in it - and allows access to areas that helicopters or boats cannot reach. <p class='sendButton'>Continue to Seating!</p>";
                } else {
                    myHoverdesc.innerHTML = "Please select one of these four hovercraft models. If none of these four hovercraft meet what you have in mind, please describe your circumstances in the custom options & notes section.";
                }
            }
        };

        function seatingselect() {
            if (mySeatdrop.checked = true) {
                if (document.getElementById("1x4sidebyside").checked) {
                    mySeatdesc.innerHTML = "4 persons, 600 lb (272 kg) average. 750 lb (340 kg) max. Overload 7 persons, 1200lb (546 kg). 2 bench seats allow 2 people to sit next to each other. <p class='sendButton'>Continue to Drivetrain!</p>";
                } else if (document.getElementById("1x4inline").checked) {
                    mySeatdesc.innerHTML =
                        "4 persons, 600 lb (272 kg) average. 750 lb (340 kg) max. Overload 7 persons, 1200lb (546 kg). 1 inline seat down the center allows 4 people to sit in a line. <p class='sendButton'>Continue to Drivetrain!</p>";
                } else if (document.getElementById("1x6sidebyside").checked) {
                    mySeatdesc.innerHTML = "6 persons, 900 lb (408 kg) average. 1025 lb (466 kg) max. 3 bench seats allow 2 people to sit next to each other. <p class='sendButton'>Continue to Drivetrain!</p>";
                } else if (document.getElementById("1x6inline").checked) {
                    mySeatdesc.innerHTML = "6 persons, 900 lb (408 kg) average. 1025 lb (466 kg) max. 1 inline seat down the center allows 6 people to sit in a line. <p class='sendButton'>Continue to Drivetrain!</p>";
                } else {
                    mySeatdesc.innerHTML = "If these seating arrangements do not meet your needs, please specify desired seating arrangements in the comments.";
                }
            }
        };

        function performanceselect() {
            if ((myEngdrop.checked = true) && (myInjdrop.checked = true)) {
                if (document.getElementById("55hp").checked) {
                    myPerfdesc.innerHTML = "Hirth 2703 55hp carbureted 2-cycle aircraft engine.";
                    if (document.getElementById("fuelinjection").checked) {
                        myPerfdesc.innerHTML = "Hirth 2703 55hp fuel injected 2-cycle aircraft engine.";
                        if (document.getElementById("oilinjection").checked) {
                            myPerfdesc.innerHTML =
                                "<font color='#ff0000'>We currently do not offer this powertrain combination. Since the quest to guarantee top quality never ends at Neoteric, new engine testing is a constant endeavor. Please let us know if there are any custom engine or drivetrain specifications that you request.</font>";
                        }
                    } else if (document.getElementById("oilinjection").checked) {
                        myPerfdesc.innerHTML = "Hirth 2703 55hp oil injected and carbureted 2-cycle aircraft engine.";
                    }
                } else if (document.getElementById("65hp").checked) {
                    myPerfdesc.innerHTML = "Hirth 3203 65hp carbureted 2-cycle aircraft engine.";
                    if (document.getElementById("fuelinjection").checked) {
                        myPerfdesc.innerHTML = "Hirth 3203 65hp fuel injected 2-cycle aircraft engine.";
                        if (document.getElementById("oilinjection").checked) {
                            myPerfdesc.innerHTML = "Hirth 3203 65hp oil and fuel injected 2-cycle aircraft engine.";
                        }
                    } else if (document.getElementById("oilinjection").checked) {
                        myPerfdesc.innerHTML = "Hirth 3203 65hp oil injected and carbureted 2-cycle aircraft engine.";
                    }
                } else if (document.getElementById("100hp").checked) {
                    myPerfdesc.innerHTML = "Hirth 3701 100hp carbureted 2-cycle aircraft engine.";
                    if (document.getElementById("fuelinjection").checked) {
                        myPerfdesc.innerHTML =
                            "<font color='#ff0000'>We currently do not offer this powertrain combination. Since the quest to guarantee top quality never ends at Neoteric, new engine testing is a constant endeavor. Please let us know if there are any custom engine or drivetrain specifications that you request.</font>";
                        if (document.getElementById("oilinjection").checked) {
                            myPerfdesc.innerHTML =
                                "<font color='#ff0000'>We currently do not offer this powertrain combination. Since the quest to guarantee top quality never ends at Neoteric, new engine testing is a constant endeavor. Please let us know if there are any custom engine or drivetrain specifications that you request.</font>";
                        }
                    } else if (document.getElementById("oilinjection").checked) {
                        myPerfdesc.innerHTML =
                            "<font color='#ff0000'>We currently do not offer this powertrain combination. Since the quest to guarantee top quality never ends at Neoteric, new engine testing is a constant endeavor. Please let us know if there are any custom engine or drivetrain specifications that you request.</font>";
                    }
                }
            }
        };

        function assselection() {
            if (myAssdrop.checked = true) {
                if (document.getElementById("full").checked) {
                    myAssdesc.innerHTML =
                        "Neoteric has a level of experience and professionalism superior to any other light hovercraft manufacturer. This is why they consistently produce hovercraft that are innovative and a standard in the industry. <p class='sendButton'>Continue to Checkout!</p>";
                } else if (document.getElementById("partial").checked) {
                    myAssdesc.innerHTML =
                        "Hovertrek is the only production model hovercraft available in a do-it-yourself, partially assembled form. This enables you to buy a craft at a substantial savings of 25% and to experience the excitement of constructing most of it yourself. <p class='sendButton'>Continue to Checkout!</p>";
                } else if (document.getElementById("self").checked) {
                    myAssdesc.innerHTML =
                        "Many have found that building a Hovertrek makes a great family project. It involves rudimentary workshop skills, a garage-sized workshop and common hand tools. <p class='sendButton'>Continue to Checkout!</p>";
                }
            }
        };

        window.addEventListener("load", hoverselect);
        window.addEventListener("load", seatingselect);
        window.addEventListener("load", performanceselect);
        window.addEventListener("load", assselection);
    </script>
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
