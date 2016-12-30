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

if ($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe']) {
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
} elseif ($_POST['submit']=='Register') {
    // If the Register form has been submitted

    $_SESSION['err'] = array();

    if (strlen($_POST['username'])<4 || strlen($_POST['username'])>32) {
        $_SESSION['err'] .= 'Your username must be between 4 and 32 characters!<br />';
    }

    if (strlen($_POST['pass']) <= '8') {
        $_SESSION['err'] .= "Your Password Must Contain At Least 8 Characters!<br />";
    }

    if (!preg_match("#[0-9]+#", $_POST['pass'])) {
        $_SESSION['err'] .= "Your Password Must Contain At Least 1 Number!<br />";
    }

    if (!preg_match("#[A-Z]+#", $_POST['pass'])) {
        $_SESSION['err'] .= "Your Password Must Contain At Least 1 Capital Letter!<br />";
    }

    if (!preg_match("#[a-z]+#", $_POST['pass'])) {
        $_SESSION['err'] .= "Your Password Must Contain At Least 1 Lowercase Letter!<br />";
    }
    if (empty($_POST['pass']) || empty($_POST['cpass'])) {
        $_SESSION['err'] .= "Please Check You've Entered Or Confirmed Your Password!<br />";
    }

    if (preg_match('/[^a-z0-9\-\_\.]+/i', $_POST['username'])) {
        $_SESSION['err'] .= 'Your username contains invalid characters!<br />';
    }

    if (!checkEmail($_POST['email'])) {
        $_SESSION['err'] .= 'Your email is not valid!<br />';
    }

    $username = mysql_real_escape_string($_POST['username']);
    $usrQuery = "SELECT * FROM contacts WHERE dbUsr='". $username . "'";
    $usrResult = mysql_query($usrQuery);

    if (mysql_num_rows($usrResult) > 0) {
        $_SESSION['err'] .= 'This username is already taken!<br />';
    }

    $email = mysql_real_escape_string($_POST['email']);
    $emailQuery = "SELECT * FROM contacts WHERE dbPrimaryEmail='". $email . "'";
    $emailResult = mysql_query($emailQuery);

    if (mysql_num_rows($emailResult) > 0) {
        $_SESSION['err'] .= 'This email is already in use!<br />';
    }

    if (!count($_SESSION['err'])) {
        // If there are no errors

        // $pass = substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)),0,6);
        // Generate a random password

        $_POST['email'] = mysql_real_escape_string($_POST['email']);
        $_POST['username'] = mysql_real_escape_string($_POST['username']);
        $_POST['pass'] = mysql_real_escape_string($_POST['pass']);
        $_POST['firstname'] = mysql_real_escape_string($_POST['firstname']);
        $_POST['lastname'] = mysql_real_escape_string($_POST['lastname']);
        $_POST['phone'] = mysql_real_escape_string($_POST['phone']);
        $_POST['company'] = mysql_real_escape_string($_POST['company']);
        $_POST['address'] = mysql_real_escape_string($_POST['address']);
        $_POST['city'] = mysql_real_escape_string($_POST['city']);
        $_POST['state'] = mysql_real_escape_string($_POST['state']);
        $_POST['country'] = mysql_real_escape_string($_POST['country']);
        $_POST['zip'] = mysql_real_escape_string($_POST['zip']);
        $_POST['kind'] = mysql_real_escape_string($_POST['kind']);
        $_POST['find'] = mysql_real_escape_string($_POST['find']);
        $_POST['land'] = mysql_real_escape_string($_POST['land']);
        $_POST['othercraft'] = mysql_real_escape_string($_POST['othercraft']);
        $_POST['message'] = mysql_real_escape_string($_POST['message']);
        // Escape the input data


        mysql_query("	INSERT INTO contacts(dbUsr,dbPassword,dbPrimaryEmail,dbIP,dbDate,dbFirstName,dbLastName,dbPhone,dbCompany,dbAddress,dbCity,dbState,dbCountry,dbZip,dbType,dbHowFind,dbWhereUse,dbVehicles,dbOtherComments)
						VALUES(

							'".$_POST['username']."',
							'".md5($_POST['pass'])."',
							'".$_POST['email']."',
							'".$_SERVER['REMOTE_ADDR']."',
							NOW(),
							'".$_POST['firstname']."',
							'".$_POST['lastname']."',
							'".$_POST['phone']."',
							'".$_POST['company']."',
							'".$_POST['address']."',
							'".$_POST['city']."',
							'".$_POST['state']."',
							'".$_POST['country']."',
							'".$_POST['zip']."',
							'".$_POST['kind']."',
							'".$_POST['find']."',
							'".$_POST['land']."',
							'".$_POST['othercraft']."',
							'".$_POST['message']."'
						)");

        if (mysql_affected_rows($link)==1) {
            send_mail('webmaster@neoterichovercraft.com',
                        $_POST['email'],
                        'Neoteric Hovercraft registration confirmation',
                        'Thank you for signing up with Neoteric Hovercraft! Your password is: '.$_POST['pass']);

            $_SESSION['msg']['reg-success']='<center>Thank you for signing up! We sent you a confirmation email.</center>';

            //clear all $_POST variables
            unset($noclear);
        } else {
            $_SESSION['err'].='Something went wrong with your request!';
        }
    }

    if (count($err)) {
        $_SESSION['msg']['reg-err'] = implode('<br />', $_SESSION['err']);
    }
    header("Location: confirmation.php");
    exit;
}


$script = '';

if ($_SESSION['msg']) {
    // The script below shows the sliding panel on page load

    $script = '
	<script type="text/javascript">

		$(function(){

			$("div#panel").show();
			$("#toggle a").toggle();
		});

	</script>';
}

?>
<?php
        $captcha;
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha=$_POST['g-recaptcha-response'];
        }
        $secretKey = "6LcN5iMTAAAAAHuzXf9O9F7Shv27JadqJJEcQ2ic";
        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response, true);

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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

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
        <section id="container">
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
                                <form id="ff" action="" method="post">
                                    <div class="col-3-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Registration Info</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <center>
                                                                <h5>All information must be provided to register.</h5></center><br />
                                                            <center>
                                                                <h5>Your username will be used to sign into the Neoteric Showroom. Your password must be 8-32 characters in length, contain at least one uppercase & one lowercase letter, and contain at least one number.</h5></center>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Username:</label>
                                                            <input type="text" name="username" id="username" required="required" placeholder="Username" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Email address:</label>
                                                            <input type="text" name="email" id="email" required="required" placeholder="Email address" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Password:</label>
                                                            <input type="password" name="pass" id="pass" required="required" placeholder="Password" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Confirm password:</label>
                                                            <input type="password" name="cpass" id="cpass" required="required" placeholder="Confirm password" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Contact Info</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <center>
                                                        <h5>Remember: Neoteric will <i>never</i> give or sell your information away.</h5></center>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>First name:</label>
                                                            <input type="text" name="firstname" id="firstname" required="required" placeholder="First name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>Last name:</label>
                                                            <input type="text" name="lastname" id="lastname" required="required" placeholder="Last name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>Company (if applicable):</label>
                                                            <input type="text" name="company" id="company" placeholder="Company (if applicable)" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>Phone number:</label>
                                                            <input type="text" name="phone" id="phone" required="required" placeholder="Phone number" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>Address:</label>
                                                            <input type="text" name="address" id="address" required="required" placeholder="Address" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>City:</label>
                                                            <input type="text" name="city" id="city" required="required" placeholder="City" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>State, territory, or province:</label>
                                                            <input type="text" name="state" id="state" required="required" placeholder="State, territory, or province" />

                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>ZIP code:</label>
                                                            <input type="text" name="zip" id="zip" required="required" placeholder="ZIP Code" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-3">
                                                        <div class="wrap-col">
                                                            <label>Country:</label>
                                                            <select name="country" required="required">
							<option disabled selected value>Country</option>
							<option value="AFG">Afghanistan</option>
							<option value="ALA">Åland Islands</option>
							<option value="ALB">Albania</option>
							<option value="DZA">Algeria</option>
							<option value="ASM">American Samoa</option>
							<option value="AND">Andorra</option>
							<option value="AGO">Angola</option>
							<option value="AIA">Anguilla</option>
							<option value="ATA">Antarctica</option>
							<option value="ATG">Antigua and Barbuda</option>
							<option value="ARG">Argentina</option>
							<option value="ARM">Armenia</option>
							<option value="ABW">Aruba</option>
							<option value="AUS">Australia</option>
							<option value="AUT">Austria</option>
							<option value="AZE">Azerbaijan</option>
							<option value="BHS">Bahamas</option>
							<option value="BHR">Bahrain</option>
							<option value="BGD">Bangladesh</option>
							<option value="BRB">Barbados</option>
							<option value="BLR">Belarus</option>
							<option value="BEL">Belgium</option>
							<option value="BLZ">Belize</option>
							<option value="BEN">Benin</option>
							<option value="BMU">Bermuda</option>
							<option value="BTN">Bhutan</option>
							<option value="BOL">Bolivia, Plurinational State of</option>
							<option value="BES">Bonaire, Sint Eustatius and Saba</option>
							<option value="BIH">Bosnia and Herzegovina</option>
							<option value="BWA">Botswana</option>
							<option value="BVT">Bouvet Island</option>
							<option value="BRA">Brazil</option>
							<option value="IOT">British Indian Ocean Territory</option>
							<option value="BRN">Brunei Darussalam</option>
							<option value="BGR">Bulgaria</option>
							<option value="BFA">Burkina Faso</option>
							<option value="BDI">Burundi</option>
							<option value="KHM">Cambodia</option>
							<option value="CMR">Cameroon</option>
							<option value="CAN">Canada</option>
							<option value="CPV">Cape Verde</option>
							<option value="CYM">Cayman Islands</option>
							<option value="CAF">Central African Republic</option>
							<option value="TCD">Chad</option>
							<option value="CHL">Chile</option>
							<option value="CHN">China</option>
							<option value="CXR">Christmas Island</option>
							<option value="CCK">Cocos (Keeling) Islands</option>
							<option value="COL">Colombia</option>
							<option value="COM">Comoros</option>
							<option value="COG">Congo</option>
							<option value="COD">Congo, the Democratic Republic of the</option>
							<option value="COK">Cook Islands</option>
							<option value="CRI">Costa Rica</option>
							<option value="CIV">Côte d'Ivoire</option>
							<option value="HRV">Croatia</option>
							<option value="CUB">Cuba</option>
							<option value="CUW">Curaçao</option>
							<option value="CYP">Cyprus</option>
							<option value="CZE">Czech Republic</option>
							<option value="DNK">Denmark</option>
							<option value="DJI">Djibouti</option>
							<option value="DMA">Dominica</option>
							<option value="DOM">Dominican Republic</option>
							<option value="ECU">Ecuador</option>
							<option value="EGY">Egypt</option>
							<option value="SLV">El Salvador</option>
							<option value="GNQ">Equatorial Guinea</option>
							<option value="ERI">Eritrea</option>
							<option value="EST">Estonia</option>
							<option value="ETH">Ethiopia</option>
							<option value="FLK">Falkland Islands (Malvinas)</option>
							<option value="FRO">Faroe Islands</option>
							<option value="FJI">Fiji</option>
							<option value="FIN">Finland</option>
							<option value="FRA">France</option>
							<option value="GUF">French Guiana</option>
							<option value="PYF">French Polynesia</option>
							<option value="ATF">French Southern Territories</option>
							<option value="GAB">Gabon</option>
							<option value="GMB">Gambia</option>
							<option value="GEO">Georgia</option>
							<option value="DEU">Germany</option>
							<option value="GHA">Ghana</option>
							<option value="GIB">Gibraltar</option>
							<option value="GRC">Greece</option>
							<option value="GRL">Greenland</option>
							<option value="GRD">Grenada</option>
							<option value="GLP">Guadeloupe</option>
							<option value="GUM">Guam</option>
							<option value="GTM">Guatemala</option>
							<option value="GGY">Guernsey</option>
							<option value="GIN">Guinea</option>
							<option value="GNB">Guinea-Bissau</option>
							<option value="GUY">Guyana</option>
							<option value="HTI">Haiti</option>
							<option value="HMD">Heard Island and McDonald Islands</option>
							<option value="VAT">Holy See (Vatican City State)</option>
							<option value="HND">Honduras</option>
							<option value="HKG">Hong Kong</option>
							<option value="HUN">Hungary</option>
							<option value="ISL">Iceland</option>
							<option value="IND">India</option>
							<option value="IDN">Indonesia</option>
							<option value="IRN">Iran, Islamic Republic of</option>
							<option value="IRQ">Iraq</option>
							<option value="IRL">Ireland</option>
							<option value="IMN">Isle of Man</option>
							<option value="ISR">Israel</option>
							<option value="ITA">Italy</option>
							<option value="JAM">Jamaica</option>
							<option value="JPN">Japan</option>
							<option value="JEY">Jersey</option>
							<option value="JOR">Jordan</option>
							<option value="KAZ">Kazakhstan</option>
							<option value="KEN">Kenya</option>
							<option value="KIR">Kiribati</option>
							<option value="PRK">Korea, Democratic People's Republic of</option>
							<option value="KOR">Korea, Republic of</option>
							<option value="KWT">Kuwait</option>
							<option value="KGZ">Kyrgyzstan</option>
							<option value="LAO">Lao People's Democratic Republic</option>
							<option value="LVA">Latvia</option>
							<option value="LBN">Lebanon</option>
							<option value="LSO">Lesotho</option>
							<option value="LBR">Liberia</option>
							<option value="LBY">Libya</option>
							<option value="LIE">Liechtenstein</option>
							<option value="LTU">Lithuania</option>
							<option value="LUX">Luxembourg</option>
							<option value="MAC">Macao</option>
							<option value="MKD">Macedonia, the former Yugoslav Republic of</option>
							<option value="MDG">Madagascar</option>
							<option value="MWI">Malawi</option>
							<option value="MYS">Malaysia</option>
							<option value="MDV">Maldives</option>
							<option value="MLI">Mali</option>
							<option value="MLT">Malta</option>
							<option value="MHL">Marshall Islands</option>
							<option value="MTQ">Martinique</option>
							<option value="MRT">Mauritania</option>
							<option value="MUS">Mauritius</option>
							<option value="MYT">Mayotte</option>
						<option value="MEX">Mexico</option>
						<option value="FSM">Micronesia, Federated States of</option>
						<option value="MDA">Moldova, Republic of</option>
						<option value="MCO">Monaco</option>
						<option value="MNG">Mongolia</option>
						<option value="MNE">Montenegro</option>
						<option value="MSR">Montserrat</option>
						<option value="MAR">Morocco</option>
						<option value="MOZ">Mozambique</option>
						<option value="MMR">Myanmar</option>
						<option value="NAM">Namibia</option>
						<option value="NRU">Nauru</option>
						<option value="NPL">Nepal</option>
						<option value="NLD">Netherlands</option>
						<option value="NCL">New Caledonia</option>
						<option value="NZL">New Zealand</option>
						<option value="NIC">Nicaragua</option>
						<option value="NER">Niger</option>
						<option value="NGA">Nigeria</option>
						<option value="NIU">Niue</option>
						<option value="NFK">Norfolk Island</option>
						<option value="MNP">Northern Mariana Islands</option>
						<option value="NOR">Norway</option>
						<option value="OMN">Oman</option>
						<option value="PAK">Pakistan</option>
						<option value="PLW">Palau</option>
						<option value="PSE">Palestinian Territory, Occupied</option>
						<option value="PAN">Panama</option>
						<option value="PNG">Papua New Guinea</option>
						<option value="PRY">Paraguay</option>
						<option value="PER">Peru</option>
						<option value="PHL">Philippines</option>
						<option value="PCN">Pitcairn</option>
						<option value="POL">Poland</option>
						<option value="PRT">Portugal</option>
						<option value="PRI">Puerto Rico</option>
						<option value="QAT">Qatar</option>
						<option value="REU">Réunion</option>
						<option value="ROU">Romania</option>
						<option value="RUS">Russian Federation</option>
						<option value="RWA">Rwanda</option>
						<option value="BLM">Saint Barthélemy</option>
						<option value="SHN">Saint Helena, Ascension and Tristan da Cunha</option>
						<option value="KNA">Saint Kitts and Nevis</option>
						<option value="LCA">Saint Lucia</option>
						<option value="MAF">Saint Martin (French part)</option>
						<option value="SPM">Saint Pierre and Miquelon</option>
						<option value="VCT">Saint Vincent and the Grenadines</option>
						<option value="WSM">Samoa</option>
						<option value="SMR">San Marino</option>
						<option value="STP">Sao Tome and Principe</option>
						<option value="SAU">Saudi Arabia</option>
						<option value="SEN">Senegal</option>
						<option value="SRB">Serbia</option>
						<option value="SYC">Seychelles</option>
						<option value="SLE">Sierra Leone</option>
						<option value="SGP">Singapore</option>
						<option value="SXM">Sint Maarten (Dutch part)</option>
						<option value="SVK">Slovakia</option>
						<option value="SVN">Slovenia</option>
						<option value="SLB">Solomon Islands</option>
						<option value="SOM">Somalia</option>
						<option value="ZAF">South Africa</option>
						<option value="SGS">South Georgia and the South Sandwich Islands</option>
						<option value="SSD">South Sudan</option>
						<option value="ESP">Spain</option>
						<option value="LKA">Sri Lanka</option>
						<option value="SDN">Sudan</option>
						<option value="SUR">Suriname</option>
						<option value="SJM">Svalbard and Jan Mayen</option>
						<option value="SWZ">Swaziland</option>
						<option value="SWE">Sweden</option>
						<option value="CHE">Switzerland</option>
						<option value="SYR">Syrian Arab Republic</option>
						<option value="TWN">Taiwan, Province of China</option>
						<option value="TJK">Tajikistan</option>
						<option value="TZA">Tanzania, United Republic of</option>
						<option value="THA">Thailand</option>
						<option value="TLS">Timor-Leste</option>
						<option value="TGO">Togo</option>
						<option value="TKL">Tokelau</option>
						<option value="TON">Tonga</option>
						<option value="TTO">Trinidad and Tobago</option>
						<option value="TUN">Tunisia</option>
						<option value="TUR">Turkey</option>
						<option value="TKM">Turkmenistan</option>
						<option value="TCA">Turks and Caicos Islands</option>
						<option value="TUV">Tuvalu</option>
						<option value="UGA">Uganda</option>
						<option value="UKR">Ukraine</option>
						<option value="ARE">United Arab Emirates</option>
						<option value="GBR">United Kingdom</option>
						<option value="USA">United States</option>
						<option value="UMI">United States Minor Outlying Islands</option>
						<option value="URY">Uruguay</option>
						<option value="UZB">Uzbekistan</option>
						<option value="VUT">Vanuatu</option>
						<option value="VEN">Venezuela, Bolivarian Republic of</option>
						<option value="VNM">Viet Nam</option>
						<option value="VGB">Virgin Islands, British</option>
						<option value="VIR">Virgin Islands, U.S.</option>
						<option value="WLF">Wallis and Futuna</option>
						<option value="ESH">Western Sahara</option>
						<option value="YEM">Yemen</option>
						<option value="ZMB">Zambia</option>
						<option value="ZWE">Zimbabwe</option>
						</select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Craft info</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="col-1-2">
                                                        <div class="wrap-col" id="craft-tag">
                                                            <center>
                                                                <h5 id="craft-tag">By answering questions about your interest in hovercraft, Neoteric can provide you with a one-on-one sales experience.</h5></center>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Which craft are you most interested in?</label>
                                                            <select name="kind" required="required">
<option disabled selected value>Which craft are you most interested in?</option>
                                                						<option value="Recreati">Recreational</option>
                                                						<option value="Rescue">Rescue</option>
                                                						<option value="Commerci">Commercial</option>
                                                						<option value="Military">Military</option>
                                                						<option value="Other">Other</option>
                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Where did you find Neoteric?</label>
                                                            <select name="find" required="required">
							<option disabled selected value>Where did you find Neoteric?</option>
                                                						<option value="Google">Google Search</option>
                                                						<option value="Facebook">Facebook</option>
                                                						<option value="Twitter">Twitter</option>
                                                						<option value="Other Social Media">Other Social Media</option>
                                                						<option value="News">News Publication</option>
                                                						<option value="Hov Recommendation">Hovercraft Recommendation</option>
                                                						<option value="Word of Mouth">Word of Mouth</option>
                                                						<option value="Other">Other</option>
                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>In what conditions will you use your craft?</label>
                                                            <select name="land" required="required">
							<option disabled selected value>In what conditions will you be using your craft?</option>
                                                	<option value="Ocean">Ocean</option>
                                                	<option value="Desert">Desert</option>
                                                	<option value="Lake">Lake</option>
                                                	<option value="River">River</option>
                                                	<option value="Woods">Forest or woods</option>
                                                	<option value="Winter">Ice or snow</option>
                                                	<option value="Other">Other</option>
                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>Do you own other recreational or unconventional vehicles?</label>
                                                            <input type="text" name="othercraft" id="othercraft" required="required" maxlength="500" placeholder="(e.g. motorcycle, ATV or 4x4, helicopter, jet ski, jet pack, etc.)" />
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <label>What has interested you about owning hovercraft?</label>
                                                            <input type="text" name="message" id="message" required="required" maxlength="500" placeholder="(e.g. I love the wilderness, I work in rescue, I am a boating enthusiast, etc.)" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3-3">
                                        <div class="wrap-col">
                                            <div class="contact">
                                                <div class="contact-header">
                                                    <h5>Confirm registration</h5>
                                                </div>
                                                <div id="contact_form">
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <center><label>Please read our <a href="privacypolicy.html" target="_blank"><u>privacy policy</u></a>.</label></center>
                                                            <center><label>A confirmation email will be sent to you upon registration.</label></center>
                                                            <center><label>Please check the captcha box.</label></center>
                                                        </div>
                                                    </div>
                                                    <div class="col-1-2">
                                                        <div class="wrap-col">
                                                            <!-- div align="center" class="g-recaptcha" data-sitekey="6LcN5iMTAAAAAG1wC7qgIgBHBfPxw6LXGUEQ8a90"></div -->
                                                            <input type="submit" name="submit" value="Register" class="bt_register submit-button" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                </form>






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
</body>

</html>
