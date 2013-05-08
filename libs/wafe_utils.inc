<?php

$version = "1.0.7";

function getColorForSeverityId( $severity_id )
{	
	switch ( $severity_id )
	{
		case 0: return "#33FF33";
		case 1: return "#FFFFFF";				
		case 2: return "#00FFFF";		
		case 3: return "#FFF500";
		case 4: return "#FF7300";
		case 5: return "#FF3333";
		default: return "#AAAAAA";
	}
}

function getColorForSeverityName( $severity_name )
{	
	switch ( strtolower( $severity_name ) )
	{
		case "clear": return "#33FF33";
		case "info": return "#FFFFFF";				
		case "warning": return "#00FFFF";		
		case "minor": return "#FFF500";
		case "major": return "#FF7300";
		case "critical": return "#FF3333";
		default: return "#AAAAAA";
	}
}
	
function getNameForSeverityId( $severity_id )
{
	switch ( $severity_id )
	{
		case 0: return "Clear";
		case 1: return "Info";		
		case 2: return "Warning";	
		case 3: return "Minor";
		case 4: return "Major";
		case 5: return "Critical";
		default: return "Unknown";
	}
}
?>
