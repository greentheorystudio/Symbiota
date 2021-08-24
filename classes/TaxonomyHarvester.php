<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/EOLUtilities.php');
include_once(__DIR__ . '/Utilities.php');
include_once(__DIR__ . '/Sanitizer.php');

class TaxonomyHarvester extends Manager{

    private $taxonomicResource = '';
    private $tidAccepted = 0;
    private $taxAuthId = 1;
    private $defaultAuthor;
    private $defaultFamily;
    private $kingdomName;
    private $kingdomTid;
    private $kingdomId;
    private $fullyResolved;

	public function __construct() {
		parent::__construct();
	}

	public function processSciname($term): bool
    {
		$term = trim($term);
		if($term){
			$this->fullyResolved = true;
			if(!$this->taxonomicResource){
				echo '<li style="margin-left:15px;">External taxonomic data source not selected</li>';
				return false;
			}
			$this->parseCleanCheck($term);
			$this->addSciname($term, $this->taxonomicResource);
		}
		return true;
	}

	private function addSciname($term, $resourceKey): void
    {
        if($term) {
            if($resourceKey === 'col'){
                echo '<li style="margin-left:15px;">Checking <b>Catalog of Life</b>...</li>';
                $this->addColTaxon($term);
            }
            elseif($resourceKey === 'worms'){
                echo '<li style="margin-left:15px;">Checking <b>WoRMS</b>...</li>';
                $this->addWormsTaxon($term);
            }
            elseif($resourceKey === 'tropicos'){
                echo '<li style="margin-left:15px;">Checking <b>TROPICOS</b>...</li>';
                $this->addTropicosTaxon($term);
            }
            elseif($resourceKey === 'eol'){
                echo '<li style="margin-left:15px;">Checking <b>EOL</b>...</li>';
                $this->addEolTaxon($term);
            }
		}
	}

	private function parseCleanCheck($term): void
    {
		$taxonArr = $this->buildTaxonArr(array('sciname' => $term));
		$tid = $this->getTid($taxonArr);
		if(!$tid && isset($taxonArr['rankid']) && (int)$taxonArr['rankid'] > 220 && $taxonArr['unitname2'] === $taxonArr['unitname3']) {
			$parentArr = $this->getParentArr($taxonArr);
			if($parentArr){
				$parentTid = $this->getTid($parentArr);
				if($parentTid){
					$taxonArr['parent']['tid'] = $parentTid;
					$parentTidAccepted = (new TaxonomyUtilities)->getTidAccepted($parentTid,$this->taxAuthId);
					if($parentTidAccepted === $parentTid){
						$tid = $this->loadNewTaxon($taxonArr);
					}
					else{
						$tid = $this->loadNewTaxon($taxonArr,$parentTidAccepted);
					}
				}
			}
		}
	}

    private function addColTaxon($sciName,$singleNode = null): int
    {
        $tid = 0;
        if($sciName && $sciName !== 'Biota'){
            $adjustedName = str_ireplace(array(' subsp. ',' subsp ',' ssp. ',' ssp ',' var. ',' var ',' f. ',' fo. '), ' ', $sciName);
            $adjustedName = str_ireplace(array('?'), ' ', $adjustedName);
            $url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name='.str_replace(' ','%20',$adjustedName);
            //echo $url.'<br/>';
            $retArr = (new Utilities)->getContentString($url);
            $content = $retArr['str'];
            $resultArr = json_decode($content,true);
            $numResults = (int)$resultArr['number_of_results_returned'];
            if($numResults > 0){
                foreach($resultArr['result'] as $k => $tArr){
                    $loadTaxon = true;
                    $taxonKingdom = $this->getColParent($tArr, 'Kingdom');
                    if($this->kingdomName && $this->kingdomName !== $taxonKingdom){
                        $colPrefix = 'http://www.catalogueoflife.org/col/browse/tree/id/';
                        if(strpos($adjustedName,' ')) {
                            $colPrefix = 'http://www.catalogueoflife.org/col/details/species/id/';
                        }
                        $msg = '<a href="'.$colPrefix.$resultArr['results'][$k]['id'].'" target="_blank">';
                        $msg .= $sciName.'</a> skipped due to not matching targeted kingdom: '.$this->kingdomName.' (!= '.$taxonKingdom.')';
                        echo '<li style="margin-left:30px;">'.$msg.'</li>';
                        $loadTaxon = false;
                    }
                    if(isset($tArr['author']) && $tArr['author'] && stripos($tArr['author'], 'nom. illeg.') !== false) {
                        $loadTaxon = false;
                    }
                    if($loadTaxon){
                        $tid = $this->addColTaxonByResult($resultArr['result'][$k],$singleNode);
                    }
                }
            }
            else{
                echo '<li style="margin-left:30px;">Taxon not found</li>';
            }
        }
        return $tid;
    }

    private function getColTaxonArrById($id): array
    {
        $retArr = array();
        if($id){
            $url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&id='.$id;
            //echo $url.'<br/>';
            $retArr = (new Utilities)->getContentString($url);
            $content = $retArr['str'];
            $resultArr = json_decode($content,true);
            if(isset($resultArr['result'][0])){
                $retArr = $resultArr['result'][0];
            }
        }
        return $retArr;
    }

    private function addColTaxonById($id): int
    {
        $tid = 0;
        if($id){
            $url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&id='.$id;
            //echo $url.'<br/>';
            $retArr = (new Utilities)->getContentString($url);
            $content = $retArr['str'];
            $resultArr = json_decode($content,true);
            if(isset($resultArr['result'][0])){
                $baseArr = $resultArr['result'][0];
                $tid = $this->addColTaxonByResult($baseArr);
            }
            else{
                echo '<li style="margin-left:30px;">Targeted taxon return does not exist</li>';
            }
        }
        else{
            echo '<li style="margin-left:15px;">ERROR harvesting COL name: null input identifier</li>';
        }
        return $tid;
    }

    private function addColTaxonByResult($baseArr,$singleNode = null): int
    {
        $taxonArr = array();
        $tidAccepted = 0;
        if($baseArr){
            $tidAccepted = 0;
            if($baseArr['name_status'] === 'synonym' && isset($baseArr['accepted_name'])){
                $tidAccepted = $this->getTid($this->getColNode($baseArr['accepted_name']));
                if(!$tidAccepted){
                    $tidAccepted = $this->addColTaxon($baseArr['accepted_name']['name']);
                }
            }
            $taxonArr = $this->getColNode($baseArr);
            if(isset($taxonArr['taxonRank']) && $taxonArr['taxonRank'] !== 'Unranked'){
                if((int)$taxonArr['rankid'] === 10){
                    $taxonArr['parent']['tid'] = 'self';
                }
                else{
                    $parentName = '';
                    $parentId = 0;
                    if(!$singleNode && isset($baseArr['classification']) && is_array($baseArr['classification'])){
                        $higherTaxArr = array_reverse($baseArr['classification']);
                        foreach($higherTaxArr as $htArr){
                            $taxArr = $this->getColNode($htArr);
                            $tId = $this->getTid($taxArr);
                            if(!$tId){
                                $colArr = $this->getColNode($this->getColTaxonArrById($taxArr['id']));
                                if(isset($colArr['taxonRank']) && $colArr['taxonRank'] !== 'Unranked'){
                                    if(isset($colArr['author'])){
                                        $taxArr['author'] = $colArr['author'];
                                    }
                                    if($parentName){
                                        $taxArr['parent']['sciname'] = $parentName;
                                    }
                                    if($parentId){
                                        $taxArr['parent']['tid'] = $parentId;
                                    }
                                    $tId = $this->loadNewTaxon($taxArr);
                                }
                            }
                            if($tId){
                                $parentName = $taxArr['sciname'];
                                $parentId = $tId;
                            }
                        }
                    }
                    elseif(isset($baseArr['accepted_name']['classification']) && $baseArr['name_status'] === 'synonym'){
                        $parentArr = current($baseArr['accepted_name']['classification']);
                        $parentId = $this->addColTaxon($parentArr['name'],true);
                        if(!$parentId){
                            $taxonArr['family'] = $this->getColParent($baseArr,'Family');
                            $parentArr = $this->buildTaxonArr($parentArr);
                            $parentId = $this->loadNewTaxon($parentArr);
                        }
                        if($parentId){
                            $parentName = $parentArr['name'];
                        }
                    }
                    else{
                        $parentArr = $this->getParentArr($taxonArr);
                        if($parentArr && $parentArr['sciname'] !== $baseArr['name']){
                            $parentId = $this->addColTaxon($parentArr['sciname'],true);
                            if(!$parentId){
                                $taxonArr['family'] = $this->getColParent($baseArr,'Family');
                                $parentArr = $this->buildTaxonArr($parentArr);
                                $parentId = $this->loadNewTaxon($parentArr);
                            }
                            if($parentId){
                                $parentName = $parentArr['sciname'];
                            }
                        }
                    }
                    if($parentName){
                        $taxonArr['parent']['sciname'] = $parentName;
                    }
                    if($parentId){
                        $taxonArr['parent']['tid'] = $parentId;
                    }
                }
            }
        }
        else{
            echo '<li style="margin-left:15px;">ERROR harvesting COL name: null result</li>';
        }
        return $this->loadNewTaxon($taxonArr, $tidAccepted);
    }

    private function getColNode($nodeArr): array
    {
        $taxonArr = array();
        if(isset($nodeArr['id'])) {
            $taxonArr['id'] = $nodeArr['id'];
        }
        if(isset($nodeArr['name'])) {
            $taxonArr['sciname'] = $nodeArr['name'];
        }
        if(isset($nodeArr['rank'])) {
            $taxonArr['taxonRank'] = $nodeArr['rank'];
        }
        if(isset($nodeArr['genus'])) {
            $taxonArr['unitname1'] = $nodeArr['genus'];
        }
        if(isset($nodeArr['species'])) {
            $taxonArr['unitname2'] = $nodeArr['species'];
        }
        if(isset($nodeArr['infraspecies'])) {
            $taxonArr['unitname3'] = $nodeArr['infraspecies'];
        }
        if(isset($nodeArr['infraspecies_marker'])){
            $taxonArr['unitind3'] = $nodeArr['infraspecies_marker'];
            $taxonArr['sciname'] = trim($taxonArr['unitname1'].' '.$taxonArr['unitname2'].' '.$taxonArr['unitind3'].' '.$taxonArr['unitname3']);
        }
        if(isset($nodeArr['author'])) {
            $taxonArr['author'] = $nodeArr['author'];
        }
        if(isset($nodeArr['source_database'])) {
            $taxonArr['source'] = $nodeArr['source_database'];
        }
        if(isset($nodeArr['source_database_url'])) {
            $taxonArr['sourceURL'] = $nodeArr['source_database_url'];
        }
        $taxonArr = $this->setRankId($taxonArr);
        if(!isset($taxonArr['unitname1']) && $taxonArr['rankid'] < 220) {
            $taxonArr['unitname1'] = $taxonArr['sciname'];
        }
        return $taxonArr;
    }

	private function getColParent($baseArr, $parentRank){
		$retStr = '';
		$classArr = array();
		if(array_key_exists('classification', $baseArr)){
			$classArr = $baseArr['classification'];
		}
		elseif(isset($baseArr['accepted_name']['classification'])){
			$classArr = $baseArr['accepted_name']['classification'];
		}

		foreach($classArr as $classNode){
			if($classNode['rank'] === $parentRank){
				$retStr = $classNode['name'];
			}
		}
		return $retStr;
	}

	private function addWormsTaxon($sciName): int
    {
		$tid = 0;
		$url = 'http://www.marinespecies.org/rest/AphiaIDByName/'.rawurlencode($sciName).'?marine_only=false';
		$retArr = (new Utilities)->getContentString($url);
		$id = $retArr['str'];
		if(is_numeric($id)){
			echo '<li style="margin-left:30px;">Taxon found within WoRMS</li>';
			$tid = $this->addWormsTaxonByID($id);
		}
		else{
			echo '<li style="margin-left:30px;">Taxon not found</li>';
		}
		return $tid;
	}

	private function addWormsTaxonByID($id): int
    {
		if(!is_numeric($id)){
			echo '<li style="margin-left:15px;">ERROR harvesting from worms: illegal identifier: '.$id.'</li>';
			return 0;
		}
		$taxonArr= array();
		$acceptedTid = 0;
		$url = 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/'.$id;
		if($resultStr = $this->getWormsReturnStr((new Utilities)->getContentString($url),$url)){
			$taxonArr= $this->getWormsNode(json_decode($resultStr,true));
			if($taxonArr['acceptance'] === 'unaccepted' && isset($taxonArr['validID'])){
				$acceptedTid = $this->addWormsTaxonByID($taxonArr['validID']);
			}
			if($taxonArr['rankid'] === 10){
				$taxonArr['parent']['tid'] = 'self';
			}
			else{
				$url = 'http://www.marinespecies.org/rest/AphiaClassificationByAphiaID/'.$id;
				if($parentStr = $this->getWormsReturnStr((new Utilities)->getContentString($url),$url)){
					$parentArr = json_decode($parentStr,true);
					if(($parentID = $this->getWormParentID($parentArr, $id)) && $parentTid = $this->addWormsTaxonByID($parentID)) {
						$taxonArr['parent'] = array('tid' => $parentTid);
					}
				}
			}
		}
		return $this->loadNewTaxon($taxonArr, $acceptedTid);
	}

	private function getWormsReturnStr($retArr,$url): string
    {
		$resultStr = '';
		if($retArr['code'] === 200){
			$resultStr = $retArr['str'];
		}
		elseif($retArr['code'] === 204){
			echo '<li style="margin-left:30px;">Identifier not found within WoRMS: '.$url.'</li>';
		}
		else{
			echo '<li style="margin-left:15px;">ERROR returning WoRMS object (code: '.$retArr['code'].'): '.$url.'</li>';
		}
		return $resultStr;
	}

	private function getWormsNode($nodeArr): array
	{
		$taxonArr = array();
		if(isset($nodeArr['AphiaID'])) {
			$taxonArr['id'] = $nodeArr['AphiaID'];
		}
		if(isset($nodeArr['scientificname'])) {
			$taxonArr['sciname'] = $nodeArr['scientificname'];
		}
		if(isset($nodeArr['authority'])) {
			$taxonArr['author'] = $nodeArr['authority'];
		}
		if(isset($nodeArr['family'])) {
			$taxonArr['family'] = $nodeArr['family'];
		}
		if(isset($nodeArr['genus'])) {
			$taxonArr['unitname1'] = $nodeArr['genus'];
		}
		if(isset($nodeArr['status'])) {
			$taxonArr['acceptance'] = $nodeArr['status'];
		}
		if(isset($nodeArr['unacceptreason'])) {
			$taxonArr['unacceptanceReason'] = $nodeArr['unacceptreason'];
		}
		if(isset($nodeArr['valid_AphiaID'])) {
			$taxonArr['validID'] = $nodeArr['valid_AphiaID'];
		}
		if(isset($nodeArr['lsid'])) {
			$taxonArr['guid'] = $nodeArr['lsid'];
		}
		if(isset($nodeArr['rank'])) {
			$taxonArr['taxonRank'] = $nodeArr['rank'];
		}
		$this->setRankId($taxonArr);
        return $this->buildTaxonArr($taxonArr);
	}

	private function getWormParentID($wormNode, $stopID, $previousID = null){
		$parentID = 0;
		if(array_key_exists('AphiaID', $wormNode)){
			$parentID = $wormNode['AphiaID'];
			if($stopID === $parentID) {
				return $previousID;
			}
			if(array_key_exists('child', $wormNode)){
				$parentID = $this->getWormParentID($wormNode['child'], $stopID, $parentID);
			}
		}
		return $parentID;
	}

	private function addTropicosTaxon($sciName): void
    {
		$newTid = 0;
		if($this->taxonomicResource = 'tropicos'){
            $searchType = 'exact';
            if(substr_count($sciName,' ') > 1) {
                $searchType = 'wildcard';
            }
            $sciName = str_replace(array(' subsp.', ' ssp.', ' var.', ' f.', '.', ' '), array('', '', '', '', '', '%20'), $sciName);
            $url = 'http://services.tropicos.org/Name/Search?type='.$searchType.'&format=json&name='.$sciName.'&apikey='.$GLOBALS['TROPICOS_API_KEY'];
            if($fh = fopen($url, 'rb')){
                $content = '';
                while($line = fread($fh, 1024)){
                    $content .= trim($line);
                }
                fclose($fh);
                $resultArr = json_decode($content,true);
                $id = 0;
                foreach($resultArr as $k => $arr){
                    if(array_key_exists('Error', $arr)){
                        echo '<li style="margin-left:30px;">Taxon not found (code:1)</li>';
                        return;
                    }
                    if(!array_key_exists('NomenclatureStatusID', $arr) || (int)$arr['NomenclatureStatusID'] === 1){
                        $id = $arr['NameId'];
                        break;
                    }
                }
                if($id){
                    echo '<li style="margin-left:30px;">Taxon found within TROPICOS</li>';
                    $this->addTropicosTaxonByID($id);
                }
                else{
                    echo '<li style="margin-left:30px;">Taxon not found (code:2)</li>';
                }
            }
            else{
                echo '<li style="margin-left:15px;">ERROR: unable to connect to TROPICOS web services ('.$url.')</li>';
            }
		}
		else{
            echo '<li style="margin-left:15px;">Error: TROPICOS API key required! Contact portal manager to establish key for portal</li>';
        }
	}

	private function addTropicosTaxonByID($id): int
    {
		$taxonArr= array();
		$url = 'http://services.tropicos.org/Name/'.$id.'?apikey='.$GLOBALS['TROPICOS_API_KEY'].'&format=json';
		if($fh = fopen($url, 'rb')){
			$content = '';
			while($line = fread($fh, 1024)){
				$content .= trim($line);
			}
			fclose($fh);
			$resultArr = json_decode($content,true);
			$taxonArr = $this->getTropicosNode($resultArr);

			if($taxonArr['rankid'] === 10){
				$taxonArr['parent']['tid'] = 'self';
			}
			else{
				$url = 'http://services.tropicos.org/Name/'.$id.'/HigherTaxa?apikey='.$GLOBALS['TROPICOS_API_KEY'].'&format=json';
				if($fh = fopen($url, 'rb')){
					$content = '';
					while($line = fread($fh, 1024)){
						$content .= trim($line);
					}
					fclose($fh);
					$parentArr = json_decode($content,true);
					$parentNode = $this->getTropicosNode(array_pop($parentArr));
					if(isset($parentNode['sciname']) && $parentNode['sciname']){
						$parentTid = $this->getTid($parentNode);
						if(!$parentTid && isset($parentNode['id'])) {
							$parentTid = $this->addTropicosTaxonByID($parentNode['id']);
						}
						if($parentTid) {
							$parentNode['tid'] = $parentTid;
						}
						$taxonArr['parent'] = $parentNode;
					}
				}
			}
			if($taxonArr['acceptedNameCount'] > 0 && $taxonArr['synonymCount'] === 0){
				$url = 'http://services.tropicos.org/Name/'.$id.'/AcceptedNames?apikey='.$GLOBALS['TROPICOS_API_KEY'].'&format=json';
				if($fh = fopen($url, 'rb')){
					$content = '';
					while($line = fread($fh, 1024)){
						$content .= trim($line);
					}
					fclose($fh);
					$resultArr = json_decode($content,true);
					if(isset($resultArr['Synonyms']['Synonym']['AcceptedName'])){
						$acceptedNode = $this->getTropicosNode($resultArr['Synonyms']['Synonym']['AcceptedName']);
                        $acceptedNode = $this->buildTaxonArr($acceptedNode);
						$acceptedTid = $this->getTid($acceptedNode);
						if(!$acceptedTid && isset($acceptedNode['id'])) {
							$this->addTropicosTaxonByID($acceptedNode['id']);
						}
					}
				}
			}
		}
		return $this->loadNewTaxon($taxonArr);
	}

	private function getTropicosNode($nodeArr): array
	{
		$taxonArr = array();
		if(isset($nodeArr['NameId'])) {
			$taxonArr['id'] = $nodeArr['NameId'];
		}
		if(isset($nodeArr['ScientificName'])) {
			$taxonArr['sciname'] = $nodeArr['ScientificName'];
		}
		if(isset($nodeArr['ScientificNameWithAuthors'])) {
			$taxonArr['scientificName'] = $nodeArr['ScientificNameWithAuthors'];
		}
		if(isset($nodeArr['Author'])) {
			$taxonArr['author'] = $nodeArr['Author'];
		}
		if(isset($nodeArr['Family'])) {
			$taxonArr['family'] = $nodeArr['Family'];
		}
		if(isset($nodeArr['SynonymCount'])) {
			$taxonArr['synonymCount'] = $nodeArr['SynonymCount'];
		}
		if(isset($nodeArr['AcceptedNameCount'])) {
			$taxonArr['acceptedNameCount'] = $nodeArr['AcceptedNameCount'];
		}
		if(isset($nodeArr['Rank'])){
			$taxonArr['taxonRank'] = $nodeArr['Rank'];
		}
		elseif(isset($nodeArr['RankAbbreviation'])){
			$taxonArr['taxonRank'] = $nodeArr['RankAbbreviation'];
		}
		if(isset($nodeArr['Genus'])) {
			$taxonArr['unitname1'] = $nodeArr['Genus'];
		}
		if(isset($nodeArr['SpeciesEpithet'])) {
			$taxonArr['unitname2'] = $nodeArr['SpeciesEpithet'];
		}
		if(isset($nodeArr['source'])) {
			$taxonArr['source'] = $nodeArr['source'];
		}
		if(!isset($taxonArr['unitname1']) && !strpos($taxonArr['sciname'],' ')) {
			$taxonArr['unitname1'] = $taxonArr['sciname'];
		}
		$this->setRankId($taxonArr);
		if(isset($taxonArr['unitname2'], $nodeArr['OtherEpithet'])){
			$taxonArr['unitname3'] = $nodeArr['OtherEpithet'];
			if($this->kingdomName !== 'Animalia'){
				if((int)$taxonArr['rankid'] === 230) {
					$taxonArr['unitind3'] = 'subsp.';
				}
				elseif((int)$taxonArr['rankid'] === 240) {
					$taxonArr['unitind3'] = 'var.';
				}
				elseif((int)$taxonArr['rankid'] === 260) {
					$taxonArr['unitind3'] = 'f.';
				}
			}
		}
		return $taxonArr;
	}

	private function addEolTaxon($term){
		$tid = 0;
		$eolManager = new EOLUtilities();
		if($eolManager->pingEOL()){
			$searchRet = $eolManager->searchEOL($term);
			if($searchRet && isset($searchRet['id'])){
				$searchSyns = (strpos($searchRet['title'], $term) === false);
				$tid = $this->addEolTaxonById($searchRet['id'], $searchSyns, $term);
			}
			else{
				echo '<li style="margin-left:30px;">Taxon not found</li>';
			}
		}
		else{
			echo '<li style="margin-left:15px;">EOL web services are not available</li>';
			return false;
		}
		return $tid;
	}

	private function addEolTaxonById($eolTaxonId, $searchSyns = null, $term = null): int
    {
        $taxonArr = array();
	    $eolManager = new EOLUtilities();
		if($eolManager->pingEOL()){
			$taxonArr = $eolManager->getPage($eolTaxonId, false);
			if($searchSyns && isset($taxonArr['syns'])){
				foreach($taxonArr['syns']as $k => $synArr){
					if(strpos($synArr['scientificName'],$term) !== 0) {
						unset($taxonArr['syns'][$k]);
					}
				}
			}
			if(isset($taxonArr['taxonConcepts']) && $taxonConceptId = key($taxonArr['taxonConcepts'])) {
				$conceptArr = $eolManager->getHierarchyEntries($taxonConceptId);
				if($conceptArr && isset($conceptArr['parent'])){
					$parentTid = $this->getTid($conceptArr['parent']);
					if(!$parentTid && isset($conceptArr['parent']['taxonConceptID'])){
						$parentTid = $this->addEolTaxonById($conceptArr['parent']['taxonConceptID']);
					}
					if($parentTid && is_string($conceptArr['parent'])){
						$conceptArr['parent']['tid'] = $parentTid;
						$taxonArr['parent'] = $conceptArr['parent'];
					}
				}
			}
			if(!isset($taxonArr['source'])) {
				$taxonArr['source'] = 'EOL - ' . date('Y-m-d G:i:s');
			}
		}
		else{
			echo '<li style="margin-left:15px;">EOL web services are not available</li>';
		}
		if($taxonArr) {
			echo '<li style="margin-left:30px;">Taxon found within EOL</li>';
		}
		else{
			echo '<li style="margin-left:30px;">Taxon ID not found ('.$eolTaxonId.')</li>';
		}
		return $this->loadNewTaxon($taxonArr);
	}

	private function loadNewTaxon($taxonArr, $tidAccepted = null): int
    {
		$this->tidAccepted = 0;
        $newTid = 0;
        if($taxonArr) {
            if((!isset($taxonArr['sciname']) || !$taxonArr['sciname']) && isset($taxonArr['scientificName']) && $taxonArr['scientificName']){
                $taxonArr = $this->buildTaxonArr($taxonArr);
            }
            $sql = 'SELECT tid FROM taxa WHERE (sciname = "'.$taxonArr['sciname'].'") ';
            if($this->kingdomName) {
                $sql .= 'AND (kingdomname = "' . $this->kingdomName . '" OR kingdomname IS NULL) ';
            }
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $newTid = (int)$r->tid;
            }
            $rs->free();
            $loadTaxon = true;
            if($newTid){
                $sql = 'SELECT tidaccepted FROM taxstatus WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.$newTid.')';
                $rs = $this->conn->query($sql);
                if($r = $rs->fetch_object()){
                    $tidAccepted = (int)$r->tidaccepted;
                    $loadTaxon = false;
                }
                $rs->free();
            }
            if($loadTaxon && $this->validateTaxonArr($taxonArr)) {
                if(!$newTid){
                    $sqlInsert = 'INSERT INTO taxa(sciname, unitind1, unitname1, unitind2, unitname2, unitind3, unitname3, author, rankid, source) '.
                        'VALUES("'.$taxonArr['sciname'].'",'.
                        (isset($taxonArr['unitind1']) && $taxonArr['unitind1']?'"'.$taxonArr['unitind1'].'"':'NULL').',"'.
                        $taxonArr['unitname1'].'",'.
                        (isset($taxonArr['unitind2']) && $taxonArr['unitind2']?'"'.$taxonArr['unitind2'].'"':'NULL').','.
                        (isset($taxonArr['unitname2']) && $taxonArr['unitname2']?'"'.$taxonArr['unitname2'].'"':'NULL').','.
                        (isset($taxonArr['unitind3']) && $taxonArr['unitind3']?'"'.$taxonArr['unitind3'].'"':'NULL').','.
                        (isset($taxonArr['unitname3']) && $taxonArr['unitname3']?'"'.$taxonArr['unitname3'].'"':'NULL').','.
                        (isset($taxonArr['author']) && $taxonArr['author']?'"'.$taxonArr['author'].'"':'NULL').','.
                        $taxonArr['rankid'].','.
                        (isset($taxonArr['source']) && $taxonArr['source']?'"'.$taxonArr['source'].'"':'NULL').')';
                    //echo $sqlInsert.'<br/>';
                    if($this->conn->query($sqlInsert)){
                        $newTid = (int)$this->conn->insert_id;
                    }
                    else{
                        echo '<li style="margin-left:15px;">ERROR inserting '.$taxonArr['sciname'].'.</li>';
                    }
                }
                if($newTid){
                    $parentTid = 0;
                    if(isset($taxonArr['parent']['tid'])){
                        if($taxonArr['parent']['tid'] === 'self') {
                            $parentTid = $newTid;
                        }
                        elseif(is_numeric($taxonArr['parent']['tid'])) {
                            $parentTid = $taxonArr['parent']['tid'];
                        }
                    }
                    if(!$parentTid && isset($taxonArr['parent']['sciname'])){
                        $parentTid = $this->getTid($taxonArr['parent']);
                    }

                    if($parentTid){
                        if(!$tidAccepted) {
                            $tidAccepted = $newTid;
                        }
                        $sqlInsert2 = 'INSERT INTO taxstatus(tid,tidAccepted,taxAuthId,parentTid,UnacceptabilityReason) '.
                            'VALUES('.$newTid.','.$tidAccepted.','.$this->taxAuthId.','.$parentTid.','.
                            (isset($taxonArr['acceptanceReason']) && $taxonArr['acceptanceReason']?'"'.$taxonArr['acceptanceReason'].'"':'NULL').')';
                        //echo $sqlInsert2.'<br/><br/>';
                        if($this->conn->query($sqlInsert2)){
                            $sqlHier = 'INSERT INTO taxaenumtree(tid,parenttid,taxauthid) '.
                                'VALUES('.$newTid.','.$parentTid.','.$this->taxAuthId.')';
                            if(!$this->conn->query($sqlHier)){
                                echo '<li style="margin-left:15px;">ERROR adding new tid to taxaenumtree (step 1).</li>';
                            }
                            $sqlHier2 = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid,taxauthid) '.
                                'SELECT '.$newTid.' AS tid, parenttid, taxauthid FROM taxaenumtree WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.$parentTid.')';
                            if(!$this->conn->query($sqlHier2)){
                                echo '<li style="margin-left:15px;">ERROR adding new tid to taxaenumtree (step 2).</li>';
                            }
                            $sqlKing = 'UPDATE taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                                'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
                                'SET t.kingdomname = t2.sciname '.
                                'WHERE (e.taxauthid = '.$this->taxAuthId.') AND (t.tid = '.$newTid.') AND (t2.rankid = 10)';
                            if(!$this->conn->query($sqlKing)){
                                echo '<li style="margin-left:15px;">ERROR updating kingdom string.</li>';
                            }
                            $taxonDisplay = $taxonArr['sciname'];
                            if(isset($GLOBALS['USER_RIGHTS']['Taxonomy'])){
                                $taxonDisplay = '<a href="'.$GLOBALS['CLIENT_ROOT'].'/taxa/taxonomy/taxoneditor.php?tid='.$newTid.'" target="_blank">'.$taxonArr['sciname'].'</a>';
                            }
                            $accStr = 'accepted';
                            if($tidAccepted !== $newTid){
                                if(isset($GLOBALS['USER_RIGHTS']['Taxonomy'])){
                                    $accStr = 'synonym of taxon <a href="'.$GLOBALS['CLIENT_ROOT'].'/taxa/taxonomy/taxoneditor.php?tid='.$tidAccepted.'" target="_blank">#'.$tidAccepted.'</a>';
                                }
                                else{
                                    $accStr = 'synonym of taxon #'.$tidAccepted;
                                }
                                $this->tidAccepted = $tidAccepted;
                            }
                            echo '<li style="margin-left:15px;">Taxon <b>'.$taxonDisplay.'</b> added to thesaurus as '.$accStr.'</li>';
                        }
                    }
                    else{
                        echo '<li style="margin-left:15px;">ERROR loading '.$taxonArr['sciname'].': unable to get parentTid</li>';
                    }
                }
            }
            if($newTid){
                if(isset($taxonArr['syns'])){
                    foreach($taxonArr['syns'] as $k => $synArr){
                        if($synArr){
                            if(isset($taxonArr['source']) && $taxonArr['source'] && (!isset($synArr['source']) || !$synArr['source'])) {
                                $synArr['source'] = $taxonArr['source'];
                            }
                            $acceptanceReason = '';
                            if(isset($taxonArr['acceptanceReason']) && $taxonArr['acceptanceReason']) {
                                $acceptanceReason = $taxonArr['acceptanceReason'];
                            }
                            if(isset($synArr['synreason']) && $synArr['synreason']) {
                                $acceptanceReason = $synArr['synreason'];
                            }
                            if($acceptanceReason === 'misspelling'){
                                echo '<li style="margin-left:15px;">Name not added because it is marked as a misspelling</li>';
                                $this->fullyResolved = false;
                            }
                            else{
                                if($acceptanceReason && (!isset($synArr['acceptanceReason']) || !$synArr['acceptanceReason'])) {
                                    $synArr['acceptanceReason'] = $acceptanceReason;
                                }
                                $this->loadNewTaxon($synArr,$newTid);
                            }
                        }
                    }
                }
                if(isset($taxonArr['verns'])){
                    foreach($taxonArr['verns'] as $k => $vernArr){
                        $sqlVern = 'INSERT INTO taxavernaculars(tid,vernacularname,language) '.
                            'VALUES('.$newTid.',"'.$vernArr['vernacularName'].'","'.$vernArr['language'].'")';
                        if(!$this->conn->query($sqlVern)){
                            echo '<li style="margin-left:15px;">ERROR loading vernacular '.$taxonArr['sciname'].'.</li>';
                        }
                    }
                }
            }
		}
		return $newTid;
	}

	private function validateTaxonArr($taxonArr): bool
	{
		$retVal = true;
	    if(is_array($taxonArr)) {
            if(!isset($taxonArr['rankid']) || !$taxonArr['rankid']){
                if(isset($taxonArr['taxonRank']) && $taxonArr['taxonRank']){
                    $this->setRankId($taxonArr);
                }
            }
            if(!$this->kingdomTid) {
                $this->setDefaultKingdom();
            }
            if(!array_key_exists('parent',$taxonArr) || !$taxonArr['parent']){
                $taxonArr['parent'] = $this->getParentArr($taxonArr);
            }
            if(!isset($taxonArr['sciname']) || !$taxonArr['sciname']){
                echo '<li style="margin-left:15px;">ERROR loading '.$taxonArr['sciname'].': Input scientific name not defined</li>';
                $retVal = false;
            }
            elseif(!isset($taxonArr['parent']) || !$taxonArr['parent']){
                echo '<li style="margin-left:15px;">ERROR loading '.$taxonArr['sciname'].': Parent name not definable</li>';
                $retVal = false;
            }
            elseif(!isset($taxonArr['unitname1']) || !$taxonArr['unitname1']){
                echo '<li style="margin-left:15px;">ERROR loading '.$taxonArr['sciname'].': unitname1 not defined</li>';
                $retVal = false;
            }
            elseif(!isset($taxonArr['rankid']) || !$taxonArr['rankid']){
                echo '<li style="margin-left:15px;">ERROR loading '.$taxonArr['sciname'].': rankid not defined</li>';
                $retVal = false;
            }
        }
	    else {
            $retVal = false;
		}
		return $retVal;
	}

    private function setRankId($taxonArr): array
    {
        $rankid = 0;
        $rankArr = array('biota' => 1, 'organism' => 1, 'kingdom' => 10, 'subkingdom' => 20, 'division' => 30, 'phylum' => 30, 'subdivision' => 40, 'subphylum' => 40, 'superclass' => 50, 'supercl.' => 50,
            'class' => 60, 'cl.' => 60, 'subclass' => 70, 'subcl.' => 70, 'infraclass' => 80, 'superorder' => 90, 'superord.' => 90, 'order' => 100, 'ord.' => 100, 'suborder' => 110, 'subord.' => 110,
            'superfamily' => 130, 'family' => 140, 'fam.' => 140, 'subfamily' => 150, 'tribe' => 160, 'subtribe' => 170, 'genus' => 180, 'gen.' => 180,
            'subgenus' => 190, 'section' => 200, 'subsection' => 210, 'species' => 220, 'sp.' => 220, 'subspecies' => 230, 'ssp.' => 230, 'subsp.' => 230, 'infraspecies' => 230,
            'variety' => 240, 'var.' => 240, 'morph' => 240, 'subvariety' => 250, 'form' => 260, 'f.' => 260, 'subform' => 270, 'cultivated' => 300);
        if(isset($taxonArr['taxonRank']) && $taxonArr['taxonRank'] && $taxonArr['taxonRank'] !== 'Unranked'){
            $taxonRank = strtolower($taxonArr['taxonRank']);
            if(array_key_exists($taxonRank, $rankArr)){
                $rankid = $rankArr[$taxonRank];
            }
            if(!$rankid){
                $sqlRank = 'SELECT rankid FROM taxonunits WHERE rankname = "'.$taxonArr['taxonRank'].'" AND kingdomid = '.$this->kingdomId;
                $rsRank = $this->conn->query($sqlRank);
                while($rRank = $rsRank->fetch_object()){
                    $rankid = (int)$rRank->rankid;
                }
                $rsRank->free();
            }
        }
        if(!$rankid && isset($taxonArr['unitind3']) && $taxonArr['unitind3']){
            $unitInd3 = strtolower($taxonArr['unitind3']);
            if(array_key_exists($unitInd3, $rankArr)){
                $rankid = $rankArr[$unitInd3];
            }
        }
        $taxonArr['rankid'] = $rankid;
        return $taxonArr;
    }

	private function setDefaultKingdom(): void
	{
		if(!$this->kingdomName || !$this->kingdomTid){
			$sql = 'SELECT t.sciname, t.tid, COUNT(e.tid) as cnt '.
				'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.parenttid '.
				'WHERE (t.rankid = 10) AND (e.taxauthid = '.$this->taxAuthId.') '.
				'GROUP BY t.sciname '.
				'ORDER BY cnt desc';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->kingdomName = $r->sciname;
				$this->kingdomTid = $r->tid;
			}
			$rs->free();
		}
	}

    private function getParentArr($taxonArr){
        if(!is_array($taxonArr)) {
            return false;
        }
        $parArr = array();
        if($taxonArr['sciname'] && isset($taxonArr['rankid']) && $taxonArr['rankid']) {
            if(!$this->kingdomTid) {
                $this->setDefaultKingdom();
            }
            if($this->kingdomName){
                $parArr = array(
                    'tid' => $this->kingdomTid,
                    'sciname' => $this->kingdomName,
                    'taxonRank' => 'Kingdom',
                    'rankid' => 10
                );
            }
            if((int)$taxonArr['rankid'] > 220){
                $parArr = array(
                    'sciname' => $taxonArr['unitname1'].' '.$taxonArr['unitname2'],
                    'taxonRank' => 'species',
                    'rankid' => 220
                );
            }
            elseif((int)$taxonArr['rankid'] > 180){
                $genusName = '';
                if(strpos($taxonArr['unitname1'], ' ') !== false){
                    $genusNameArr = explode(' ', $taxonArr['unitname1']);
                    if($genusNameArr){
                        $genusName = $genusNameArr[0];
                    }
                }
                else{
                    $genusName = $taxonArr['unitname1'];
                }
                $parArr = array(
                    'sciname' => $genusName,
                    'taxonRank' => 'genus',
                    'rankid' => 180
                );
            }
            elseif((int)$taxonArr['rankid'] > 140){
                $familyStr = $this->defaultFamily;
                if(isset($taxonArr['family']) && $taxonArr['family']) {
                    $familyStr = $taxonArr['family'];
                }
                if($familyStr){
                    $sqlFam = 'SELECT tid FROM taxa WHERE (sciname = "'.Sanitizer::cleanInStr($this->defaultFamily).'") AND (rankid = 140)';
                    //echo $sqlFam;
                    $rs = $this->conn->query($sqlFam);
                    if($r = $rs->fetch_object()){
                        $parArr = array(
                            'tid' => $r->tid,
                            'sciname' => $this->defaultFamily,
                            'taxonRank' => 'family',
                            'rankid' => 140
                        );
                    }
                    $rs->free();
                }
            }
        }
        return $parArr;
    }

	public function buildTaxonArr($taxonArr): array
	{
		if(is_array($taxonArr)){
			$rankid = array_key_exists('rankid', $taxonArr)?$taxonArr['rankid']:0;
			$sciname = array_key_exists('sciname', $taxonArr)?$taxonArr['sciname']:'';
			if(!$sciname && array_key_exists('scientificName', $taxonArr)) {
				$sciname = $taxonArr['scientificName'];
			}
			if($sciname){
				$taxonArr = array_merge((new TaxonomyUtilities)->parseScientificName($sciname,$rankid),$taxonArr);
			}
		}
		return $taxonArr;
	}

	public function getCloseMatch($taxonStr): array
	{
		$retArr = array();
		$taxonStr = Sanitizer::cleanInStr($taxonStr);
		if($taxonStr){
			$infraArr = array('subsp','ssp','var','f');
			$taxonStringArr = explode(' ',$taxonStr);
			$unitname1 = array_shift($taxonStringArr);
			if(strlen($unitname1) === 1) {
				$unitname1 = array_shift($taxonStringArr);
			}
			$unitname2 = array_shift($taxonStringArr);
			if(strlen($unitname2) === 1) {
				$unitname2 = array_shift($taxonStringArr);
			}
			$unitname3= array_shift($taxonStringArr);
			if($taxonStringArr){
				while($val = array_shift($taxonStringArr)){
					if(in_array(str_replace('.', '', $val), $infraArr, true)) {
						$unitname3 = array_shift($taxonStringArr);
					}
				}
			}
			if($unitname3){
				$sql = 'SELECT tid, sciname FROM taxa WHERE (unitname1 = "'.$unitname1.'") AND (unitname2 = "'.$unitname2.'") AND (unitname3 = "'.$unitname3.'") ';
				if($this->kingdomName) {
					$sql .= 'AND (kingdomname = "' . $this->kingdomName . '" OR kingdomname IS NULL) ';
				}
				$sql .= 'ORDER BY sciname';
				//echo $sql.'<br/>';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					$retArr[$row->tid] = $row->sciname;
				}
				$rs->free();
			}

			if($unitname2){
				if(!$retArr){
					$searchStr = substr($unitname1,0,4).'%';
					$searchStr .= ' '.substr($unitname2,0,4).'%';
					if($unitname3 && count($unitname3) > 2) {
						$searchStr .= ' ' . substr($unitname3, 0, 5) . '%';
					}
					$sql = 'SELECT tid, sciname FROM taxa WHERE (sciname LIKE "'.$searchStr.'") ';
					if($this->kingdomName) {
						$sql .= 'AND (kingdomname = "' . $this->kingdomName . '" OR kingdomname IS NULL) ';
					}
					$sql .= 'ORDER BY sciname LIMIT 15';
					//echo $sql.'<br/>';
					$rs = $this->conn->query($sql);
					while($row = $rs->fetch_object()){
						similar_text($taxonStr,$row->sciname,$percent);
						if($percent > 70) {
							$retArr[$row->tid] = $row->sciname;
						}
					}
					$rs->free();
				}

				if(!$retArr){
					$sql = 'SELECT tid, sciname FROM taxa WHERE (sciname LIKE "'.substr($unitname1,0,2).'% '.$unitname2.'") ';
					if($this->kingdomName) {
						$sql .= 'AND (kingdomname = "' . $this->kingdomName . '" OR kingdomname IS NULL) ';
					}
					$sql .= 'ORDER BY sciname';
					//echo $sql.'<br/>';
					$rs = $this->conn->query($sql);
					while($row = $rs->fetch_object()){
						$retArr[$row->tid] = $row->sciname;
					}
					$rs->free();
				}
			}
			$sql = 'SELECT tid, sciname FROM taxa WHERE SOUNDEX(sciname) = SOUNDEX("'.$taxonStr.'") ';
			if($this->kingdomName) {
				$sql .= 'AND (kingdomname = "' . $this->kingdomName . '" OR kingdomname IS NULL) ';
			}
			$sql .= 'ORDER BY sciname LIMIT 5';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				if(!strpos($taxonStr,' ') || strpos($row->sciname,' ')){
					$retArr[$row->tid] = $row->sciname;
				}
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getTid($taxonArr): int
    {
		$tid = 0;
		if(isset($taxonArr['sciname']) && $taxonArr['sciname']){
			$sciname = $taxonArr['sciname'];
			$tidArr = array();
			$sql = 'SELECT tid, author, rankid FROM taxa WHERE (sciname = "'.Sanitizer::cleanInStr($sciname).'") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$tidArr[$r->tid]['author'] = $r->author;
				$tidArr[$r->tid]['rankid'] = $r->rankid;
			}
			$rs->free();
			if($tidArr) {
                if(count($tidArr) === 1){
                    $tid = key($tidArr);
                }
                elseif(count($tidArr) > 1){
                    $sqlPar = 'SELECT DISTINCT e.tid, t.tid AS parenttid, t.sciname, t.rankid '.
                        'FROM taxaenumtree e INNER JOIN taxa t ON e.parenttid = t.tid '.
                        'WHERE (e.taxauthid = '.$this->taxAuthId.') AND (e.tid IN('.implode(',',array_keys($tidArr)).')) AND (t.rankid IN (10,140)) ';
                    $rsPar = $this->conn->query($sqlPar);
                    while($rPar = $rsPar->fetch_object()){
                        if($r->rankid === 10) {
                            $tidArr[$rPar->tid]['kingdom'] = $rPar->sciname;
                        }
                        elseif($r->rankid === 140) {
                            $tidArr[$rPar->tid]['family'] = $rPar->sciname;
                        }
                    }
                    $rsPar->free();

                    $goodArr = array();
                    foreach($tidArr as $t => $tArr){
                        $goodArr[$t] = 0;
                        if(isset($taxonArr['rankid']) && $taxonArr['rankid'] && $tArr['rankid'] === $taxonArr['rankid']) {
                            $goodArr[$t] = 1;
                        }
                        if(isset($tArr['family']) && $tArr['family']){
                            if(isset($taxonArr['family']) && $taxonArr['family']){
                                if(strtolower($tArr['family']) === strtolower($taxonArr['family'])){
                                    $goodArr[$t] += 2;
                                }
                            }
                            elseif($this->defaultFamily){
                                if(strtolower($tArr['family']) === strtolower($this->defaultFamily)){
                                    $goodArr[$t] += 2;
                                }
                            }
                        }
                        if($this->kingdomName && isset($tArr['kingdom']) && $tArr['kingdom'] && strtolower($tArr['kingdom']) === strtolower($this->kingdomName)) {
                            $goodArr[$t] += 2;
                        }
                        if(isset($taxonArr['author']) && $taxonArr['author']){
                            $author1 = str_replace(array(' ','.'), '', $taxonArr['author']);
                            $author2 = str_replace(array(' ','.'), '', $tArr['author']);
                            similar_text($author1, $author2, $percent);
                            if($author1 === $author2) {
                                $goodArr[$t] += 2;
                            }
                            elseif($percent > 80) {
                                ++$goodArr[$t];
                            }
                        }
                    }
                    asort($goodArr);
                    end($goodArr);
                    $tid = key($goodArr);
                }
			}
		}
		return (int)$tid;
	}

	public function getTidAccepted(): int
    {
		return $this->tidAccepted;
	}

    public function setTaxAuthId($id): void
	{
		if(is_numeric($id)){
			$this->taxAuthId = $id;
		}
	}

    public function setKingdomTid($id): void
    {
        if(is_numeric($id)) {
            $this->kingdomTid = $id;
            $this->setKingdomId($id);
        }
    }

    public function setKingdomId($tid): void
    {
        $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = '.$tid.' ';
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $this->kingdomId = $r->kingdom_id;
        }
        $rs->free();
    }

	public function setKingdomName($name): void
	{
		if(preg_match('/^[a-zA-Z]+$/', $name)) {
			$this->kingdomName = $name;
		}
	}

	public function setTaxonomicResources($resource): void
	{
		if(is_string($resource) && trim($resource)){
            $this->taxonomicResource = $resource;
        }
		else{
            echo '<li>ERROR: Taxonomic Data Source is not defined</li>';
        }
	}

	public function setDefaultAuthor($str): void
	{
		$this->defaultAuthor = $str;
	}

    public function setDefaultFamily($familyStr): void
    {
        $this->defaultFamily = $familyStr;
    }

	public function isFullyResolved(){
		return $this->fullyResolved;
	}
}
