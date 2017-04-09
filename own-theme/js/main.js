var map, workshops;
var	services = [],
	disciplines = [],
	materials = [],
	processes = [];

jQuery( document ).ready( function() {
	L.mapbox.accessToken = 'pk.eyJ1IjoidW5rbm93bmRvbWFpbiIsImEiOiJOMUxIcm1RIn0.801sde-cFjqY2gtBTHm-zA';
	
	// Initialise map
	map = L.mapbox.map( 'map', 'unknowndomain.2cb05038', {
		zoom: 11,
		minZoom: 11,
		zoomControl: false,
		center: L.latLng( OWN.map.center.lat, OWN.map.center.lng ),
		// maxBounds: L.latLngBounds( L.latLng( 51.73128109939871, -0.538330078125 ), L.latLng( 51.25418003130773, 0.30487060546875 ) ),
		// dragging: false,
		scrollWheelZoom: false,
		tap: false
	} );

	// Add zoom controls
	new L.Control.Zoom( { position: 'bottomright' } ).addTo( map );

	// Create workshop layer
	workshops = L.mapbox.featureLayer().addTo( map );
	workshops.on( 'layeradd', styleMarker );

	// Get marker ata
	jQuery.getJSON( "http://unknowndomain.co.uk/OWN/wp-admin/admin-ajax.php?action=workshop", function ( data ) {
		workshops.setGeoJSON( data );
		workshops.eachLayer( createPopup );
	} );
	
	// Enable filter checkboxes
	jQuery( '#filter input[type=checkbox]' ).on( 'change', setFilter );

} );

function styleMarker( e ) {
	var marker = e.layer,
	location = marker.feature.properties;

	// Set hover name to location name
	marker.options.title = location.name;

	// Create custom icon
	marker.setIcon( L.divIcon( {
		className: 'icon',
		iconSize: L.point( 50, 50 ),
		html: '<span style="border-top-color:' + location.colour + ';"><img src="' + location.icon + '" /></span>',
		iconAnchor: L.point( 25, 63 )
	} ) );
}

function setFilter() {
	// Clear out the filters
	services = []; disciplines = []; materials = []; processes = [];

	// Find service checkboxes
	jQuery( '.services input[type=checkbox]:checked' ).each( function( item ) {
		var items = jQuery( this ).attr( 'id' ).split( '_' );
		services.push( items[items.length-1] );
	} );
	
	// Find disciplines checkboxes
	jQuery( '.disciplines input[type=checkbox]:checked' ).each( function( item ) {
		var items = jQuery( this ).attr( 'id' ).split( '_' );
		disciplines.push( items[items.length-1] );
	} );

	// Find materials checkboxes
	jQuery( '.materials input[type=checkbox]:checked' ).each( function( item ) {
		var items = jQuery( this ).attr( 'id' ).split( '_' );
		materials.push( items[items.length-1] );
	} );
	
	// Find processes checkboxes
	jQuery( '.processes input[type=checkbox]:checked' ).each( function( item ) {
		var items = jQuery( this ).attr( 'id' ).split( '_' );
		processes.push( items[items.length-1] );
	} );

	// Run that filter!
	workshops.setFilter( filter );

	// Recreate popups
	workshops.eachLayer( createPopup );
}

function filter( marker ) {
	// Show all if no filters are selected
	if ( services.length === 0 && disciplines.length === 0 && materials.length === 0 && processes.length == 0 )
		return true;

	// Filter out markers without any taxonomies
	if ( marker.properties.taxonomies === undefined )
		return false;
	
	// Filter for services
	if ( marker.properties.taxonomies.services !== undefined ) {
		for ( i in services ) {
			if ( marker.properties.taxonomies.services.indexOf( services[i] ) !== -1 )
				return true;
		}
	}
	
	// Filter for disciplines
	if ( marker.properties.taxonomies.disciplines !== undefined ) {
		for ( i in disciplines ) {
			if ( marker.properties.taxonomies.disciplines.indexOf( disciplines[i] ) !== -1 )
				return true;
		}
	}

	// Filter for materials
	if ( marker.properties.taxonomies.materials !== undefined ) {
		for ( i in materials ) {
			if ( marker.properties.taxonomies.materials.indexOf( materials[i] ) !== -1 )
				return true;
		}
	}
	
	// Filter for processes
	if ( marker.properties.taxonomies.processes !== undefined ) {
		for ( i in processes ) {
			if ( marker.properties.taxonomies.processes.indexOf( processes[i] ) !== -1 )
				return true;
		}
	}

	// Don't show it
	return false;
}

function createPopup( layer ) {
	var marker = layer.feature.properties;

	// Wrapper and permalink
	var content = '<div class="wrapper" style="border-left-color: ' + marker.colour + ';">';

	// Avatar image
	content += '<img src="' + marker.icon + '" /><h1>' + marker.name + '</h1>';

	// Admin
	if ( marker.admin != undefined )
		content += '<p class="admin"><a href="' + marker.admin + '">Edit</a></p>';

	// Description text
	if ( marker.text !== undefined )
		content += '<p class="desc">' + marker.text + '</p>';

	// Disciplines
	if ( marker.disciplines !== undefined && marker.disciplines != "" )
		content += '<p class="disciplines"><strong>Disciplines</strong>' + marker.disciplines + '</p>';

	// More info
	if ( marker.permalink != undefined )
		content += '<a class="permalink" href="' + marker.permalink + '">More info</a>';

	// Photo
	if ( marker.photo != undefined )
		content += '<div class="photo" style="background-image: url(' + marker.photo + ');"></div>';

	// Close wrapper
	content += "</div>";

	var popup = L.popup( {
		// closeButton: false,
		minWidth: 325,
		className: 'popup',
		keepInView: true,
		autoPanPaddingTopLeft: L.point( 0, 125 ),
		autoPanPaddingBottomRight: L.point( 0, 25 )
	} ).setContent( content );

	layer.bindPopup( popup );
}