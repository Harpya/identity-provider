<html>

<head>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <link href="/assets/css/profile.css" rel="stylesheet">
</head>

<body>


    <div class="container">
        <div class="row">
            <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
                <!-- <A href="edit.html">Edit Profile</A> -->

                <A href="/auth/logout">Logout</A>
                <br>
                <p class=" text-info">{{ now }} </p>
            </div>
            <div
                class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">


                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ user['name']  | default(user['email'])  }}</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic"
                                    src="/assets/img/avatar-300x300.png" class="img-circle img-responsive"> </div>


                            <div class=" col-md-9 col-lg-9 ">
                                <table class="table table-user-information">
                                    <tbody>
                                        {% for attribute in user_dyn_attributes %}
                                        <tr>
                                            <td>{{ attribute['name'] }}</td>
                                            <td>{{ attribute['value'] }}</td>
                                        </tr>
                                        {%  endfor %}
                                        <tr>
                                            <td>Email:</td>
                                            <td>{{ user['email'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Signup at:</td>
                                            <td>{{ user['created_at'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Last update:</td>
                                            <td>{{ user['updated_at']|default(user['created_at'] ) }}</td>
                                        </tr>



                                        </tr>

                                    </tbody>
                                </table>


                                <div>
                                    {% for hist in user_hist_applications %}
                                    <div>
                                        <a href='{{ hist["url"] }}'>{{ hist['name'] }}</a>
                                    </div>
                                    {% endfor %}
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <!-- <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button"
                            class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a> -->

                        <!-- <span class="pull-right"> -->

                        <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button"
                            class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>

                        <a data-original-title="Remove this user" data-toggle="tooltip" type="button"
                            class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>

                        <!-- </span> -->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var panels = $('.user-infos');
            var panelsButton = $('.dropdown-user');
            panels.hide();

            //Click dropdown
            panelsButton.click(function () {
                //get data-for attribute
                var dataFor = $(this).attr('data-for');
                var idFor = $(dataFor);

                //current button
                var currentButton = $(this);
                idFor.slideToggle(400, function () {
                    //Completed slidetoggle
                    if (idFor.is(':visible')) {
                        currentButton.html(
                            '<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
                    } else {
                        currentButton.html(
                            '<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
                    }
                })
            });


            $('[data-toggle="tooltip"]').tooltip();

            $('button').click(function (e) {
                e.preventDefault();
                alert("This is a demo.\n :-)");
            });
        });
    </script>
</body>

</html>