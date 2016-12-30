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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/responsiveslides.css">
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->

    <script src="login_panel/js/slide.js" type="text/javascript"></script>

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
                nav: false,
                speed: 800,
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
<body style="background:url(images/genwata.jpg) no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;">
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
        <section id="container" style="margin-top:60px;">
            <div class="wrap-box taco">
                <!--Start Box-->
                <div class="zerogrid">
                    <div class="header usdhead">
                        <h2>LATEST HOVERCRAFT</h2>
                    </div>
                    <div class="row">
                        <div class="col-2-3">
                            <div class="wrap-col">
                                <div class="row">
                                    <div class="item">
                                        <div class="col-1-3">
                                            <div class="item-container">
                                                <a href="single.php">
                                                    <div class="item-caption">
                                                        <div class="item-caption-inner">
                                                            <div class="item-caption-inner1">
                                                                <span>6 Passenger / 100 HP / Carbureted / Hirth Engine / Inline Seating / Rescue Package</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <img src="images/ShowroomRescue.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2-3">
                                            <div class="wrap-col" style="margin: 0px">
                                                <div class="item-info">
                                                    <a href="single.php"><h5>Neoteric Rescue HoverTrek™</h5></a>
                                                    <p>$58,000 with trailer</p>
                                                    <p>Equipped with a saltwater marinization package, a front cabin, a stretcher mount, a PA system, a siren and a reinforced heavy duty body.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="sidebar" class="col-1-3">
                            <div class="wrap-col">
                                <!---- Start Widget ---->

                                <!---- Start Widget ---->
                                <div class="widget header-unit">
                                    <div id="video-container">
                                        <video autoplay loop class="fillWidth">
                                                 <source src="mp4.mp4" type="video/mp4"/>
                                                 <source src="ogv.ogv" type="video/ogg"/>
                                                 <source src="1080p_3.webm" type="video/webm"/>
                                        </video>
                                    </div>
                                    <!-- end video-container -->


                                </div>
                                <!---- Start Widget ---->
                                <div class="widget wid-post">
                                    <div class="wid-header">
                                        <h5>Testimonials</h5>
                                    </div>
                                    <div class="wid-content">
                                        <div class="slider">
                                            <!-- Slideshow -->
                                            <div class="callbacks_container">
                                                <ul class="rslides" id="slider">
                                                    <li style="min-height: 230px;">
                                                        <div class="caption">
                                                            <h5>Gary Meyers:</h5>

                                                            <p class="captionpara">“Thanks again for the reverse thrust buckets. Without them a hovercraft would be a low-flying helicopter out of control.” Lake Havasu City, Arizona USA</p>


                                                        </div>

                                                    </li>
                                                    <li style="min-height: 230px;">

                                                        <div class="caption">
                                                            <h5>Barry Eison:</h5>

                                                            <p class="captionpara">“I took the hovercraft out on the Mississippi River on Friday afternoon and deliberately targeted rough waters. The craft performed absolutely flawlessly. ” Tennessee, USA</p>


                                                        </div>
                                                    </li>
                                                    <li style="min-height: 230px;">

                                                        <div class="caption">
                                                            <h5>John Wright:</h5>

                                                            <p class="captionpara">“I hover nearly every weekend and have clocked up some good experience, 50+ hours. I have been in Gale Force 3 conditions: 0.5m – 1 m waves and winds around 35km/h in Durban Harbour.” &nbsp;&nbsp;Ontario,
                                                                Canada</p>


                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
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
    </body>
</html>
