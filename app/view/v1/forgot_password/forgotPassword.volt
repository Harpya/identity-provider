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

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">


                <div id="frm-reset-password">
                    <div class="myform form ">
                        <div class="logo mb-3">
                            <div class="col-md-12 text-center">
                                <h1>Reset password</h1>
                            </div>
                        </div>
                        <form action="/password/forgot" method="post" name="reset-password">
                            <input type='hidden' name='{{ csrfKey }}' value='{{ csrfValue }}' />

                            {{ partial('notification') }}


                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="col-md-12 text-center mb-3">
                                <button type="submit" class=" btn btn-block mybtn btn-primary tx-tfm">
                                    Reset your password
                                </button>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <p class="text-center"><a href="/auth/login" class="signin">Already have an
                                            account?</a></p>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>






            </div>
        </div>
    </div>



    <script src="/assets/js/index.js"></script>
</body>

</html>