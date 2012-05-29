
// I am a method that cleans ColdFusion-based JSON responses. 
// By default, ColdFusion upper-cases all its keys. This method
// will lowercase the ColdFusion keys.
var cleanColdFusionJSONResponse = function( apiAction, response ){
	// Check to see if this it the load.
	if (apiAction == "load"){
		
		// Loop over response array.
		jQuery.each(
			response,
			function( index, tagData ){
				// Translate the ColdFusion keys 
				// to lowercase. This will create
				// dupliate keys, but it doesn't 
				// matter for our use-case.
				jQuery.each(
					tagData,
					function( key, value ){
						tagData[ key.toLowerCase() ] = value;
					}
				);
			
			}
		);
	
	}
	
	// Return cleaned response.
	return( response );
}