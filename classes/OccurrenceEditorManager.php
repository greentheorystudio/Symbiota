<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/OccurrenceEditorDeterminations.php');
include_once(__DIR__ . '/OccurrenceEditorImages.php');
include_once(__DIR__ . '/OccurrenceEditorMedia.php');
include_once(__DIR__ . '/../services/UuidService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/SOLRService.php');

class OccurrenceEditorManager {

    protected $conn;
    protected $occid;
    protected $collId = 0;
    protected $collMap = array();
    protected $occurrenceMap = array();
    private $occFieldArr;
    private $sqlWhere;
    private $occIndex;
    private $recLimit;
    private $orderByStr;
    private $qryArr = array();
    private $crowdSourceMode = 0;
    private $exsiccatiMode = 0;
    protected $errorArr = array();
    protected $isShareConn = false;

    public function __construct($conn = null){
        if($conn){
            $this->conn = $conn;
            $this->isShareConn = true;
        }
        else{
            $connection = new DbService();
            $this->conn = $connection->getConnection();
        }
        $this->occFieldArr = array('dbpk', 'catalognumber', 'othercatalognumbers', 'occurrenceid','family', 'verbatimscientificname', 'sciname',
            'tid', 'scientificnameauthorship', 'identifiedby', 'dateidentified', 'identificationreferences',
            'identificationremarks', 'taxonremarks', 'identificationqualifier', 'typestatus', 'recordedby', 'recordnumber',
            'associatedcollectors', 'eventdate', 'year', 'month', 'day', 'startdayofyear', 'enddayofyear', 'fieldnotes',
            'verbatimeventdate', 'habitat', 'substrate', 'fieldnumber','occurrenceremarks', 'associatedtaxa', 'verbatimattributes',
            'dynamicproperties', 'reproductivecondition', 'cultivationstatus', 'establishmentmeans',
            'lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations','datageneralizations',
            'country', 'stateprovince', 'county', 'municipality', 'locality', 'localitysecurity', 'localitysecurityreason',
            'decimallatitude', 'decimallongitude','geodeticdatum', 'coordinateuncertaintyinmeters', 'footprintwkt', 'locationid',
            'locationremarks', 'verbatimcoordinates', 'georeferencedby', 'georeferenceprotocol', 'georeferencesources',
            'georeferenceverificationstatus', 'georeferenceremarks', 'minimumelevationinmeters', 'maximumelevationinmeters','verbatimelevation',
            'minimumdepthinmeters', 'maximumdepthinmeters', 'verbatimdepth', 'disposition', 'language', 'duplicatequantity', 'genericcolumn1', 'genericcolumn2',
            'labelproject','observeruid','basisofrecord','institutioncode','collectioncode','ownerinstitutioncode','datelastmodified', 'processingstatus',
            'recordenteredby', 'dateentered');
    }

    public function __destruct(){
        if(!$this->isShareConn && $this->conn) {
            $this->conn->close();
        }
    }

    public function setOccId($id): void
    {
        if(is_numeric($id)){
            $this->occid = SanitizerService::cleanInStr($this->conn,$id);
        }
    }

    public function setCollId($id): void
    {
        if($id && is_numeric($id)){
            if($id !== $this->collId){
                unset($this->collMap);
                $this->collMap = array();
            }
            $this->collId = SanitizerService::cleanInStr($this->conn,$id);
        }
    }

    public function getCollMap(): array
    {
        if(!$this->collMap){
            $sqlWhere = '';
            if($this->collId){
                $sqlWhere .= 'WHERE c.collid = '.$this->collId.' ';
            }
            elseif($this->occid){
                $sqlWhere .= 'INNER JOIN omoccurrences AS o ON c.collid = o.collid '.
                    'WHERE o.occid = '.$this->occid.' ';
            }
            if($sqlWhere){
                $sql = 'SELECT c.collid, c.collectionname, c.institutioncode, c.collectioncode, c.colltype, c.managementtype '.
                    'FROM omcollections AS c '.$sqlWhere;
                $rs = $this->conn->query($sql);
                if($row = $rs->fetch_object()){
                    $this->collMap['collid'] = $row->collid;
                    $this->collMap['collectionname'] = SanitizerService::cleanOutStr($row->collectionname);
                    $this->collMap['institutioncode'] = $row->institutioncode;
                    $this->collMap['collectioncode'] = $row->collectioncode;
                    $this->collMap['colltype'] = $row->colltype;
                    $this->collMap['managementtype'] = $row->managementtype;
                }
                $rs->free();
            }
        }
        if(!$this->collId) {
            $this->collId = $this->collMap['collid'];
        }
        return $this->collMap;
    }

    public function setOccIndex($occIndex): void
    {
        $this->occIndex = $occIndex;
    }

    public function setRecLimit($recLimit): void
    {
        $this->recLimit = $recLimit;
    }

    public function setOrderByStr($orderByStr): void
    {
        $this->orderByStr = $orderByStr;
    }

    public function setQueryVariables($overrideQry = null): void
    {
        if($overrideQry){
            $this->qryArr = $overrideQry;
            unset($_SESSION['editorquery']);
        }
        elseif(array_key_exists('q_catalognumber',$_REQUEST) || array_key_exists('q_identifier',$_REQUEST)){
            if(array_key_exists('q_identifier',$_REQUEST) && $_REQUEST['q_identifier']) {
                $this->qryArr['cn'] = trim($_REQUEST['q_identifier']);
            }
            if($_REQUEST['q_catalognumber']) {
                $this->qryArr['cn'] = trim($_REQUEST['q_catalognumber']);
            }
            if(array_key_exists('q_othercatalognumbers',$_REQUEST) && $_REQUEST['q_othercatalognumbers']) {
                $this->qryArr['ocn'] = trim($_REQUEST['q_othercatalognumbers']);
            }
            if(array_key_exists('q_recordedby',$_REQUEST) && $_REQUEST['q_recordedby']) {
                $this->qryArr['rb'] = trim($_REQUEST['q_recordedby']);
            }
            if(array_key_exists('q_recordnumber',$_REQUEST) && $_REQUEST['q_recordnumber']) {
                $this->qryArr['rn'] = trim($_REQUEST['q_recordnumber']);
            }
            if(array_key_exists('q_eventdate',$_REQUEST) && $_REQUEST['q_eventdate']) {
                $this->qryArr['ed'] = trim($_REQUEST['q_eventdate']);
            }
            if(array_key_exists('q_recordenteredby',$_REQUEST) && $_REQUEST['q_recordenteredby']) {
                $this->qryArr['eb'] = trim($_REQUEST['q_recordenteredby']);
            }
            if(array_key_exists('q_observeruid',$_REQUEST) && is_numeric($_REQUEST['q_observeruid'])) {
                $this->qryArr['ouid'] = $_REQUEST['q_observeruid'];
            }
            if(array_key_exists('q_processingstatus',$_REQUEST) && $_REQUEST['q_processingstatus']) {
                $this->qryArr['ps'] = trim($_REQUEST['q_processingstatus']);
            }
            if(array_key_exists('q_datelastmodified',$_REQUEST) && $_REQUEST['q_datelastmodified']) {
                $this->qryArr['dm'] = trim($_REQUEST['q_datelastmodified']);
            }
            if(array_key_exists('q_exsiccatiid',$_REQUEST) && is_numeric($_REQUEST['q_exsiccatiid'])) {
                $this->qryArr['exsid'] = $_REQUEST['q_exsiccatiid'];
            }
            if(array_key_exists('q_dateentered',$_REQUEST) && $_REQUEST['q_dateentered']) {
                $this->qryArr['de'] = trim($_REQUEST['q_dateentered']);
            }
            if(array_key_exists('q_imgonly',$_REQUEST) && $_REQUEST['q_imgonly']) {
                $this->qryArr['io'] = 1;
            }
            if(array_key_exists('q_withoutimg',$_REQUEST) && $_REQUEST['q_withoutimg']) {
                $this->qryArr['woi'] = 1;
            }
            for($x=1;$x<6;$x++){
                if(array_key_exists('q_customandor'.$x,$_REQUEST) && $_REQUEST['q_customandor'.$x]) {
                    $this->qryArr['cao' . $x] = $_REQUEST['q_customandor' . $x];
                }
                if(array_key_exists('q_customopenparen'.$x,$_REQUEST) && $_REQUEST['q_customopenparen'.$x]) {
                    $this->qryArr['cop' . $x] = $_REQUEST['q_customopenparen' . $x];
                }
                if(array_key_exists('q_customfield'.$x,$_REQUEST) && $_REQUEST['q_customfield'.$x]) {
                    $this->qryArr['cf' . $x] = $_REQUEST['q_customfield' . $x];
                }
                if(array_key_exists('q_customtype'.$x,$_REQUEST) && $_REQUEST['q_customtype'.$x]) {
                    $this->qryArr['ct' . $x] = $_REQUEST['q_customtype' . $x];
                }
                if(array_key_exists('q_customvalue'.$x,$_REQUEST) && $_REQUEST['q_customvalue'.$x]) {
                    $this->qryArr['cv' . $x] = trim($_REQUEST['q_customvalue' . $x]);
                }
                if(array_key_exists('q_customcloseparen'.$x,$_REQUEST) && $_REQUEST['q_customcloseparen'.$x]) {
                    $this->qryArr['ccp' . $x] = $_REQUEST['q_customcloseparen' . $x];
                }
            }
            if(array_key_exists('orderby',$_REQUEST)) {
                $this->qryArr['orderby'] = trim($_REQUEST['orderby']);
            }
            if(array_key_exists('orderbydir',$_REQUEST)) {
                $this->qryArr['orderbydir'] = trim($_REQUEST['orderbydir']);
            }
            unset($_SESSION['editorquery']);
        }
        elseif(isset($_SESSION['editorquery'])){
            $this->qryArr = json_decode($_SESSION['editorquery'], true);
        }
    }

    public function setSqlWhere($occIndex = null, $recLimit = null): void
    {
        if(!$recLimit){
            $recLimit = 1;
        }
        if ($this->qryArr === null) {
            $this->qryArr=array();
        }
        $sqlWhere = '';
        $catNumIsNum = false;
        if(array_key_exists('cn',$this->qryArr)){
            $idTerm = $this->qryArr['cn'];
            if(strtolower($idTerm) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.catalognumber) ';
            }
            else{
                $isOccid = false;
                if(strncmp($idTerm, 'occid', 5) === 0){
                    $idTerm = trim(substr($idTerm,5));
                    $isOccid = true;
                }
                $iArr = explode(',',$idTerm);
                $iBetweenFrag = array();
                $iInFrag = array();
                foreach($iArr as $v){
                    $v = trim($v);
                    if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$v)){
                        $v = str_ireplace(array('>',' and ','<'),array('',' - ',''),$v);
                    }
                    if($p = strpos($v,' - ')){
                        $term1 = SanitizerService::cleanInStr($this->conn,substr($v,0,$p));
                        $term2 = SanitizerService::cleanInStr($this->conn,substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $catNumIsNum = true;
                            if($isOccid){
                                $iBetweenFrag[] = 'o2.occid BETWEEN '.$term1.' AND '.$term2.' ';
                            }
                            else{
                                $iBetweenFrag[] = 'o2.catalogNumber BETWEEN '.$term1.' AND '.$term2.' ';
                            }
                        }
                        else{
                            $catTerm = 'o2.catalogNumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
                            if(strlen($term1) === strlen($term2)) {
                                $catTerm .= ' AND LENGTH(o2.catalogNumber) = ' . strlen($term2);
                            }
                            $iBetweenFrag[] = '('.$catTerm.')';
                        }
                    }
                    else{
                        $vStr = SanitizerService::cleanInStr($this->conn,$v);
                        if(is_numeric($vStr)){
                            if($iInFrag){
                                $catNumIsNum = true;
                            }
                            if(strncmp($vStr, '0', 1) === 0){
                                $iInFrag[] = ltrim($vStr,0);
                            }
                        }
                        $iInFrag[] = $vStr;
                    }
                }
                $iWhere = '';
                if($iBetweenFrag){
                    $iWhere .= 'OR '.implode(' OR ',$iBetweenFrag);
                }
                if($iInFrag){
                    if($isOccid){
                        foreach($iInFrag as $term){
                            if(strncmp($term, '<', 1) === 0 || strncmp($term, '>', 1) === 0){
                                $iWhere .= 'OR o2.occid '. $term[0] .' '.trim(substr($term,1)).' ';
                            }
                            else{
                                $iWhere .= 'OR o2.occid = '.$term.' ';
                            }
                        }
                    }
                    else{
                        foreach($iInFrag as $term){
                            if(strncmp($term, '<', 1) === 0 || strncmp($term, '>', 1) === 0){
                                $tStr = trim(substr($term,1));
                                if(!is_numeric($tStr)) {
                                    $tStr = '"' . $tStr . '"';
                                }
                                $iWhere .= 'OR o2.catalognumber '. $term[0] .' '.$tStr.' ';
                            }
                            else{
                                $iWhere .= 'OR o2.catalognumber = "'.$term.'" ';
                            }
                        }
                    }
                }
                $sqlWhere .= 'AND ('.substr($iWhere,3).') ';
            }
        }
        $otherCatNumIsNum = false;
        if(array_key_exists('ocn',$this->qryArr)){
            if(strtolower($this->qryArr['ocn']) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.othercatalognumbers) ';
            }
            else{
                $ocnArr = explode(',',$this->qryArr['ocn']);
                $ocnBetweenFrag = array();
                $ocnInFrag = array();
                foreach($ocnArr as $v){
                    $v = SanitizerService::cleanInStr($this->conn,$v);
                    if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$v)){
                        $v = str_ireplace(array('>',' and ','<'),array('',' - ',''),$v);
                    }
                    if(strpos('%',$v) !== false){
                        $ocnBetweenFrag[] = 'o2.othercatalognumbers LIKE "'.$v.'" ';
                    }
                    elseif($p = strpos($v,' - ')){
                        $term1 = trim(substr($v,0,$p));
                        $term2 = trim(substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $otherCatNumIsNum = true;
                            $ocnBetweenFrag[] = 'o2.othercatalognumbers BETWEEN '.$term1.' AND '.$term2.' ';
                        }
                        else{
                            $ocnTerm = 'o2.othercatalognumbers BETWEEN "'.$term1.'" AND "'.$term2.'" ';
                            if(strlen($term1) === strlen($term2)) {
                                $ocnTerm .= ' AND LENGTH(o2.othercatalognumbers) = ' . strlen($term2);
                            }
                            $ocnBetweenFrag[] = '('.$ocnTerm.')';
                        }
                    }
                    else{
                        $ocnInFrag[] = $v;
                        if(is_numeric($v)){
                            $otherCatNumIsNum = true;
                            if(strncmp($v, '0', 1) === 0){
                                $ocnInFrag[] = ltrim($v,0);
                            }
                        }
                    }
                }
                $ocnWhere = '';
                if($ocnBetweenFrag){
                    $ocnWhere .= 'OR '.implode(' OR ',$ocnBetweenFrag);
                }
                if($ocnInFrag){
                    foreach($ocnInFrag as $term){
                        if(strncmp($term, '<', 1) === 0 || strncmp($term, '>', 1) === 0){
                            $tStr = trim(substr($term,1));
                            if(!is_numeric($tStr)) {
                                $tStr = '"' . $tStr . '"';
                            }
                            $ocnWhere .= 'OR o2.othercatalognumbers '. $term[0] .' '.$tStr.' ';
                        }
                        else{
                            $ocnWhere .= 'OR o2.othercatalognumbers = "'.$term.'" ';
                        }
                    }
                }
                $sqlWhere .= 'AND '.substr($ocnWhere,3).' ';
            }
        }
        if(array_key_exists('rn',$this->qryArr)){
            if(strtolower($this->qryArr['rn']) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.recordnumber) ';
            }
            else{
                $rnArr = explode(',',$this->qryArr['rn']);
                $rnBetweenFrag = array();
                $rnInFrag = array();
                foreach($rnArr as $v){
                    $v = SanitizerService::cleanInStr($this->conn,$v);
                    if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$v)){
                        $v = str_ireplace(array('>',' and ','<'),array('',' - ',''),$v);
                    }
                    if($p = strpos($v,' - ')){
                        $term1 = trim(substr($v,0,$p));
                        $term2 = trim(substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $rnBetweenFrag[] = 'o2.recordnumber BETWEEN '.$term1.' AND '.$term2.' ';
                        }
                        else{
                            $catTerm = 'o2.recordnumber BETWEEN "'.$term1.'" AND "'.$term2.'" ';
                            if(strlen($term1) === strlen($term2)) {
                                $catTerm .= ' AND LENGTH(o2.recordnumber) = ' . strlen($term2);
                            }
                            $rnBetweenFrag[] = '('.$catTerm.')';
                        }
                    }
                    else{
                        $condStr = '=';
                        if(strncmp($v, '<', 1) === 0 || strncmp($v, '>', 1) === 0){
                            $condStr = $v[0];
                            $v = trim(substr($v,1));
                        }
                        if(is_numeric($v)){
                            $rnInFrag[] = $condStr.' '.$v;
                        }
                        else{
                            $rnInFrag[] = $condStr.' "'.$v.'"';
                        }
                    }
                }
                $rnWhere = '';
                if($rnBetweenFrag){
                    $rnWhere .= 'OR '.implode(' OR ',$rnBetweenFrag);
                }
                if($rnInFrag){
                    foreach($rnInFrag as $term){
                        $rnWhere .= 'OR o2.recordnumber '.$term.' ';
                    }
                }
                $sqlWhere .= 'AND '.substr($rnWhere,3).' ';
            }
        }
        if(array_key_exists('rb',$this->qryArr)){
            if(strtolower($this->qryArr['rb']) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.recordedby) ';
            }
            elseif(strncmp($this->qryArr['rb'], '%', 1) === 0){
                $collStr = SanitizerService::cleanInStr($this->conn,substr($this->qryArr['rb'],1));
                $sqlWhere .= 'AND o2.recordedby LIKE "%'.$collStr.'%" ';
            }
            else{
                $sqlWhere .= 'AND o2.recordedby LIKE "'.SanitizerService::cleanInStr($this->conn,$this->qryArr['rb']).'%" ';
            }
        }
        if(array_key_exists('ed',$this->qryArr)){
            if(strtolower($this->qryArr['ed']) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.eventdate) ';
            }
            else{
                $edv = SanitizerService::cleanInStr($this->conn,$this->qryArr['ed']);
                if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$edv)){
                    $edv = str_ireplace(array('>',' and ','<'),array('',' - ',''),$edv);
                }
                $edv = str_replace(' to ',' - ',$edv);
                if($p = strpos($edv,' - ')){
                    $sqlWhere .= 'AND o2.eventdate BETWEEN "'.trim(substr($edv,0,$p)).'" AND "'.trim(substr($edv,$p+3)).'" ';
                }
                elseif(strncmp($edv, '<', 1) === 0 || strncmp($edv, '>', 1) === 0){
                    $sqlWhere .= 'AND o2.eventdate '. $edv[0] .' "'.trim(substr($edv,1)).'" ';
                }
                else{
                    $sqlWhere .= 'AND o2.eventdate = "'.$edv.'" ';
                }
            }
        }
        if(array_key_exists('eb',$this->qryArr)){
            if(strtolower($this->qryArr['eb']) === 'is null'){
                $sqlWhere .= 'AND ISNULL(o2.recordEnteredBy) ';
            }
            else{
                $sqlWhere .= 'AND o2.recordEnteredBy = "'.SanitizerService::cleanInStr($this->conn,$this->qryArr['eb']).'" ';
            }
        }
        if(array_key_exists('ouid',$this->qryArr) && is_numeric($this->qryArr['ouid'])){
            $sqlWhere .= 'AND o2.observeruid = '.$this->qryArr['ouid'].' ';
        }
        if(array_key_exists('de',$this->qryArr)){
            $de = SanitizerService::cleanInStr($this->conn,$this->qryArr['de']);
            if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$de)){
                $de = str_ireplace(array('>',' and ','<'),array('',' - ',''),$de);
            }
            $de = str_replace(' to ',' - ',$de);
            if($p = strpos($de,' - ')){
                $sqlWhere .= 'AND DATE(o2.dateentered) BETWEEN "'.trim(substr($de,0,$p)).'" AND "'.trim(substr($de,$p+3)).'" ';
            }
            elseif(strncmp($de, '<', 1) === 0 || strncmp($de, '>', 1) === 0){
                $sqlWhere .= 'AND o2.dateentered '. $de[0] .' "'.trim(substr($de,1)).'" ';
            }
            else{
                $sqlWhere .= 'AND DATE(o2.dateentered) = "'.$de.'" ';
            }
        }
        if(array_key_exists('dm',$this->qryArr)){
            $dm = SanitizerService::cleanInStr($this->conn,$this->qryArr['dm']);
            if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$dm)){
                $dm = str_ireplace(array('>',' and ','<'),array('',' - ',''),$dm);
            }
            $dm = str_replace(' to ',' - ',$dm);
            if($p = strpos($dm,' - ')){
                $sqlWhere .= 'AND DATE(o2.datelastmodified) BETWEEN "'.trim(substr($dm,0,$p)).'" AND "'.trim(substr($dm,$p+3)).'" ';
            }
            elseif(strncmp($dm, '<', 1) === 0 || strncmp($dm, '>', 1) === 0){
                $sqlWhere .= 'AND o2.datelastmodified '. $dm[0] .' "'.trim(substr($dm,1)).'" ';
            }
            else{
                $sqlWhere .= 'AND DATE(o2.datelastmodified) = "'.$dm.'" ';
            }
        }
        if(array_key_exists('ps',$this->qryArr)){
            if($this->qryArr['ps'] === 'isnull'){
                $sqlWhere .= 'AND ISNULL(o2.processingstatus) ';
            }
            else{
                $sqlWhere .= 'AND o2.processingstatus = "'.SanitizerService::cleanInStr($this->conn,$this->qryArr['ps']).'" ';
            }
        }
        if(array_key_exists('woi',$this->qryArr)){
            $sqlWhere .= 'AND o2.occid NOT IN(SELECT DISTINCT occid FROM images) ';
        }
        if(array_key_exists('exsid',$this->qryArr) && is_numeric($this->qryArr['exsid'])){
            $sqlWhere .= 'AND exn.ometid = '.$this->qryArr['exsid'].' ';
        }
        for($x=1;$x<6;$x++){
            $cao = (array_key_exists('cao'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['cao'.$x]):'');
            $cop = (array_key_exists('cop'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['cop'.$x]):'');
            $cf = (array_key_exists('cf'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['cf'.$x]):'');
            $ct = (array_key_exists('ct'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['ct'.$x]):'');
            $cv = (array_key_exists('cv'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['cv'.$x]):'');
            $ccp = (array_key_exists('ccp'.$x,$this->qryArr)?SanitizerService::cleanInStr($this->conn,$this->qryArr['ccp'.$x]):'');
            if(!$cao) {
                $cao = 'AND';
            }
            if($cf){
                if($cf === 'username'){
                    $cf = 'ul.username';
                }
                elseif($cf === 'verbatimsciname'){
                    $cf = 'oas.verbatimsciname';
                }
                else{
                    $cf = 'o2.'.$cf;
                }
                if($ct === 'NULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ISNULL('.$cf.') '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'NOTNULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' IS NOT NULL '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'NOT EQUALS' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' <> '.$cv.' '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'GREATER' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' > '.$cv.' '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'LESS' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' < '.$cv.' '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'LIKE' && $cv){
                    if(strpos($cv,'%') !== false){
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' LIKE "'.$cv.'" '.($ccp?$ccp.' ':'');
                    }
                    else{
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' LIKE "%'.$cv.'%" '.($ccp?$ccp.' ':'');
                    }
                }
                elseif($ct === 'STARTS' && $cv){
                    if(strpos($cv,'%') !== false){
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' LIKE "'.$cv.'" '.($ccp?$ccp.' ':'');
                    }
                    else{
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' LIKE "'.$cv.'%" '.($ccp?$ccp.' ':'');
                    }
                }
                elseif($cv){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' '.$cf.' = "'.$cv.'" '.($ccp?$ccp.' ':'');
                }
                if($cf === 'oas.verbatimsciname' && $ct !== 'NULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' oas.relationship = "host" '.($ccp?$ccp.' ':'');
                }
            }
            else if($x > 1 && $ccp){
                $sqlWhere .= ' '.$ccp.' ';
            }
        }
        if($this->crowdSourceMode){
            $sqlWhere .= 'AND q.reviewstatus = 0 ';
        }
        if($sqlWhere){
            if(strncmp($sqlWhere, 'OR', 2) === 0){
                $sqlWhere = 'WHERE '.substr($sqlWhere,3).' ';
            }
            else{
                $sqlWhere = 'WHERE '.substr($sqlWhere,4).' ';
            }
        }
        if($this->collId) {
            $sqlWhere .= ($sqlWhere ? 'AND o2.collid = ' . $this->collId . ' ' : 'WHERE o2.collid = ' . $this->collId . ' ');
        }
        if(isset($this->qryArr['orderby'])){
            $orderBy = SanitizerService::cleanInStr($this->conn,$this->qryArr['orderby']);
            if($orderBy === 'catalognumber'){
                if($catNumIsNum){
                    $sqlOrderBy = 'catalogNumber+1';
                }
                else{
                    $sqlOrderBy = 'catalogNumber';
                }
            }
            elseif($orderBy === 'othercatalognumbers'){
                if($otherCatNumIsNum){
                    $sqlOrderBy = 'othercatalognumbers+1';
                }
                else{
                    $sqlOrderBy = 'othercatalognumbers';
                }
            }
            elseif($orderBy === 'recordnumber'){
                $sqlOrderBy = 'recordnumber+1';
            }
            else{
                $sqlOrderBy = $orderBy;
            }
            if($sqlOrderBy) {
                $this->setOrderByStr($sqlOrderBy);
            }
        }

        $this->setOccIndex($occIndex);
        $this->setRecLimit($recLimit);
        //echo $sqlWhere; exit;
        $this->sqlWhere = $sqlWhere;
    }

    protected function setOccurArr(): void
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.'.implode(',o.',$this->occFieldArr);
        if($this->sqlWhere && strpos($this->sqlWhere,'oas.verbatimsciname') !== false){
            $sql .= ', oas.verbatimsciname';
        }
        $sql .= ' FROM omoccurrences AS o ';
        if($this->sqlWhere && strpos($this->sqlWhere,'oas.verbatimsciname') !== false){
            $sql .= 'LEFT JOIN omoccurassociations AS oas ON o.occid = oas.occid ';
        }
        if($this->occid){
            $sql .= 'WHERE o.occid = '.$this->occid.' ';
        }
        elseif($this->sqlWhere){
            $sql .= 'INNER JOIN (SELECT o2.occid FROM omoccurrences AS o2 ';
            $sql = $this->addTableJoins($sql);
            $sql .= $this->sqlWhere;
            $sql .= ') AS a ON o.occid = a.occid ';
            if($this->orderByStr) {
                $sql .= 'ORDER BY o.' . $this->orderByStr . ' ' . $this->qryArr['orderbydir'] . ' ';
            }
            $sql .= 'LIMIT '.($this->occIndex>0?$this->occIndex.',':'').$this->recLimit;
        }
        if($sql){
            //echo '<div>' .$sql. '</div>';
            $occid = 0;
            $rs = $this->conn->query($sql);
            while($row = $rs->fetch_assoc()){
                if($occid !== $row['occid']){
                    $occid = $row['occid'];
                    $retArr[$occid] = array_change_key_case($row);
                }
            }
            $rs->free();
            if($retArr && count($retArr) === 1){
                if(!$this->occid) {
                    $this->occid = $occid;
                }
                if(!array_key_exists('institutioncode',$retArr[$occid])) {
                    $retArr[$occid]['institutioncode'] = $this->collMap['institutioncode'];
                }
                if(!array_key_exists('collectioncode',$retArr[$occid])) {
                    $retArr[$occid]['collectioncode'] = $this->collMap['collectioncode'];
                }
                if(!array_key_exists('ownerinstitutioncode',$retArr[$occid])) {
                    $retArr[$occid]['ownerinstitutioncode'] = $this->collMap['institutioncode'];
                }
            }
            $this->occurrenceMap = SanitizerService::cleanOutArray($retArr);
            if($this->occid){
                $this->setLoanData();
                if($this->exsiccatiMode) {
                    $this->setExsiccati();
                }
            }
        }
    }

    private function addTableJoins($sql): string
    {
        if(array_key_exists('io',$this->qryArr)){
            $sql .= 'INNER JOIN images AS i ON o2.occid = i.occid ';
        }
        if(strpos($this->sqlWhere,'ul.username')){
            $sql .= 'LEFT JOIN omoccuredits AS ome ON o2.occid = ome.occid LEFT JOIN users AS ul ON ome.uid = ul.uid ';
        }
        if(strpos($this->sqlWhere,'oas.verbatimsciname')){
            $sql .= 'LEFT JOIN omoccurassociations AS oas ON o2.occid = oas.occid ';
        }
        if(strpos($this->sqlWhere,'exn.ometid')){
            $sql .= 'INNER JOIN omexsiccatiocclink AS exocc ON o2.occid = exocc.occid INNER JOIN omexsiccatinumbers AS exn ON exocc.omenid = exn.omenid ';
        }
        if($this->crowdSourceMode){
            $sql .= 'INNER JOIN omcrowdsourcequeue AS q ON q.occid = o2.occid ';
        }

        return $sql;
    }

    public function addOccurrence($occArr): string
    {
        $status = 'SUCCESS: new occurrence record submitted successfully ';
        if($occArr){
            $fieldArr = array('basisOfRecord' => 's', 'catalogNumber' => 's', 'otherCatalogNumbers' => 's', 'occurrenceid' => 's',
                'ownerInstitutionCode' => 's', 'institutionCode' => 's', 'collectionCode' => 's',
                'family' => 's', 'sciname' => 's', 'tid' => 'n', 'scientificNameAuthorship' => 's', 'identifiedBy' => 's', 'dateIdentified' => 's',
                'identificationReferences' => 's', 'identificationremarks' => 's', 'taxonRemarks' => 's', 'identificationQualifier' => 's', 'typeStatus' => 's',
                'recordedBy' => 's', 'recordNumber' => 's', 'associatedCollectors' => 's', 'eventDate' => 'd', 'year' => 'n', 'month' => 'n', 'day' => 'n', 'startDayOfYear' => 'n', 'endDayOfYear' => 'n',
                'verbatimEventDate' => 's', 'habitat' => 's', 'substrate' => 's', 'fieldnumber' => 's', 'occurrenceRemarks' => 's', 'fieldNotes' => 's', 'associatedTaxa' => 's', 'verbatimattributes' => 's',
                'dynamicProperties' => 's', 'reproductiveCondition' => 's', 'cultivationStatus' => 's', 'establishmentMeans' => 's',
                'lifestage' => 's', 'sex' => 's', 'individualcount' => 's', 'samplingprotocol' => 's', 'preparations' => 's', 'locationID' => 's', 'locationRemarks' => 's',
                'country' => 's', 'stateProvince' => 's', 'county' => 's', 'municipality' => 's', 'locality' => 's', 'localitySecurity' => 'n', 'localitysecurityreason' => 's',
                'decimalLatitude' => 'n', 'decimalLongitude' => 'n', 'geodeticDatum' => 's', 'coordinateUncertaintyInMeters' => 'n', 'verbatimCoordinates' => 's',
                'footprintwkt' => 's', 'georeferencedBy' => 's', 'georeferenceProtocol' => 's', 'georeferenceSources' => 's', 'georeferenceVerificationStatus' => 's',
                'georeferenceRemarks' => 's', 'minimumElevationInMeters' => 'n', 'maximumElevationInMeters' => 'n','verbatimElevation' => 's',
                'minimumDepthInMeters' => 'n', 'maximumDepthInMeters' => 'n', 'verbatimDepth' => 's','disposition' => 's', 'language' => 's', 'duplicateQuantity' => 'n',
                'labelProject' => 's','processingstatus' => 's', 'recordEnteredBy' => 's', 'observeruid' => 'n', 'dateentered' => 'd', 'genericcolumn2' => 's');
            $sql = 'INSERT INTO omoccurrences(collid, '. implode(',', array_keys($fieldArr)) .') '.
                'VALUES ('.$occArr['collid'];
            $fieldArr = array_change_key_case($fieldArr);
            if(!isset($occArr['dateentered']) || !$occArr['dateentered']) {
                $occArr['dateentered'] = date('Y-m-d H:i:s');
            }
            if(!isset($occArr['basisofrecord']) || !$occArr['basisofrecord']) {
                $occArr['basisofrecord'] = ($this->collMap['colltype'] === 'HumanObservation' ? 'HumanObservation' : 'PreservedSpecimen');
            }
            if(isset($occArr['institutionCode']) && $occArr['institutionCode'] === $this->collMap['institutioncode']) {
                $occArr['institutionCode'] = '';
            }
            if(isset($occArr['collectionCode']) && $occArr['collectionCode'] === $this->collMap['collectioncode']) {
                $occArr['collectionCode'] = '';
            }

            foreach($fieldArr as $fieldStr => $fieldType){
                $fieldValue = '';
                if(array_key_exists($fieldStr,$occArr)) {
                    $fieldValue = $occArr[$fieldStr];
                }
                if($fieldValue){
                    if($fieldType === 'n'){
                        if(is_numeric($fieldValue)){
                            $sql .= ', '.$fieldValue;
                        }
                        else{
                            $sql .= ', NULL';
                        }
                    }
                    else{
                        $sql .= ', "'.SanitizerService::cleanInStr($this->conn,$fieldValue).'"';
                    }
                }
                else{
                    $sql .= ', NULL';
                }
            }
            $sql .= ')';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $this->occid = $this->conn->insert_id;
                $this->conn->query('UPDATE omcollectionstats SET recordcnt = recordcnt + 1 WHERE collid = '.$this->collId);

                $guid = UuidService::getUuidV4();
                if(!$this->conn->query('INSERT INTO guidoccurrences(guid,occid) VALUES("'.$guid.'",'.$this->occid.')')){
                    $status .= '(WARNING: GUID mapping failed) ';
                }
                if(isset($occArr['ometid'], $occArr['exsnumber'])){
                    $ometid = SanitizerService::cleanInStr($this->conn,$occArr['ometid']);
                    $exsNumber = SanitizerService::cleanInStr($this->conn,$occArr['exsnumber']);
                    if($ometid && $exsNumber){
                        $exsNumberId = '';
                        $sql = 'SELECT omenid FROM omexsiccatinumbers WHERE ometid = '.$ometid.' AND exsnumber = "'.$exsNumber.'"';
                        $rs = $this->conn->query($sql);
                        if($r = $rs->fetch_object()){
                            $exsNumberId = $r->omenid;
                        }
                        $rs->free();
                        if(!$exsNumberId){
                            $sqlNum = 'INSERT INTO omexsiccatinumbers(ometid,exsnumber) '.
                                'VALUES('.$ometid.',"'.$exsNumber.'")';
                            if($this->conn->query($sqlNum)){
                                $exsNumberId = $this->conn->insert_id;
                            }
                            else{
                                $status .= '(WARNING adding exsiccati number.) ';
                            }
                        }
                        if($exsNumberId){
                            $sql1 = 'INSERT INTO omexsiccatiocclink(omenid, occid) '.
                                'VALUES('.$exsNumberId.','.$this->occid.')';
                            if(!$this->conn->query($sql1)){
                                $status .= '(WARNING adding exsiccati.) ';
                            }
                        }
                    }
                }
                if(array_key_exists('host',$occArr)){
                    $sql1 = 'INSERT INTO omoccurassociations(occid,relationship,verbatimsciname) '.
                        'VALUES('.$this->occid.',"host","'.SanitizerService::cleanInStr($this->conn,$occArr['host']).'")';
                    if(!$this->conn->query($sql1)){
                        $status .= '(WARNING adding host.) ';
                    }
                }

                if(isset($occArr['clidvoucher'], $occArr['tid'])){
                    $status .= $this->linkChecklistVoucher($occArr['clidvoucher'],$occArr['tid']);
                }
            }
            else{
                $status = 'ERROR - failed to add occurrence record.';
            }
        }
        return $status;
    }

    public function deleteOccurrence($delOccid): bool
    {
        $status = true;
        if(is_numeric($delOccid)){
            $archiveArr = array();
            $sql = 'SELECT * FROM omoccurrences WHERE occid = '.$delOccid;
            //echo $sql; exit;
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_assoc()){
                foreach($r as $k => $v){
                    if($v) {
                        $archiveArr[$k] = $this->encodeStrTargeted($v, 'UTF-8', 'utf8');
                    }
                }
            }
            $rs->free();
            if($archiveArr){
                $detArr = array();
                $sql = 'SELECT * FROM omoccurdeterminations WHERE occid = '.$delOccid;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_assoc()){
                    $detId = $r['detid'];
                    foreach($r as $k => $v){
                        if($v) {
                            $detArr[$detId][$k] = $this->encodeStrTargeted($v, 'UTF-8', 'utf8');
                        }
                    }
                    $detObj = json_encode($detArr[$detId]);
                    $sqlArchive = 'UPDATE guidoccurdeterminations '.
                        'SET archivestatus = 1, archiveobj = "'.SanitizerService::cleanInStr($this->conn,$this->encodeStrTargeted($detObj,'utf8','UTF-8')).'" '.
                        'WHERE (detid = '.$detId.')';
                    $this->conn->query($sqlArchive);
                }
                $rs->free();
                $archiveArr['dets'] = $detArr;

                $imgArr = array();
                $sql = 'SELECT * FROM images WHERE occid = '.$delOccid;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_assoc()){
                    $imgId = $r['imgid'];
                    foreach($r as $k => $v){
                        if($v) {
                            $imgArr[$imgId][$k] = $this->encodeStrTargeted($v, 'UTF-8', 'utf8');
                        }
                    }
                    $imgObj = json_encode($imgArr[$imgId]);
                    $sqlArchive = 'UPDATE guidimages '.
                        'SET archivestatus = 1, archiveobj = "'.SanitizerService::cleanInStr($this->conn,$this->encodeStrTargeted($imgObj,'utf8','UTF-8')).'" '.
                        'WHERE (imgid = '.$imgId.')';
                    $this->conn->query($sqlArchive);
                }
                $rs->free();
                $archiveArr['imgs'] = $imgArr;
                if($imgArr){
                    $imgidStr = implode(',',array_keys($imgArr));
                    if(!$this->conn->query('DELETE FROM imagetag WHERE (imgid IN('.$imgidStr.'))')){
                        $this->errorArr[] = 'ERROR removing imageTags linked to images.';
                    }
                    if(!$this->conn->query('DELETE FROM images WHERE (imgid IN('.$imgidStr.'))')){
                        $this->errorArr[] = 'ERROR removing image links.';
                    }
                }

                $exsArr = array();
                $sql = 'SELECT t.ometid, t.title, t.abbreviation, t.editor, t.exsrange, t.startdate, t.enddate, t.source, t.notes as titlenotes, '.
                    'n.omenid, n.exsnumber, n.notes AS numnotes, l.notes, l.ranking '.
                    'FROM omexsiccatiocclink AS l INNER JOIN omexsiccatinumbers AS n ON l.omenid = n.omenid '.
                    'INNER JOIN omexsiccatititles AS t ON n.ometid = t.ometid '.
                    'WHERE l.occid = '.$delOccid;
                $rs = $this->conn->query($sql);
                if($r = $rs->fetch_assoc()){
                    foreach($r as $k => $v){
                        if($v) {
                            $exsArr[$k] = $this->encodeStrTargeted($v, 'UTF-8', 'utf8');
                        }
                    }
                }
                $rs->free();
                $archiveArr['exsiccati'] = $exsArr;

                $archiveArr['dateDeleted'] = date('r').' by '.$GLOBALS['USER_DISPLAY_NAME'];
                $archiveObj = json_encode($archiveArr);
                $sqlArchive = 'UPDATE guidoccurrences '.
                    'SET archivestatus = 1, archiveobj = "'.SanitizerService::cleanInStr($this->conn,$this->encodeStrTargeted($archiveObj,'utf8','UTF-8')).'" '.
                    'WHERE (occid = '.$delOccid.')';
                //echo $sqlArchive;
                $this->conn->query($sqlArchive);
            }

            $sqlDel = 'DELETE FROM omoccurrences WHERE (occid = '.$delOccid.')';
            if($this->conn->query($sqlDel)){
                $this->conn->query('UPDATE omcollectionstats SET recordcnt = recordcnt - 1 WHERE collid = '.$this->collId);
            }
            else{
                $this->errorArr[] = 'ERROR trying to delete occurrence record.';
                $status = false;
            }
        }
        return $status;
    }

    public function mergeRecords($targetOccid,$sourceOccid): bool
    {
        $status = true;
        if(!$targetOccid || !$sourceOccid){
            $this->errorArr[] = 'ERROR: target or source is null';
            $status = false;
        }
        elseif($targetOccid === $sourceOccid){
            $this->errorArr[] = 'ERROR: target and source are equal';
            $status = false;
        }
        else{
            $oArr = array();
            $sql = 'SELECT * FROM omoccurrences WHERE occid = '.$targetOccid.' OR occid = '.$sourceOccid;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_assoc()){
                $tempArr = array_change_key_case($r);
                $id = $tempArr['occid'];
                unset($tempArr['occid'], $tempArr['collid'], $tempArr['dbpk'], $tempArr['datelastmodified']);
                $oArr[$id] = $tempArr;
            }
            $rs->free();

            $tArr = $oArr[$targetOccid];
            $sArr = $oArr[$sourceOccid];
            $sqlFrag = '';
            foreach($sArr as $k => $v){
                if(($v !== '') && $tArr[$k] === ''){
                    $sqlFrag .= ','.$k.'="'.SanitizerService::cleanInStr($this->conn,$v).'"';
                }
            }
            if($sqlFrag){
                $sqlIns = 'UPDATE omoccurrences SET '.substr($sqlFrag,1).' WHERE occid = '.$targetOccid;
                //echo $sqlIns;
                if(!$this->conn->query($sqlIns)){
                    $this->errorArr[] = 'ABORT due to error merging records.';
                    return false;
                }
            }

            $sql = 'UPDATE omoccurdeterminations SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping determinations.';
                $status = false;
            }

            $sql = 'DELETE FROM omoccuredits WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping occurrence edits.';
                $status = false;
            }

            $sql = 'UPDATE images SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping images.';
                $status = false;
            }

            $sql = 'UPDATE omoccurgenetic SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping genetic resources.';
                $status = false;
            }

            $sql = 'UPDATE omexsiccatiocclink SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                if(strpos($this->conn->error,'Duplicate') !== false){
                    $this->conn->query('DELETE FROM omexsiccatiocclink WHERE occid = '.$sourceOccid);
                }
                else{
                    $this->errorArr[] .= '; ERROR remapping exsiccati.';
                    $status = false;
                }
            }

            $sql = 'UPDATE omoccurdatasetlink SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                if(strpos($this->conn->error,'Duplicate') !== false){
                    $this->conn->query('DELETE FROM omoccurdatasetlink WHERE occid = '.$sourceOccid);
                }
                else{
                    $this->errorArr[] .= '; ERROR remapping dataset links.';
                    $status = false;
                }
            }

            $sql = 'UPDATE omoccurloanslink SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                if(strpos($this->conn->error,'Duplicate') !== false){
                    $this->conn->query('DELETE FROM omoccurloanslink WHERE occid = '.$sourceOccid);
                }
                else{
                    $this->errorArr[] .= '; ERROR remapping loans.';
                    $status = false;
                }
            }

            $sql = 'UPDATE fmvouchers SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                if(strpos($this->conn->error,'Duplicate') !== false){
                    $this->conn->query('DELETE FROM fmvouchers WHERE occid = '.$sourceOccid);
                }
                else{
                    $this->errorArr[] .= '; ERROR remapping voucher links.';
                    $status = false;
                }
            }

            if(!$this->deleteOccurrence($sourceOccid)){
                $status = false;
            }
            if($GLOBALS['SOLR_MODE']) {
                $solrManager = new SOLRService();
                $solrManager->deleteSOLRDocument($sourceOccid);
            }
        }
        return $status;
    }

    private function setLoanData(): void
    {
        $sql = 'SELECT l.loanid, l.datedue, i.institutioncode '.
            'FROM omoccurloanslink AS ll INNER JOIN omoccurloans AS l ON ll.loanid = l.loanid '.
            'INNER JOIN institutions AS i ON l.iidBorrower = i.iid '.
            'WHERE ISNULL(ll.returndate) AND ISNULL(l.dateclosed) AND occid = '.$this->occid;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $this->occurrenceMap[$this->occid]['loan']['id'] = $r->loanid;
            $this->occurrenceMap[$this->occid]['loan']['date'] = $r->datedue;
            $this->occurrenceMap[$this->occid]['loan']['code'] = $r->institutioncode;
        }
        $rs->free();
    }

    private function setExsiccati(): void
    {
        $sql = 'SELECT l.notes, l.ranking, l.omenid, n.exsnumber, t.ometid, t.title, t.abbreviation, t.editor '.
            'FROM omexsiccatiocclink AS l INNER JOIN omexsiccatinumbers AS n ON l.omenid = n.omenid '.
            'INNER JOIN omexsiccatititles AS t ON n.ometid = t.ometid '.
            'WHERE l.occid = '.$this->occid;
        //echo $sql;
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $this->occurrenceMap[$this->occid]['ometid'] = $r->ometid;
            $this->occurrenceMap[$this->occid]['exstitle'] = $r->title.($r->abbreviation?' ['.$r->abbreviation.']':'');
            $this->occurrenceMap[$this->occid]['exsnumber'] = $r->exsnumber;
        }
        $rs->free();
    }

    public function batchUpdateField($fieldName,$oldValue,$newValue,$buMatch): string
    {
        $statusStr = '';
        $fn = SanitizerService::cleanInStr($this->conn,$fieldName);
        $ov = SanitizerService::cleanInStr($this->conn,$oldValue);
        $nv = SanitizerService::cleanInStr($this->conn,$newValue);
        if($fn && ($ov || $nv)){
            $occidArr = array();
            $sqlOccid = 'SELECT DISTINCT o2.occid FROM omoccurrences AS o2 ';
            $sqlOccid = $this->addTableJoins($sqlOccid);
            $sqlOccid .= $this->getBatchUpdateWhere($fn,$ov,$buMatch);
            //echo $sqlOccid.'<br/>';
            $rs = $this->conn->query($sqlOccid);
            while($r = $rs->fetch_object()){
                $occidArr[] = $r->occid;
            }
            $rs->free();
            if($occidArr){
                $sqlWhere = 'WHERE o.occid IN('.implode(',',$occidArr).')';

                if(!$buMatch || $ov===''){
                    $nvSqlFrag = ($nv===''?'NULL':'"'.$nv.'"');
                }
                else{
                    $nvSqlFrag = 'REPLACE(o.'.$fn.',"'.$ov.'","'.$nv.'")';
                }

                $hasEditType = false;
                $rsTest = $this->conn->query('SHOW COLUMNS FROM omoccuredits WHERE field = "editType"');
                if($rsTest->num_rows) {
                    $hasEditType = true;
                }
                $rsTest->free();

                $sql2 = 'INSERT INTO omoccuredits(occid,fieldName,fieldValueOld,fieldValueNew,appliedStatus,uid'.($hasEditType?',editType ':'').') '.
                    'SELECT o.occid, "'.$fn.'" AS fieldName, IFNULL(o.'.$fn.',"") AS oldValue, IFNULL('.$nvSqlFrag.',"") AS newValue, '.
                    '1 AS appliedStatus, '.$GLOBALS['SYMB_UID'].' AS uid'.($hasEditType?',1':'').' FROM omoccurrences AS o ';
                $sql2 .= $sqlWhere;
                //echo $sql2.'<br/>';
                if(!$this->conn->query($sql2)){
                    $statusStr = 'ERROR adding update to omoccuredits.';
                }

                $sql = 'UPDATE omoccurrences AS o SET o.'.$fn.' = '.$nvSqlFrag.' '.$sqlWhere;
                //echo $sql; exit;
                if(!$this->conn->query($sql)){
                    $statusStr = 'ERROR applying batch update.';
                }
            }
            else{
                $statusStr = 'ERROR applying batch update: no records match the criteria';
            }
        }
        return $statusStr;
    }

    public function getBatchUpdateCount($fieldName,$oldValue,$buMatch): int
    {
        $retCnt = 0;

        $fn = SanitizerService::cleanInStr($this->conn,$fieldName);
        $ov = SanitizerService::cleanInStr($this->conn,$oldValue);

        $sql = 'SELECT COUNT(DISTINCT o2.occid) AS retcnt '.
            'FROM omoccurrences AS o2 ';
        $sql = $this->addTableJoins($sql);
        $sql .= $this->getBatchUpdateWhere($fn,$ov,$buMatch);

        $result = $this->conn->query($sql);
        while ($row = $result->fetch_object()) {
            $retCnt = $row->retcnt;
        }
        $result->free();
        return $retCnt;
    }

    private function getBatchUpdateWhere($fn,$ov,$buMatch): string
    {
        $sql = '';
        $sql .= $this->sqlWhere;

        if(!$buMatch || $ov===''){
            $sql .= ' AND (o2.'.$fn.' '.($ov===''?'IS NULL':'= "'.$ov.'"').') ';
        }
        else{
            $sql .= ' AND (o2.'.$fn.' LIKE "%'.$ov.'%") ';
        }
        return $sql;
    }

    public function linkChecklistVoucher($clid,$tid): string
    {
        $status = '';
        if(is_numeric($clid) && is_numeric($tid)){
            $clTid = 0;
            $sqlCl = 'SELECT cl.tid '.
                'FROM fmchklsttaxalink AS cl INNER JOIN taxa AS t ON cl.tid = t.tid '.
                'WHERE t.tidaccepted = '.$tid.' AND cl.clid = '.$clid.' ';
            $rsCl = $this->conn->query($sqlCl);
            //echo $sqlCl;
            if($rowCl = $rsCl->fetch_object()){
                $clTid = $rowCl->tid;
            }
            $rsCl->free();
            if(!$clTid){
                $sqlCl1 = 'INSERT INTO fmchklsttaxalink(clid, tid) VALUES('.$clid.','.$tid.') ';
                if($this->conn->query($sqlCl1)){
                    $clTid = $tid;
                }
                else{
                    $status .= '(WARNING adding scientific name to checklist.';
                }
            }
            if($clTid){
                $sqlCl2 = 'INSERT INTO fmvouchers(occid,clid,tid) values('.$this->occid.','.$clid.','.$clTid.')';
                //echo $sqlCl2;
                if(!$this->conn->query($sqlCl2)){
                    $status .= '(WARNING adding voucher link.';
                }
            }
        }
        return $status;
    }

    public function getErrorStr(): ?string
    {
        if($this->errorArr) {
            return implode('; ', $this->errorArr);
        }

        return '';
    }

    private function encodeStrTargeted($inStr,$inCharset,$outCharset): string
    {
        return $inStr;
    }

    protected function encodeStr($inStr): string
    {
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
        $replace = array("'","'",'"','"','*','-','-');
        return str_replace($search, $replace, $inStr);
    }

}
