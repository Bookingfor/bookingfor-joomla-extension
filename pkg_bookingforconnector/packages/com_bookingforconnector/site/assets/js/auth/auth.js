
/*--------------- GOOGLE--------------------*/
	
	var accessTokenGoogle;
	var configGoogle = {
		'client_id': '547683899985.apps.googleusercontent.com',
		'scope': ['https://www.googleapis.com/auth/userinfo.profile','https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/plus.me'],
	};    

	function authGoogle() {
 
		gapi.auth.authorize(configGoogle, function() {
			accessTokenGoogle = gapi.auth.getToken().access_token;
//			console.log('We have got our token....');
//			console.log(accessTokenGoogle);
//			console.log('We are now going to validate our token....');
			validateTokenGoogle();
				   
		});
	}


function validateTokenGoogle() {
    jQuery.ajax({
        url: 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' + accessTokenGoogle,
        data: null,
        success: function(response){  
//            console.log('Our token is valid....');
//            console.log('We now want to get info about the user using our token....');
            getUserInfoGoogle();
        },  
        error: function(error) {
            console.log('Our token is not valid....');
        },
        dataType: "jsonp" 
    });
}
 
function getUserInfoGoogle() {
    jQuery.ajax({
        url: 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' + accessTokenGoogle,
        data: null,
        success: function(response) {
//            console.log('We have gotten info back....');
//            console.log(response);
            jQuery('#Name').val(response.given_name);
            jQuery('#Surname').val(response.family_name);
            jQuery('#Email').val(response.email);
            jQuery('#Email2').val(response.email);
            jQuery('#Password').val(response.id);
            jQuery('#Password2').val(response.id);
			jQuery('#Send').trigger('click');
        },
        dataType: "jsonp"
    });
} 

/*--------------- end GOOGLE--------------------*/


/*--------------- FACEBOOK--------------------*/
      // Init the SDK upon load
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '356428027835170', // App ID
          channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });
		facebookloaded = true;

        // listen for and handle auth.statusChange events
        FB.Event.subscribe('auth.statusChange', function(response) {
          if (response.authResponse) {
            // user has auth'd your app and is logged into Facebook
            FB.api('/me', function(me){
              if (me.name) {
					jQuery('#Name').val(response.first_name);
					jQuery('#Surname').val(response.last_name);
					jQuery('#Email').val(response.email);
					jQuery('#Email2').val(response.email);
					jQuery('#Password').val(response.id);
					jQuery('#Password2').val(response.id);
					jQuery('#Send').trigger('click');
                
				//document.getElementById('auth-displayname').innerHTML = me.name;
              }
            })
          } else {
          }
        });
		SSOLoginFacebook();
      } 
/*--------------- FACEBOOK--------------------*/


var bntClicked;
var googleloaded =false;
var facebookloaded =false;
var windowsloaded =false;

function SSOLoginGoogle() {
	//		authGoogle();
	googleloaded =true;
	jQuery(bntClicked).trigger('click');
}
function SSOLoginFacebook() {
	//		authGoogle();
			FB.login(function(response) {
				if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					 FB.api('/me', function(response) {
//					   console.log('Good to see you, ' + response.name + '.');
//					   console.log('Good to see id, ' + response.id + '.');
//					   console.log('Good to see email, ' + response.email + '.');
						jQuery('#Name').val(response.first_name);
						jQuery('#Surname').val(response.last_name);
						jQuery('#Email').val(response.email);
						jQuery('#Email2').val(response.email);
						jQuery('#Password').val(response.id);
						jQuery('#Password2').val(response.id);
						jQuery('#Send').trigger('click');
					 });
			   } else {
				 console.log('User cancelled login or did not fully authorize.');
			   }
			 }, {scope: 'email'});
}

function SSOLoginWindows() {
	WL.login({
			scope: ["wl.signin", "wl.basic", "wl.birthday", "wl.emails"]
		}).then(
			function (response) {
				WL.api({
					path: "me",
					method: "GET"
				}).then(
					function (response) {
						jQuery('#Name').val(response.first_name);
						jQuery('#Surname').val(response.last_name);
						jQuery('#Email').val(response.emails.preferred);
						jQuery('#Email2').val(response.emails.preferred);
						jQuery('#Password').val(response.id);
						jQuery('#Password2').val(response.id);
						jQuery('#Send').trigger('click');
//						document.getElementById("first_name").innerText = response.first_name;
//						document.getElementById("last_name").innerText = response.last_name;
//						document.getElementById("email").innerText = response.emails.preferred;
//						document.getElementById("gender").innerText = response.gender;
//						document.getElementById("birthday").innerText =
//							response.birth_month + " " + response.birth_day + " " + response.birth_year;
					}
				);
			}
		);
//		hello( auth.network ).api( '/me' ).success(function(response){
//				jQuery('#Name').val(response.first_name);
//				jQuery('#Surname').val(response.last_name);
//				jQuery('#Email').val(response.email);
//				jQuery('#Email2').val(response.email);
//				jQuery('#Password').val(response.id);
//				jQuery('#Password2').val(response.id);
//			jQuery('#Send').trigger('click');
//		});

//	});

}

//hello.on('auth.login', function(auth){
//	// call user information, for the given network
//
////		var $target = $("#profile_"+ auth.network );
////		if($target.length==0){
////			$target = $("<div id='profile_"+auth.network+"'></div>").appendTo("#profile");
////		}
////		$target.html('<img src="'+ r.thumbnail +'" /> Hey '+r.name).attr('title', r.name + " on "+ auth.network);
//});

//hello.init( {windows: '000000004010E836'} )

function SSOLogin(s,provider) {
	bntClicked = s;
	if(provider==='google'){
		if (!googleloaded){
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "https://apis.google.com/js/client.js?onload=SSOLoginGoogle";
			document.body.appendChild(script);
		}else{
			authGoogle();
		}
	}
	if(provider==='facebook'){
      // Load the SDK Asynchronously
      if (!facebookloaded){
		  (function(d){
			 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			 if (d.getElementById(id)) {return;}
			 js = d.createElement('script'); js.id = id; js.async = true;
			 js.src = "//connect.facebook.net/en_US/all.js";
			 ref.parentNode.insertBefore(js, ref);
		   }(document));
		}else{
			SSOLoginFacebook();
		}
	}
	if(provider==='windows'){		
		if (!windowsloaded){
		jQuery.getScript( "//js.live.net/v5.0/wl.js" )
		  .done(function( script, textStatus ) {
			windowsloaded=true;
			WL.init({
				client_id: '000000004010E836',
				scope: "wl.signin", 
				redirect_uri: "/components/com_bookingforconnector/assets/authwindows.html",
				response_type: "token"
				
			});
			
			SSOLoginWindows();
		  });
		}else{
			SSOLoginWindows();
		}
	}
}
