<?php
session_start();          
ob_start();

include_once("db.php");

//Let the user logout
if(isset($_GET['q'])&&$_GET['q']==="logout") {
  // remove all the variables in the session
  session_unset(); 
  
  // destroy the session
  session_destroy();
  
  // redirect to index
  header("Location: $site");
}


if(isset($_POST['submitted'])) {
  $user = pg_escape_string($_POST['username']);
  $password = pg_escape_string($_POST['password']);

  $bind = array(
    ":username" => "$user",
    ":pass" => "$password"
    );

  $res = $db->select("wirelessadviser.users", "user_id=:username AND password=:pass LIMIT 1", $bind);

  if(!$res) {
    echo "try again";
  } else {
    $_SESSION['valid_user'] = "true";
    $_SESSION['user_id'] = $res[0]['user_id'];
    $_SESSION['account_type'] = $res[0]['account_type'];
    header( "Location: $site/" );
    exit();
  }
}

ob_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign in &middot; Twitter Bootstrap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Le styles -->
  <link href="../assets/css/bootstrap.css" rel="stylesheet">
  <style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    max-width: 400px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
    -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
    box-shadow: 0 1px 2px rgba(0,0,0,.05);
  }
  .form-signin .form-signin-heading,
  .form-signin .checkbox {
    margin-bottom: 10px;
  }
  .form-signin input[type="text"],
  .form-signin input[type="password"] {
    font-size: 16px;
    height: auto;
    margin-bottom: 15px;
    padding: 7px 9px;
  }

  </style>
  <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
      <![endif]-->

      <!-- Fav and touch icons -->
      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
      <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
      <link rel="shortcut icon" href="../assets/ico/favicon.png">
    </head>

    <body>

      <div class="container">

        <form class="form-signin" method="POST" action="<?php echo $site ?>/user/login">
          <h1 class="form-signin-heading">Wireless Adviser</h1>
          <h2 class="form-signin-heading">Please sign in</h2>
          <input type="text" class="input-block-level" placeholder="username" name="username">
          <input type="password" class="input-block-level" placeholder="password" name="password">
          <button class="btn btn-large btn-primary" type="submit" name="submitted">Sign in</button>
        </form>

      </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
  </html>
