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

if (isset($_GET['logoff'])) {
    $_SESSION = array();
    session_destroy();

    header("Location: index.php");
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

                                <div class="col-3-3">
                                    <div class="wrap-col">
                                        <div class="contact">
                                            <div class="contact-header">
                                                <h5>Privacy Policy</h5>
                                            </div>
                                            <div id="contact_form" style="margin-bottom: 50px;">
                                                <div class="innerText">This privacy policy has been compiled to better serve those who are concerned with how their 'Personally Identifiable Information' (PII) is being used online. PII, as described in US privacy law and information
                                                    security, is information that can be used on its own or with other information to identify, contact, or locate a single person, or to identify an individual in context. Please read our privacy policy
                                                    carefully to get a clear understanding of how we collect, use, protect or otherwise handle your Personally Identifiable Information in accordance with our website.<br></div><span id="infoCo"></span><br>
                                                <div
                                                    class="grayText"><strong>What personal information do we collect from the people that visit our blog, website or app?</strong></div><br>
                                                <div class="innerText">When ordering or registering on our site, as appropriate, you may be asked to enter your name, email address, mailing address, phone number or other details to help you with your experience.</div><br>
                                                <div
                                                    class="grayText"><strong>When do we collect information?</strong></div><br>
                                                <div class="innerText">We collect information from you when you register on our site or enter information on our site.</div><br> <span id="infoUs"></span><br>
                                                <div class="grayText"><strong>How do we use your information? </strong></div><br>
                                                <div class="innerText"> We may use the information we collect from you when you register, make a purchase, sign up for our newsletter, respond to a survey or marketing communication, surf the website, or use certain other site
                                                    features in the following ways:<br><br></div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> To personalize your experience and to allow us to deliver the type of content and product offerings in which you are most interested.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> To allow us to better service you in responding to your customer service requests.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> To follow up with them after correspondence (live chat, email or phone inquiries)</div><span id="infoPro"></span><br>
                                                <div class="grayText"><strong>How do we protect your information?</strong></div><br>
                                                <div class="innerText">We do not use vulnerability scanning and/or scanning to PCI standards.</div>
                                                <div class="innerText">We only provide articles and information. We never ask for credit card numbers.</div>
                                                <div class="innerText">We use regular Malware Scanning.<br><br></div>
                                                <div class="innerText">Your personal information is contained behind secured networks and is only accessible by a limited number of persons who have special access rights to such systems, and are required to keep the information
                                                    confidential. In addition, all sensitive/credit information you supply is encrypted via Secure Socket Layer (SSL) technology. </div><br>
                                                <div class="innerText">We implement a variety of security measures when a user enters, submits, or accesses their information to maintain the safety of your personal information.</div><br>
                                                <div class="innerText">All transactions are processed through a gateway provider and are not stored or processed on our servers.</div><span id="coUs"></span><br>
                                                <div class="grayText"><strong>Do we use 'cookies'?</strong></div><br>
                                                <div class="innerText">Yes. Cookies are small files that a site or its service provider transfers to your computer's hard drive through your Web browser (if you allow) that enables the site's or service provider's systems to recognize
                                                    your browser and capture and remember certain information. For instance, we use cookies to help us remember and process the items in your shopping cart. They are also used to help us understand your
                                                    preferences based on previous or current site activity, which enables us to provide you with improved services. We also use cookies to help us compile aggregate data about site traffic and site interaction
                                                    so that we can offer better site experiences and tools in the future.</div>
                                                <div class="innerText"><br><strong>We use cookies to:</strong></div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Help remember and process the items in the shopping cart.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Understand and save user's preferences for future visits.</div>
                                                <div class="innerText"><br>You can choose to have your computer warn you each time a cookie is being sent, or you can choose to turn off all cookies. You do this through your browser settings. Since browser is a little different,
                                                    look at your browser's Help Menu to learn the correct way to modify your cookies.<br></div>
                                                <div class="innerText"><br><strong>If users disable cookies in their browser:</strong></div><br>
                                                <div class="innerText">If you turn cookies off, some features will be disabled. Some of the features that make your site experience more efficient and may not function properly.</div><br>
                                                <div class="innerText">However, you will still be able to place orders
                                                    <div class="innerText">Saved login information</div>over the telephone by contacting customer service.</div><br><span id="trDi"></span><br>
                                                <div class="grayText"><strong>Third-party disclosure</strong></div><br>
                                                <div class="innerText">We do not sell, trade, or otherwise transfer to outside parties your Personally Identifiable Information unless we provide users with advance notice. This does not include website hosting partners and other
                                                    parties who assist us in operating our website, conducting our business, or serving our users, so long as those parties agree to keep this information confidential. We may also release information when
                                                    it's release is appropriate to comply with the law, enforce our site policies, or protect ours or others' rights, property or safety. <br><br> However, non-personally identifiable visitor information
                                                    may be provided to other parties for marketing, advertising, or other uses. </div><span id="trLi"></span><br>
                                                <div class="grayText"><strong>Third-party links</strong></div><br>
                                                <div class="innerText">We do not include or offer third-party products or services on our website.</div><span id="gooAd"></span><br>
                                                <div class="blueText"><strong>Google</strong></div><br>
                                                <div class="innerText">Google's advertising requirements can be summed up by Google's Advertising Principles. They are put in place to provide a positive experience for users. https://support.google.com/adwordspolicy/answer/1316548?hl=en
                                                    <br><br></div>
                                                <div class="innerText">We have not enabled Google AdSense on our site but we may do so in the future.</div><span id="calOppa"></span><br>
                                                <div class="blueText"><strong>California Online Privacy Protection Act</strong></div><br>
                                                <div class="innerText">CalOPPA is the first state law in the nation to require commercial websites and online services to post a privacy policy. The law's reach stretches well beyond California to require any person or company
                                                    in the United States (and conceivably the world) that operates websites collecting Personally Identifiable Information from California consumers to post a conspicuous privacy policy on its website stating
                                                    exactly the information being collected and those individuals or companies with whom it is being shared. - See more at: http://consumercal.org/california-online-privacy-protection-act-caloppa/#sthash.0FdRbT51.dpuf<br></div>
                                                <div
                                                    class="innerText"><br><strong>According to CalOPPA, we agree to the following:</strong><br></div>
                                                <div class="innerText">Users can visit our site anonymously.</div>
                                                <div class="innerText">Once this privacy policy is created, we will add a link to it on our home page or as a minimum, on the first significant page after entering our website.<br></div>
                                                <div class="innerText">Our Privacy Policy link includes the word 'Privacy' and can be easily be found on the page specified above.</div>
                                                <div class="innerText"><br>You will be notified of any Privacy Policy changes:</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> On our Privacy Policy Page<br></div>
                                                <div class="innerText">Can change your personal information:</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> By logging in to your account</div>
                                                <div class="innerText"><br><strong>How does our site handle Do Not Track signals?</strong><br></div>
                                                <div class="innerText">We honor Do Not Track signals and Do Not Track, plant cookies, or use advertising when a Do Not Track (DNT) browser mechanism is in place. </div>
                                                <div class="innerText"><br><strong>Does our site allow third-party behavioral tracking?</strong><br></div>
                                                <div class="innerText">It's also important to note that we do not allow third-party behavioral tracking</div><span id="coppAct"></span><br>
                                                <div class="blueText"><strong>COPPA (Children Online Privacy Protection Act)</strong></div><br>
                                                <div class="innerText">When it comes to the collection of personal information from children under the age of 13 years old, the Children's Online Privacy Protection Act (COPPA) puts parents in control. The Federal Trade Commission,
                                                    United States' consumer protection agency, enforces the COPPA Rule, which spells out what operators of websites and online services must do to protect children's privacy and safety online.<br><br></div>
                                                <div
                                                    class="innerText">We do not specifically market to children under the age of 13 years old.</div><span id="ftcFip"></span><br>
                                                <div class="blueText"><strong>Fair Information Practices</strong></div><br>
                                                <div class="innerText">The Fair Information Practices Principles form the backbone of privacy law in the United States and the concepts they include have played a significant role in the development of data protection laws around
                                                    the globe. Understanding the Fair Information Practice Principles and how they should be implemented is critical to comply with the various privacy laws that protect personal information.<br><br></div>
                                                <div
                                                    class="innerText"><strong>In order to be in line with Fair Information Practices we will take the following responsive action, should a data breach occur:</strong></div>
                                                <div class="innerText">We will notify you via email</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Within 7 business days</div>
                                                <div class="innerText">We will notify the users via in-site notification</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Within 7 business days</div>
                                                <div class="innerText"><br>We also agree to the Individual Redress Principle which requires that individuals have the right to legally pursue enforceable rights against data collectors and processors who fail to adhere to the
                                                    law. This principle requires not only that individuals have enforceable rights against data users, but also that individuals have recourse to courts or government agencies to investigate and/or prosecute
                                                    non-compliance by data processors.</div><span id="canSpam"></span><br>
                                                <div class="blueText"><strong>CAN SPAM Act</strong></div><br>
                                                <div class="innerText">The CAN-SPAM Act is a law that sets the rules for commercial email, establishes requirements for commercial messages, gives recipients the right to have emails stopped from being sent to them, and spells
                                                    out tough penalties for violations.<br><br></div>
                                                <div class="innerText"><strong>We collect your email address in order to:</strong></div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Send information, respond to inquiries, and/or other requests or questions</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Process orders and to send information and updates pertaining to orders.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Send you additional information related to your product and/or service</div>
                                                <div class="innerText"><br><strong>To be in accordance with CANSPAM, we agree to the following:</strong></div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Not use false or misleading subjects or email addresses.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Identify the message as an advertisement in some reasonable way.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Include the physical address of our business or site headquarters.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Monitor third-party email marketing services for compliance, if one is used.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Honor opt-out/unsubscribe requests quickly.</div>
                                                <div class="innerText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>•</strong> Allow users to unsubscribe by using the link at the bottom of each email.</div>
                                                <div class="innerText"><strong><br>If at any time you would like to unsubscribe from receiving future emails, you can email us at</strong></div>
                                                <div class="innerText">erica@neoterichovercraft.com and we will promptly remove you from <strong>ALL</strong> correspondence.</div><br><span id="ourCon"></span><br>
                                                <div class="blueText"><strong>Contacting Us</strong></div><br>
                                                <div class="innerText">If there are any questions regarding this privacy policy, you may contact us using the information below.<br><br></div>
                                                <div class="innerText">neoterichovercraft.com</div>
                                                <div class="innerText">1649 Tippecanoe Ave.</div>Terre Haute, Indiana 47803
                                                <div class="innerText">United States</div>
                                                <div class="innerText">erica@neoterichovercraft.com</div>
                                                <div class="innerText"><br>Last Edited on 2016-08-17</div>
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
</body>
</html>
