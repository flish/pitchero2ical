<?php
/*

	Pitchero Results to iCal Parser

	@author		Andy Flisher - af@xyroh.com
	@license	MIT Licences (Enclosed)

	Credits to http://simplehtmldom.sourceforge.net/ for 

*/	

require_once("./inc/simple_html_dom.php");
require_once("./inc/WebCal.class.php");

class Pitchero2Ical
{
	public $sResultsUrl;		//string
	
	public $ClubName = "";
	public $TeamName = "";
	public $FixtureCount = 0;
	
	private $oHTMLParser;		//instance of simple_html_dom class
	private $oWebCal;			//instance of WebCal class
	private $bIsLoaded = false;
	private $aFixtures = array();
	private $iFixtureLength=60;
	private $serverName="pitchero2ICal.local";
	
	public function __construct()
	{
		$this->oHTMLParser = new simple_html_dom();
	}
	
	public function Load($sUrl, $sUIDSuffix="")
	{
		/*
			Return true if parses and fins results else false
		*/
		
		$this->sResultsUrl = $sUrl;
		if($sUIDSuffix != "")
		{
			$sUIDSuffix="-".$sUIDSuffix;
		}
		
		if($this->sResultsUrl == "")	//Crying out for better URL Validation
		{
			return false;
		}else{
			$this->oHTMLParser->load_file($this->sResultsUrl);
			$this->bIsLoaded = true;
			
			return $this->Parse($sUIDSuffix);
		}
	}
	
	private function Parse($sUIDSuffix)
	{
		if(!$this->bIsLoaded == true)
		{
			return false;
		}else{
			//Get Club
			$tmpDOM = $this->oHTMLParser->find('h1[id=club-title]');
			$this->ClubName=$tmpDOM[0]->plaintext;

			//Get Team
			$tmpDOM = $this->oHTMLParser->find('h2.title-text a');
			$this->TeamName=$tmpDOM[0]->plaintext;
			
			$tmpDOM = $this->oHTMLParser->find('div.fixture-timeline-month');
			foreach($tmpDOM as $div)
			{
				$tmpMonthYear = $div->children(0)->children(0)->plaintext;
	
				foreach($div->find('table tr.fixture-timeline-row') as $tr)
				{
					$this->FixtureCount++;
					$tmpStatus = "CONFIRMED";
					$tmpFixtureDate = trim($tr->children(1)->plaintext);
					$tmpFixtureHomeAway = strtoupper(trim($tr->children(0)->plaintext));
					$tmpFixtureOpponent = trim($tr->children(2)->plaintext);
					$tmpFixtureType = trim($tr->children(3)->plaintext);
		
					$aTmpDate = explode(" ",$tmpMonthYear);
					$fixtureYear = $aTmpDate[1];
					$fixtureMonth = $aTmpDate[0];
					$aTmpDate = explode(",",$tmpFixtureDate);
					$fixtureDay = $aTmpDate[0];
					$fixtureTime = $aTmpDate[1];
					$fixtureString = $fixtureDay." ".$fixtureMonth." ".$fixtureYear." ".$fixtureTime;
					date_default_timezone_set('UTC');		//UNix Epoch always UTC, if don't potentialy wrong time
					$fixtureTimeStamp = strtotime($fixtureString);
		
					if($tmpFixtureHomeAway == "A")
					{
						$tmpTitle = $tmpFixtureOpponent." vs ".$this->TeamName." (".$tmpFixtureHomeAway.") ".$tmpFixtureType;
					}else{
						$tmpTitle = $this->TeamName." vs ".$tmpFixtureOpponent." (".$tmpFixtureHomeAway.") ".$tmpFixtureType;
					}
		
					$tmpDesc=$this->ClubName." ".$this->TeamName." Fixtures\n ".$tmpTitle;
					
					if(defined("ICALSERVERNAME"))
					{
						$this->serverName = ICALSERVERNAME;
					}
					
					if(defined("FIXTURELENGTH"))
					{
						$this->iFixtureLength= FIXTURELENGTH;
					}

					$aTmp=array(
						uid=>$this->FixtureCount.$sUIDSuffix."@".$this->serverName,
						status=>$tmpStatus,
						start=>date("Ymd\THis", $fixtureTimeStamp),
						end=>date("Ymd\THis", $fixtureTimeStamp + (60 * $this->iFixtureLength)),		//Duration is in minutes
						created=>date("Ymd\THis", time()),
						modified=>date("Ymd\THis", time()),
						summary=>$tmpTitle,
						desc=>$tmpDesc
					);
					array_push($this->aFixtures, $aTmp);	
				} //end foreach through individual fixture rows
				
			} //end foreach through ficture months
			
			if($this->FixtureCount > 0)
			{
				return true;
			}else{
				return false;
			}
		} // end check if URl has been loaded
		
	} //Parse
	
	public function GetIcal()
	{
		/*
			Create an instance of the WebCal generator, and return an iCalendat formatted string of fixtures
		*/
		
		if($this->FixtureCount > 0)
		{
			$this->oWebCal=new WebCal();
			return $this->oWebCal->Build($this->aFixtures);
		}else{
			return false;
		}
		
	} //GetIcal
	
	public function Dump()
	{
		/*
			Raw HTML Dump of the Parsed URL
		*/
		if($this->bIsLoaded == true)
		{
			return $this->oHTMLParser;
		}
	} //Dump
	
	
	public function __destruct()
	{
		unset($tmpDOM);
		unset($this->oHTMLParser);
	}
	
}
	
?>