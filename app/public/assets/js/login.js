$(function () {

	$("form[name='frm-login']").validate({
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

			// Encrypt this password
			form.password.value = sha256(email + pass);
			form.submit();
		}
	});



});