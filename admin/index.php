<?php 
	// init app-arena once, use init_session.php later...
 	include_once( "../init_session.php" );
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	
	<!-- Include bootstrap css files -->
	<style type="text/css">
		<?=$session->config['css_bootstrap']['value'];?>
		<?=$session->config['css']['value'];?>
	</style>
	<link type="text/css" rel="stylesheet" href="css/jquery-ui-1.8.18.custom.css" />
</head>

<body>
	<!-- Here starts the header -->
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
	     chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
    <div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
        	<div class="container-fluid">
            	<nav>
					<ul class="nav">
						<li><a class="template-getwinner"><?=__p("Select winner");?></a></li>
						<li><a class="template-exportparticipants"><?=__p("Export participants");?></a></li>
						<?php if ($session->config['admin_reset']['value']) {?>
							<li><a class="template-reset"><?=__p("Reset");?></a></li>
						<?php }?>
					</ul>
				</nav>
			</div>
		</div>
    </div>

	<div id="main" class="container admin-popup">
			<!-- the main content is managed by initApp() -->
	</div> <!-- #main -->
	
	<footer>
		<div class="branding">
			<?=$session->config['footer']['value'];?>
		</div>
	</footer>
	
	<!--<span class="btn" onclick='$("#_debug").toggle();'>Show debug info</span>
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
	<script>window.jQuery || document.write('<script src="js/jquery-1.7.1.min.js"><\/script>')</script>
	
	<!-- scripts concatenated and minified via ant build script-->
	<script src="js/plugins.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/script.js"></script>
	<!-- end scripts-->
	
	<!--[if lt IE 7 ]>
		<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
		<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
	<![endif]-->
	
	<div id="fb-root"></div>
	<?
	$landingPageTemplate = "default";
	if (isset($_GET['p']) && $_GET['p'] != "")
		$landingPageTemplate = $_GET['p'];
	?>
	<script type="text/javascript">
	
		$(document).ready(function() {
			
			userHasAuthorized = false;
	
			//$( "#jQueryButton" ).button();
			
			/***********************
			 * Get aa vars for js. *
			 ***********************/
			fb_app_id     = '<?=$session->instance["fb_app_id"]?>';
			aa_inst_id    = '<?=$session->instance["aa_inst_id"]?>';
			fb_canvas_url = '<?=$session->instance["fb_canvas_url"]?>';
			
			/**
			name          = '<?=$session->config["fb_share_title"]["value"];?>';
			description   = '<?=$session->config["fb_share_text"]["value"];?>';
	
			// instance["fb_page_url"] is empty!
			link          = '<?=$session->config["fb_page_url"];?>';
			picture       = '<?=$session->config["fb_share_image"]["value"];?>';
			caption       = '<?=$session->config["fb_share_subtitle"]["value"];?>'; */
			
			show_loading();
			
			initApp('<?=$landingPageTemplate?>');
			
		});
	
	
		window.fbAsyncInit = function() {
		    FB.init({
		      appId      : fb_app_id, // App ID
			  channelUrl : fb_canvas_url + 'channel.html', // Channel File
		      status     : true, // check login status
		      cookie     : true, // enable cookies to allow the server to access the session
		      xfbml      : true, // parse XFBML
		      oauth		 : true
	
		    });
		    FB.Canvas.setAutoGrow();
	
		    // Additional initialization code here
		    FB.getLoginStatus(function(response) {
		    	  if (response.status === 'connected') {
		    	    // the user is logged in and connected to your
		    	    // app, and response.authResponse supplies
		    	    // the user?s ID, a valid access token, a signed
		    	    // request, and the time the access token 
		    	    // and signed request each expire
		    	    fb_user_id   = response.authResponse.userID;
					fb_user_name = response.authResponse.userName;
		    	    
		    	    var accessToken = response.authResponse.accessToken;
		    	    userHasAuthorized = true;
	
		    	    // get user name
		    	    FB.api('/me', function(response) {
						fb_user_name = response.name;
			     	});
	
		    	    
		    	  } else if (response.status === 'not_authorized') {
		    	    // the user is logged in to Facebook, 
		    	    //but not connected to the app
					//alert("not connected");
		    	  } else {
		    	    // the user isn't even logged in to Facebook.
		    		  alert("not logged in");
		    	  }
		    	});
		  };
	
		  // Load the SDK Asynchronously
		  (function(d){
		     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
		     js = d.createElement('script'); js.id = id; js.async = true;
		     js.src = "//connect.facebook.net/en_US/all.js";
		     d.getElementsByTagName('head')[0].appendChild(js);
		   }(document));
	</script>
	
</body>
</html>