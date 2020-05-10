var map, workshops, count, lightMap, darkMap
var	services = []
var disciplines = []
var materials = []
var tools = []
var minOpenedYear = new Date().getFullYear()
var maxOpenedYear = new Date().getFullYear()

const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)').addListener(detectDarkmode)

jQuery(document).ready(() => {
	jQuery('.post').fitVids()

	jQuery('.filter input[type=search]').on('input', () => {
		var search_text = this.value.toLowerCase()
		var items = jQuery(this).parents('.filter').find('ul li')
		items.hide()
		items.filter((i, item) => {
			if (jQuery(item).find('label').text().toLowerCase().indexOf(search_text) != -1)
				return true
			return false
		}).show()
	})

	L.mapbox.accessToken = 'pk.eyJ1IjoidW5rbm93bmRvbWFpbiIsImEiOiJOMUxIcm1RIn0.801sde-cFjqY2gtBTHm-zA'

	if (jQuery('#map').length > 0) {

		// Initialise map
		map = L.mapbox.map('map')
		darkMap = L.mapbox.styleLayer('mapbox://styles/unknowndomain/ck9ws2q800qmd1jlj43a59w9e')
		lightMap = L.mapbox.styleLayer('mapbox://styles/unknowndomain/ck9wrkd4206hs1ittxlqu8sod')
		detectDarkmode(window.matchMedia('(prefers-color-scheme: dark)'))
		map.setView([OWN.map.center.lat, OWN.map.center.lng], OWN.map.zoom)
		map.scrollWheelZoom.disable()
		map.zoomControl.remove()

		if (window.matchMedia('(max-width: 50em)').matches)
			map.dragging.disable()

		if (window.matchMedia('(min-width: 75em)').matches)
			map.setZoom(parseInt(OWN.map.zoom)+1)

		// Add zoom controls
		new L.Control.Zoom({position: 'bottomright'}).addTo(map)

		// Create workshop layer
		workshops = L.mapbox.featureLayer().addTo(map)
		workshops.on('layeradd', styleMarker)

		// Get marker data
		NProgress.start()
		jQuery.getJSON(OWN.ajax_url, (data) => {
			workshops.setGeoJSON(data)
			workshops.eachLayer(createPopup)
			workshops.eachLayer(createListItem)
			count = 0
			workshops.eachLayer(updateMinYears, workshops)
			if (window.matchMedia('(max-width: 50em)').matches) {
				map.on('popupopen', (e) => {
					var popup = jQuery(e.popup._content).addClass('popup')
					map.closePopup()
					jQuery('body').append(popup)
					jQuery(popup).prepend('<a class="close">Close</a>')
					jQuery('.close').click(() => {jQuery(popup).remove()})
				})
			}
		})

		// Enable filter checkboxes
		jQuery('#homepage .filter input[type=checkbox]').on('change', setFilter)
	}
})

function detectDarkmode(e) {
	if (e.matches) {
		map.addLayer(darkMap)
		setTimeout(() => {map.removeLayer(lightMap)}, 100)
	} else {
		map.addLayer(lightMap)
		setTimeout(() => {map.removeLayer(darkMap)}, 100)
	}
}

function updateMinYears(layer) {
	var marker = layer.feature.properties

	if (marker.opened !== null && marker.opened < minOpenedYear) minOpenedYear = parseInt(marker.opened)

	if (count++ >= Object.keys(workshops._layers).length - 1) {
		jQuery('.opened div').noUiSlider({
			start: [minOpenedYear, maxOpenedYear],
			range: {
				min: [minOpenedYear],
				max: [maxOpenedYear]
			},
			connect: true,
			step: 1,
			behaviour: 'drag-tap'
		}).on('slide', (e) => {
			var range = updateSlider('.opened')
			minOpenedYear = Math.round(range[0])
			maxOpenedYear = Math.round(range[1])
			rebuildMap()
		})
		updateSlider('.opened')
		NProgress.done()
	}
}

function rebuildMap() {
	// Run that filter!
	workshops.setFilter(filter)

	jQuery('#list ul').empty()
	jQuery('#list span').text(0)
	workshops.eachLayer(createListItem)

	// Recreate popups
	workshops.eachLayer(createPopup)
}

function updateSlider(slider) {
	var range = jQuery(slider).children('div').val()
	if (range[0] != range[1]) {
		jQuery(slider).children('span').html(Math.round(range[0]) + '&mdash;' + Math.round(range[1]))
	} else {
		jQuery(slider).children('span').html(Math.round(range[0]))
	}
	return range
}

function styleMarker(e) {
	var marker = e.layer,
	location = marker.feature.properties

	if (location.location === null)
		location.location = 'top'

	// Set hover name to location name
	marker.options.title = location.name

	var anchor = L.point(20, 53)
	if (location.location == 'bottom') {
		anchor = L.point(20, -7)
	} else if (location.location == 'left') {
		anchor = L.point(50, 22)
	} else if (location.location == 'right') {
		anchor = L.point(-10, 22)
	}

	// Create custom icon
	marker.setIcon(L.divIcon({
		className: 'icon',
		iconSize: L.point(40, 40),
		html: `<span class="${location.location}" style="border-color:${location.colour};"><img src="${location.icon}" /></span>`,
		iconAnchor: anchor
	}))
}

function createListItem(layer) {
	var marker = layer.feature.properties

	var item = jQuery(`<li><a href="${marker.permalink}">${marker.name}</a></li>`).click((e) => {
		layer.openPopup()
		map.setView(layer.getLatLng(), 14)
		return false
	})
	jQuery('#list ul').append(item)
	jQuery('#list span').text(jQuery('#list ul li').length)
}

function setFilter() {
	// Clear out the filters
	services = []
	disciplines = []
	materials = []
	tools = []

	// Find service checkboxes
	jQuery('.services input[type=checkbox]:checked').each((item) => {
		var items = jQuery(this).attr('id').split('_')
		services.push(items[items.length-1])
	})

	// Find disciplines checkboxes
	jQuery('.disciplines input[type=checkbox]:checked').each((item) => {
		var items = jQuery(this).attr('id').split('_')
		disciplines.push(items[items.length-1])
	})

	// Find materials checkboxes
	jQuery('.materials input[type=checkbox]:checked').each((item) => {
		var items = jQuery(this).attr('id').split('_')
		materials.push(items[items.length-1])
	})

	// Find tools checkboxes
	jQuery('.tools input[type=checkbox]:checked').each((item) => {
		var items = jQuery(this).attr('id').split('_')
		tools.push(items[items.length-1])
	})

	rebuildMap()
}

function filter(marker) {

	// Filter out markers if the year is outside the bounds
	if (marker.properties.opened != undefined && (marker.properties.opened < minOpenedYear || marker.properties.opened > maxOpenedYear))
		return false

	// Show all if no filters are selected
	if (services.length === 0 && disciplines.length === 0 && materials.length === 0 && tools.length == 0)
		return true

	// Filter out markers without any taxonomies
	if (marker.properties.taxonomies === undefined)
		return false

	// Filter for services
	if (marker.properties.taxonomies.services !== undefined) {
		for (i in services) {
			if (marker.properties.taxonomies.services.indexOf(services[i]) == -1)
				return false
		}
	} else {
		return false
	}

	// Filter for disciplines
	if (marker.properties.taxonomies.disciplines !== undefined) {
		for (i in disciplines) {
			if (marker.properties.taxonomies.disciplines.indexOf(disciplines[i]) == -1)
				return false
		}
	} else {
		return false
	}

	// Filter for materials
	if (marker.properties.taxonomies.materials !== undefined) {
		for (i in materials) {
			if (marker.properties.taxonomies.materials.indexOf(materials[i]) == -1)
				return false
		}
	} else {
		return false
	}

	// Filter for tools
	if (marker.properties.taxonomies.tools !== undefined) {
		for (i in tools) {
			if (marker.properties.taxonomies.tools.indexOf(tools[i]) == -1)
				return false
		}
	} else {
		return false
	}

	// Show it
	return true
}

function createPopup(layer) {
	var marker = layer.feature.properties

	// Wrapper and permalink
	var content = `<div class="wrapper" style="border-left-color:${marker.colour};">`

	// Avatar image
	content += `<img src="${marker.icon}"><h1>${marker.name}</h1>`

	// Admin
	if (marker.admin != undefined)
		content += `<p class="admin"><a href="${marker.admin}">Edit</a></p>`

	// Description text
	if (marker.text !== undefined)
		content += `<p class="desc">${marker.text}</p>`

	// Website
	if (marker.url != undefined)
		content += `<a class="permalink" href="${marker.url}">Website</a>`

	// More info
	if (marker.permalink != undefined)
		content += `<a class="permalink" href="${marker.permalink}">More info</a>`

	// Photo
	if (marker.photo != undefined)
		content += `<div class="photo" style="background-image: url(${marker.photo});"></div>`

	// Close wrapper
	content += '</div>'

	var popup = L.popup({
		minWidth: 325,
		className: 'popup',
		keepInView: true,
		offset: new L.Point(0, 7),
		autoPanPaddingTopLeft: L.point(0, 125),
		autoPanPaddingBottomRight: L.point(0, 25)
	}).setContent(content)

	layer.bindPopup(popup)
}
