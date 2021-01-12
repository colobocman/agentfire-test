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

};