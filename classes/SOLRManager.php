<?php
include_once('OccurrenceManager.php');

class SOLRManager extends OccurrenceManager{

    protected $recordCount = 0;
    protected $sortField1 = '';
    protected $sortField2 = '';
    protected $sortOrder = '';
    protected $qStr = '';
    protected $spatial = false;
    private $checklistTaxaCnt = 0;
    private $iconColors;
    private $collArr = array();
    private $taxaSearchType = 0;

    public function __construct(){
        parent::__construct();
        $this->iconColors = array('fc6355','5781fc','fcf357','00e13c','e14f9e','55d7d7','ff9900','7e55fc');
    }

    public function getMaxCnt($geo = false){
        global $SOLR_URL;
        if($geo) {
            $this->setSpatial();
        }
        $solrWhere = $this->getSOLRWhere();
        $solrGeoWhere = $this->getSOLRGeoWhere();
        $solrURL = $SOLR_URL.'/select?'.($solrWhere?'q='.$solrWhere:'').($solrGeoWhere?'&fq='.$solrGeoWhere:'');
        $solrURL .= '&rows=1&start=1&wt=json';
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        return $solrArr['response']['numFound'];
    }

    public function getTaxaArr(): array
    {
        global $SOLR_URL;
        $cnt = $this->getMaxCnt();
        $solrWhere = $this->getSOLRWhere();
        $solrGeoWhere = $this->getSOLRGeoWhere();
        $solrURLpre = $SOLR_URL.'/select?';
        $solrURLsuf = '&rows='.$cnt.'&start=1&fl=family,tidinterpreted,sciname,accFamily&group=true&group.field=familyscinamecode&wt=json';
        $solrURL = $solrURLpre.($solrWhere?'q='.$solrWhere:'').($solrGeoWhere?'&fq='.$solrGeoWhere:'').$solrURLsuf;
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        return $solrArr['grouped']['familyscinamecode']['groups'];
    }

    public function getOccArr($geo = false): array{
        global $SOLR_URL;
        $returnArr = array();
        $cnt = $this->getMaxCnt();
        $solrWhere = $this->getSOLRWhere($geo);
        $solrGeoWhere = $this->getSOLRGeoWhere();
        $solrURLpre = $SOLR_URL.'/select?';
        $solrURLsuf = '&rows='.$cnt.'&start=0&fl=occid&wt=json';
        $solrURL = $solrURLpre.($solrWhere?'q='.$solrWhere:'').($solrGeoWhere?'&fq='.$solrGeoWhere:'').$solrURLsuf;
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $recArr = $solrArr['response']['docs'];
        foreach($recArr as $k){
            $returnArr[] = $k['occid'];
        }

        return $returnArr;
    }

    public function getRecordArr($pageRequest,$cntPerPage){
        global $SOLR_URL;
        $canReadRareSpp = false;
        $bottomLimit = ($pageRequest - 1)*$cntPerPage;
        $solrWhere = $this->getSOLRWhere();
        $solrGeoWhere = $this->getSOLRGeoWhere();
        $solrURLpre = $SOLR_URL.'/select?';
        if($this->sortField1 || $this->sortField2 || $this->sortOrder){
            $sortArr = array();
            $sortFields = array('Collection' => 'CollectionName','Catalog Number' => 'catalogNumber','Family' => 'family',
                'Scientific Name' => 'sciname','Collector' => 'recordedBy','Number' => 'recordNumber','Event Date' => 'eventDate',
                'Individual Count' => 'individualCount','Life Stage' => 'lifeStage','Sex' => 'sex',
                'Country' => 'country','State/Province' => 'StateProvince','County' => 'county','Elevation' => 'minimumElevationInMeters');
            if($this->sortField1) {
                $this->sortField1 = $sortFields[$this->sortField1];
            }
            if($this->sortField2) {
                $this->sortField2 = $sortFields[$this->sortField2];
            }
            $solrURLsuf = '&sort=';
            if(!$canReadRareSpp) {
                $sortArr[] = 'localitySecurity asc';
            }
            $sortArr[] = $this->sortField1.' '.$this->sortOrder;
            if($this->sortField2) {
                $sortArr[] = $this->sortField2 . ' ' . $this->sortOrder;
            }
            $solrURLsuf .= implode(',',$sortArr);
        }
        else{
            $solrURLsuf = '&sort=SortSeq asc,CollectionName asc,sciname asc,';
            if(!$canReadRareSpp) {
                $solrURLsuf .= 'localitySecurity asc,';
            }
            $solrURLsuf .= 'recordedBy asc,recordNumber asc';
        }
        $solrURLsuf .= '&rows='.$cntPerPage.'&start='.$bottomLimit.'&wt=json';
        $solrURL = $solrURLpre.($solrWhere?'q='.$solrWhere:'').($solrGeoWhere?'&fq='.$solrGeoWhere:'').$solrURLsuf;
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $this->recordCount = $solrArr['response']['numFound'];
        return $solrArr['response']['docs'];
    }

    public function getGeoArr($pageRequest,$cntPerPage){
        global $SOLR_URL;
        $bottomLimit = 0;
        $solrWhere = $this->getSOLRWhere(true);
        $solrGeoWhere = $this->getSOLRGeoWhere();
        if($pageRequest > 0) {
            $bottomLimit = ($pageRequest - 1) * $cntPerPage;
        }
        $solrURLpre = $SOLR_URL.'/select?';
        $solrURLsuf = '&rows='.$cntPerPage.'&start='.($bottomLimit?:'0');
        $solrURLsuf .= '&fl=occid,recordedBy,recordNumber,displayDate,sciname,family,accFamily,tidinterpreted,decimalLatitude,decimalLongitude,'.
            'localitySecurity,locality,collid,catalogNumber,otherCatalogNumbers,InstitutionCode,CollectionCode,CollectionName&wt=json';
        $solrURL = $solrURLpre.($solrWhere?'q='.$solrWhere:'').($solrGeoWhere?'&fq='.$solrGeoWhere:'').$solrURLsuf;
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $this->recordCount = $solrArr['response']['numFound'];
        return $solrArr['response']['docs'];
    }

    public function checkQuerySecurity($q){
        global $USER_RIGHTS, $IS_ADMIN;
        $canReadRareSpp = false;
        if($USER_RIGHTS){
            if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
                $canReadRareSpp = true;
            }
        }
        if(!$canReadRareSpp){
            if($q === '*:*'){
                $q = '(localitySecurity:0)';
            }
            else{
                $q .= ' AND (localitySecurity:0)';
            }
        }

        return $q;
    }

    public function translateSOLRRecList($sArr): array{
        global $USER_RIGHTS, $IS_ADMIN, $IMAGE_DOMAIN;
        $returnArr = array();
        $canReadRareSpp = false;
        if($USER_RIGHTS){
            if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
                $canReadRareSpp = true;
            }
        }
        foreach($sArr as $k){
            $occId = $k['occid'];
            $collId = $k['collid'];
            $locality = (isset($k['locality'])?$k['locality'][0]:'');
            $locality .= (isset($k['decimalLatitude'])?', '.round((float)$k['decimalLatitude'],5).(isset($k['decimalLongitude'])?' '.round((float)$k['decimalLongitude'],5):''):'');
            $elev = ($k['minimumElevationInMeters'] ?? '');
            $elev .= (isset($k['minimumElevationInMeters'], $k['maximumElevationInMeters']) ?' - ':'');
            $elev .= ($k['maximumElevationInMeters'] ?? '');
            $returnArr[$occId]['collid'] = $collId;
            $returnArr[$occId]['institutioncode'] = ($k['InstitutionCode'] ?? '');
            $returnArr[$occId]['collectioncode'] = ($k['CollectionCode'] ?? '');
            $returnArr[$occId]['collectionname'] = ($k['CollectionName'] ?? '');
            $returnArr[$occId]['collicon'] = (isset($k['collicon'])?$k['collicon'][0]:'');
            $returnArr[$occId]['accession'] = ($k['catalogNumber'] ?? '');
            $returnArr[$occId]['family'] = ($k['family'] ?? '');
            $returnArr[$occId]['sciname'] = ($k['sciname'] ?? '');
            $returnArr[$occId]['tid'] = ($k['tidinterpreted'] ?? '');
            $returnArr[$occId]['author'] = ($k['scientificNameAuthorship'] ?? '');
            $returnArr[$occId]['collector'] = ($k['recordedBy'] ?? '');
            $returnArr[$occId]['country'] = ($k['country'] ?? '');
            $returnArr[$occId]['state'] = ($k['StateProvince'] ?? '');
            $returnArr[$occId]['county'] = ($k['county'] ?? '');
            $returnArr[$occId]['assochost'] = (isset($k['assocverbatimsciname'])?$k['assocverbatimsciname'][0]:'');
            $returnArr[$occId]['observeruid'] = ($k['observeruid'] ?? '');
            $returnArr[$occId]['individualCount'] = ($k['individualCount'] ?? '');
            $returnArr[$occId]['lifeStage'] = ($k['lifeStage'] ?? '');
            $returnArr[$occId]['sex'] = ($k['sex'] ?? '');
            $localitySecurity = ($k['localitySecurity'] ?? false);
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'], true))
                || (array_key_exists('RareSppReader', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['RareSppReader'], true))){
                $returnArr[$occId]['locality'] = str_replace('.,',',',$locality);
                $returnArr[$occId]['collnumber'] = ($k['recordNumber'] ?? '');
                $returnArr[$occId]['habitat'] = (isset($k['habitat'])?$k['habitat'][0]:'');
                $returnArr[$occId]['date'] = ($k['displayDate'] ?? '');
                $returnArr[$occId]['eventDate'] = ($k['eventDate'] ?? '');
                $returnArr[$occId]['elev'] = $elev;
            }
            else{
                $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                if(isset($k['localitySecurityReason'])){
                    $securityStr .= $k['localitySecurityReason'];
                }
                else{
                    $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                }
                $returnArr[$occId]['locality'] = $securityStr.'</span>';
            }
            if(isset($k['thumbnailurl'])){
                $tnUrl = $k['thumbnailurl'][0];
                if($IMAGE_DOMAIN && strpos($tnUrl, '/') === 0) {
                    $tnUrl = $IMAGE_DOMAIN . $tnUrl;
                }
                $returnArr[$occId]['img'] = $tnUrl;
            }
        }

        return $returnArr;
    }

    public function translateSOLRMapRecList($sArr): array{
        global $USER_RIGHTS, $IS_ADMIN;
        $returnArr = array();
        $canReadRareSpp = false;
        if($USER_RIGHTS){
            if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
                $canReadRareSpp = true;
            }
        }
        foreach($sArr as $k){
            $occId = $k['occid'];
            $collId = $k['collid'];
            $locality = (isset($k['locality'])?$k['locality'][0]:'');
            $locality .= (isset($k['decimalLatitude'])?', '.round((float)$k['decimalLatitude'],5).(isset($k['decimalLongitude'])?' '.round((float)$k['decimalLongitude'],5):''):'');
            $localitySecurity = ($k['LocalitySecurity'] ?? 0);
            $returnArr[$occId]['i'] = ($k['InstitutionCode'] ?? '');
            $returnArr[$occId]['cat'] = ($k['catalogNumber'] ?? '');
            $returnArr[$occId]['c'] = ($k['recordedBy'] ?? '').(isset($k['recordNumber'])?' '.$k['recordNumber']:'');
            $returnArr[$occId]['e'] = ($k['displayDate'] ?? '');
            $returnArr[$occId]['f'] = ($k['family'] ?? '');
            $returnArr[$occId]['s'] = ($k['sciname'] ?? '');
            $returnArr[$occId]['lat'] = ($k['decimalLatitude'] ?? '');
            $returnArr[$occId]['lon'] = ($k['decimalLongitude'] ?? '');
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'], true))
                || (array_key_exists('RareSppReader', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['RareSppReader'], true))){
                $returnArr[$occId]['l'] = str_replace('.,',',',$locality);
            }
            else{
                $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                if(isset($k['localitySecurityReason'])){
                    $securityStr .= $k['localitySecurityReason'];
                }
                else{
                    $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                }
                $returnArr[$occId]['l'] = $securityStr.'</span>';
            }
        }

        return $returnArr;
    }


    public function translateSOLRGeoCollList($sArr): array{
        global $USER_RIGHTS, $IS_ADMIN;
        $returnArr = array();
        $color = 'e69e67';
        foreach($sArr as $k){
            $canReadRareSpp = false;
            $collid = $this->xmlentities($k['collid']);
            $localitySecurity = $k['localitySecurity'];
            if($USER_RIGHTS){
                if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('RareSppAdmin',$USER_RIGHTS) || array_key_exists('RareSppReadAll',$USER_RIGHTS)){
                    $canReadRareSpp = true;
                }
                elseif(array_key_exists('RareSppReader',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['RareSppReader'], true)){
                    $canReadRareSpp = true;
                }
            }
            $decLat = $k['decimalLatitude'];
            $decLong = $k['decimalLongitude'];
            if((($decLong <= 180 && $decLong >= -180) && ($decLat <= 90 && $decLat >= -90)) && ($canReadRareSpp || !$localitySecurity)){
                $occId = $k['occid'];
                $collName = $k['CollectionName'];
                $tidInterpreted = (isset($k['tidinterpreted'])?$this->xmlentities($k['tidinterpreted']):'');
                $identifier = ($k['recordedBy'] ?? '');
                $identifier .= ((isset($k['recordNumber']) || isset($k['displayDate']))?' ':'');
                $identifier .= ((isset($k['recordNumber']) && !isset($k['displayDate']))?$k['recordNumber']:'');
                $identifier .= ((!isset($k['recordNumber']) && isset($k['displayDate']))?$k['displayDate']:'');
                $latLngStr = $decLat. ',' .$decLong;
                $returnArr[$collName][$occId]['latLngStr'] = $latLngStr;
                $returnArr[$collName][$occId]['collid'] = $collid;
                $tidcode = strtolower(str_replace(' ', '',$tidInterpreted.$k['sciname']));
                $tidcode = preg_replace('/[^A-Za-z0-9 ]/', '',$tidcode);
                $returnArr[$collName][$occId]['namestring'] = $this->xmlentities($tidcode);
                $returnArr[$collName][$occId]['tidinterpreted'] = $tidInterpreted;
                if(isset($k['accFamily'])){
                    $returnArr[$collName][$occId]['family'] = $this->xmlentities($k['accFamily']);
                }
                elseif(isset($k['family'])){
                    $returnArr[$collName][$occId]['family'] = $this->xmlentities($k['family']);
                }
                else{
                    $returnArr[$collName][$occId]['family'] = '';
                }
                if($returnArr[$collName][$occId]['family']){
                    $returnArr[$collName][$occId]['family'] = strtoupper($returnArr[$collName][$occId]['family']);
                }
                else{
                    $returnArr[$collName][$occId]['family'] = 'undefined';
                }
                $returnArr[$collName][$occId]['sciname'] = ($k['sciname'] ?? '');
                $returnArr[$collName][$occId]['identifier'] = $this->xmlentities($identifier);
                $returnArr[$collName][$occId]['institutioncode'] = $this->xmlentities($k['InstitutionCode']);
                $returnArr[$collName][$occId]['collectioncode'] = $this->xmlentities($k['CollectionCode']);
                $returnArr[$collName][$occId]['catalognumber'] = $this->xmlentities($k['catalogNumber']);
                $returnArr[$collName][$occId]['othercatalognumbers'] = $this->xmlentities($k['otherCatalogNumbers']);
                $returnArr[$collName]['color'] = $color;
            }
        }
        if(isset($returnArr['undefined'])){
            $returnArr['undefined']['color'] = $color;
        }

        return $returnArr;
    }

    public function translateSOLRGeoTaxaList($sArr): array{
        global $USER_RIGHTS, $IS_ADMIN;
        $returnArr = array();
        $taxaMapper = array();
        $taxaMapper['undefined'] = 'undefined';
        $cnt = 0;
        foreach($this->taxaArr as $key => $valueArr){
            $coordArr[$key] = Array('color' => $this->iconColors[$cnt%7]);
            $cnt++;
            $taxaMapper[$key] = $key;
            if(array_key_exists('scinames',$valueArr)){
                $scinames = $valueArr['scinames'];
                foreach($scinames as $sciname){
                    $taxaMapper[$sciname] = $key;
                }
            }
            if(array_key_exists('synonyms',$valueArr)){
                $synonyms = $valueArr['synonyms'];
                foreach($synonyms as $syn){
                    $taxaMapper[$syn] = $key;
                }
            }
        }
        foreach($sArr as $k){
            $canReadRareSpp = false;
            $collid = $k['collid'];
            $localitySecurity = $k['localitySecurity'];
            if($USER_RIGHTS){
                if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('RareSppAdmin',$USER_RIGHTS) || array_key_exists('RareSppReadAll',$USER_RIGHTS)){
                    $canReadRareSpp = true;
                }
                elseif(array_key_exists('RareSppReader',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['RareSppReader'], true)){
                    $canReadRareSpp = true;
                }
            }
            $decLat = $k['decimalLatitude'];
            $decLong = $k['decimalLongitude'];
            if((($decLong <= 180 && $decLong >= -180) && ($decLat <= 90 && $decLat >= -90)) && ($canReadRareSpp || !$localitySecurity)){
                $occId = $k['occid'];
                $sciName = $k['sciname'];
                $family = $k['family'];
                $identifier = ($k['recordedBy'] ?? '');
                $identifier .= ((isset($k['recordNumber']) || isset($k['displayDate']))?' ':'');
                $identifier .= ((isset($k['recordNumber']) && !isset($k['displayDate']))?$k['recordNumber']:'');
                $identifier .= ((!isset($k['recordNumber']) && isset($k['displayDate']))?$k['displayDate']:'');
                $latLngStr = $decLat. ',' .$decLong;
                if(!array_key_exists($sciName,$taxaMapper)){
                    foreach($taxaMapper as $keySciname => $v){
                        if(strpos($sciName,$keySciname) === 0){
                            $sciName = $keySciname;
                            break;
                        }
                    }
                    if(!array_key_exists($sciName,$taxaMapper) && array_key_exists($family,$taxaMapper)){
                        $sciName = $family;
                    }
                }
                if(!array_key_exists($sciName,$taxaMapper)) {
                    $sciName = 'undefined';
                }
                $returnArr[$taxaMapper[$sciName]][$occId]['collid'] = $collid;
                $returnArr[$taxaMapper[$sciName]][$occId]['latLngStr'] = $latLngStr;
                $returnArr[$taxaMapper[$sciName]][$occId]['identifier'] = $identifier;
                $returnArr[$taxaMapper[$sciName]][$occId]['tidinterpreted'] = $k['tidinterpreted'];
                $returnArr[$taxaMapper[$sciName]][$occId]['institutioncode'] = $k['InstitutionCode'];
                $returnArr[$taxaMapper[$sciName]][$occId]['collectioncode'] = $k['CollectionCode'];
                $returnArr[$taxaMapper[$sciName]][$occId]['catalognumber'] = $k['catalogNumber'];
                $returnArr[$taxaMapper[$sciName]][$occId]['othercatalognumbers'] = $k['otherCatalogNumbers'];
            }
        }
        if(isset($returnArr['undefined'])){
            $returnArr['undefined']['color'] = $this->iconColors[7];
        }

        return $returnArr;
    }

    public function translateSOLRTaxaList($sArr): array{
        $returnArr = array();
        $this->checklistTaxaCnt = 0;
        foreach($sArr as $k){
            $family = '';
            if(isset($k['doclist']['docs'][0]['accFamily'])){
                $family = strtoupper($k['doclist']['docs'][0]['accFamily']);
            }
            elseif(isset($k['doclist']['docs'][0]['family'])){
                $family = strtoupper($k['doclist']['docs'][0]['family']);
            }
            if(!$family) {
                $family = 'undefined';
            }
            $returnArr[$family][] = $k['doclist']['docs'][0]['sciname'];
            $this->checklistTaxaCnt++;
        }

        return $returnArr;
    }

    public function getSOLRTidList($sArr): array{
        $returnArr = array();
        foreach($sArr as $k){
            if(isset($k['doclist']['docs'][0]['tidinterpreted']) && !in_array($k['doclist']['docs'][0]['tidinterpreted'], $returnArr, true)){
                $returnArr[] = $k['doclist']['docs'][0]['tidinterpreted'];
            }
        }
        return $returnArr;
    }

    public function getRecordCnt(): int{
        return $this->recordCount;
    }

    public function getChecklistTaxaCnt(): int{
        return $this->checklistTaxaCnt;
    }

    public function setCollArr($cArr): void{
        $this->collArr = $cArr;
    }

    public function setSpatial(): void{
        $this->spatial = true;
    }

    public function setQStr($str): void{
        $this->qStr = $str;
    }

    public function setSorting($sf1,$sf2,$so): void{
        $this->sortField1 = $sf1;
        $this->sortField2 = $sf2;
        $this->sortOrder = $so;
    }

    public function updateSOLR(): void{
        global $SOLR_URL;
        $needsFullUpdate = $this->checkLastSOLRUpdate();
        $command = ($needsFullUpdate?'dock':'delta-import');
        file_get_contents($SOLR_URL.'/dataimport?command='.$command.'&clean=false');
        if($needsFullUpdate){
            $this->resetSOLRInfoFile();
        }
    }

    public function deleteSOLRDocument($occid): void{
        global $SOLR_URL;
        $pArr = array();
        if(!is_array($occid) || count($occid) < 1000){
            if(is_array($occid)){
                $occidStr = '('.implode(' ',$occid).')';
            }
            else{
                $occidStr = '('.$occid.')';
            }
            $pArr['commit'] = 'true';
            $pArr['stream.body'] = '<delete><query>(occid:'.$occidStr.')</query></delete>';

            $headers = array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Content-Length: '.strlen(http_build_query($pArr))
            );
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $SOLR_URL.'/update',
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_POSTFIELDS => http_build_query($pArr),
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            curl_close($ch);
        }
        else{
            $delCnt = count($occid);
            $i = 0;
            do{
                $subArr = array_slice($occid,$i,1000);
                $occidStr = '('.implode(' ',$subArr).')';
                $pArr['commit'] = 'true';
                $pArr['stream.body'] = '<delete><query>(occid:'.$occidStr.')</query></delete>';

                $headers = array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'Content-Length: '.strlen(http_build_query($pArr))
                );
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => $SOLR_URL.'/update',
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_POSTFIELDS => http_build_query($pArr),
                    CURLOPT_RETURNTRANSFER => true
                );
                curl_setopt_array($ch, $options);
                curl_close($ch);
                $i += 1000;
            } while($i < $delCnt);
        }
    }

    public function cleanSOLRIndex($collid): void{
        global $SOLR_URL;
        $SOLROccArr = array();
        $mysqlOccArr = array();
        $solrWhere = 'q=(collid:('.$collid.'))';
        $solrURL = $SOLR_URL.'/select?'.$solrWhere;
        $solrURL .= '&rows=1&start=1&wt=json';
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $cnt = $solrArr['response']['numFound'];
        $occURL = $SOLR_URL.'/select?'.$solrWhere.'&rows='.$cnt.'&start=1&fl=occid&wt=json';
        //echo str_replace(' ','%20',$occURL);
        $solrOccArrJson = file_get_contents(str_replace(' ','%20',$occURL));
        $solrOccArr = json_decode($solrOccArrJson, true);
        $recArr = $solrOccArr['response']['docs'];
        foreach($recArr as $k){
            $SOLROccArr[] = $k['occid'];
        }
        $sql = 'SELECT occid FROM omoccurrences WHERE collid = '.$collid;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $mysqlOccArr[] = $r->occid;
            }
        }
        $delOccArr = array_diff($SOLROccArr,$mysqlOccArr);
        if($delOccArr){
            $this->deleteSOLRDocument($delOccArr);
        }
        echo '<li>...Complete!</li>';
    }

    private function checkLastSOLRUpdate(): bool{
        global $SERVER_ROOT, $SOLR_FULL_IMPORT_INTERVAL;
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:sP');
        $needsUpdate = false;

        if(file_exists($SERVER_ROOT.'/temp/data/solr.json')){
            $infoArr = json_decode(file_get_contents($SERVER_ROOT.'/temp/data/solr.json'), true);
            $lastDate = ($infoArr['lastFullImport'] ?? '');
            if($lastDate){
                try {
                    $lastDate = new DateTime($lastDate);
                } catch (Exception $e) {}
                try {
                    $now = new DateTime($now);
                } catch (Exception $e) {}
                $interval = $now->diff($lastDate);
                $hours = $interval->h;
                $hours += ($interval->days * 24);
                if($hours >= $SOLR_FULL_IMPORT_INTERVAL){
                    $needsUpdate = true;
                }
            }
            else{
                $needsUpdate = true;
            }
        }
        else{
            $this->resetSOLRInfoFile();
        }

        return $needsUpdate;
    }

    private function resetSOLRInfoFile(): void{
        global $SERVER_ROOT;
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:sP');
        $infoArr = array();

        if(file_exists($SERVER_ROOT.'/temp/data/solr.json')){
            $infoArr = json_decode(file_get_contents($SERVER_ROOT.'/temp/data/solr.json'), true);
            unlink($SERVER_ROOT.'/temp/data/solr.json');
        }
        $infoArr['lastFullImport'] = $now;

        $fp = fopen($SERVER_ROOT.'/temp/data/solr.json', 'wb');
        fwrite($fp, json_encode($infoArr));
        fclose($fp);
    }

    public function getSOLRWhere($spatial = false): string
    {
        $qArr = array();
        if(array_key_exists('clid',$this->searchTermsArr) && $this->searchTermsArr['clid']){
            $value = $this->searchTermsArr['clid'];
            if(substr($value,-1) === ','){
                $value = substr($value,0,-1);
            }
            $qArr[] = '(CLID:(' .str_replace(',',' ',$value). '))';
        }
        if(array_key_exists('db',$this->searchTermsArr) && $this->searchTermsArr['db']){
            if($this->searchTermsArr['db'] !== 'all'){
                $value = $this->searchTermsArr['db'];
                if(substr($value,-1) === ','){
                    $value = substr($value,0,-1);
                }
                $qArr[] = '(collid:(' .str_replace(',',' ',$value). '))';
            }
        }
        if(array_key_exists('taxa',$this->searchTermsArr)){
            $sqlWhereTaxa = '';
            $useThes = (array_key_exists('usethes',$this->searchTermsArr)?$this->searchTermsArr['usethes']:0);
            $this->taxaSearchType = (int)$this->searchTermsArr['taxontype'];
            $taxaArr = explode(';',trim($this->searchTermsArr['taxa']));
            $this->taxaArr = array();
            foreach($taxaArr as $sName){
                $this->taxaArr[trim($sName)] = array();
            }
            if($this->taxaSearchType === 5){
                $this->setSciNamesByVerns();
            }
            elseif($useThes){
                $this->setSynonyms();
            }

            foreach($this->taxaArr as $key => $valueArray){
                if($this->taxaSearchType === 4){
                    $rs1 = $this->conn->query("SELECT ts.tidaccepted FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid WHERE (t.sciname = '".$key."')");
                    if($r1 = $rs1->fetch_object()){
                        $sqlWhereTaxa = 'OR (parenttid:'.$r1->tidaccepted.') ';
                    }
                }
                else{
                    if($this->taxaSearchType === 5){
                        $famArr = array();
                        if(array_key_exists('families',$valueArray)){
                            $famArr = $valueArray['families'];
                        }
                        if(array_key_exists('tid',$valueArray)){
                            $tidArr = $valueArray['tid'];
                            $sql = 'SELECT DISTINCT t.sciname '.
                                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                                'WHERE t.rankid = 140 AND e.taxauthid = 1 AND e.parenttid IN('.implode(',',$tidArr).')';
                            $rs = $this->conn->query($sql);
                            while($r = $rs->fetch_object()){
                                $famArr[] = $r->family;
                            }
                        }
                        if($famArr){
                            $famArr = array_unique($famArr);
                            $sqlWhereTaxa .= 'OR (family:('.implode(' ',$famArr).')) ';
                        }
                        if(array_key_exists('scinames',$valueArray)){
                            foreach($valueArray['scinames'] as $sciName){
                                $sqlWhereTaxa .= 'OR ((sciname:' .str_replace(' ','\ ',$sciName). ') OR (sciname:' .str_replace(' ','\ ',$sciName)."\ *)) ";
                            }
                        }
                    }
                    else{
                        if($this->taxaSearchType === 2 || ($this->taxaSearchType === 1 && (strtolower(substr($key,-5)) === 'aceae' || strtolower(substr($key,-4)) === 'idae'))){
                            $sqlWhereTaxa .= 'OR (family:'.$key.') ';
                        }
                        if($this->taxaSearchType === 3 || ($this->taxaSearchType === 1 && strtolower(substr($key,-5)) !== 'aceae' && strtolower(substr($key,-4)) !== 'idae')){
                            $sqlWhereTaxa .= 'OR ((sciname:' .str_replace(' ','\ ',$key). ') OR (sciname:' .str_replace(' ','\ ',$key)."\ *)) ";
                        }
                    }
                    if(array_key_exists('synonyms',$valueArray)){
                        $synArr = $valueArray['synonyms'];
                        if($synArr){
                            if($this->taxaSearchType === 1 || $this->taxaSearchType === 2 || $this->taxaSearchType === 5){
                                foreach($synArr as $synTid => $sciName){
                                    if(strpos($sciName,'aceae') || strpos($sciName,'idae')){
                                        $sqlWhereTaxa .= 'OR (family:'.$sciName.') ';
                                    }
                                }
                            }
                            $sqlWhereTaxa .= 'OR (tidinterpreted:('.implode(' ',array_keys($synArr)).')) ';
                        }
                    }
                }
            }
            $qArr[] = '(' .substr($sqlWhereTaxa,3). ')';
        }
        if(array_key_exists('country',$this->searchTermsArr) && $this->searchTermsArr['country']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['country']);
            $countryArr = explode(';',$searchStr);
            $tempArr = array();
            foreach($countryArr as $k => $value){
                if($value === 'NULL'){
                    $countryArr[$k] = '-country:["" TO *]';
                    $tempArr[] = '(Country IS NULL)';
                }
                else{
                    $tempArr[] = '(country:"'.trim($value).'")';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$countryArr);
        }
        if(array_key_exists('state',$this->searchTermsArr) && $this->searchTermsArr['state']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['state']);
            $stateAr = explode(';',$searchStr);
            $tempArr = array();
            foreach($stateAr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '-StateProvince:["" TO *]';
                    $stateAr[$k] = 'State IS NULL';
                }
                else{
                    $tempArr[] = '(StateProvince:"'.trim($value).'")';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$stateAr);
        }
        if(array_key_exists('county',$this->searchTermsArr) && $this->searchTermsArr['county']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['county']);
            $countyArr = explode(';',$searchStr);
            $tempArr = array();
            foreach($countyArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '-county:["" TO *]';
                    $countyArr[$k] = 'County IS NULL';
                }
                else{
                    $value = trim(str_ireplace(' county',' ',$value));
                    $tempArr[] = '(county:'.str_replace(' ','\ ',trim($value)).'*)';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$countyArr);
        }
        if(array_key_exists('local',$this->searchTermsArr) && $this->searchTermsArr['local']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['local']);
            $localArr = explode(';',$searchStr);
            $tempArr = array();
            foreach($localArr as $k => $value){
                if(strpos($value,' ')){
                    $wordArr = explode(' ',$value);
                    $tempStrArr = array();
                    foreach($wordArr as $w => $word){
                        $tempStrArr[] = '((municipality:'.trim($word).'*) OR (locality:*'.trim($word).'*))';
                    }
                    $tempArr[] = '('.implode(' AND ',$tempStrArr).')';
                }
                else if($value === 'NULL'){
                    $tempArr[] = '-locality:["" TO *]';
                    $localArr[$k] = 'Locality IS NULL';
                }
                else{
                    $tempArr[] = '((municipality:'.trim($value).'*) OR (locality:*'.trim($value).'*))';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$localArr);
        }
        if((array_key_exists('elevlow',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevlow'])) || (array_key_exists('elevhigh',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevhigh']))){
            $elevlow = 0;
            $elevhigh = 30000;
            if(array_key_exists('elevlow',$this->searchTermsArr)){
                $elevlow = $this->searchTermsArr['elevlow'];
            }
            if(array_key_exists('elevhigh',$this->searchTermsArr)){
                $elevhigh = $this->searchTermsArr['elevhigh'];
            }
            $whereStr = 'AND ((minimumElevationInMeters:['.$elevlow.' TO *] AND maximumElevationInMeters:[* TO '.$elevhigh.']) OR '.
                '(-maximumElevationInMeters:[* TO *] AND minimumElevationInMeters:['.$elevlow.' TO *] AND minimumElevationInMeters:[* TO '.$elevhigh.']))';
            $qArr[] = '('.$whereStr.')';
        }
        if(array_key_exists('assochost',$this->searchTermsArr) && $this->searchTermsArr['assochost']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['assochost']);
            $hostAr = explode(';',$searchStr);
            $tempArr = array();
            foreach($hostAr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '((assocrelationship:"host") AND (-assocverbatimsciname:["" TO *]))';
                    $hostAr[$k] = 'Host IS NULL';
                }
                else{
                    $tempArr[] = '((assocrelationship:"host") AND (assocverbatimsciname:*'.str_replace(' ','\ ',trim($value)).'*))';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$hostAr);
        }
        if(array_key_exists('collector',$this->searchTermsArr) && $this->searchTermsArr['collector']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['collector']);
            $collectorArr = explode(';',$searchStr);
            $tempArr = array();
            if(count($collectorArr) === 1){
                if($collectorArr[0] === 'NULL'){
                    $tempArr[] = '(-recordedBy:["" TO *])';
                    $collectorArr[] = 'Collector IS NULL';
                }
                else{
                    $tempInnerArr = array();
                    $collValueArr = explode(' ',trim($collectorArr[0]));
                    foreach($collValueArr as $collV){
                        $tempInnerArr[] = '(recordedBy:*'.str_replace(' ','\ ',$collV).'*) ';
                    }
                    $tempArr[] = implode(' AND ', $tempInnerArr);
                }
            }
            elseif(count($collectorArr) > 1){
                $tempArr[] = '(recordedBy:('.implode(' ',$collectorArr).')) ';
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(', ',$collectorArr);
        }
        if(array_key_exists('collnum',$this->searchTermsArr) && $this->searchTermsArr['collnum']){
            $collNumArr = explode(';',$this->searchTermsArr['collnum']);
            $rnWhere = '';
            foreach($collNumArr as $v){
                $v = trim($v);
                if($p = strpos($v,' - ')){
                    $term1 = trim(substr($v,0,$p));
                    $term2 = trim(substr($v,$p+3));
                    if(is_numeric($term1) && is_numeric($term2)){
                        $rnWhere .= 'OR (recordNumber:['.$term1.' TO '.$term2.'])';
                    }
                    else{
                        if(strlen($term2) > strlen($term1)) {
                            $term1 = str_pad($term1, strlen($term2), '0', STR_PAD_LEFT);
                        }
                        $rnWhere .= 'OR (recordNumber:["'.$term1.'" TO "'.$term2.'"])';
                    }
                }
                else{
                    $rnWhere .= 'OR (recordNumber:"'.$v.'") ';
                }
            }
            if($rnWhere){
                $qArr[] = '(' .substr($rnWhere,3). ')';
                $this->localSearchArr[] = implode(', ',$collNumArr);
            }
        }
        if(array_key_exists('eventdate1',$this->searchTermsArr) && $this->searchTermsArr['eventdate1']){
            $dateArr = array();
            if(strpos($this->searchTermsArr['eventdate1'],' to ')){
                $dateArr = explode(' to ',$this->searchTermsArr['eventdate1']);
            }
            elseif(strpos($this->searchTermsArr['eventdate1'],' - ')){
                $dateArr = explode(' - ',$this->searchTermsArr['eventdate1']);
            }
            else{
                $dateArr[] = $this->searchTermsArr['eventdate1'];
                if(isset($this->searchTermsArr['eventdate2'])){
                    $dateArr[] = $this->searchTermsArr['eventdate2'];
                }
            }
            if($dateArr[0] === 'NULL'){
                $qArr[] = '(-eventDate:["" TO *])';
                $this->localSearchArr[] = 'Date IS NULL';
            }
            elseif($eDate1 = $this->formatDate($dateArr[0])){
                $eDate2 = (count($dateArr)>1?$this->formatDate($dateArr[1]):'');
                if($eDate2){
                    $qArr[] = '(eventDate:['.$eDate1.'T00:00:00Z TO '.$eDate2.'T23:59:59.999Z])';
                }
                else if(substr($eDate1,-5) === '00-00'){
                    $qArr[] = '(coll_year:'.substr($eDate1,0,4).')';
                }
                elseif(substr($eDate1,-2) === '00'){
                    $qArr[] = '((coll_year:'.substr($eDate1,0,4).') AND (coll_month:'.substr($eDate1,5,7).'))';
                }
                else{
                    $qArr[] = '(eventDate:['.$eDate1.'T00:00:00Z TO '.$eDate1.'T23:59:59.999Z])';
                }
                $this->localSearchArr[] = $this->searchTermsArr['eventdate1'].(isset($this->searchTermsArr['eventdate2'])?' to '.$this->searchTermsArr['eventdate2']:'');
            }
        }
        if(array_key_exists('occurrenceRemarks',$this->searchTermsArr) && $this->searchTermsArr['occurrenceRemarks']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['occurrenceRemarks']);
            $remarksArr = explode(';',$searchStr);
            $tempArr = array();
            foreach($remarksArr as $k => $value){
                if(strpos($value,' ')){
                    $wordArr = explode(' ',$value);
                    $tempStrArr = array();
                    foreach($wordArr as $w => $word){
                        $tempStrArr[] = '((occurrenceRemarks:*'.trim($word).'*))';
                    }
                    $tempArr[] = '('.implode(' AND ',$tempStrArr).')';
                }
                else if($value === 'NULL'){
                    $tempArr[] = '-occurrenceRemarks:["" TO *]';
                    $remarksArr[$k] = 'Occurrence Remarks IS NULL';
                }
                else{
                    $tempArr[] = '((occurrenceRemarks:*'.trim($value).'*))';
                }
            }
            $qArr[] = '('.implode(' OR ',$tempArr).')';
            $this->localSearchArr[] = implode(' OR ',$remarksArr);
        }
        if(array_key_exists('catnum',$this->searchTermsArr) && $this->searchTermsArr['catnum']){
            $catStr = $this->searchTermsArr['catnum'];
            $includeOtherCatNum = array_key_exists('othercatnum',$this->searchTermsArr)?true:false;
            $catArr = explode(',',str_replace(';',',',$catStr));
            $betweenFrag = array();
            $inFrag = array();
            foreach($catArr as $v){
                if($p = strpos($v,' - ')){
                    $term1 = trim(substr($v,0,$p));
                    $term2 = trim(substr($v,$p+3));
                    if(is_numeric($term1) && is_numeric($term2)){
                        $betweenFrag[] = '(catalogNumber:['.$term1.' TO '.$term2.'])';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(otherCatalogNumbers:['.$term1.' TO '.$term2.'])';
                        }
                    }
                    else{
                        $catTerm = '(catalogNumber:["'.$term1.'" TO "'.$term2.'"])';
                        $betweenFrag[] = '('.$catTerm.')';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(otherCatalogNumbers:["'.$term1.'" TO "'.$term2.'"])';
                        }
                    }
                }
                else{
                    $vStr = trim($v);
                    $inFrag[] = $vStr;
                }
            }
            $catWhere = '';
            if($betweenFrag){
                $catWhere .= 'OR '.implode(' OR ',$betweenFrag);
            }
            if($inFrag){
                $catWhere .= 'OR (catalogNumber:("'.implode('" "',$inFrag).'")) ';
                if($includeOtherCatNum){
                    $catWhere .= 'OR (otherCatalogNumbers:("'.implode('" "',$inFrag).'")) ';
                }
            }
            $qArr[] = '('.substr($catWhere,3).')';
            $this->localSearchArr[] = $this->searchTermsArr['catnum'];
        }
        if(array_key_exists('typestatus',$this->searchTermsArr)){
            $qArr[] = '(typeStatus:[* TO *])';
            $this->localSearchArr[] = 'is type';
        }
        if(array_key_exists('hasimages',$this->searchTermsArr)){
            $qArr[] = '(imgid:[* TO *])';
            $this->localSearchArr[] = 'has images';
        }
        if(array_key_exists('hasgenetic',$this->searchTermsArr)){
            $qArr[] = '(resourcename:[* TO *])';
            $this->localSearchArr[] = 'has genetic data';
        }
        if(array_key_exists('upperlat',$this->searchTermsArr) && $this->searchTermsArr['upperlat']){
            $whereStr = '((decimalLatitude:['.$this->searchTermsArr['bottomlat'].' TO *] AND decimalLatitude:[* TO '.$this->searchTermsArr['upperlat'].']) AND '.
                '(decimalLongitude:['.$this->searchTermsArr['leftlong'].' TO *] AND decimalLongitude:[* TO '.$this->searchTermsArr['rightlong'].']))';
            $qArr[] = '('.$whereStr.')';
        }
        if(array_key_exists('pointlat',$this->searchTermsArr) && $this->searchTermsArr['pointlat']){
            $whereStr = 'geo:{!geofilt sfield=geo pt='.$this->searchTermsArr['pointlat'].','.$this->searchTermsArr['pointlong'].' d='.$this->searchTermsArr['groundradius'].'}';
            $qArr[] = $whereStr;
            $this->localSearchArr[] = 'Point radius: ' .$this->searchTermsArr['pointlat']. ', ' .$this->searchTermsArr['pointlong']. ', within ' .(array_key_exists('radiustemp',$this->searchTermsArr)?$this->searchTermsArr['radiustemp']:$this->searchTermsArr['groundradius']). ' '.(array_key_exists('radiusunits',$this->searchTermsArr)?$this->searchTermsArr['radiusunits']:'km');
        }
        if(array_key_exists('circleArr',$this->searchTermsArr) && $this->searchTermsArr['circleArr']){
            $objArr = $this->searchTermsArr['circleArr'];
            if(!is_array($objArr)){
                $objArr = json_decode($objArr, true);
            }
            if($objArr){
                $tempArr = array();
                foreach($objArr as $obj => $oArr){
                    $whereStr = 'geo:{!geofilt sfield=geo pt='.$oArr['pointlat'].','.$oArr['pointlong'].' d='.$oArr['groundradius'].'}';
                    $tempArr[] = $whereStr;
                    $this->localSearchArr[] = 'Point radius: ' .$oArr['pointlat']. ', ' .$oArr['pointlong']. ', within ' .(array_key_exists('radiustemp',$oArr)?$oArr['radiustemp']:$oArr['groundradius']). ' '.(array_key_exists('radiusunits',$oArr)?$oArr['radiusunits']:'km');
                }
                $qArr[] = '('.implode(' OR ',$tempArr).')';
            }
        }
        if($spatial){
            $qArr[] = '(decimalLatitude:[* TO *] AND decimalLongitude:[* TO *])';
        }
        if($qArr){
            $retStr = implode(' AND ',$qArr);
        }
        else{
            $retStr = '*:*';
        }
        return $this->checkQuerySecurity($retStr);
    }

    public function getSOLRGeoWhere(): string
    {
        $fqArr = array();
        if(array_key_exists('polyArr',$this->searchTermsArr) && $this->searchTermsArr['polyArr']){
            $geomArr = $this->searchTermsArr['polyArr'];
            if(!is_array($geomArr)){
                $geomArr = json_decode($geomArr, true);
            }
            if($geomArr){
                foreach($geomArr as $geom){
                    $fqArr[] = 'geo:"Intersects('.$geom.')"';
                }
            }
        }
        return ($fqArr?implode(' OR ',$fqArr):'');
    }

    public function getCloseTaxaMatch($name): array{
        $retArr = array();
        $searchName = trim($name);
        $sql = 'SELECT tid, sciname FROM taxa WHERE soundex(sciname) = soundex("'.$searchName.'") AND sciname != "'.$searchName.'"';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[] = $r->sciname;
            }
        }
        return $retArr;
    }

    private function xmlentities($string){
        return str_replace(array ('&','"',"'",'<','>','?'),array ('&amp;','&quot;','&apos;','&lt;','&gt;','&apos;'),$string);
    }
}
