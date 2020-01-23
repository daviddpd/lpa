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


$.fn.editable.defaults.mode = 'inline'; // popup or inline
$.fn.editable.defaults.onblur = 'submit'; // cancel|submit|ignore


function userLDADInitAdd()
{
	$('#add_meta').on("click", function() {
		var sectionData = {};
		sectionData.pk = $(this).attr('meta-id');
		sectionData.action = 'add';
		$.post('api.php?a=add', sectionData).done(
			function( data ) {
//				$( "#userLDAPattrabute " ).empty().append( data );
				userLDADInit();
			}
		);
		return false;
	});
}

function userLDADDelete() {

	var sectionData = {};
	sectionData.pk = $(this).attr('ldap-field');
	sectionData.action = 'delete';
	sectionData.ldapdata = $(this).attr('ldapdata');
	console.log (sectionData);

	$.post('api.php?a=delete', sectionData).done();


}

function userLDADInit()
{
    $('#userLDAPattrabute  .meta-inline').editable();
    console.log( $('#userLDAPattrabute  .meta-inline') ) ;

	$('#userLDAPattrabute  .delete_dpdMeta').on("click", function() {
		var i = $(this).find(".fa-gray");
		i.removeClass( "fa-gray" ).addClass( "red" );
		$(this).on("click", userLDADDelete);
		return false;
	});
}
//$(document).ready(customMetaInit);
//$userLDADInitAdd;

