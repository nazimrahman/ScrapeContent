<?php
class ScrapeContent
{
	// member data
	//var $sSourceAddress;		// url of source
	var $aLinks;				// array to store links
	var $sFilterPattern;		// filter pattern
	var $aFilteredResults;		// filtered results

	// constructor
	function __construct() {
		// initialize member data
		$this->aLinks = array();
		$this->sFilterPattern = '';
		$this->aFilteredResults = array();
	}

	public function getLinks($txt,$fp = '') {
		if (strlen($fp) > 1) {
			$this->sFilterPattern = $fp;
			$this->scrapeLinks($txt);
			$this->filterLinks('links');
		} else {
			$this->scrapeLinks();
		}
		return $this->aLinks;
	}

	public function resetLinksArray() {
		$this->aLinks = array();
	}

	private function scrapeLinks($txt) {
		// get html content
		$sHTML = file_get_contents($txt);

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

	public function findWithXpath($xpath, $aaPattern) {
		// pre: get xpath instance, list of patterns, and content
		// post: return an associated array of matching text

		// run all patterns through xquery
		foreach ($aaPattern as $aPattern) {
			$pattern = $aPattern[0];
			$key = $aPattern[1];

			// run the query
			$nodelist = $xpath->query($pattern);
			$node_counts = $nodelist->length;

			// if there are resultss, store them in array
			if ($node_counts) {
				foreach ($nodelist as $element) {
					// if you want breaks in different lines
					// source content needs the following modification
					// $source = preg_replace("/\<br\/\>/",'-br-',$source);
					// $source = preg_replace("/\<br\>/",'-br-',$source);
					if (preg_match("/-br-/", $element->nodeValue)) {
						$tmparray = preg_split("/-br-/",$element->nodeValue);
						if (count($tmparray) > 0) {
							$aaData[$key][] = $tmparray;	
						} 
					} else {
						$aaData[$key][] = $element->nodeValue;	
					}
				}
			}
		}
		return $aaData; 		
	}
}
?>