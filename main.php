<?php
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Sat, 3 Jul 1965 23:50:00 GMT" );
?>

<?php
session_start();
if ( !isset( $_SESSION['valid_user'] ) || $_SESSION['valid_user'] != "true" )
{
	header( "Location: index.php" );
	exit();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="_LANGUAGE" xml:lang="_LANGUAGE">
<head>


<title>Wireless Adviser</title>

<link rel="shortcut icon" href="images/cambium-icon.png"/>

<link rel="stylesheet" type="text/css" media="screen" href="cambium.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="libs/colorbox.css"/>

<script type="text/javascript" language="javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" language="javascript" src="https://www.google.com/jsapi"></script>

<script type="text/javascript" language="javascript" src="libs/jquery-1.6.3.js"></script>
<script type="text/javascript" language="javascript" src="libs/jquery.tablesorter-2.0.3.js"></script> 
<script type="text/javascript" language="javascript" src="libs/jquery.tablesorter-filter.js"></script> 
<script type="text/javascript" language="javascript" src="libs/jquery_ready_handler.js"></script>
<script type="text/javascript" language="javascript" src="libs/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="libs/log4javascript.js"></script>

<script type="text/javascript">
	var log = log4javascript.getNullLogger();
	// NOTE: To enable logging (will show a popup window with log messages), uncomment the following line.
	//log = log4javascript.getDefaultLogger();
</script>

<script type="text/javascript" language="javascript" src="libs/wafe_utils.js"></script>
<script type="text/javascript">
function about()
{
	var aboutText = '<div align="center" class="about">';
	aboutText += '<a href="http://cambiumnetworks.com" target="_blank"><img src="images/cambium-logo-about.png" width="243" height="87"></a>';
	aboutText += '<br><br><span class="about_large">Wireless Adviser</span>';
	aboutText += '<br><i class="about_small">(Version ' + version + ')</i>';
	aboutText += '<br><br><hr/>';
	aboutText += '<i class="about_small">Comments? Suggestions?  Send email to <a href="mailto:wirelessadviser@cambiumnetworks.com">Wireless Adviser Comments</a></i>';
	aboutText += '</div>';
	$.colorbox({html:aboutText, transition:"none", width:"285px", height:"270px"});
}

function adminCheck()
{
	accountType = "<?php echo $_SESSION['account_type'] ?>";
	if ( accountType != "admin" )
	{
		alert( "Error: Account Type is " + accountType + ".  You must be an Admin user to access the Administration interface." );
	}
	else
	{
		//window.open( "admin.php" );	
		window.location="admin.php";
	}
}
</script>


</head>
<body>

<table>
<tr><td>
<div class="container">

    <ul class="tabs">
    	<li><a href="#tab1" id="showMap">Map</a></li>
    	<li><a href="#tab2" id="showInventory">Inventory</a></li>		
    	<li><a href="#tab3" id="showDashboard">Dashboard</a></li>
    	<li><a href="#tab4" id="showEvents">Events</a></li>
    	<li><a href="#tab5" id="showAddons">Addons</a></li>
    </ul>

   	<div class="tab_container">

		<!--
    	############################################################################################
    	## Map Tab
    	##
    	############################################################################################
    	-->
	    <div id="tab1" class="tab_content">
 	  		<script type="text/javascript" language="javascript" src="js/networkmap.min.js"></script>
			<div id="map_canvas"></div>
			<input type="submit" value="Refresh" onclick="refreshNetworkMap();"/>						
    	</div>

		<!--
    	############################################################################################
    	## Inventory Tab
    	##
    	############################################################################################
    	-->
	    <div id="tab2" class="tab_content">
 	  		<script type="text/javascript" language="javascript" src="js/inventory.js"></script>
			<div id="inventory_table" class="inventory_panel"></div>
			<input type="submit" value="Refresh" onclick="refreshInventory();"/>			
    	</div>		
		
		
	    <!--
	    ################################################################################################
	    ## Dashboard Tab
	    ##
	    ################################################################################################
	    -->
	    <div id="tab3" class="tab_content">
			<script type="text/javascript" language="javascript" src="js/dashboard.js"></script>
			<table>
				<tr>
					<td><div id="all_events_by_severity" class="dash_panel"></div></td>
					<td><div id="all_severities_by_type" class="dash_panel"></div></td>
				</tr>
				<tr>
					<td><div id="max_device_events_by_severity" class="dash_panel"></div></td>
					<td><div id="max_device_severities_by_type" class="dash_panel"></div></td>
				</tr>
				
			</table>
			<input type="submit" value="Refresh" onclick="refreshDashboard();"/>			
		</div>

		
	    <!--
	    ################################################################################################
	    ## Events Tab
	    ##
	    ################################################################################################
	    -->	
		<div id="tab4" class="tab_content">		
			<script type="text/javascript" language="javascript" src="js/events.js"></script>
			<div id="event_table" class="event_panel"></div>
			<input type="submit" value="Refresh" onclick="refreshEvents();"/>
			<input type="submit" value="Select All Rows" onclick="selectAllEventRows();"/>
			<input type="submit" value="Deselect All Rows" onclick="deSelectAllEventRows();"/>
			<input type="submit" value="Delete Selected Rows" onclick="deleteSelectedEventRows();"/>			
		</div>
		
		
	    <!--
	    ################################################################################################
	    ## Addons Tab
	    ##
	    ################################################################################################
	    -->		
		<div id="tab5" class="tab_content">
			<?php include( 'addons/listaddons.php' ); ?>
		</div>		
		
	</div>	

</div>
</td><td>

</td></tr>
</table>
<br><br><span class="footer">| <a href="#" style="color:#FFFFFF;" onclick="about();">About Wireless Adviser</a>
| <a href="../docs/user/UserManual.html" style="color:#FFFFFF;" target="_blank">User Manual</a>
| <a href="#" onclick="adminCheck();" style="color:#FFFFFF;">Administration Interface</a>
| <a href="http://cambiumnetworks.com" style="color:#FFFFFF;" target="_blank">Cambium Networks</a>
| <a href="logout.php" style="color:#FFFFFF;">Logout</a>
|</span>
</body>
</html>
