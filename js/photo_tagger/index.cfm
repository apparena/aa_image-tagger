
<!DOCTYPE HTML>
<html>
<head>
	<title>Image Tagger</title>
	<style type="text/css">
		
		div.photo-column {
			float: left ; 
			margin-right: 10px ;
		}
		
		div.photo-container {
			border: 1px solid #333333 ;
			margin-bottom: 13px ;
		}
		
	</style>
	<script type="text/javascript" src="js/libs/jquery-1.7.1.min.js"><\/script>
	<script type="text/javascript" src="js/photo_tagger/coldfusion.json.js"></script>
	<script type="text/javascript" src="js/photo_tagger/phototagger.jquery.js"></script>
	<script type="text/javascript">
		
		// When the DOM is ready, initialize the scripts.
		jQuery(function( $ ){
			
			// Set up the photo tagger.
			$( "div.photo-container" ).photoTagger({
				
				// The API urls.
				loadURL: "js/photo_tagger/load_tags.cfm",
				saveURL: "js/photo_tagger/save_Tag.cfm",
				deleteURL: "js/photo_tagger/delete_tag.cfm",
				
				// Default to turned on.
				// isTagCreationEnabled: false,
				
				// This will allow us to clean the response from 
				// a ColdFusion server (it will convert the 
				// uppercase keys to lowercase keys expected by
				// the photoTagger plugin.
				//cleanAJAXResponse: cleanColdFusionJSONResponse
			});
			
			
			// Hook up the enable create links.
			/*
			$( "a.enable-create" ).click(
				function( event ){
					// Prevent relocation.
					event.preventDefault();
					
					// Get the container and enable the tag 
					// creation on it.
					$( this ).prevAll( "div.photo-container" )
						.photoTagger( "enableTagCreation" )
					;
				}
			);
			
			
			// Hook up the disabled create links.
			$( "a.disable-create" ).click(
				function( event ){
					// Prevent relocation.
					event.preventDefault();
					
					// Get the container and enable the tag 
					// creation on it.
					$( this ).prevAll( "div.photo-container" )
						.photoTagger( "disableTagCreation" )
					;
				}
			);
			
			
			// Hook up the enable delete links.
			$( "a.enable-delete" ).click(
				function( event ){
					// Prevent relocation.
					event.preventDefault();
					
					// Get the container and enable the tag 
					// deletion on it.
					$( this ).prevAll( "div.photo-container" )
						.photoTagger( "enableTagDeletion" )
					;
				}
			);
			*/
			
			// Hook up the disabled delete links.
			$( "a.disable-delete" ).click(
				function( event ){
					// Prevent relocation.
					event.preventDefault();
					
					// Get the container and disabled the tag 
					// deletion on it.
					$( this ).prevAll( "div.photo-container" )
						.photoTagger( "disableTagDeletion" )
					;
				}
			);
		
		});
		
	</script>
</head>
<body>

	<h1>
		Image Tagger
	</h1>

	
	<div class="photo-column">
	
		<div class="photo-container">
			<img 
				id="photo3" 
				src=<?=$session->config["image_to_tag"]["value"]?>
				width="520" 
				height="347" 
				alt="Verewige dich im Bild" 
				/>
		</div>
	
	</div>

</body>
</html>