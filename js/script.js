/** 
 * Author: Guntram Pollock & Sebastian Buckpesch
 */



/**
 * Initializes the content functionality of the app.
 */
function initApp() {
	// initialize the menu buttons with onclick functions, which load the according template into the #main-div and save the landing content to display (the first menu-item).
	var landingContent = initMenu();
	
	var date=new Date();
	var t=date.getTime();
	// set the first menu item as the landing content.
	$("#main").slideUp( 0, function(){
		$("#main").load( "templates/" + landingContent + ".phtml?aa_inst_id=" + aa_inst_id, function(){

       $("#main").slideDown(600,function(){
          //reinit facebook
			FB.init({
				appId      : fb_app_id, // App ID
				channelUrl : fb_canvas_url + 'channel.html', // Channel File
				status     : true, // check login status
				cookie     : true, // enable cookies to allow the server to access the session
				xfbml      : true, // parse XFBML
				oauth    : true
			});

       });
			hide_loading();

		});
	});
}


/**
 * Initializes the menu buttons.
 * Looks for each menu a-tag if it has a "template-myTemplate" class.
 * If it finds a template class name the template named "myTemplate.phtml" will be loaded into the #main-div.
 * If it does not find a template class name the "default.phtml" template will be loaded into the #main-div.
 * 
 * @return the first menu class name, "default" if there is none.
 */
function initMenu() {
	var firstMenuItem = "default"; // if no class is found the default class will be loaded and returned at the end.
	var firstTemplate = true; // determine if it was the first item to only save this for returning it later on.
	
	// loop through all menu elements (a-tags)
	$('a').each( function( index ) {
    	var foundTemplate = false; // determine after the loop if a template class was found   	 	
    	var loadDefault = true; // determine if there were classes to load the default template at the end if not
		
		// get all classes from this element
    	var thisClassAttr = $(this).attr("class");
    	// get an array containing each class in one element (only if there is at least one class, otherwise split() will not work and break the script)
    	if( typeof( thisClassAttr ) != "undefined" ) {
    		thisClasses = thisClassAttr.split(" ");
    		// set the limit for the loop below
    		classCount = thisClasses.length;
    		loadDefault = false;
    	} else {
    		// do not loop below (this element has no classes)
    		classCount = 0;
    	}
    	
    	// loop through the classes 
    	for(var x = 0; x < classCount; x++) {
    		// check if one of the classes contain a template class
    		if( thisClasses[x].indexOf("template") >= 0 ) {			
    			// template class found, get the filename (format: "welcome-filename")
    			var templateToLoad = thisClasses[x].split("-")[1];

    			// bind an onclick function to this menu element
    			$(this).click( function(){
    				// show the loading screen
    				show_loading();
    				// if clicked, load the template into the #main div (append ".phtml" to the template filename)
    				$("#main").slideUp( 0, function(){
    					$("#main").load( "templates/" + templateToLoad + ".phtml?aa_inst_id=" + aa_inst_id, function(){
							$("#main").slideDown(600,function(){
								//reinit facebook
								FB.init({
								   appId      : fb_app_id, // App ID
								   channelUrl : fb_canvas_url + 'channel.html', // Channel File
								   status     : true, // check login status
								   cookie     : true, // enable cookies to allow the server to access the session
								   xfbml      : true, // parse XFBML
								   oauth    : true
								});
							});    						
    					});    					
    				});   				
    			});   			
    			// found a template class, remember that!
    			foundTemplate = true;
    			
    			// if it is the first menu-template item, save it to return it later
    			if( firstTemplate == true ) {   				
	    			firstMenuItem = templateToLoad;	    			
	    			// remember that this was the first one
	    			firstTemplate = false;	    			
    			}    			
    		}    		
    	} // end loop through the classes of this element
    	// if no template class was found, use the default one
		if( foundTemplate == false && loadDefault == true ){
			$(this).click(function(){
				$("#main").slideUp( 0, function(){
					$("#main").load( "templates/default.phtml?aa_inst_id=" + aa_inst_id, function(){
						$("#main").slideDown(600,function(){
						   //reinit facebook
						   FB.init({
							  appId      : fb_app_id, // App ID
							  channelUrl : fb_canvas_url + 'channel.html', // Channel File
							  status     : true, // check login status
							  cookie     : true, // enable cookies to allow the server to access the session
							  xfbml      : true, // parse XFBML
							  oauth    : true
						   });
						});
					});					
				});				
			});			
		}
	}); // end loop through all menu elements
	return firstMenuItem;
};

/**
 * Authorize the user when he clicks on the tag image.
 */
function authUser( xCoord, yCoord ) {
	
//alert( "debug: " + userHasAuthorized );
	
	if( userHasAuthorized == false ) {
		
		FB.login(function(response) {
			
			if (response.authResponse) {
				
				FB.api('/me', function(response) {
					
					fb_user_id = response.id;
					fb_user_name = response.name;
					
					// Init Game because it won't be inited when the user just authorized the app.
					
					userHasAuthorized = true;
					
					saveTag( xCoord, yCoord );
					
				});
				
			} else {
				
				//alert( 'Du musst die Abfrage zulassen um einen Stern zu hinterlassen.' );
				
				modal( 'Hinweis', 'Du musst die Abfrage zulassen um einen Stern zu hinterlassen.', false );
				
			}
			
		}, {scope: 'publish_actions'});
		
	} else {
		
		saveTag( xCoord, yCoord );
		
	}
	
}

/**
 * Save the users tag coords and his fb_id.
 */
function saveTag( xCoord, yCoord ) {
	
	$.ajax({
		
		type : 'POST',
		async: true,
		url  : 'save_tag.php?aa_inst_id=' + aa_inst_id,
		data : ({
			
			aa_inst_id   : aa_inst_id,
			fb_user_id   : fb_user_id,
			fb_user_name : fb_user_name,
			x_coord      : xCoord,
			y_coord      : yCoord
			
		}),
		
		/*****************************************************************
		 * this function gets executed when the ajax call above returns. *
		 *****************************************************************/
		success: function( data ) {
			
			if( data.indexOf( "false" ) <= 0 ) {
				
				//var graph = '<img src="https://graph.facebook.com/' + fb_user_id + '/picture?type=square" />';
				
				if( showFaces == '1' ) {
				
					$( "#image1" ).append( '<div id="tag_new"><img src="https://graph.facebook.com/' + fb_user_id + '/picture?type=square" class="fb-profile-img" /><img class="img-tag" src="' + tag_image + '" rel="tooltip" title="' + fb_user_name + '" /></div>' );
				
				} else {
				
					$( "#image1" ).append( '<div id="tag_new"><img class="img-tag" src="' + tag_image + '" rel="tooltip" title="' + fb_user_name + '" /></div>' );
				
				}
				
				$( "#tag_new" ).attr( "style", "left: " + xCoord + "px; top: " + yCoord + "px; position: absolute;" );
				
				$( "[rel=tooltip]" ).tooltip();
				//post to user wall

				var params={
					name:fb_share_title,
					link:fb_share_link,
     					caption: fb_share_subtitle, 
     					description: fb_share_desc, 
     					picture:fb_share_img 
				};

				FB.api('/me/feed','post',params,function(response){
				});

				
			} // end if data
			  else {
				
				modal( 'Hinweis', 'Du hast schon einen Stern an Bibis Himmel.', false );
				
			}
				
		} // end success function
		
	}); // end ajax call save_tag.php
	
}


function loadTags() {
	
	$.ajax({
		
		type : 'POST',
		async: true,
		url  : 'get_tags.php?aa_inst_id=' + aa_inst_id,
		dataType: 'json',
		data : ({}),
		
		/*****************************************************************
		 * this function gets executed when the ajax call above returns. *
		 *****************************************************************/
		success: function( data ) {
			
			if( data.indexOf( "false" ) >= 0 ) {
				// error/no tags yet
			} // end if data
			else {
				
				// display tags
				for( var index = 0; index < data.length; index++ ) {
					
					var user = data[ index ];
					
					//var graph = '<img src="https://graph.facebook.com/' + user.fb_user_id + '/picture?type=square" />';
					
					if( showFaces == '1' ) {
					
						$( "#image1" ).append( '<div id="tag_' + index + '"><img src="https://graph.facebook.com/' + user.fb_user_id + '/picture?type=square" class="fb-profile-img" /><img class="img-tag" src="' + tag_image + '" rel="tooltip" title="' + user.fb_user_name + '" /></div>' );
					
					} else {
					
						$( "#image1" ).append( '<div id="tag_' + index + '"><img class="img-tag" src="' + tag_image + '" rel="tooltip" title="' + user.fb_user_name + '" /></div>' );
					
					}
					$( "#tag_" + index ).attr( "style", "left: " + user.x_coord + "px; top: " + user.y_coord + "px; position: absolute;" );
					
					$( "[rel=tooltip]" ).tooltip();
					
				}
				
			}
			
				
		} // end success function
		
	}); // end ajax call get_tags.php
	
}


/**
 * Show bootstrap modal
 * @param title
 * @param body
 * @param buttons
 */
function modal(title,body,buttons) {
 if( buttons  == false )
  buttons='<a href="#" data-dismiss="modal" class="btn">OK</a>';
 
 var html='<div class="modal hide fade" id="mymodal">'+
  '<div class="modal-header"><a class="close" data-dismiss="modal">x</a><h3>' + title + '</h3></div>' +
  '<div class="modal-body"><p>' + body + '</p></div>' + 
  '<div class="modal-footer">' + buttons + '</div></div>';
 
 jQuery("#mymodal").remove();
 jQuery("body").append(html);
 jQuery("#mymodal").modal('show');
}
