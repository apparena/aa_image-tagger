<?php 
	// init app-arena once, use init_session.php later...
 	include_once( "init.php" );
	$session->app['fb_share_url'] = "https://apps.facebook.com/" . $session->instance['fb_app_url']."/fb_share.php?aa_inst_id=".$session->instance['aa_inst_id'];
	$user_is_fan = false;
	$user_is_fan = is_fan();
?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
         
	<!-- Facebook Meta Data -->
    <meta property="fb:app_id" content="<?=$session->instance['fb_app_id']?>" />
    <meta property="og:title" content="<?=$session->config['title']['value'];?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?=$session->instance['fb_page_url']."?sk=app_".$session->instance['fb_app_id']?>" />
    <meta property="og:image" content="<?=$session->config['image']['value'];?>" />
    <meta property="og:site_name" content="<?=$session->config['title']['value'];?>" />
    <meta property="og:description" content=""/>

	<title></title>
	<meta name="description" content="">
	<meta name="author" content="iConsultants UG - www.app-arena.com">

	<meta name="viewport" content="width=device-width">
	
	<!-- Include bootstrap css files -->
	<style type="text/css">
		<?=$session->config['css_bootstrap']['value'];?>
	</style>
	
	<script src="js/libs/modernizr-2.5.2-respond-1.1.0.min.js"></script>
</head>

<body>
	<!-- Here starts the header -->
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
	     chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	
	<?php // Here you can integrate your fangate
	if ($user_is_fan == false && $session->config['check_nofan']['value'] == '1') { ?>
		<div class="page_non_fans_layer">
			<div class="img_non_fans">
				<img src="<?php echo $session->config['page_welcome_nonfans']['value']?>" />
				
			</div>
			<div id="non_fan_background">&nbsp;</div>
		</div>
		
	<?php 
//	exit(0);
	} else {
		
		//include "templates/.phtml";
		//include 'tagger.php';
	
	}?>
	
    <!--<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
        	<div class="container-fluid">
            	<nav>
					<ul class="nav">
						<li><a class="template-welcome"><?//=__p("Homepage");?></a></li>
						<li><a class="template-terms"><?//=__p("Terms & Conditions");?></a></li>
					</ul>
				</nav>
			</div>
		</div>
    </div>-->
	
	<!-- this is the div you can append info/alert/error messages to -->
	<div id="msg-container"></div> 
	
	<div class="custom-header">
		<?php //echo $session->config['custom_header']['value']; ?>
	</div>
	
	<div id="main" class="container">
			<!-- the main content is managed by initApp() -->
	</div> <!-- #main -->
	
	<div class="custom-footer">
		<?php //echo $session->config['custom_footer']['value']; ?>
	</div>
	
	<!--<footer>
		<div class="terms-and-conditions-container">
			<?php
			//$terms_and_conditions_link = "<a class='template-terms'>" . __t("Terms & Conditions") . "</a>";
			//__p("This promotion is not associated to Facebook and is not promoted, supported or organized by Facebook. Please check the %s for further details", $terms_and_conditions_link);
			?>
		</div>
		
		<div class="branding">
			<?php //echo $session->config['footer']['value'];?>
		</div>
	</footer>-->

	<!-- Debug area -->
	<!--<span class="btn" onclick='jQuery("#_debug").toggle();'>Show debug info</span>
	<div id="_debug" style="display:none;">
		<h1>Debug information</h1>
		<?php //Zend_Debug::dump($session->fb, "session->fb");?>
		<?php //Zend_Debug::dump($session->app, "session->app");?>
		<?php //Zend_Debug::dump($_COOKIE, "_COOKIE");?>
		<?php //Zend_Debug::dump(parse_signed_request($_REQUEST['signed_request']), "decoded fb signed request");?>
	</div>-->
	
	 	
 	<?php // include the file for the loading screen
 	require_once( dirname(__FILE__).'/templates/loading_screen.phtml' );
 	?>
 	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	
	<!-- scripts concatenated and minified via ant build script-->
	<script src="js/bootstrap.min.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/script.js?v5"></script>
	<!-- end scripts-->
	
	<!--<script>
		var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']]; // Change UA-XXXXX-X to be your site's ID
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>-->
	
	<!--[if lt IE 7 ]>
		<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
		<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
	<![endif]-->
	
	<div id="fb-root"></div>
	
	<script type="text/javascript">
		/** Init AppManager vars for js */
		var fb_user_id='';
			//defined post wall variables
			var fb_share_title=  '<?=$session->config["fb_share_title"]["value"]?>';
			var fb_share_link=  'https://apps.facebook.com/<?=$session->instance["fb_app_url"].'/fb_share.php?aa_inst_id='.$session->instance['aa_inst_id'] ?>';
			var fb_share_subtitle= '<?=$session->config["fb_share_subtitle"]["value"]?>';
			var fb_share_desc =  '<?=$session->config["fb_share_desc"]["value"]?>';
			var fb_share_img='<?=$session->config["fb_share_img"]["value"]?>';
		
		
		$(document).ready(function() {
			userHasAuthorized = false;
			fb_app_id     = '<?=$session->instance["fb_app_id"]?>';
			fb_canvas_url = '<?=$session->instance["fb_canvas_url"]?>';
			aa_inst_id    = '<?=$session->instance["aa_inst_id"]?>';
			
//alert( aa_inst_id );
			
			tag_image = '<?=$session->config["image_tagger"]["value"]?>';
			
			showFaces = '<?=$session->config["show_faces"]["value"]?>';
			
			loadTags();
			
			xCoord = 0;
			yCoord = 0;
			
			//show_loading();
			//initApp();
			
			//width  = $("#img_tag").width();
			//heightOff = $("#header_img").height();
			
/*
			if( heightOff == 0 ) {
			
				heightOff = $("#header_img").attr("height");
			}
*/
			
			$("#img_tag").click( function(e){
				
//alert("x:"+e.pageX+" - y:"+e.pageY+"\noffX:"+this.offsetLeft+" - offY:"+this.offsetTop);

				heightOff = $("#header_img").height();
				
//$("#_debug").html("offset height: " + heightOff);

				// get the mouse-coords where the user clicked the image
				xCoord = ( e.pageX - this.offsetLeft );
				yCoord = ( e.pageY - heightOff );
				
				authUser( xCoord, yCoord );
				
			});
			
		});
	
		window.fbAsyncInit = function() {
		
			if( typeof( fb_app_id ) == "undefined" ) {
				
				fb_app_id = '<?=$session->instance["fb_app_id"]?>';
				
			}
		
			FB.init({
		      appId      : fb_app_id, // App ID
		      status     : true, // check login status
		      cookie     : true, // enable cookies to allow the server to access the session
		      xfbml      : true, // parse XFBML
		      oauth		 : true
		    });
			
			FB.Canvas.setAutoGrow();
			//FB.Canvas.setSize({ width: 640, height: 1800 });
		    
		    // Additional initialization code here
			FB.getLoginStatus(function(response) {
		    	  if (response.status === 'connected') {
		    	    // the user is logged in and connected to your
		    	    // app, and response.authResponse supplies
		    	    // the users ID, a valid access token, a signed
		    	    // request, and the time the access token 
		    	    // and signed request each expire
		    	    fb_user_id   = response.authResponse.userID;
					fb_user_name = response.authResponse.userName;
		    	    fb_status = "connected";
					
		    	    var fb_accessToken = response.authResponse.accessToken;
		    	    userHasAuthorized = true;
	
		    	    // get user name
		    	    FB.api('/me', function(response) {
						fb_user_name = response.name;
			     	});
		    	  } else if (response.status === 'not_authorized') {
		    	    // the user is logged in to Facebook, 
		    	    //but not connected to the app
					//alert("not connected");
						fb_status = "not_authorized";
		    	  } else {
		    	    // the user isn't even logged in to Facebook.
		    		  fb_status = "not_logged_in";
		    	  }
			});
		};
		// Load the SDK Asynchronously
		(function(d){
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/de_DE/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));
	</script>
	
	<!-- Show admin panel if user is admin -->
	<?php // Show admin panel, when page admin
	if (is_admin()) {
		//include_once 'admin/admin_panel.php';?>		
	<?php } ?>
	<div id="_debug"></div>
	<?php if ($user_is_fan == true || $session->config['check_nofan']['value'] != '1'): ?>
	<div id="header">
		
		<img id="header_img" src="<?=$session->config['image_header']['value']?>" />
		
	</div>
	
	<div id="imgMain" style="position:relative;">
	
	
		<div id="image1">
			
			<img id="img_tag" src="<?=$session->config['image_to_tag']['value']?>" />
			
		</div>
	
		<div id="image2">
			
			<img src="<?=$session->config['image_notag']['value']?>" />
			
		</div>
	
	</div>
	
	<?php endif; ?>
	
	
</body>
</html>
