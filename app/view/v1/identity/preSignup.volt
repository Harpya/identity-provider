<html>

<head>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <script src="/assets/js/sha256.js"></script>

    <link href="/assets/css/login.css" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">


                <div id="frm-signup">
                    <div class="myform form ">
                        <div class="logo mb-3">
                            <div class="col-md-12 text-center">
                                <h1>Signup</h1>
                            </div>
                        </div>
                        <form action="/identity/signup" method="post" name="frm-signup">



                            <input type='hidden' name='{{ csrfKey }}' value='{{ csrfValue }}' />

                            {{ partial('notification') }}

                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    aria-describedby="passwordHelp" placeholder="Enter Password">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm password</label>
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="form-control" aria-describedby="confirmPasswordHelp"
                                    placeholder="Confirm Password">
                            </div>
                            <div class="form-group">
                                <label for="accept_terms">
                                    <input type="checkbox" name="accept_terms" id="accept_terms" value="yes"
                                        aria-describedby="acceptTermsHelp" placeholder="Accept terms and conditions" />
                                    Accept {{ link_terms }} and conditions</label>
                            </div>
                            <div class="col-md-12 text-center mb-3">
                                <button type="submit" id="btn_submit" class="btn btn-block mybtn btn-primary tx-tfm"
                                    disabled>
                                    Signup
                                </button>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <p class="text-center"><a href="/auth/login" class="signin">Already have an account?
                                            Click here to login</a></p>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>



            </div>
        </div>
    </div>



    <script src="/assets/js/preSignup.js"></script>
</body>

</html>