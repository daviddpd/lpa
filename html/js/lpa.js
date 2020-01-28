function updateSection(event) 
{

	url = event.data.url;
	id = event.data.id;
	if ( event.data.user )
	{
		target_user = $("#"+event.data.user).val();
		if (  target_user !== 'undefined' )
		{
			url = url + "?user=" + target_user;
		}
	}

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
	$( "#edit-user-link" ).on ("click",
		{ url: "html/sections/profile.php", id: "top", user: "target-ldap-user" },
		updateSection );
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
 $('#userLDAPattrabute  .meta-inline').editable({
	name: _targetUser,
});
//    console.log( $('#userLDAPattrabute  .meta-inline') ) ;

/*	$('#userLDAPattrabute  .delete_dpdMeta').on("click", function() {
		var i = $(this).find(".fa-gray");
		i.removeClass( "fa-gray" ).addClass( "red" );
		$(this).on("click", userLDADDelete);
		return false;
	});
*/

	$('#savePassword').on("click", function(event) {
		//console.info ( "Save Button Pushed");
		//console.info ( event );
		var p0 = $('#oldpassword1').val();
		var p1 = $('#password1').val();
		var p2 = $('#password2').val();

		 $('#oldpassword1').val("");
		 $('#password1').val("");
		 $('#password2').val("");

		if (  p1 === p2 ) {

		var sectionData = {};
		sectionData.user = $('#uid').html().trim();
		sectionData.action = 'passwordChange';
		sectionData.oldpassword1 = p0;
		sectionData.password1 = p1;
		sectionData.password2 = p2;
		$.post('api.php?action=changePasword&field=userpassword&ou=user', sectionData).done(
			function( data ) {
				//console.info ( data );
				if ( data.errno != 0 ) {
					 $('#oldpassword1').val("");
					 $('#password1').val("");
					 $('#password2').val("");
					 $('#pwdChgStatus').html(data.result);
					 $('#pwdChgStatus').addClass("red");
				} else {
					 $('#pwdChgStatus').addClass("green");
					 $('#pwdChgStatus').html(data.result);
					$('#closePassword').trigger ("click");
				}
			}
		);
		} else {
			 $('#oldpassword1').val("");
			 $('#password1').val("");
			 $('#password2').val("");
			 $('#pwdChgStatus').html("Sorry, your new passwords did not match.");
			 $('#pwdChgStatus').addClass("red");
		}
	});

	 $('#password2').keypress(function( event ) {
		  if ( event.which == 13 ) {
			 event.preventDefault();
			$('#savePassword').trigger ("click");
		  }
		});

	$('#closePassword').on("click", function(event) {
		//console.info ( "Closed Button Pushed");
		 $('#oldpassword1').val("");
		 $('#password1').value = "";
		 $('#password2').value = "";

	});

	$('#oldpassword1').on("focus", function(event) {
		//console.info ( "Old password field focused.");
		 $('#pwdChgStatus').html("&nbsp;");
		 $('#pwdChgStatus').removeClass("red");
		 $('#pwdChgStatus').removeClass("green");

	});

	$('#saveSSH').on("click", function(event) {
		//console.info ( "saveSSH Button Pushed");
		var sshkey = $('#sshKeyUpload').val();

		var sectionData = {};
		sectionData.user = $('#uid').html().trim();
		sectionData.action = 'sshKeyUpload';
		sectionData.sshkey = sshkey;
		$.post('api.php?action=sshKeyUpload&field=sshpublickey&ou=user', sectionData).done(
			function( data ) {
				console.info ( data );
				$('#sshKeyUpload').val("");
				$('#closeSSH').trigger ("click");

/*				if ( data.EDITMODE === "admin" ) {
					$('#edit-user-link').trigger("click");
					updateSection(
						{ data: { url: "html/sections/profile.php",
							id: "top",
							user: "target-ldap-user"
						}});
				} else {
					$('#profile-link').trigger("click");
				}
*/

}
);
});
}
//$(document).ready(customMetaInit);
//$userLDADInitAdd;

