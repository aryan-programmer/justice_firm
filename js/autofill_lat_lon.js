function on_pos(position) {
	console.log(position);
	document.getElementById("latitude").value = position.coords.latitude;
	document.getElementById("longitude").value = position.coords.longitude;
}

function autofill_lat_lon(v) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(on_pos);
	} else if (v) {
		alert("Geolocation is not supported by this browser.");
	}
}
