function updateSection(event) 
{
	url = event.data.url;
	id = event.data.id;
	$.ajax({
		url: url,
		method: "GET",
		dataType: "html"
	}).done(function( msg ) {
		$("#"+id).html( msg );
	});
}
function setEvents() {
	$( "#top-link" ).on ("click", { url: "html/sections/main.php", id: "top" },  updateSection );
	$( "#profile-link" ).on ("click", { url: "html/sections/profile.php", id: "top" }, updateSection );
	$( "#top-link" ).trigger( "click");

}
$(document).ready(setEvents);
