//<![CDATA[

// Define color scheme for severities.
var waGreen = '#33FF33';
var waYellow = '#FFF500';
var waOrange = '#FF7300';
var waRed = '#FF3333';
var waBlue = '#3333FF';
var waCyan = '#00FFFF';
var waGrey = '#AAAAAA';
var waBlack = '#000000';
var waWhite = '#FFFFFF';
var waGraphTitleColor = '#4261B1';

// Define infowindow size.
var infoWindowHeight = 600;  // Needs to match inventory settings.  Stylesheet will also need to be tweaked if this is changed.
var infoWindowWidth = 600;   // Needs to match inventory settings.  Stylesheet will also need to be tweaked if this is changed.

// Define the product version.
var version = "1.0.7";	


/**
 * Return the severity name for a given severity id.	
 */
function getNameForSeverityId( severity_id )
{
	switch ( parseInt( severity_id ) )
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


/**
 * Return the hex color code for a given severity id.
 */
function getColorForSeverityId( severity_id )
{	
	switch ( parseFloat( severity_id ) )
	{
		case 0: return waGreen;
		case 1: return waWhite;				
		case 2: return waCyan;		
		case 3: return waYellow;
		case 4: return waOrange;
		case 5: return waRed;
		default: return waGrey;
	}
}


/**
 * Given the epoch time, convert it to a locale based time string.
 */
function convertEpochToReadableTime( time_string )
{
	var date = new Date( parseFloat(time_string) * 1000 ); 
	if ( date != null ) 
	{	
		date = date.toDateString() + " " + date.toLocaleTimeString(); 
	}
	else 
	{ 
		date = time_string; 
	}
	
	return date;
}


/**
 * Convert a string to it's hex equivalent - to get around the fact the IE 8 is dumb.
 */
function encodeToHex( str )
{
    var r = "";
    var e = str.length;
    var c = 0;
    var h;
    while( c < e )
	{
        h = str.charCodeAt(c++).toString( 16 );
        while( h.length < 3 )
		{
			h = "0" + h;
		}
        r += h;
    }
    return r;
}


/**
 * Launch the device sheet for the specified device id an page..
 */
function launchDeviceSheet( device_id, device_page )
{
	var devicePage = "device/" + device_page + "?device_id=" + device_id;
	var windowOptions = "location=0,status=0,scrollbars=1,width=" + infoWindowWidth + ",height=" + infoWindowHeight;
	var deviceWindow = (window.open( devicePage, encodeToHex( device_id ), windowOptions ));
	deviceWindow.focus();	
}

//]]>