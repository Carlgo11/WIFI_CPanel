<?php
$content_dir = __DIR__ . '/res/';
include_once $content_dir . 'header.php';
if (isset($_POST['username'])) {

    $time = NULL;
    if (isset($_POST['expire'])) {
            $time = time() + 24*60*60;
    }

    include_once __DIR__ . "/res/API.php";
    Radius_db::createUser($_POST["username"], $_POST["password"], $time);
    header("Refresh:0");
}
?>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Radius Control Panel</a><span class="glyphicon glyphicon-cloud" aria-hidden="true" style="color: whitesmoke; font-size:300%;"/>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">

                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container" style="padding-top: 50px; ">

        <div class="starter-template" style="padding: 40px 15px;text-align: center;">
            <h1>Radius Server Control Panel</h1>
            <p class="lead">Use this Control Panel to edit user access to WIFI capable devices on your network.</p>
        </div>

        <!-- Left Div -->
        <div style=" float: left; width: 50%">
            <h2>Create User Account</h2>
            <form class="form-horizontal" style="width: 30%;" action="" method="POST" accept-charset="UTF-8" >
                <input class="form-control" type="text" name="username" id="username" required="" placeholder="Username"/>
                <input class="form-control" type="password" name="password" id="password" required=""  placeholder="Password"/>
                <div class="form-group" id="days">
                    <label for="expiry_days" class="control-label col-sm-4">Days</label>
                    <div class="col-sm-6">
                        <input class="form-control" type="checkbox" name="expire" id="expire">
                    </div>
                </div>
                <button class="btn btn-lg btn-success" type="submit" id="submit" name="submit" >Create User</button>
            </form>
        </div>


        <!-- Right Div -->
        <div style="float: right;width: 50%; align-items: center;text-align: center">
            <h2 style="text-align: center">Active accounts</h2>
            <table class="table table-striped table-hover" style="width: 100%">
                <thead>
                    <tr>
                        <th style="text-align: center">ID</th>
                        <th style="text-align: center">Username</th>
                    </tr>
                </thead>
                <tbody><?php
                    include_once __DIR__ . "/res/API.php";
                    $users = Radius_db::getAllUsers();
                    foreach ($users as $user) {
                        echo '<tr>';
                        foreach ($user as $element) {
                            echo '<td>' . $element . '</td>';
                        }
                        echo '</tr>';
                    }
                    ?></tbody>
            </table>
        </div>

    </div><!-- /.container -->
</body>
</html>
