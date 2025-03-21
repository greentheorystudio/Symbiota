<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class KeyDataManager extends Manager{

	private $sql = '';
	private $relevanceValue = .9;		//Percent (as a decimal) of Taxa that must be coded for a CID to be displayed
	private $taxonFilter;
	private $clid;
	private $clName;
	private $clAuthors;
	private $clType;
	private $searchterms;
	private $charArr = array();
	private $taxaCount;
	private $lang;
    private $langArr = array();
	private $commonDisplay = false;
	private $pid;
	private $dynClid;

	public function __construct(){
        parent::__construct();
    }

	public function setProject($projValue){
		if(is_numeric($projValue)){
			$this->pid = $projValue;
		}
		else{
			$sql = "SELECT p.pid FROM fmprojects p WHERE (p.projname = '".SanitizerService::cleanInStr($this->conn,$projValue)."')";
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->pid = $row->pid;
			}
			$result->close();
		}
		return $this->pid;
	}

    public function setLanguage($l): void
	{
        $this->lang = $l;
        $this->langArr[] = $l;
        $sql = "SELECT iso639_1 FROM adminlanguages WHERE langname = '".SanitizerService::cleanInStr($this->conn,$l)."' ";
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $this->langArr[] = $row->iso639_1;
        }
        $result->close();
    }

	public function setCommonDisplay($bool): void
	{
		$this->commonDisplay = $bool;
	}

	public function getTaxaFilterList(): array
	{
		$returnArr = array();
		$sql = 'SELECT DISTINCT t.UnitName1, t.family ';
		if($this->clid && $this->clType === 'static'){
			$sql .= 'FROM taxa AS t INNER JOIN fmchklsttaxalink AS cltl ON t.TID = cltl.TID ' .
				'WHERE cltl.CLID = ' .$this->clid. ' ';
		}
		else if($this->dynClid){
			$sql .= 'FROM taxa AS t INNER JOIN fmdyncltaxalink AS dcltl ON t.TID = dcltl.TID ' .
				'WHERE dcltl.dynclid = ' .$this->dynClid. ' ';
		}
		else{
			$sql .= 'FROM ((taxa AS t INNER JOIN fmchklsttaxalink AS cltl ON t.TID = cltl.TID) ' .
				'INNER JOIN fmchecklists AS cl ON cltl.CLID = cl.CLID) ' .
				'INNER JOIN fmchklstprojlink AS clpl ON cl.CLID = clpl.clid ' .
				'WHERE clpl.pid = ' .$this->pid. ' ';
		}
		//echo $sql.'<br/>'; exit;
		if($result = $this->conn->query($sql)){
            while($row = $result->fetch_object()){
                $genus = $row->UnitName1;
                $family = $row->family;
                if($genus) {
                    $returnArr[] = $genus;
                }
                if($family) {
                    $returnArr[] = $family;
                }
            }

            $result->free();
            $returnArr = array_unique($returnArr);
            natcasesort($returnArr);
            array_unshift($returnArr, '--------------------------');
            array_unshift($returnArr, 'All Species');
        }
		return $returnArr;
	}

	public function setTaxonFilter($t): void
	{
		$this->taxonFilter = SanitizerService::cleanInStr($this->conn,$t);
	}

	public function setClValue($clv){
		if($this->dynClid){
			$sql = 'SELECT d.name, d.details, d.type '.
				'FROM fmdynamicchecklists d WHERE (dynclid = '.$this->dynClid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->clName = $row->name;
				$this->clType = $row->type;
			}
			$result->close();
		}
		else{
			if(is_numeric($clv)){
				$sql = 'SELECT cl.CLID, cl.Name, cl.Authors, cl.Type, cl.searchterms ' .
					'FROM fmchecklists cl WHERE (cl.CLID = ' .$clv. ')';
			}
			else{
				$sql = 'SELECT cl.CLID, cl.Name, cl.Authors, cl.Type, cl.searchterms ' .
					"FROM fmchecklists cl WHERE (cl.Name = '".$clv."') OR (cl.Title = '".$clv."')";
			}
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->clid = $row->CLID;
				$this->clName = $row->Name;
				$this->clAuthors = $row->Authors;
				$this->clType = ($row->Type?:'static');
				$this->searchterms = $row->searchterms;
			}
			$result->close();
		}
		return $this->clid;
	}

	public function getClid(){
		return $this->clid;
	}

	public function setDynClid($id): void
	{
		$this->dynClid = $id;
	}

	public function setAttrs($attrs): void
	{
		if(is_array($attrs)){
			foreach($attrs as $attr){
				if(strpos($attr,'-') !== false) {
                    $fragments = explode('-',$attr);
                    if($fragments){
                        $cid = $fragments[0];
                        $cs = $fragments[1];
                        $this->charArr[$cid][] = $cs;
                    }
                }
			}
		}
	}

	public function getTaxaCount(){
		return $this->taxaCount;
	}

	public function getClName(){
		return $this->clName;
	}

	public function getClAuthors(){
		return $this->clAuthors;
	}

	public function getClType(){
		return $this->clType;
	}

	public function setRelevanceValue($rel): void
	{
		$this->relevanceValue = ($rel?:0);
	}

	public function getRelevanceValue(): float
	{
		return $this->relevanceValue;
	}

 	public function getData(): array
    {
 		$charArray = array();
		$taxaArray = array();
		if(($this->clid && $this->taxonFilter) || $this->dynClid){
		    $this->setTaxaListSQL();
			$taxaArray = $this->getTaxaList();
			$charArray = $this->getCharList();
		}
		$returnArray['chars'] = $charArray;
		$returnArray['taxa'] = $taxaArray;
		return $returnArray;
	}

	public function getCharList(): ?array
    {
		$returnArray = array();
		$charList = array();
		$countMin = $this->taxaCount * $this->relevanceValue;
		$loopCnt = 0;
		while(!$charList && $loopCnt < 10){
			$sqlRev = 'SELECT tc.CID, COUNT(tc.TID) AS c FROM '.
				'(SELECT DISTINCT tList.TID, d.CID FROM ('.$this->sql.') AS tList INNER JOIN kmdescr d ON tList.TID = d.TID WHERE (d.CS <> "-")) AS tc '.
				'GROUP BY tc.CID HAVING ((Count(tc.TID)) > ' .$countMin. ')';
			$rs = $this->conn->query($sqlRev);
			//echo $sqlRev.'<br/>';
			while($row = $rs->fetch_object()){
				$charList[] = $row->CID;
			}
			$countMin *= 0.9;
			$loopCnt++;
		}
		$charList = array_merge($charList,array_keys($this->charArr));

		if($charList){
			$sqlChar = 'SELECT DISTINCT cs.CID, cs.CS, cs.CharStateName, cs.Description AS csdescr, chars.CharName,' .
				'chars.description AS chardescr, chars.hid, chead.headingname, chars.helpurl, Count(cs.CS) AS Ct, chars.DifficultyRank,' .
				'chars.display, chars.defaultlang ' .
				'FROM ((((' .$this->sql. ') AS tList INNER JOIN kmdescr d ON tList.TID = d.TID)' .
				'INNER JOIN kmcs cs ON (d.CS = cs.CS)	AND (d.CID = cs.CID)) INNER JOIN kmcharacters chars ON chars.cid = cs.CID) ' .
				'INNER JOIN kmcharheading chead ON chars.hid = chead.hid ' .
				'GROUP BY chead.language, cs.CID, cs.CS, cs.CharStateName, chars.CharName, chead.headingname, chars.helpurl, ' .
				"chars.DifficultyRank, chars.defaultlang, chars.chartype HAVING (chead.language = 'English' AND ((cs.CID) In (".implode(',',$charList).")) AND ((cs.CS)<>'-') AND ".
				"((chars.chartype)='UM' Or (chars.chartype)='OM') AND chars.DifficultyRank < 3) ".
				'ORDER BY chead.sortsequence, chars.SortSequence, cs.SortSequence ';
			//echo $sqlChar.'<br/>';
			$result = $this->conn->query($sqlChar);

			$langList = array();
			$headingArray = array();
			if(!$result) {
				return null;
			}
            $currentCID = '';
			$csNumValue = 0;
			while($row = $result->fetch_object()){
				$ct = $row->Ct;			//count of how many times the CS was used in this species list
				$charCID = $row->CID;
				if($ct < $this->taxaCount || array_key_exists($charCID,$this->charArr)){		//add to return if stateUseCount is less than taxaCount (ie: state is useless if all taxa code true) or is an attribute selected by user
                    $language = $row->defaultlang;
                    $display = 'checkbox';
                    if($row->display) {
						$display = $row->display;
					}
                    if(!in_array($language, $langList, true)) {
						$langList[] = $language;
					}
                    $headingName = $row->headingname;
                    $headingID = $row->hid;
                    $charName = $row->CharName;
                    $charDescr = $row->chardescr;
                    if($charDescr) {
						$charName = "<span class='charHeading' title='" . $charDescr . "'>" . $charName . '</span>';
					}
                    $url = $row->helpurl;
                    if($url) {
						$charName .= " <a href='$url' target='_blank' style='border:0;'><i style='height:15px;width:15px;color:green;' class='fas fa-info-circle'></i></a>";
					}
                    $cs = $row->CS;
                    $charStateName = $row->CharStateName;
                    $csDescr = $row->csdescr;
                    if($csDescr) {
						$charStateName = "<span class='characterStateName' title='" . $csDescr . "'>" . $charStateName . '</span>';
					}
                    $diffRank = false;
                    if($row->DifficultyRank && $row->DifficultyRank > 1 && !array_key_exists($charCID,$this->charArr)) {
						$diffRank = true;
					}

                    $headingArray[$headingID]['HeadingNames'][$language] = $headingName;

                    if(!array_key_exists($headingID, $headingArray) || !array_key_exists($charCID, $headingArray[$headingID]) || !array_key_exists('CharNames', $headingArray[$headingID][$charCID]) || !array_key_exists($language, $headingArray[$headingID][$charCID]['CharNames'])){
                        $headingArray[$headingID][$charCID]['display'] = $display;
                        $headingArray[$headingID][$charCID]['CharNames'][$language] = "<div class='dynam'".($diffRank?" style='display:none;' ": ' ')."><span class='dynamlang' lang='".$language."'".
                            ($language === $this->lang? ' ' :" style='display:none;'"). '>&nbsp;&nbsp;' .$charName. '</span></div>';
                    }

                    if($display === 'checkbox'){
                        $checked = '';
                        if($this->charArr && array_key_exists($charCID,$this->charArr) && in_array($cs, $this->charArr[$charCID], true)) {
							$checked = 'checked';
						}
                        if(!array_key_exists($headingID,$headingArray) || !array_key_exists($charCID,$headingArray[$headingID]) || !array_key_exists($cs,$headingArray[$headingID][$charCID]) || !$headingArray[$headingID][$charCID][$cs]['ROOT']){
                            $headingArray[$headingID][$charCID][$cs]['ROOT'] = "<div class='dynamopt'".
                                ">&nbsp;&nbsp;<input type='checkbox' name='attr[]' id='cb".$charCID. '-' .$cs."' value='".$charCID. '-' .$cs."' $checked onclick='javascript: document.keyform.submit();'>";
                        }
                    }
                    elseif($display === 'slider'){
						if(!$currentCID || ($currentCID && ($currentCID !== $charCID))){
                            $csArr = array();
                            $csArr[0]['name'] = 'Any';
                            $csArr[0]['id'] = 0;
                            $csNumValue = 1;
                            $currentCID = $charCID;
                        }
                        $selected = '';
                        if($this->charArr && array_key_exists($charCID,$this->charArr)) {
							$selected = $this->charArr[$charCID];
						}
                        $headingArray[$headingID][$charCID]['selected'] = $selected;
                        $csArr[$csNumValue]['name'] = $charStateName;
                        $csArr[$csNumValue]['id'] = $cs;
                        $headingArray[$headingID][$charCID]['csarr'] = $csArr;
                        $headingArray[$headingID][$charCID]['language'] = $language;
                        $csNumValue++;
                    }

                    $headingArray[$headingID][$charCID][$cs][$language] = $charStateName;
				}
			}
			$result->free();
			$returnArray['Languages'] = $langList;
			foreach($headingArray as $HID => $cArray){
				$displayHeading = true;
				$headNameArray = $cArray['HeadingNames'];
				unset($cArray['HeadingNames']);
				$endStr = '';
				foreach($cArray as $cid => $csArray){
					if(array_key_exists($cid,$this->charArr) || count($csArray) > 2){
						if($displayHeading){
							$returnArray[] = "<div class='headingname' id='headingname".$HID."' style='font-weight:bold;margin-top:1em;'>\n";
							foreach($headNameArray as $langValue => $headValue){
								$returnArray[] .= "<span lang='".$langValue."' style='".($langValue === $this->lang? '' : 'display:none;')."'>$headValue</span>\n";
							}
							$returnArray[] = "</div>\n";
							$returnArray[] = "<div class='heading' id='heading".$HID."' style=''>";
							$endStr = "</div>\n";
						}
						$displayHeading = false;
                        $displayType = $csArray['display'];
                        unset($csArray['display']);
						$chars = $csArray['CharNames'];
						unset($csArray['CharNames']);
						$returnArray[] = "<div id='char".$cid."'>";
						foreach($chars as $names){
							$returnArray[] = $names;
						}
						if($displayType === 'checkbox'){
                            foreach($csArray as $csKey => $stateNames){
                                if(array_key_exists('ROOT',$stateNames)) {
									$returnArray[] = $stateNames['ROOT'];
								}
                                unset($stateNames['ROOT']);
                                foreach($stateNames as $csLang => $csValue){
                                    $returnArray[] = "<span lang='".$csLang."' ".
                                        ($csLang === $this->lang? '' :" style='display:none;'").">$csValue</span>";
                                }
                                $returnArray[] = '</div>';
                            }
                            $returnArray[] = '</div>';
                        }
                        elseif($displayType === 'slider'){
                            if(array_key_exists('csarr',$csArray)){
                                $sliderArr = $csArray['csarr'];
                                unset($csArray['csarr']);
								$cSelected = 0;
                                if($csArray['selected']){
                                    foreach($sliderArr as $k => $selCS){
                                        if($csArray['selected'][0] === $selCS['id']){
                                            $cSelected = $k;
                                        }
                                    }
                                }
                                unset($csArray['selected']);
                                $cLanguage = $csArray['language'];
                                unset($csArray['language']);

                                if($cLanguage === $this->lang){
                                    $sliderMax = count($sliderArr) - 1;
                                    $returnArray[] = '<script type="text/javascript">';
                                    $returnArray[] = 'var sliderValues' .$cid." = JSON.parse('". json_encode($sliderArr) ."');";
                                    $returnArray[] = '$( function() {';
                                    $returnArray[] = '$( "#slider'.$cid.'" ).slider({';
                                    $returnArray[] = 'value: '.$cSelected.',';
                                    $returnArray[] = 'min: 0,';
                                    $returnArray[] = 'max: '.$sliderMax.',';
                                    $returnArray[] = 'step: 1,';
                                    $returnArray[] = 'slide: function( event, ui ) {';
                                    $returnArray[] = '$( "#csdispvalue'.$cid.'" ).html( sliderValues'.$cid.'[ui.value]["name"] );';
                                    $returnArray[] = 'if(ui.value > 0){$( "#cshidvalue'.$cid.'" ).val( "'.$cid.'-"+sliderValues'.$cid.'[ui.value]["id"] );}';
                                    $returnArray[] = 'else{$( "#cshidvalue'.$cid.'" ).val( "" );}';
                                    $returnArray[] = '},';
                                    $returnArray[] = 'stop: function( event, ui ) {';
                                    $returnArray[] = 'document.keyform.submit();';
                                    $returnArray[] = '}';
                                    $returnArray[] = '});';
                                    $returnArray[] = '$( "#csdispvalue'.$cid.'" ).html( sliderValues'.$cid.'[$( "#slider'.$cid.'" ).slider( "value" )]["name"] );';
                                    $returnArray[] = 'if($( "#slider'.$cid.'" ).slider( "value" ) > 0){$( "#cshidvalue'.$cid.'" ).val( "'.$cid.'-"+sliderValues'.$cid.'[$( "#slider'.$cid.'" ).slider( "value" )]["id"] );}';
                                    $returnArray[] = '} );';
                                    $returnArray[] = '</script>';
                                    $returnArray[] = '<div id="slider'.$cid.'"></div>';
                                    $returnArray[] = '<div class="dynam">';
                                    $returnArray[] = '<div id="csdispvalue'.$cid.'"></div>';
                                    $returnArray[] = '<input type="hidden" name="attr[]" id="cshidvalue'.$cid.'" readonly style="border:0; font-weight:bold;">';
                                    $returnArray[] = '</div>';
                                }
                                $returnArray[] = '</div>';
                            }
                        }
					}
				}
				if($endStr) {
					$returnArray[] = $endStr;
				}
			}
		}
		return $returnArray;
 	}

    public function getTaxaList(): array
	{
        //echo $this->sql; exit;
        $result = $this->conn->query($this->sql);
        $returnArray = array();
        $count = 0;
        while ($row = $result->fetch_object()){
            $sppArr = array();
            $family = $row->family;
            $tid = $row->tid;
            $displayName = $row->DisplayName;
            if(array_key_exists($family, $returnArray)) {
				$sppArr = $returnArray[$family];
			}
            if(!array_key_exists($tid, $sppArr)) {
                $count++;
                $sppArr[$tid] = $displayName;
                $returnArray[$family] = $sppArr;
            }
        }
        $this->taxaCount = $count;
        $result->close();
        return $returnArray;
    }

    public function setTaxaListSQL(): void
	{
        if(!$this->sql){
            $sqlBase = 'SELECT DISTINCT t.tid, t.family, ' .($this->commonDisplay?'IFNULL(v.VernacularName,t.SciName)':'t.SciName'). ' AS DisplayName, t.parenttid ';
            if($this->dynClid){
                $sqlFromBase = 'LEFT JOIN fmdyncltaxalink AS clk ON t.tid = clk.tid ';
                $sqlWhere = 'WHERE clk.dynclid = ' .$this->dynClid. ' AND t.RankId = 220 ';
            }
            else{
                if($this->clType === 'dynamic'){
                    $sqlFromBase = 'INNER JOIN omoccurrences AS o ON t.tid = o.tid ';
                }
                else{
                    $sqlFromBase = 'INNER JOIN fmchklsttaxalink AS clk ON t.tid = clk.tid ';
                }
                if($this->clType === 'dynamic'){
                    $sqlWhere = 'WHERE t.RankId = 220 AND (' .$this->searchterms. ') ';
                }
                else{
                    $sqlWhere = 'WHERE clk.clid = ' .$this->clid. ' AND t.RankId = 220 ';
                }
            }
            if($this->commonDisplay){
                $sqlFromBase .= 'LEFT JOIN taxavernaculars AS v ON t.tid = v.tid ';
                if($this->langArr){
                    $sqlWhere .= "AND (v.Language IN('".implode("','",$this->langArr)."') OR ISNULL(v.Language)) ";
                }
            }
            if($this->taxonFilter && $this->taxonFilter !== 'All Species'){
                $sqlWhere .= 'AND ((t.family = "'.$this->taxonFilter.'") OR (t.UnitName1 = "'.$this->taxonFilter.'")) ';
            }

            $count = 0;
            if($this->charArr){
                foreach($this->charArr as $cid => $states){
                    $count++;
                    $sqlFromBase .= 'INNER JOIN kmdescr AS D'.$count.' ON t.TID = D'.$count.'.TID) ';
                    $stateStr = '';
                    foreach($states as $cs){
                        $stateStr.=(empty($stateStr)? '' : 'OR '). '(D' .$count.".CS='$cs') ";
                    }
                    $sqlWhere .= ' AND (D' .$count. '.CID=' .$cid. ') AND (' .$stateStr. ') ';
                }
            }
            $sqlFrom = 'FROM ' .str_repeat('(',$count). 'taxa AS t ' .$sqlFromBase;
            $this->sql = $sqlBase.$sqlFrom.$sqlWhere;
        }
    }

    public function getIntroHtml(): string
	{
        $returnStr = "<h2>Please enter a checklist, taxonomic group, and then select 'Submit Criteria'</h2>";
        $returnStr .= 'This key is still in the developmental phase. The application, data model, and actual data will need tuning. ' .
			'The key has been developed to minimize the exclusion of species due to the ' .
            "lack of data. The consequences of this is that a 'shrubs' selection may show non-shrubs until that information is corrected. ".
            "User input is necessary for the key to improve! Please email me with suggestions, comments, or problems: <a href='".$GLOBALS['ADMIN_EMAIL']."'>".$GLOBALS['ADMIN_EMAIL']. '</a><br><br>';
        $returnStr .= '<b>Note:</b> If few morphological characters are displayed for a particular checklist, it is likely due to not yet having enough ' .
			'morphological data compiled for that subset of species. If you would like to help, please email me at the above address. ';
        return $returnStr;
    }
}
