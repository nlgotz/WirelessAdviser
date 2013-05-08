<?php
	session_start();					
	ob_start();
	include 'libs/wafe_utils.inc';
	//removing login
	//header( "Location: main.php" );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="_LANGUAGE" xml:lang="_LANGUAGE">
<head>
	<title>Wireless Adviser</title>
	<link rel="shortcut icon" href="images/cambium-icon.png"/>
	<link rel="stylesheet" type="text/css" media="screen" href="cambium.css"/>
</head>

<body>
<div class="login">
	<a href="http://cambiumnetworks.com" target="_blank"><img src="images/cambium-logo-about.png" width="243" height="87"></a>
	<br><br><span class="about_large">Wireless Adviser</span>
	<br><i class="about_small">(<?php echo $version ?>)</i>
	<br><br><hr/>

	<form method="post" action="index.php">
		<table width="100%">
			<tr>
				<td align="left"><b>Login</b></td>
				<td align="right">
					<?php
						echo '<input type="text" name="login" value="';
							if ( array_key_exists('login', $_POST) == 1 ) 
							{
								$login = $_POST["login"];
								echo $login;
							}
							else
							{
								echo "";
							}	
						echo '"/><br>';
					?>	
				</td>
			</tr>
			<tr>
				<td align="left"><b>Password</b></td>
				<td align="right">
					<?php
						echo '<input type="password" name="password" value="';
						if ( array_key_exists('password', $_POST) == 1 ) 
						{
							$password = $_POST["password"];
							echo $password;
						}
						else
						{
							echo "";
						}	
						echo '"/><br>';
					?>	
				</td>
			</tr>					
			<tr>
				<td colspan="2"><br><input type="submit" value="Submit"/></td>
			</tr>
		</table>
		<input type="hidden" name="_passwordSubmitted" value="1"/> 		
	</form>
	
	<i class="about_small">Comments? Suggestions?  Send email to <a href="mailto:wirelessadviser@cambiumnetworks.com">Wireless Adviser Comments</a></i>

	<?php
		if ( array_key_exists( '_passwordSubmitted', $_POST ) == true ) 
		{
			// Recover data.
			$login = $_POST["login"];
			$password = $_POST["password"];					
			
			// Check credentials.
			$ch = curl_init();
			$url = $_SERVER['HTTP_HOST'] . "/model/login.php?user_id=" . $login . "&password=" . $password;
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			
			if ( strpos( $result, 'does not exist' ) === false )
			{
				if ( strpos( $result, 'ERROR' ) === false )
				{
					$_SESSION['valid_user'] = "true";
					$_SESSION['user_id'] = $login;
					$_SESSION['account_type'] = $result;
					header( "Location: main.php" );
					exit();
				}
				else
				{
					echo "<p class='about_small'><font color='#FF0000'><i>" . $result . "</i></font></p>";
				}
			}
			else
			{
				$_SESSION['valid_user'] = "true";
				$_SESSION['user_id'] = $login;
				$_SESSION['account_type'] = "admin";
				header( "Location: admin.php" );
				exit();
			}
				
		}
	?>

</div>

</body>

</html>
<?php
	ob_flush();
?>