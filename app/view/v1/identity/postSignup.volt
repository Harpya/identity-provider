<html>

<head>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <link href="/assets/css/profile.css" rel="stylesheet">
</head>

<body>


    <div class="container">
        <div class="jumbotron">
            <h1 class="display-3">Signup</h1>

            {% if success %}
            <p class="alert alert-info">
                <a href="/auth/login" class="btn  btn-primary pull-right">Login</a>
                {{ msg }}
            </p>

            {% else %}
            <p class=" alert alert-danger">
                <a href="/identity/signup" class="btn  btn-warning pull-right">Try again</a>
                {{ msg }}
            </p>

            {% endif %}
            <!-- Button trigger modal -->
            <a class="btn btn-primary" href="/auth/login">
                Go to Login
            </a>
        </div>
    </div>
</body>

</html>