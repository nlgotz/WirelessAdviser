<?php
session_start();
include_once("db.php");
if ( !isset( $_SESSION['valid_user'] ) || $_SESSION['valid_user'] != "true" )
{
    header( "Location: $site/user/login" );
    exit();
}

require_once 'h2o/h2o.php';


    //check if the name get parameter is set, if not, set it to the default
if(isset($_GET['name']))
{
    $name = $_GET['name'];
} else {
    $name = "map";
} 



switch ($name) {
    case 'inventory':
    $h2o = new h2o('templates/inventory.tpl');

    $name = array(
        'title' => 'Device Inventory',
        't' => 'in',
        );
        //$results = $db->select("CurrentDay");
        /*
"SELECT devices.device_id, display_name, ip_address, device_type, device_state, latitude, longitude, azimuth, height, max_severity as severity_id
FROM wirelessadviser.devices LEFT OUTER JOIN wirelessadviser.max_severity_for_device
ON devices.device_id = max_severity_for_device.device_id
ORDER BY devices.device_id"
*/

break;
case 'dashboard':
$h2o = new h2o('templates/dashboard.tpl');

$name = array(
    'title' => 'Dashboard',
    't' => 'da',
    );
break;
case 'events':
$h2o = new h2o('templates/events.tpl');

$name = array(
    'title' => 'Events',
    't' => 'ev',
    );
break;
case 'device':
$h2o = new h2o('templates/device.tpl');

$name = array(
    'title' => 'Device',
    't' => 'de',
    );
break;
case 'map':
default:
$h2o = new h2o('templates/map.tpl');

$name = array(
    'title' => 'Map',
    't' => 'ma',
    );
}
$name['site'] = "http://".$_SERVER['HTTP_HOST']."/wafe/";
# render your awesome name
echo $h2o->render(compact(array('results', 'name')));

?>
