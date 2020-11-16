<html>

<head>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <link href="/assets/css/login.css" rel="stylesheet">
    <script src="/assets/js/sha256.js"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">



                <div id="frm-login">

                    <div class="myform form ">
                        <div class="logo mb-3">
                            <div class="col-md-12 text-center">
                                <h1>Login</h1>
                            </div>
                        </div>
                        <form action="/auth/login" method="post" name="frm-login">
                            <input type='hidden' name='{{ csrfKey }}' value='{{ csrfValue }}' />

                            {{ partial('notification') }}


                            <div class="form-group">
                                <label for="login-email">Email address</label>
                                <input type="email" name="email" class="form-control" id="login-email"
                                    aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="login-password">Password</label>
                                <input type="password" name="password" id="login-password" class="form-control"
                                    aria-describedby="emailHelp" placeholder="Enter Password">
                            </div>
                            <div class="col-md-12 text-center ">
                                <button type="submit" class=" btn btn-block mybtn btn-primary tx-tfm">Login</button>
                            </div>
                            <!-- 
                            <div class="col-md-12 ">
                                <div class="login-or">
                                    <hr class="hr-or">
                                    <span class="span-or">or</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <p class="text-center">
                                    <a href="javascript:void();" class="google btn mybtn"><i class="fa fa-google-plus">
                                        </i> Signup using Google
                                    </a>
                                </p>
                            </div>
                             -->

                            <a href="/password/forgot" class="reset-password">Forgot password?</a>

                            <div class="col-md-12 ">
                                <div class="login-or">
                                    <span class="span-or">&nbsp;</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <p class="text-center">Don't have account? <a href="/identity/signup"
                                        class="signup">Sign up here</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>


            </div>
        </div>
    </div>


    <script src="/assets/js/login.js"></script>
</body>

</html>