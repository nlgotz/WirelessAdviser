
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{name.title}} | Wireless Adviser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Le styles -->
  <link href="{{name.site}}assets/css/bootstrap.css" rel="stylesheet">
  <link href="{{name.site}}assets/css/bootstrap-responsive.css" rel="stylesheet">


  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="{{name.site}}assets/js/html5shiv.js"></script>
      <![endif]-->

      <!-- Fav and touch icons -->
      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{name.site}}assets/ico/apple-touch-icon-144-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{name.site}}assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{name.site}}assets/ico/apple-touch-icon-72-precomposed.png">
      <link rel="apple-touch-icon-precomposed" href="{{name.site}}assets/ico/apple-touch-icon-57-precomposed.png">
      <link rel="shortcut icon" href="{{name.site}}assets/ico/favicon.png">
      <!--reloadr.js and script should be disabled for productions-->
      <script src="reloadr.js"></script>
      <script>
    // full, awesome syntax
    Reloadr.go({
      client: [
      '{{name.site}}assets/css/main.css'
      ],
      server: [
      '*.php',
      'templates/*'
      ],
      path: 'reloadr.php',
      frequency:100
    });
    </script>
    <link href="{{name.site}}assets/css/main.css" rel="stylesheet">

    <script src="{{name.site}}assets/js/jquery.min.js"></script>
    <script src="{{name.site}}assets/js/jquery.dataTables.min.js"></script>

  </head>

  <body>
    <div id="wrap">
      <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
          <div class="container-fluid">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="brand" href="{{name.site}}">Wireless Adviser</a>
            <div class="nav-collapse collapse">
              <p class="navbar-text pull-right">
                <a href="{{name.site}}login.php?p=logout" class="navbar-link">Logout</a>
              </p>
              <ul class="nav">
                <li {% if name.t == "ma"%} class="active"{% endif %}>
                  <a href="{{name.site}}index.php?p=map">Map</a></li>
                  <li{% if name.t == "in"%} class="active"{% endif %}>
                  <a href="{{name.site}}index.php?p=inventory">Inventory</a></li>
                  <li{% if name.t == "da"%} class="active"{% endif %}>
                  <a href="{{name.site}}index.php?p=dashboard">Dashboard</a></li>
                  <li{% if name.t == "ev"%} class="active"{% endif %}>
                  <a href="{{name.site}}index.php?p=events">Events</a></li>
                </ul>
              </div><!--/.nav-collapse -->
            </div>
          </div>
        </div>

        <div class="container-fluid">
         {% block container %}{% endblock %}
       </div>

       <div id="push"></div>
     </div>

     <div id="footer">
      <div class="container">
        <a href="{{name.site}}index.php?p=admin">Admin</a> | <a href="#">Manual</a> | 
        <a href="#About" data-toggle="modal">About</a>
      </div>
    </div>

    <!--About Modal-->
    <div id="About" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="AboutLabel">About Wireless Adviser</h3>
      </div>
      <div class="modal-body">
        <p>Wireless Adviser {{wa.version}} created by Nathan Gotz</p>
        <p>Original Wireless Adviser created by Cambium Networks</p>
        <p>Licensed under GNU GPLv3</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>


    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{name.site}}assets/js/bootstrap.min.js"></script>

  </body>
  </html>
