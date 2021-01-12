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

};

function filter(checked) {
	 markers.setFilter(function(f) {
        // If the data-filter attribute is set to "all", return
        // all (true). Otherwise, filter on markers that have
        let result = false;
        checked.forEach(function(element) {
        	if (f.properties[element] === true) result = true;
        });
        return result;
        // a value set to true based on the filter name.
    });
}

jQuery('input.tags-filter').on('click', function() {
	let checked = [];

	jQuery('#test-markers input.tags-filter:checkbox:checked').each(function(e) {
		checked.push(jQuery(this).val());
	});

	filter(checked);
});