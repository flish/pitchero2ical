<?php
/*

	Turns an Array of Events into an iCalendar formatted string

	@author		Andy Flisher - af@xyroh.com
	@license	MIT Licences (Enclosed)

*/	

 
class WebCal
{
	
	private $iNow;
	private $sHeader="";
	private $sFooter="";
	private $sTimeZone="Europe/London";
	
	public $sCal="";
		
	public function __construct()
	{
		$this->iNow=time();
		
		if(defined("ICALTIMEZONE"))
		{
			$this->sTimeZone = ICALTIMEZONE;
		}
		
		$this->sHeader="BEGIN:VCALENDAR\n";
		$this->sHeader.="X-PUBLISHED-TTL:PT60M\n";
		$this->sHeader.="PRODID:-//Xyroh v1.0//NONSGML iCalendar Endpoint//EN\n";
		$this->sHeader.="VERSION:2.0\n";
		$this->sHeader.="CALSCALE:GREGORIAN\n";
		
		//Support for Europ/London Timezone
		$this->sHeader.="BEGIN:VTIMEZONE\n";
		$this->sHeader.="TZID:".$this->sTimeZone."\n";
		$this->sHeader.="BEGIN:DAYLIGHT\n";
		$this->sHeader.="TZOFFSETFROM:+0000\n";
		$this->sHeader.="TZOFFSETTO:+0100\n";
		$this->sHeader.="DTSTART:19810329T010000\n";
		$this->sHeader.="RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU\n";
		$this->sHeader.="TZNAME:BST\n";
		$this->sHeader.="END:DAYLIGHT\n";
		$this->sHeader.="BEGIN:STANDARD\n";
		$this->sHeader.="TZOFFSETFROM:+0100\n";
		$this->sHeader.="TZOFFSETTO:+0000\n";
		$this->sHeader.="DTSTART:19961027T020000\n";
		$this->sHeader.="RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU\n";
		$this->sHeader.="TZNAME:GMT\n";
		$this->sHeader.="END:STANDARD\n";
		$this->sHeader.="END:VTIMEZONE\n";
		//End Timezone

		$this->sHeader.="METHOD:PUBLISH\n";
		
		
		$this->sFooter="END:VCALENDAR\n";
	}
	
	public function __destruct()
	{

	}
	
	public function build($aEvents)
	{
		$this->sCal=$this->sHeader;
		$dTimeStamp=date("Ymd\THis");
		
		for($i=0;$i<count($aEvents);$i++)
		{
			$this->sCal.="BEGIN:VEVENT\n";
			$this->sCal.="DTSTART;TZID=".$this->sTimeZone.":".$aEvents[$i][start]."\n";
			$this->sCal.="DTEND;TZID=".$this->sTimeZone.":".$aEvents[$i][end]."\n";
			$this->sCal.="DTSTAMP;TZID=".$this->sTimeZone.":".$dTimeStamp."\n";
			$this->sCal.="UID:".$aEvents[$i][uid]."\n";
			$this->sCal.="CREATED;TZID=".$this->sTimeZone.":".$aEvents[$i][created]."\n";
			$this->sCal.="DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".quoted_printable_encode($aEvents[$i][desc])."\n";
			$this->sCal.="LAST-MODIFIED;TZID=".$this->sTimeZone.":".$aEvents[$i][modified]."\n";
			$this->sCal.="SEQUENCE:0\n";
			$this->sCal.="STATUS:".$aEvents[$i][status]."\n";
			$this->sCal.="SUMMARY:".$aEvents[$i][summary]."\n";
			$this->sCal.="TRANSP:OPAQUE\n";
			$this->sCal.="END:VEVENT\n";
		}
		
		$this->sCal.=$this->sFooter;
		
		return $this->sCal;
	}
		
		
} //end WebCal class
	