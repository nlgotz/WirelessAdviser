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

if ( !isset( $_SESSION['account_type'] ) || $_SESSION['account_type'] != "admin" )
{
	header( "Location: main.php" );
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

<script type="text/javascript" language="javascript" src="libs/jquery-1.6.3.js"></script>
<script type="text/javascript" language="javascript" src="libs/jquery.tablesorter-2.0.3.js"></script> 
<script type="text/javascript" language="javascript" src="libs/jquery.tablesorter-filter.js"></script> 
<script type="text/javascript" language="javascript" src="libs/jquery_ready_handler.js"></script>
<script type="text/javascript" language="javascript" src="libs/jquery.form.js"></script> 
<script type="text/javascript" language="javascript" src="libs/jquery.colorbox-min.js"></script>

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
</script>

<script type="text/javascript" language="javascript">
/**
 * 
 */
function createDatabase()
{
	var jsonData = $.ajax({
		url: "model/createdatabase.php",
		dataType:"html",
		async: false,
		cache: false
	}).responseText;
	alert( jsonData );
}
	

/**
 * 
 */
function deleteDatabase()
{
	var response = confirm( "Deleting database will result in loss of all data - are you sure?" );
	if ( response == true )
	{
		var jsonData = $.ajax({
			url: "model/deletedatabase.php",
			dataType:"html",
			async: false,
			cache: false
		}).responseText;
		alert( jsonData );
	}
}
	
	
/**
 * 
 */
function testDatabase()
{
	var jsonData = $.ajax({
		url: "model/testdatabase.php",
		dataType:"json",
		async: false,
		cache: false
	}).responseText;
	alert( jsonData );
}

	
/**
 * 
 */
function reinitializeDatabase()
{
	var response = confirm( "Reinitializing the database will result in loss of all data - are you sure?" );
	if ( response == true )
	{
		// Reinitialize the database.
		var response = $.ajax({
			url: "model/reinitializedatabase.php",
			dataType:"json",
			async: false,
			cache: false
		}).responseText;
		
		// Call each of the loaders individually.
		response += "\n\n" + $.ajax({
			url: "model/loadcsv.php?type=devices&reload=true",
			async: false,
			cache: false
		}).responseText;
		
		response += "\n\n" + $.ajax({
			url: "model/loadcsv.php?type=links&reload=true",
			async: false,
			cache: false
		}).responseText;
		
		response += "\n\n" + $.ajax({
			url: "model/loadcsv.php?type=users&reload=true",
			async: false,
			cache: false
		}).responseText;
		
		response += "\n\n" + $.ajax({
			url: "model/loadcsv.php?type=policies&reload=true",
			async: false,
			cache: false
		}).responseText;
				
		// Let the user know what happened!
		alert( response );
	}
}


/**
 * Register upload forms.
 */
$(document).ready( function() 
{	
	$('#devicesUploadForm').ajaxForm( {
		success: function ( responseText, statusText, xhr, form ) {
			alert( responseText );
		},
		error: function( responseText, statusText, xhr, form ) {
			alert( "Error uploading file to server." + responseText );
		}
	});
	$('#linksUploadForm').ajaxForm( {
		success: function ( responseText, statusText, xhr, form ) {
			alert( responseText );
		},
		error: function( responseText, statusText, xhr, form ) {
			alert( "Error uploading file to server." + responseText );
		}
	});
	$('#usersUploadForm').ajaxForm( {
		success: function ( responseText, statusText, xhr, form ) {
			alert( responseText );
		},
		error: function( responseText, statusText, xhr, form ) {
			alert( "Error uploading file to server." + responseText );
		}
	});
	$('#policiesUploadForm').ajaxForm( {
		success: function ( responseText, statusText, xhr, form ) {
			alert( responseText );
		},
		error: function( responseText, statusText, xhr, form ) {
			alert( "Error uploading file to server." + responseText );
		}
	});	
});

</script>
	
</head>

<body>
<table>
<tr><td>
<div class="container">

    <ul class="tabs">
    	<li><a href="#tab1" id="dbMgmt">Database Management</a></li>
    	<li><a href="#tab2" id="dMgmt">Data Management</a></li>		
    </ul>

   	<div class="tab_container">

		<!--
    	############################################################################################
    	## Database Management
    	##
    	############################################################################################
    	-->
	    <div id="tab1" class="tab_content">
			<table id="dbMgmtTable" class="tablesorter">
				<thead>
					<tr>
						<th>Operation</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input style="width: 200px;" type="submit" value="Create Database" onclick="createDatabase();"/></td>
						<td>Creates a new database based on the parameters in <application-root>/conf/database.xml.  After the new database is created remember to reinitialize the database.</td>
					</tr>
					<tr>
						<td><input style="width: 200px;" type="submit" value="Delete Database" onclick="deleteDatabase();"/></td>
						<td>Deletes the Wireless Adviser database.  <font color="red"><b>Warning:</b> all data will be lost.</font></td>
					</tr>					
					<tr>
						<td><input style="width: 200px;" type="submit" value="Test Database Connection" onclick="testDatabase();"/></td>
						<td>Verifies that credentials used by Wireless Adviser work with the database server and that the database exists.  If the credential check fails, update the database configuration in <application-root>/conf/database.xml.</td>
					</tr>
					<tr>
						<td><input style="width: 200px;" type="submit" value="Reinitialize Database" onclick="reinitializeDatabase();"/></td>
						<td>Reinitializes the database and reloads the csv files containing device, link, policy, and user data.  <font color="red"><b>Warning:</b> all data will be lost.</font></td>
					</tr>
					<tr>
						<td><input style="width: 200px;" type="submit" value="Launch PHP Postgres Admin" onclick='window.open( "phppgadmin" ); return false;'/></td>
						<td>Launches the PHP Postgres Admin interface for direct access to the database.</td>
					</tr>
				</tbody>
			</table>
    	</div>

		<!--
    	############################################################################################
    	## Data Management
    	##
    	############################################################################################
    	-->
	    <div id="tab2" class="tab_content">
			<table id="dMgmtTable" class="tablesorter">
				<thead>
					<tr>
						<th>Operation</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>		
					<tr>
						<td width="25%">
							<form id="devicesUploadForm" action="model/loadcsv.php?type=devices" method="post" enctype="multipart/form-data">
								<input size="25%" type="file" name="csvFile"/>
								<hr class="dm">
								<input style="width: 100%;" type="submit" value="Load/Reload Devices"/>
							</form>
						</td>
						<td>Will copy the selected file to <application-root>/wabe/conf/devices.csv and load it into the devices database table.  If entries are updated, inserted, or removed from devices.csv, the devices table will be updated accordingly.  Data that is "stranded" due to removal will be automatically removed by the cleanup policies.</td>
					</tr>
					<tr>
						<td>
							<form id="linksUploadForm" action="model/loadcsv.php?type=links" method="post" enctype="multipart/form-data">
								<input size="25%" type="file" name="csvFile"/>
								<hr class="dm">
								<input style="width: 100%;" type="submit" value="Load/Reload Links"/>
							</form>
						</td>
						<td>Will copy the selected file to <application-root>/wabe/conf/links.csv and load it into the links database table.  If entries are updated, inserted, or removed from links.csv, the links table will be updated accordingly.</td>
					</tr>
					<tr>
						<td>
							<form id="usersUploadForm" action="model/loadcsv.php?type=users" method="post" enctype="multipart/form-data">
								<input size="25%" type="file" name="csvFile"/>
								<hr class="dm">
								<input style="width: 100%;" type="submit" value="Load/Reload Users"/>
							</form>
						</td>						
						<td>Will copy the selected file to <application-root>/wabe/conf/users.csv and load it into the users database table.  If entries are updated, inserted, or removed from users.csv, the users table will be updated accordingly.</td>
					</tr>
					<tr>
						<td>
							<form id="policiesUploadForm" action="model/loadcsv.php?type=policies" method="post" enctype="multipart/form-data">
								<input size="25%" type="file" name="csvFile"/>
								<hr class="dm">
								<input style="width: 100%;" type="submit" value="Load/Reload Policies"/>
							</form>
						</td>						<td>Will copy the selected file to <application-root>/wabe/conf/policies.csv and load it into the policies database table.  If entries are updated, inserted, or removed from policies.csv, the policies table will be updated accordingly.</td>					
					</tr>
				</tbody>
			</table>					
    	</div>
	</div>
</div>
</td><td>
<div class="logo">
	<img src="images/cambium-logo-main.png" height="650px"/>
</div>
</td></tr>
</table>
<br><br><span class="footer">| <a href="#" style="color:#FFFFFF;" onclick="about();">About Wireless Adviser</a>
| <a href="../docs/user/UserManual.html" style="color:#FFFFFF;" target="_blank">User Manual</a>
| <a href="main.php" style="color:#FFFFFF;">Main Interface</a>
| <a href="http://cambiumnetworks.com" style="color:#FFFFFF;" target="_blank">Cambium Networks</a>
| <a href="logout.php" style="color:#FFFFFF;">Logout</a>
|</span>

</body>

</html>
