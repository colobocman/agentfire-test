var markers;
var checked = [];
var map = L.mapbox.map('map')
.setView([42.68, -95.63], 4) 
.addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11'))
.on('click', function(e) {
	if (AgentFireTest.current_user !== '' ) {
		console.log(AgentFireTest.current_user);
		jQuery('#add_marker_form input[name ="lat"]').val(e.latlng.lat);
		jQuery('#add_marker_form input[name ="lng"]').val(e.latlng.lng);
		jQuery('#add_marker_modal').modal('show');
	}
});
reload_map();

// Reloads map and filters. If reload_json is true - it reload all markers from backend
function reload_map(reload_json = true) {
	if (reload_json === true) {
		if (typeof markers === 'object') markers.remove();
		fetch('/wp-json/agentfire-test/v1/markers', {method: 'GET'})
		.then(response => response.text())
		.then((response) => {
			markers = L.mapbox.featureLayer()
			.setGeoJSON(JSON.parse(response))
			.addTo(map)
			.setFilter(function(f) {
				let result = false;
				if (checked.length > 0) {
					checked.forEach(function(element) {
						if (f.properties[element] === true) result = true;
					});
				} else {
					result = true;
				}
				return result;
			})
		});
	}
	else
	{
		markers.setFilter(function(f) {
			let result = false;
			if (checked.length > 0) {
				checked.forEach(function(element) {
					if (f.properties[element] === true) result = true;
				});
			} else {
				result = true;
			}
			return result;
		})
	}
}

add_marker_form.onsubmit = async (form) => {
	form.preventDefault();

	var loaded_data = new FormData(add_marker_form);

	var tags 	= loaded_data.getAll('tags[]');
	var lat 	= loaded_data.get('lat');
	var lng 	= loaded_data.get('lng');
	var name 	= loaded_data.get('name');
	var username = AgentFireTest.current_user;
	var nonce 	= AgentFireTest.nonce;

	data = {
		"nonce": nonce,
		"name": name,
		"lat": lat,
		"lng": lng,
		"tags": tags,
		"username": username
	}
	console.log(data);

	let response = await fetch('/wp-json/agentfire-test/v1/marker', {
		method: 'POST',
		body: JSON.stringify(data)
	});
	// let result = await response.json();
	let result = await response.json();
	console.log(result.message);
	
	jQuery('#add_marker_modal').modal('hide');

	reload_map();
};

jQuery('input.tags-filter').on('click', function() {
	checked=[];
	jQuery('#test-markers input.tags-filter:checkbox:checked').each(function(e) {
		checked.push(jQuery(this).val());
	});
	reload_map(false);
});