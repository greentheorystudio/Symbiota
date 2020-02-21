<?php
include_once('TaxonomyUtilities.php');

class EOLUtilities {

	private $targetLanguages = array('en');
	private $errorStr;

	public function __construct() {
	}

	public function __destruct(){
	}

	public function pingEOL(): bool
	{
		$pingUrl = 'http://eol.org/api/ping/1.0.json';
		if($fh = fopen($pingUrl, 'rb')){
			$content = '';
			while($line = fread($fh, 1024)){
				$content .= trim($line);
			}
			fclose($fh);
			$pingArr = json_decode($content, true);
			if(($resObj = $pingArr->response) && $resObj->message && $resObj->message === 'Success') {
				return true;
			}
		}
		else{
			$this->errorStr = 'ERROR opening EOL ping url: '.$pingUrl;
		}
		return false;
	}

	public function searchEOL($sciName){
		$retArr = array();
		if($sciName){
			$retArr['searchTaxon'] = $sciName;
			$url = 'http://eol.org/api/search/1.0.json?q='.str_replace(' ', '%20',$sciName).'&page=1';
			if($fh = fopen($url, 'rb')){
				$content = '';
				while($line = fread($fh, 1024)){
					$content .= trim($line);
				}
				fclose($fh);
				$searchObj = json_decode($content, true);
				if($searchObj->totalResults){
					$resultObj = $searchObj->results;
					foreach($resultObj as $index => $result){
						$retArr['id'] = $result->id;
						$retArr['title'] = $result->title;
						$retArr['link'] = $result->link;
						if(stripos($result->title,$sciName) === 0){
							break;
						}
					}
				}
				else{
					$this->errorStr = 'No EOL results returned';
					return false;
				}
			}
			else{
				$this->errorStr = 'ERROR opening EOL search url: '.$url;
			}
		}
		return $retArr;
	}

	public function getPage($id, $includeSynonyms = true, $includeCommonNames = false, $contentLimit = 1): array
	{
		$taxonArr = array();
		$url = 'http://eol.org/api/pages/1.0/'.$id.'.json?images=0&videos=0&sounds=0&maps=0&text=0&iucn=false&subjects=overview&licenses=all&details=true';
		$url .= '&common_names='.($includeCommonNames?'true':'false').'&synonyms='.($includeSynonyms?'true':'false').'&references=false&vetted=0&cache_ttl=';
		if($fh = fopen($url, 'rb')){
			$content = '';
			while($line = fread($fh, 1024)){
				$content .= trim($line);
			}
			fclose($fh);
			$eolObj = json_decode($content, true);
			$taxonArr = (new TaxonomyUtilities)->parseScientificName($eolObj->scientificName);
			if($eolObj->scientificName) {
				$taxonArr['scientificName'] = $eolObj->scientificName;
			}
			if(isset($eolObj->taxonConcepts)){
				$cnt = 1;
				foreach($eolObj->taxonConcepts as $tcObj){
					$taxonArr['taxonConcepts'][$tcObj->identifier] = $tcObj->nameAccordingTo;
					if(!isset($taxonArr['taxonRank']) && isset($tcObj->taxonRank)){
						$taxonArr['taxonRank'] = $tcObj->taxonRank;
					}
					$cnt++;
					if($cnt > $contentLimit) {
						break;
					}
				}
			}
			if($includeSynonyms && isset($eolObj->synonyms)){
				$cnt = 0;
				$uniqueList = array();
				foreach($eolObj->synonyms as $synObj){
					if(!in_array($synObj->synonym, $uniqueList, true)){
						$uniqueList[] = $synObj->synonym;
						$taxonArr['syns'][$cnt]['scientificName'] = $synObj->synonym;
						if(isset($synObj->relationship)) {
							$taxonArr['syns'][$cnt]['synreason'] = $synObj->relationship;
						}
						$cnt++;
					}
				}
			}
			if($includeCommonNames && isset($eolObj->vernacularNames)){
				foreach($eolObj->vernacularNames as $vernObj){
					if(in_array($vernObj->language, $this->targetLanguages, true)){
						$taxonArr['verns'][] = array('language' => $vernObj->language, 'vernacularName' => $vernObj->vernacularName);
					}
				}
			}
		}
		else{
			$this->errorStr = 'ERROR opening EOL page url: '.$url;
		}
		return $taxonArr;
	}

	public function getHierarchyEntries($id, $includeSynonyms = true, $includeCommonNames = true){
		$taxonArr = array();
		if($id){
			$url = 'http://eol.org/api/hierarchy_entries/1.0/'.$id.'.json?common_names='.($includeCommonNames?'true':'false').'&synonyms='.($includeSynonyms?'true':'false');
			if($fh = fopen($url, 'rb')){
				$content = '';
				while($line = fread($fh, 1024)){
					$content .= trim($line);
				}
				fclose($fh);

				$eolObj = json_decode($content, true);
				if($eolObj->scientificName){
					$taxonArr = (new TaxonomyUtilities)->parseScientificName($eolObj->scientificName);
					$taxonArr['scientificName'] = $eolObj->scientificName;
					$taxonArr['taxonRank'] = $eolObj->taxonRank;
					if(isset($eolObj->nameAccordingTo)) {
						$taxonArr['source'] = $eolObj->nameAccordingTo[0];
					}
					if(isset($eolObj->source)) {
						$taxonArr['sourceURL'] = $eolObj->source;
					}

					if($includeSynonyms){
						$synonyms = $eolObj->synonyms;
						foreach($synonyms as $synObj){
							$taxonArr['syns'][] = array('scientificName' => $synObj->scientificName,'synreason' => $synObj->taxonomicStatus);
						}
					}
					if($includeCommonNames){
						$vernacularNames = $eolObj->vernacularNames;
						foreach($vernacularNames as $vernObj){
							if(in_array($vernObj->language, $this->targetLanguages, true)){
								$taxonArr['verns'][] = array('language' => $vernObj->language, 'vernacularName' => $vernObj->vernacularName);
							}
						}
					}
					if($eolObj->ancestors && $eolObj->parentNameUsageID){
						$ancArr = array_reverse((array)$eolObj->ancestors);
						$parArr = $this->getParentArray($ancArr,$eolObj->parentNameUsageID);
						if($parArr) {
							$taxonArr['parent'] = $parArr;
						}
					}
					$taxonArr['id'] = $id;
				}
			}
			else{
				$this->errorStr = 'ERROR opening EOL hierarchy url: '.$url;
			}
		}
		else{
			$this->errorStr = 'Input ID is null';
			return false;
		}
		return $taxonArr;
	}

	private function getParentArray($ancestors, $parentId){
		$retArr = array();
		if(!$ancestors || !$parentId) {
			return false;
		}
		foreach($ancestors as $k => $ancObj){
			if($ancObj->taxonID === $parentId){
				$retArr['id'] = $ancObj->taxonID;
				$retArr['sciname'] = $ancObj->scientificName;
				$retArr['taxonConceptID'] = $ancObj->taxonConceptID;
				if(isset($ancObj->taxonRank)) {
					$retArr['taxonRank'] = $ancObj->taxonRank;
				}
				if(isset($ancObj->source)) {
					$retArr['sourceURL'] = $ancObj->source;
				}
				$parentId = $ancObj->parentNameUsageID;
				unset($ancestors[$k]);
				break;
			}
		}
		if($ancestors && $parentId){
			$parArr = $this->getParentArray($ancestors,$parentId);
			if($parArr) {
				$retArr['parent'] = $parArr;
			}
		}
		return $retArr;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

}
