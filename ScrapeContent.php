<?php
class ScrapeContent
{
	// member data
	var $sSourceAddress;		// url of source
	var $aLinks;				// array to store links
	var $sFilterPattern;		// filter pattern
	var $aFilteredResults;		// filtered results

	// constructor
	function __construct() {
		// initialize member data
		$this->sSourceAddress = "/tmp";
		$this->aLinks = array();
		$this->sFilterPattern = '';
		$this->aFilteredResults = array();
	}

	public function getSourceAddress() {
		return $this->sSourceAddress;
	}

	public function setSourceAddress($address) {
		$this->sSourceAddress = $address;
	}

	public function getLinks($fp = '') {
		if (strlen($fp) > 1) {
			$this->sFilterPattern = $fp;
			$this->scrapeLinks();
			$this->filterLinks('links');
		} else {
			$this->scrapeLinks();
		}
		return $this->aLinks;
	}

	private function scrapeLinks() {
		// get html content
		$sHTML = file_get_contents($this->sSourceAddress);

		//Create a new DOM document
		$dom = new DOMDocument;

		// load html into DOM. @ is to suppress parsing errors from malformed HTML
		@$dom->loadHTML($sHTML);

		// get anchor tags
		$aA = $dom->getElementsByTagName('a');

		// bundle links into an array
		foreach ($aA as $a){
			$nv = $a->nodeValue;
			$lk = $a->getAttribute('href');
			$this->aLinks[$lk] = $nv; 
			// I chose the format link => name because url is more likely to be unique
		}

		// release memory
		unset($dom);
		unset($aA);
	}

	private function filterLinks($type) {
		// initialize variables
		$aLinks = array();
		
		// get appropriate source data
		if ($type = 'links') {
			$aSource = $this->aLinks;
		}

		// apply pattern to filter data
		foreach ($aSource as $link => $name) {
			if (preg_match($this->sFilterPattern, $link)) {
				$aLinks[$link] = $name;
			}
		}

		$this->aLinks = $aLinks;
		
		// release memory
		unset($aLinks);
	}
}
?>