<?php
/*

	Usage Example for pitchero2iCal

	@author		Andy Flisher - af@xyroh.com
	@license	MIT Licences (Enclosed)

*/	
	

require_once ('./Pitchero2Ical.class.php');

$sUrl='http://www.pitchero.com/clubs/darlingtonmowdenparkrfc/m/fixtures-results-32099.html';

/* Optional */
//define('ICALSERVERNAME', "xyroh.com"); 		//default is pitchero2ICal.local
//define('ICALTIMEZONE', "US/Pacific");		//default is Europe/London
//define('FIXTURELENGTH', 120); 				//in minutes, default is 60






$oPitchero = new Pitchero2Ical();
//echo $oPitchero->Load($sUrl);
if(!$oPitchero->Load($sUrl))
{
	//Failed to find a valid results Url
	echo "Are You Sure that's a valid Pitchero Fixtures Page?";
}else{
	
	/*
		Commented variables below highlight other public variables availble
	*/
	
	//echo $oPitchero->TeamName."\n";
	//echo $oPitchero->ClubName."\n";
	//echo $oPitchero->FixtureCount." Fixtures\n";
	
	/* 
		GetIcal returns a string formatted in iCalender format
	
		if you just want an .ics file then do something like php example.php >> output.ics at cli
	
		if you want to use this on the web as an iCal / Webcal subscription endpoint then comment out the headers below
	
	*/
	
	$sCal=$oPitchero->GetIcal();
	//header("Content-type:text/calendar");
	//header('Content-Disposition: attachment; filename=webcal.ics');
	//Header('Content-Length: '.strlen($sCal));
	//Header('Connection: close');
	echo $sCal;
	
}



unset($oPitchero);
?>