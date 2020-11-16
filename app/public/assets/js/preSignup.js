// $(function () {


// $(".signup").click(function () {
// 	$("#frm-reset-password").fadeOut("fast");
// 	$("#frm-login").fadeOut("fast", function () {
// 		$("#frm-signup").fadeIn("fast");
// 	});
// });

// $(".signin").click(function () {
// 	$("#frm-reset-password").fadeOut("fast");
// 	$("#frm-signup").fadeOut("fast", function () {
// 		$("#frm-login").fadeIn("fast");
// 	});
// });


// $(".reset-password").click(function () {
// 	// $("#frm-signup").fadeOut("fast");
// 	$("#frm-login").fadeOut("fast", function () {
// 		$("#frm-reset-password").fadeIn("fast");
// 	});
// });



// 	$("form[name='login']").validate({
// 		rules: {

// 			email: {
// 				required: true,
// 				email: true
// 			},
// 			password: {
// 				required: true,

// 			}
// 		},
// 		messages: {
// 			email: "Please enter a valid email address",

// 			password: {
// 				required: "Please enter password",

// 			}

// 		},
// 		submitHandler: function (form) {
// 			form.submit();
// 		}
// 	});
// });



$(function () {

	$("#accept_terms").on('change', function (e) {
		var shouldBeDisabled = true;
		if ($(this).is(':checked')) {
			shouldBeDisabled = false;
		} else {
			shouldBeDisabled = true;
		}
		console.log(shouldBeDisabled);
		$("#btn_submit").prop('disabled', shouldBeDisabled);
	});



	$("form[name='frm-signup']").validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				minlength: 5
			}
		},

		messages: {
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			email: "Please enter a valid email address"
		},

		submitHandler: function (form) {
			var email = form.email.value;
			var pass = form.password.value;
			var confirm_password = form.confirm_password.value;

			if (pass !== confirm_password) {
				// trigger error
				return false;
			}
			// Encrypt this password
			form.password.value = sha256(email + pass);
			form.confirm_password.value = sha256(email + confirm_password);
			form.submit();
		}
	});




	// $("form[name='frm-login']").validate({
	// 	rules: {
	// 		email: {
	// 			required: true,
	// 			email: true
	// 		},
	// 		password: {
	// 			required: true,
	// 			minlength: 5
	// 		}
	// 	},

	// 	messages: {
	// 		password: {
	// 			required: "Please provide a password",
	// 			minlength: "Your password must be at least 5 characters long"
	// 		},
	// 		email: "Please enter a valid email address"
	// 	},

	// 	submitHandler: function (form) {
	// 		var email = form.email.value;
	// 		var pass = form.password.value;

	// 		// Encrypt this password
	// 		form.password.value = sha256(email + pass);
	// 		form.submit();
	// 	}
	// });



});