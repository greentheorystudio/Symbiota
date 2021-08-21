<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceEditorDeterminations.php');
include_once(__DIR__ . '/OccurrenceEditorImages.php');
include_once(__DIR__ . '/OccurrenceDuplicate.php');
include_once(__DIR__ . '/SOLRManager.php');
include_once(__DIR__ . '/UuidFactory.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceEditorManager {

    protected $conn;
    protected $occid;
    private $collId = 0;
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
            $connection = new DbConnection();
            $this->conn = $connection->getConnection();
        }
        $this->occFieldArr = array('dbpk', 'catalognumber', 'othercatalognumbers', 'occurrenceid','family', 'scientificname', 'sciname',
            'tidinterpreted', 'scientificnameauthorship', 'identifiedby', 'dateidentified', 'identificationreferences',
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
            $this->occid = Sanitizer::cleanInStr($id);
        }
    }

    public function getOccId(){
        return $this->occid;
    }

    public function setCollId($id): void
    {
        if($id && is_numeric($id)){
            if($id !== $this->collId){
                unset($this->collMap);
                $this->collMap = array();
            }
            $this->collId = Sanitizer::cleanInStr($id);
        }
    }

    public function getCollMap(): array
    {
        if(!$this->collMap){
            $sqlWhere = '';
            if($this->collId){
                $sqlWhere .= 'WHERE (c.collid = '.$this->collId.')';
            }
            elseif($this->occid){
                $sqlWhere .= 'INNER JOIN omoccurrences o ON c.collid = o.collid '.
                    'WHERE (o.occid = '.$this->occid.')';
            }
            if($sqlWhere){
                $sql = 'SELECT c.collid, c.collectionname, c.institutioncode, c.collectioncode, c.colltype, c.managementtype '.
                    'FROM omcollections c '.$sqlWhere;
                $rs = $this->conn->query($sql);
                if($row = $rs->fetch_object()){
                    $this->collMap['collid'] = $row->collid;
                    $this->collMap['collectionname'] = Sanitizer::cleanOutStr($row->collectionname);
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

    public function setCrowdSourceMode($m): void
    {
        $this->crowdSourceMode = $m;
    }

    public function setExsiccatiMode($exsMode): void
    {
        $this->exsiccatiMode = $exsMode;
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
            if(array_key_exists('q_ocrfrag',$_REQUEST) && $_REQUEST['q_ocrfrag']) {
                $this->qryArr['ocr'] = trim($_REQUEST['q_ocrfrag']);
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
            $this->qryArr = json_decode($_SESSION['editorquery'],true);
        }
    }

    public function getQueryVariables(): array
    {
        return $this->qryArr;
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
                $sqlWhere .= 'AND (o2.catalognumber IS NULL) ';
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
                        $term1 = Sanitizer::cleanInStr(substr($v,0,$p));
                        $term2 = Sanitizer::cleanInStr(substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $catNumIsNum = true;
                            if($isOccid){
                                $iBetweenFrag[] = '(o2.occid BETWEEN '.$term1.' AND '.$term2.')';
                            }
                            else{
                                $iBetweenFrag[] = '(o2.catalogNumber BETWEEN '.$term1.' AND '.$term2.')';
                            }
                        }
                        else{
                            $catTerm = 'o2.catalogNumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
                            if(strlen($term1) === strlen($term2)) {
                                $catTerm .= ' AND length(o2.catalogNumber) = ' . strlen($term2);
                            }
                            $iBetweenFrag[] = '('.$catTerm.')';
                        }
                    }
                    else{
                        $vStr = Sanitizer::cleanInStr($v);
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
                                $iWhere .= 'OR (o2.occid '. $term[0] .' '.trim(substr($term,1)).') ';
                            }
                            else{
                                $iWhere .= 'OR (o2.occid = '.$term.') ';
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
                                $iWhere .= 'OR (o2.catalognumber '. $term[0] .' '.$tStr.') ';
                            }
                            else{
                                $iWhere .= 'OR (o2.catalognumber = "'.$term.'") ';
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
                $sqlWhere .= 'AND (o2.othercatalognumbers IS NULL) ';
            }
            else{
                $ocnArr = explode(',',$this->qryArr['ocn']);
                $ocnBetweenFrag = array();
                $ocnInFrag = array();
                foreach($ocnArr as $v){
                    $v = Sanitizer::cleanInStr($v);
                    if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$v)){
                        $v = str_ireplace(array('>',' and ','<'),array('',' - ',''),$v);
                    }
                    if(strpos('%',$v) !== false){
                        $ocnBetweenFrag[] = '(o2.othercatalognumbers LIKE "'.$v.'")';
                    }
                    elseif($p = strpos($v,' - ')){
                        $term1 = trim(substr($v,0,$p));
                        $term2 = trim(substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $otherCatNumIsNum = true;
                            $ocnBetweenFrag[] = '(o2.othercatalognumbers BETWEEN '.$term1.' AND '.$term2.')';
                        }
                        else{
                            $ocnTerm = 'o2.othercatalognumbers BETWEEN "'.$term1.'" AND "'.$term2.'"';
                            if(strlen($term1) === strlen($term2)) {
                                $ocnTerm .= ' AND length(o2.othercatalognumbers) = ' . strlen($term2);
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
                            $ocnWhere .= 'OR (o2.othercatalognumbers '. $term[0] .' '.$tStr.') ';
                        }
                        else{
                            $ocnWhere .= 'OR (o2.othercatalognumbers = "'.$term.'") ';
                        }
                    }
                }
                $sqlWhere .= 'AND ('.substr($ocnWhere,3).') ';
            }
        }
        if(array_key_exists('rn',$this->qryArr)){
            if(strtolower($this->qryArr['rn']) === 'is null'){
                $sqlWhere .= 'AND (o2.recordnumber IS NULL) ';
            }
            else{
                $rnArr = explode(',',$this->qryArr['rn']);
                $rnBetweenFrag = array();
                $rnInFrag = array();
                foreach($rnArr as $v){
                    $v = Sanitizer::cleanInStr($v);
                    if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$v)){
                        $v = str_ireplace(array('>',' and ','<'),array('',' - ',''),$v);
                    }
                    if($p = strpos($v,' - ')){
                        $term1 = trim(substr($v,0,$p));
                        $term2 = trim(substr($v,$p+3));
                        if(is_numeric($term1) && is_numeric($term2)){
                            $rnBetweenFrag[] = '(o2.recordnumber BETWEEN '.$term1.' AND '.$term2.')';
                        }
                        else{
                            $catTerm = 'o2.recordnumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
                            if(strlen($term1) === strlen($term2)) {
                                $catTerm .= ' AND length(o2.recordnumber) = ' . strlen($term2);
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
                        $rnWhere .= 'OR (o2.recordnumber '.$term.') ';
                    }
                }
                $sqlWhere .= 'AND ('.substr($rnWhere,3).') ';
            }
        }
        if(array_key_exists('rb',$this->qryArr)){
            if(strtolower($this->qryArr['rb']) === 'is null'){
                $sqlWhere .= 'AND (o2.recordedby IS NULL) ';
            }
            elseif(strncmp($this->qryArr['rb'], '%', 1) === 0){
                $collStr = Sanitizer::cleanInStr(substr($this->qryArr['rb'],1));
                if(strlen($collStr) < 4 || strtolower($collStr) === 'best'){
                    $sqlWhere .= 'AND (o2.recordedby LIKE "%'.$collStr.'%") ';
                }
                else{
                    $sqlWhere .= 'AND (MATCH(f.recordedby) AGAINST("'.$collStr.'")) ';
                }
            }
            else{
                $sqlWhere .= 'AND (o2.recordedby LIKE "'.Sanitizer::cleanInStr($this->qryArr['rb']).'%") ';
            }
        }
        if(array_key_exists('ed',$this->qryArr)){
            if(strtolower($this->qryArr['ed']) === 'is null'){
                $sqlWhere .= 'AND (o2.eventdate IS NULL) ';
            }
            else{
                $edv = Sanitizer::cleanInStr($this->qryArr['ed']);
                if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$edv)){
                    $edv = str_ireplace(array('>',' and ','<'),array('',' - ',''),$edv);
                }
                $edv = str_replace(' to ',' - ',$edv);
                if($p = strpos($edv,' - ')){
                    $sqlWhere .= 'AND (o2.eventdate BETWEEN "'.trim(substr($edv,0,$p)).'" AND "'.trim(substr($edv,$p+3)).'") ';
                }
                elseif(strncmp($edv, '<', 1) === 0 || strncmp($edv, '>', 1) === 0){
                    $sqlWhere .= 'AND (o2.eventdate '. $edv[0] .' "'.trim(substr($edv,1)).'") ';
                }
                else{
                    $sqlWhere .= 'AND (o2.eventdate = "'.$edv.'") ';
                }
            }
        }
        if(array_key_exists('eb',$this->qryArr)){
            if(strtolower($this->qryArr['eb']) === 'is null'){
                $sqlWhere .= 'AND (o2.recordEnteredBy IS NULL) ';
            }
            else{
                $sqlWhere .= 'AND (o2.recordEnteredBy = "'.Sanitizer::cleanInStr($this->qryArr['eb']).'") ';
            }
        }
        if(array_key_exists('ouid',$this->qryArr) && is_numeric($this->qryArr['ouid'])){
            $sqlWhere .= 'AND (o2.observeruid = '.$this->qryArr['ouid'].') ';
        }
        if(array_key_exists('de',$this->qryArr)){
            $de = Sanitizer::cleanInStr($this->qryArr['de']);
            if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$de)){
                $de = str_ireplace(array('>',' and ','<'),array('',' - ',''),$de);
            }
            $de = str_replace(' to ',' - ',$de);
            if($p = strpos($de,' - ')){
                $sqlWhere .= 'AND (DATE(o2.dateentered) BETWEEN "'.trim(substr($de,0,$p)).'" AND "'.trim(substr($de,$p+3)).'") ';
            }
            elseif(strncmp($de, '<', 1) === 0 || strncmp($de, '>', 1) === 0){
                $sqlWhere .= 'AND (o2.dateentered '. $de[0] .' "'.trim(substr($de,1)).'") ';
            }
            else{
                $sqlWhere .= 'AND (DATE(o2.dateentered) = "'.$de.'") ';
            }
        }
        if(array_key_exists('dm',$this->qryArr)){
            $dm = Sanitizer::cleanInStr($this->qryArr['dm']);
            if(preg_match('/^>.*\s{1,3}AND\s{1,3}<./i',$dm)){
                $dm = str_ireplace(array('>',' and ','<'),array('',' - ',''),$dm);
            }
            $dm = str_replace(' to ',' - ',$dm);
            if($p = strpos($dm,' - ')){
                $sqlWhere .= 'AND (DATE(o2.datelastmodified) BETWEEN "'.trim(substr($dm,0,$p)).'" AND "'.trim(substr($dm,$p+3)).'") ';
            }
            elseif(strncmp($dm, '<', 1) === 0 || strncmp($dm, '>', 1) === 0){
                $sqlWhere .= 'AND (o2.datelastmodified '. $dm[0] .' "'.trim(substr($dm,1)).'") ';
            }
            else{
                $sqlWhere .= 'AND (DATE(o2.datelastmodified) = "'.$dm.'") ';
            }
        }
        if(array_key_exists('ps',$this->qryArr)){
            if($this->qryArr['ps'] === 'isnull'){
                $sqlWhere .= 'AND (o2.processingstatus IS NULL) ';
            }
            else{
                $sqlWhere .= 'AND (o2.processingstatus = "'.Sanitizer::cleanInStr($this->qryArr['ps']).'") ';
            }
        }
        if(array_key_exists('woi',$this->qryArr)){
            $sqlWhere .= 'AND (i.imgid IS NULL) ';
        }
        if(array_key_exists('ocr',$this->qryArr)){
            $sqlWhere .= 'AND (ocr.rawstr LIKE "%'.Sanitizer::cleanInStr($this->qryArr['ocr']).'%") ';
        }
        if(array_key_exists('exsid',$this->qryArr) && is_numeric($this->qryArr['exsid'])){
            $sqlWhere .= 'AND (exn.ometid = '.$this->qryArr['exsid'].') ';
        }
        for($x=1;$x<6;$x++){
            $cao = (array_key_exists('cao'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['cao'.$x]):'');
            $cop = (array_key_exists('cop'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['cop'.$x]):'');
            $cf = (array_key_exists('cf'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['cf'.$x]):'');
            $ct = (array_key_exists('ct'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['ct'.$x]):'');
            $cv = (array_key_exists('cv'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['cv'.$x]):'');
            $ccp = (array_key_exists('ccp'.$x,$this->qryArr)?Sanitizer::cleanInStr($this->qryArr['ccp'.$x]):'');
            if(!$cao) {
                $cao = 'AND';
            }
            if($cf){
                if($cf === 'ocrFragment'){
                    $cf = 'ocr.rawstr';
                }
                elseif($cf === 'username'){
                    $cf = 'ul.username';
                }
                elseif($cf === 'verbatimsciname'){
                    $cf = 'oas.verbatimsciname';
                }
                else{
                    $cf = 'o2.'.$cf;
                }
                if($ct === 'NULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' IS NULL) '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'NOTNULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' IS NOT NULL) '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'NOT EQUALS' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' <> '.$cv.') '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'GREATER' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' > '.$cv.') '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'LESS' && $cv){
                    if(!is_numeric($cv)) {
                        $cv = '"' . $cv . '"';
                    }
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' < '.$cv.') '.($ccp?$ccp.' ':'');
                }
                elseif($ct === 'LIKE' && $cv){
                    if(strpos($cv,'%') !== false){
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' LIKE "'.$cv.'") '.($ccp?$ccp.' ':'');
                    }
                    else{
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' LIKE "%'.$cv.'%") '.($ccp?$ccp.' ':'');
                    }
                }
                elseif($ct === 'STARTS' && $cv){
                    if(strpos($cv,'%') !== false){
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' LIKE "'.$cv.'") '.($ccp?$ccp.' ':'');
                    }
                    else{
                        $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' LIKE "'.$cv.'%") '.($ccp?$ccp.' ':'');
                    }
                }
                elseif($cv){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' ('.$cf.' = "'.$cv.'") '.($ccp?$ccp.' ':'');
                }
                if($cf === 'oas.verbatimsciname' && $ct !== 'NULL'){
                    $sqlWhere .= $cao.($cop?' '.$cop:'').' (oas.relationship = "host") '.($ccp?$ccp.' ':'');
                }
            }
            else if($x > 1 && !$cf && $ccp){
                $sqlWhere .= ' '.$ccp.' ';
            }
        }
        if($this->crowdSourceMode){
            $sqlWhere .= 'AND (q.reviewstatus = 0) ';
        }
        if($sqlWhere){
            if(strncmp($sqlWhere, 'OR', 2) === 0){
                $sqlWhere = 'WHERE ('.substr($sqlWhere,3).') ';
            }
            else{
                $sqlWhere = 'WHERE ('.substr($sqlWhere,4).') ';
            }
        }
        if($this->collId) {
            $sqlWhere .= ($sqlWhere ? 'AND (o2.collid = ' . $this->collId . ') ' : 'WHERE (o2.collid = ' . $this->collId . ') ');
        }
        if(isset($this->qryArr['orderby'])){
            $orderBy = Sanitizer::cleanInStr($this->qryArr['orderby']);
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

    public function getQueryRecordCount($reset = null){
        if(!$reset && array_key_exists('rc',$this->qryArr)) {
            return (int)$this->qryArr['rc'];
        }
        $recCnt = 0;
        if($this->sqlWhere){
            $sql = 'SELECT COUNT(DISTINCT o2.occid) AS reccnt FROM omoccurrences AS o2 ';
            $sql = $this->addTableJoins($sql);
            $sqlWhere = $this->sqlWhere;
            $sql .= $sqlWhere;
            //echo '<div>'.$sql.'</div>'; exit;
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $recCnt = (int)$r->reccnt;
            }
            $rs->free();
            $this->qryArr['rc'] = $recCnt;
            $_SESSION['editorquery'] = json_encode($this->qryArr);
        }
        return $recCnt;
    }

    public function getOccurMap(): array
    {
        if(!$this->occurrenceMap && ($this->occid || $this->sqlWhere)){
            $this->setOccurArr();
        }
        return $this->occurrenceMap;
    }

    protected function setOccurArr(): void
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.'.implode(',o.',$this->occFieldArr);
        if($this->sqlWhere && strpos($this->sqlWhere,'oas.verbatimsciname') !== false){
            $sql .= ', oas.verbatimsciname';
        }
        $sql .= ' FROM omoccurrences o ';
        if($this->sqlWhere && strpos($this->sqlWhere,'oas.verbatimsciname') !== false){
            $sql .= 'LEFT JOIN omoccurassociations oas ON o.occid = oas.occid ';
        }
        if($this->occid){
            $sql .= 'WHERE (o.occid = '.$this->occid.')';
        }
        elseif($this->sqlWhere){
            $sql .= 'INNER JOIN (SELECT o2.occid FROM omoccurrences o2 ';
            $sql = $this->addTableJoins($sql);
            $sql .= $this->sqlWhere;
            $sql .= ') AS a ON o.occid = a.occid ';
            if($this->orderByStr) {
                $sql .= 'ORDER BY (o.' . $this->orderByStr . ') ' . $this->qryArr['orderbydir'] . ' ';
            }
            $sql .= 'LIMIT '.($this->occIndex>0?$this->occIndex.',':'').$this->recLimit;
        }
        if($sql){
            //echo "<div>".$sql."</div>";
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
            $this->occurrenceMap = $this->cleanOutArr($retArr);
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
        if(strpos($this->sqlWhere,'ocr.rawstr')){
            if(strpos($this->sqlWhere,'ocr.rawstr IS NULL') && array_key_exists('io',$this->qryArr)){
                $sql .= 'INNER JOIN images i ON o2.occid = i.occid LEFT JOIN specprocessorrawlabels ocr ON i.imgid = ocr.imgid ';
            }
            elseif(strpos($this->sqlWhere,'ocr.rawstr IS NULL')){
                $sql .= 'LEFT JOIN images i ON o2.occid = i.occid LEFT JOIN specprocessorrawlabels ocr ON i.imgid = ocr.imgid ';
            }
            else{
                $sql .= 'INNER JOIN images i ON o2.occid = i.occid INNER JOIN specprocessorrawlabels ocr ON i.imgid = ocr.imgid ';
            }
        }
        elseif(array_key_exists('io',$this->qryArr)){
            $sql .= 'INNER JOIN images i ON o2.occid = i.occid ';
        }
        elseif(array_key_exists('woi',$this->qryArr)){
            $sql .= 'LEFT JOIN images i ON o2.occid = i.occid ';
        }
        if(strpos($this->sqlWhere,'ul.username')){
            $sql .= 'LEFT JOIN omoccuredits ome ON o2.occid = ome.occid LEFT JOIN users ul ON ome.uid = ul.uid ';
        }
        if(strpos($this->sqlWhere,'oas.verbatimsciname')){
            $sql .= 'LEFT JOIN omoccurassociations oas ON o2.occid = oas.occid ';
        }
        if(strpos($this->sqlWhere,'exn.ometid')){
            $sql .= 'INNER JOIN omexsiccatiocclink exocc ON o2.occid = exocc.occid INNER JOIN omexsiccatinumbers exn ON exocc.omenid = exn.omenid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')){
            $sql .= 'INNER JOIN omoccurrencesfulltext f ON o2.occid = f.occid ';
        }
        if($this->crowdSourceMode){
            $sql .= 'INNER JOIN omcrowdsourcequeue q ON q.occid = o2.occid ';
        }

        return $sql;
    }

    public function editOccurrence($occArr,$autoCommit): string
    {
        $quickHostEntered = false;
        if(!$autoCommit && $this->getObserverUid() === $GLOBALS['SYMB_UID']){
            $autoCommit = 1;
        }
        if($autoCommit === 3){
            $autoCommit = 0;
        }

        $editedFields = trim($occArr['editedfields']);
        $editArr = array_unique(explode(';',$editedFields));
        if($editArr){
            foreach($editArr as $k => $fName){
                $trimmedName = trim($fName);
                if($trimmedName === 'host' || $trimmedName === 'hostassocid'){
                    $quickHostEntered = true;
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                }
                if(!$trimmedName){
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                }
                else if(strcasecmp($fName, 'exstitle') === 0) {
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                    if($editArr && (is_string($k) || is_int($k))){
                        $editArr[$k] = 'title';
                    }
                }

                if($trimmedName === 'host' || $trimmedName === 'hostassocid'){
                    $quickHostEntered = true;
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                }
                if(!$trimmedName){
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                }
                else if(strcasecmp($fName, 'exstitle') === 0) {
                    if($editArr && (is_string($k) || is_int($k))){
                        unset($editArr[$k]);
                    }
                    if($editArr && (is_string($k) || is_int($k))){
                        $editArr[$k] = 'title';
                    }
                }
            }
        }
        if($editArr || $quickHostEntered){
            if($editArr){
                if($occArr['sciname'] && !$occArr['tidinterpreted'] && in_array('sciname', $editArr, true)){
                    $sql2 = 'SELECT t.tid, t.author, ts.family '.
                        'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
                        'WHERE ts.taxauthid = 1 AND sciname = "'.$occArr['sciname'].'"';
                    $rs2 = $this->conn->query($sql2);
                    while($r2 = $rs2->fetch_object()){
                        $occArr['tidinterpreted'] = $r2->tid;
                        if(!$occArr['scientificnameauthorship']) {
                            $occArr['scientificnameauthorship'] = $r2->author;
                        }
                        if(!$occArr['family']) {
                            $occArr['family'] = $r2->family;
                        }
                    }
                    $rs2->free();
                }
                if(in_array('ometid', $editArr, true) || in_array('exsnumber', $editArr, true)){
                    $sql = 'SELECT '.str_replace(array('ometid','exstitle'),array('et.ometid','et.title'),($editArr?implode(',',$editArr):'')).',et.title'.
                        (in_array('processingstatus', $editArr, true) ?'':',processingstatus').(in_array('recordenteredby', $editArr, true) ?'':',recordenteredby').
                        ' FROM omoccurrences o LEFT JOIN omexsiccatiocclink el ON o.occid = el.occid '.
                        'LEFT JOIN omexsiccatinumbers en ON el.omenid = en.omenid '.
                        'LEFT JOIN omexsiccatititles et ON en.ometid = et.ometid '.
                        'WHERE o.occid = '.$occArr['occid'];
                }
                else{
                    $sql = 'SELECT '.($editArr?implode(',',$editArr):'').(in_array('processingstatus', $editArr, true) ?'':',processingstatus').
                        (in_array('recordenteredby', $editArr, true) ?'':',recordenteredby').
                        ' FROM omoccurrences WHERE occid = '.$occArr['occid'];
                }
                //echo $sql;
                $rs = $this->conn->query($sql);
                $oldValues = $rs->fetch_assoc();
                $rs->free();

                if($oldValues['recordenteredby'] === 'preprocessed' || (!$oldValues['recordenteredby'] && ($oldValues['processingstatus'] === 'unprocessed' || $oldValues['processingstatus'] === 'stage 1'))){
                    $occArr['recordenteredby'] = $GLOBALS['USERNAME'];
                    if($editArr){
                        $editArr[] = 'recordenteredby';
                    }
                }

                $sqlEditsBase = 'INSERT INTO omoccuredits(occid,reviewstatus,appliedstatus,uid,fieldname,fieldvaluenew,fieldvalueold) '.
                    'VALUES ('.$occArr['occid'].',1,'.($autoCommit?'1':'0').','.$GLOBALS['SYMB_UID'].',';
                foreach($editArr as $fieldName){
                    if(is_string($fieldName) || is_int($fieldName)){
                        if(!array_key_exists($fieldName,$occArr)){
                            $occArr[$fieldName] = 0;
                        }
                        $newValue = Sanitizer::cleanInStr($occArr[$fieldName]);
                        $oldValue = Sanitizer::cleanInStr($oldValues[$fieldName]);
                        if(($oldValue !== $newValue) && $fieldName !== 'tidinterpreted') {
                            if($fieldName === 'ometid'){
                                $exsTitleStr = '';
                                $sql = 'SELECT title FROM omexsiccatititles WHERE ometid = '.$occArr['ometid'];
                                $rs = $this->conn->query($sql);
                                if($r = $rs->fetch_object()){
                                    $exsTitleStr = $r->title;
                                }
                                $rs->free();
                                if($newValue) {
                                    $newValue = $exsTitleStr . ' (ometid: ' . $occArr['ometid'] . ')';
                                }
                                if($oldValue) {
                                    $oldValue = $oldValues['title'] . ' (ometid: ' . $oldValues['ometid'] . ')';
                                }
                            }
                            $sqlEdit = $sqlEditsBase.'"'.$fieldName.'","'.$newValue.'","'.$oldValue.'")';
                            //echo '<div>'.$sqlEdit.'</div>';
                            $this->conn->query($sqlEdit);
                        }
                    }
                }
            }
            if($autoCommit){
                $status = 'SUCCESS: edits submitted and activated ';
                $sql = '';
                if(array_key_exists('autoprocessingstatus',$occArr) && $occArr['autoprocessingstatus']){
                    $occArr['processingstatus'] = $occArr['autoprocessingstatus'];
                }
                if(isset($occArr['institutioncode']) && $occArr['institutioncode'] === $this->collMap['institutioncode']) {
                    $occArr['institutioncode'] = '';
                }
                if(isset($occArr['collectioncode']) && $occArr['collectioncode'] === $this->collMap['collectioncode']) {
                    $occArr['collectioncode'] = '';
                }
                if(isset($occArr['ownerinstitutioncode']) && $occArr['ownerinstitutioncode'] === $this->collMap['institutioncode']) {
                    $occArr['ownerinstitutioncode'] = '';
                }
                foreach($occArr as $oField => $ov){
                    if($oField !== 'observeruid' && in_array($oField, $this->occFieldArr, true)){
                        $vStr = Sanitizer::cleanInStr($ov);
                        $sql .= ','.$oField.' = '.($vStr!==''?'"'.$vStr.'"':'NULL');
                        if(array_key_exists($this->occid,$this->occurrenceMap) && array_key_exists($oField,$this->occurrenceMap[$this->occid])){
                            $this->occurrenceMap[$this->occid][$oField] = $vStr;
                        }
                    }
                }
                if(in_array('tidinterpreted', $editArr, true)){
                    $sqlImgTid = 'UPDATE images SET tid = '.($occArr['tidinterpreted']?:'NULL').' '.
                        'WHERE occid = ('.$occArr['occid'].')';
                    $this->conn->query($sqlImgTid);
                }
                if($quickHostEntered){
                    if($occArr['hostassocid']){
                        if($occArr['host']){
                            $sqlHost = 'UPDATE omoccurassociations SET verbatimsciname = "'.$occArr['host'].'" '.
                                'WHERE associd = '.$occArr['hostassocid'].' ';
                        }
                        else{
                            $sqlHost = 'DELETE FROM omoccurassociations WHERE associd = '.$occArr['hostassocid'].' ';
                        }
                    }
                    else{
                        $sqlHost = 'INSERT INTO omoccurassociations(occid,relationship,verbatimsciname) '.
                            'VALUES('.$occArr['occid'].',"host","'.$occArr['host'].'")';
                    }
                    $this->conn->query($sqlHost);
                }
                $sql = 'UPDATE omoccurrences SET '.substr($sql,1).' WHERE (occid = '.$occArr['occid'].')';
                //echo $sql;
                if($this->conn->query($sql)){
                    if(strtolower($occArr['processingstatus']) !== 'unprocessed'){
                        $isVolunteer = true;
                        if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($this->collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
                            $isVolunteer = false;
                        }
                        elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($this->collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
                            $isVolunteer = false;
                        }

                        $sql = 'UPDATE omcrowdsourcequeue SET uidprocessor = '.$GLOBALS['SYMB_UID'].', reviewstatus = 5 ';
                        if(!$isVolunteer) {
                            $sql .= ', isvolunteer = 0 ';
                        }
                        $sql .= 'WHERE (uidprocessor IS NULL) AND (occid = '.$occArr['occid'].')';
                        if(!$this->conn->query($sql)){
                            $status = 'ERROR tagging user as the crowdsourcer (#'.$occArr['occid'].').';
                        }
                    }
                    if(in_array('ometid', $editArr, true) || in_array('exsnumber', $editArr, true)){
                        $ometid = Sanitizer::cleanInStr($occArr['ometid']);
                        $exsNumber = Sanitizer::cleanInStr($occArr['exsnumber']);
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
                                    $status = 'ERROR adding exsiccati number.';
                                }
                            }
                            if($exsNumberId){
                                $sql1 = 'REPLACE INTO omexsiccatiocclink(omenid, occid) '.
                                    'VALUES('.$exsNumberId.','.$occArr['occid'].')';
                                //echo $sql1;
                                if(!$this->conn->query($sql1)){
                                    $status = 'ERROR adding exsiccati.';
                                }
                            }
                        }
                        else{
                            $sql = 'DELETE FROM omexsiccatiocclink WHERE occid = '.$occArr['occid'];
                            $this->conn->query($sql);
                        }
                    }
                    if(isset($occArr['linkdupe']) && $occArr['linkdupe']){
                        $dupTitle = $occArr['recordedby'].' '.$occArr['recordnumber'].' '.$occArr['eventdate'];
                        $status .= $this->linkDuplicates($occArr['linkdupe'],$dupTitle);
                    }
                }
                else{
                    $status = 'ERROR: failed to edit occurrence record (#'.$occArr['occid'].').';
                }
            }
            else{
                $status = 'Edits submitted, but not activated.<br/> '.
                    'Once edits are reviewed and approved by a data manager, they will be activated.<br/> '.
                    'Thank you for aiding us in improving the data. ';
            }
        }
        else{
            $status = 'ERROR: edits empty for occid #'.$occArr['occid'].'.';
        }
        return $status;
    }

    public function addOccurrence($occArr): string
    {
        $status = 'SUCCESS: new occurrence record submitted successfully ';
        if($occArr){
            $fieldArr = array('basisOfRecord' => 's', 'catalogNumber' => 's', 'otherCatalogNumbers' => 's', 'occurrenceid' => 's',
                'ownerInstitutionCode' => 's', 'institutionCode' => 's', 'collectionCode' => 's',
                'family' => 's', 'sciname' => 's', 'tidinterpreted' => 'n', 'scientificNameAuthorship' => 's', 'identifiedBy' => 's', 'dateIdentified' => 's',
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
                $occArr['basisofrecord'] = (strpos($this->collMap['colltype'], 'Observations') !== false ? 'HumanObservation' : 'PreservedSpecimen');
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
                        $sql .= ', "'.Sanitizer::cleanInStr($fieldValue).'"';
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

                $guid = UuidFactory::getUuidV4();
                if(!$this->conn->query('INSERT INTO guidoccurrences(guid,occid) VALUES("'.$guid.'",'.$this->occid.')')){
                    $status .= '(WARNING: Symbiota GUID mapping failed) ';
                }
                if(isset($occArr['ometid'], $occArr['exsnumber'])){
                    $ometid = Sanitizer::cleanInStr($occArr['ometid']);
                    $exsNumber = Sanitizer::cleanInStr($occArr['exsnumber']);
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
                        'VALUES('.$this->occid.',"host","'.Sanitizer::cleanInStr($occArr['host']).'")';
                    if(!$this->conn->query($sql1)){
                        $status .= '(WARNING adding host.) ';
                    }
                }

                if(isset($occArr['confidenceranking']) && $occArr['confidenceranking']){
                    $this->editIdentificationRanking($occArr['confidenceranking'],'');
                }
                if(isset($occArr['clidvoucher'], $occArr['tidinterpreted'])){
                    $status .= $this->linkChecklistVoucher($occArr['clidvoucher'],$occArr['tidinterpreted']);
                }
                if(isset($occArr['linkdupe']) && $occArr['linkdupe']){
                    $dupTitle = $occArr['recordedby'].' '.$occArr['recordnumber'].' '.$occArr['eventdate'];
                    $status .= $this->linkDuplicates($occArr['linkdupe'],$dupTitle);
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
                        $archiveArr[$k] = $this->encodeStrTargeted($v, $GLOBALS['CHARSET'], 'utf8');
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
                            $detArr[$detId][$k] = $this->encodeStrTargeted($v, $GLOBALS['CHARSET'], 'utf8');
                        }
                    }
                    $detObj = json_encode($detArr[$detId]);
                    $sqlArchive = 'UPDATE guidoccurdeterminations '.
                        'SET archivestatus = 1, archiveobj = "'.Sanitizer::cleanInStr($this->encodeStrTargeted($detObj,'utf8',$GLOBALS['CHARSET'])).'" '.
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
                            $imgArr[$imgId][$k] = $this->encodeStrTargeted($v, $GLOBALS['CHARSET'], 'utf8');
                        }
                    }
                    $imgObj = json_encode($imgArr[$imgId]);
                    $sqlArchive = 'UPDATE guidimages '.
                        'SET archivestatus = 1, archiveobj = "'.Sanitizer::cleanInStr($this->encodeStrTargeted($imgObj,'utf8',$GLOBALS['CHARSET'])).'" '.
                        'WHERE (imgid = '.$imgId.')';
                    $this->conn->query($sqlArchive);
                }
                $rs->free();
                $archiveArr['imgs'] = $imgArr;
                if($imgArr){
                    $imgidStr = implode(',',array_keys($imgArr));
                    if(!$this->conn->query('DELETE FROM specprocessorrawlabels WHERE (imgid IN('.$imgidStr.'))')){
                        $this->errorArr[] = 'ERROR removing OCR blocks linked to images.';
                    }
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
                    'FROM omexsiccatiocclink l INNER JOIN omexsiccatinumbers n ON l.omenid = n.omenid '.
                    'INNER JOIN omexsiccatititles t ON n.ometid = t.ometid '.
                    'WHERE l.occid = '.$delOccid;
                $rs = $this->conn->query($sql);
                if($r = $rs->fetch_assoc()){
                    foreach($r as $k => $v){
                        if($v) {
                            $exsArr[$k] = $this->encodeStrTargeted($v, $GLOBALS['CHARSET'], 'utf8');
                        }
                    }
                }
                $rs->free();
                $archiveArr['exsiccati'] = $exsArr;

                $archiveArr['dateDeleted'] = date('r').' by '.$GLOBALS['USER_DISPLAY_NAME'];
                $archiveObj = json_encode($archiveArr);
                $sqlArchive = 'UPDATE guidoccurrences '.
                    'SET archivestatus = 1, archiveobj = "'.Sanitizer::cleanInStr($this->encodeStrTargeted($archiveObj,'utf8',$GLOBALS['CHARSET'])).'" '.
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
                    $sqlFrag .= ','.$k.'="'.Sanitizer::cleanInStr($v).'"';
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

            if($GLOBALS['QUICK_HOST_ENTRY_IS_ACTIVE']){
                $sql = 'UPDATE omoccurassociations SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
                if(!$this->conn->query($sql)){
                    $this->errorArr[] .= '; ERROR remapping associations.';
                    $status = false;
                }
            }

            $sql = 'UPDATE omoccurcomments SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping comments.';
                $status = false;
            }

            $sql = 'UPDATE omoccurgenetic SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping genetic resources.';
                $status = false;
            }

            $sql = 'UPDATE omoccuridentifiers SET occid = '.$targetOccid.' WHERE occid = '.$sourceOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] .= '; ERROR remapping occurrence identifiers.';
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
                $solrManager = new SOLRManager();
                $solrManager->deleteSOLRDocument($sourceOccid);
            }
        }
        return $status;
    }

    public function transferOccurrence($targetOccid,$transferCollid): bool
    {
        $status = true;
        if(is_numeric($targetOccid) && is_numeric($transferCollid)){
            $sql = 'UPDATE omoccurrences SET collid = '.$transferCollid.' WHERE occid = '.$targetOccid;
            if(!$this->conn->query($sql)){
                $this->errorArr[] = 'ERROR trying to delete occurrence record.';
                $status = false;
            }
        }
        return $status;
    }

    private function setLoanData(): void
    {
        $sql = 'SELECT l.loanid, l.datedue, i.institutioncode '.
            'FROM omoccurloanslink ll INNER JOIN omoccurloans l ON ll.loanid = l.loanid '.
            'INNER JOIN institutions i ON l.iidBorrower = i.iid '.
            'WHERE ll.returndate IS NULL AND l.dateclosed IS NULL AND occid = '.$this->occid;
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
            'FROM omexsiccatiocclink l INNER JOIN omexsiccatinumbers n ON l.omenid = n.omenid '.
            'INNER JOIN omexsiccatititles t ON n.ometid = t.ometid '.
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

    public function getObserverUid(): int
    {
        $obsId = 0;
        if($this->occurrenceMap && array_key_exists('observeruid',$this->occurrenceMap[$this->occid])){
            $obsId = (int)$this->occurrenceMap[$this->occid]['observeruid'];
        }
        elseif($this->occid){
            $this->setOccurArr();
            $obsId = (int)$this->occurrenceMap[$this->occid]['observeruid'];
        }
        return $obsId;
    }

    public function batchUpdateField($fieldName,$oldValue,$newValue,$buMatch): string
    {
        $statusStr = '';
        $fn = Sanitizer::cleanInStr($fieldName);
        $ov = Sanitizer::cleanInStr($oldValue);
        $nv = Sanitizer::cleanInStr($newValue);
        if($fn && ($ov || $nv)){
            $occidArr = array();
            $sqlOccid = 'SELECT DISTINCT o2.occid FROM omoccurrences o2 ';
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
                    '1 AS appliedStatus, '.$GLOBALS['SYMB_UID'].' AS uid'.($hasEditType?',1':'').' FROM omoccurrences o ';
                $sql2 .= $sqlWhere;
                //echo $sql2.'<br/>';
                if(!$this->conn->query($sql2)){
                    $statusStr = 'ERROR adding update to omoccuredits.';
                }

                $sql = 'UPDATE omoccurrences o SET o.'.$fn.' = '.$nvSqlFrag.' '.$sqlWhere;
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

        $fn = Sanitizer::cleanInStr($fieldName);
        $ov = Sanitizer::cleanInStr($oldValue);

        $sql = 'SELECT COUNT(o2.occid) AS retcnt '.
            'FROM omoccurrences o2 ';
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

    public function carryOverValues($fArr): array
    {
        $locArr = Array('recordedby','associatedcollectors','eventdate','verbatimeventdate','month','day','year',
            'startdayofyear','enddayofyear','country','stateprovince','county','municipality','locality','decimallatitude','decimallongitude',
            'verbatimcoordinates','coordinateuncertaintyinmeters','footprintwkt','geodeticdatum','georeferencedby','georeferenceprotocol',
            'georeferencesources','georeferenceverificationstatus','georeferenceremarks', 'locationid', 'fieldnumber',
            'minimumelevationinmeters','maximumelevationinmeters','verbatimelevation','minimumdepthinmeters','maximumdepthinmeters','verbatimdepth',
            'habitat','substrate','lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations',
            'associatedtaxa','basisofrecord','language','labelproject');
        return $this->cleanOutArr(array_intersect_key($fArr,array_flip($locArr)));
    }

    public function getIdentificationRanking(): array
    {
        $retArr = array();
        $sql = 'SELECT v.ovsid, v.ranking, v.notes, l.username '.
            'FROM omoccurverification v LEFT JOIN users l ON v.uid = l.uid '.
            'WHERE v.category = "identification" AND v.occid = '.$this->occid;
        //echo "<div>".$sql."</div>";
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $retArr['ovsid'] = $r->ovsid;
            $retArr['ranking'] = $r->ranking;
            $retArr['notes'] = $r->notes;
            $retArr['username'] = $r->username;
        }
        $rs->free();
        return $retArr;
    }

    public function editIdentificationRanking($ranking, $notes = null): string
    {
        $statusStr = '';
        if(is_numeric($ranking)){
            $sql = 'REPLACE INTO omoccurverification(occid,category,ranking,notes,uid) '.
                'VALUES('.$this->occid.',"identification",'.$ranking.','.($notes?'"'.Sanitizer::cleanInStr($notes).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
            if(!$this->conn->query($sql)){
                $statusStr .= 'WARNING editing/add confidence ranking failed.';
                //echo $sql;
            }
        }
        return $statusStr;
    }

    public function getVoucherChecklists(): array
    {
        $retArr = array();
        $sql = 'SELECT c.clid, c.name '.
            'FROM fmchecklists c INNER JOIN fmvouchers v ON c.clid = v.clid '.
            'WHERE v.occid = '.$this->occid;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->clid] = $r->name;
        }
        $rs->free();
        asort($retArr);
        return $retArr;
    }

    public function linkChecklistVoucher($clid,$tid): string
    {
        $status = '';
        if(is_numeric($clid) && is_numeric($tid)){
            $clTid = 0;
            $sqlCl = 'SELECT cl.tid '.
                'FROM fmchklsttaxalink cl INNER JOIN taxstatus ts1 ON cl.tid = ts1.tid '.
                'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
                'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = '.$tid.') AND (cl.clid = '.$clid.')';
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

    public function deleteChecklistVoucher($clid): string
    {
        $status = '';
        if(is_numeric($clid)){
            $sql = 'DELETE FROM fmvouchers WHERE clid = '.$clid.' AND occid = '.$this->occid;
            if(!$this->conn->query($sql)){
                $status = 'ERROR deleting voucher from checklist.';
            }
        }
        return $status;
    }

    public function getUserChecklists(): array
    {
        $retArr = array();
        if(isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
            $sql = 'SELECT clid, name, access '.
                'FROM fmchecklists '.
                'WHERE (clid IN('.implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']).')) ';
            //echo $sql; exit;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->clid] = $r->name.($r->access === 'private'?' (private)':'');
            }
            $rs->free();
            asort($retArr);
        }
        return $retArr;
    }

    private function linkDuplicates($occidStr,$dupTitle): string
    {
        $status = '';
        $dupManager = new OccurrenceDuplicate();
        $dupManager->linkDuplicates($this->occid,$occidStr,$dupTitle);
        return $status;
    }

    public function getGeneticArr(): array
    {
        $retArr = array();
        if($this->occid){
            $sql = 'SELECT idoccurgenetic, identifier, resourcename, locus, resourceurl, notes '.
                'FROM omoccurgenetic '.
                'WHERE occid = '.$this->occid;
            $result = $this->conn->query($sql);
            if($result){
                while($r = $result->fetch_object()){
                    $retArr[$r->idoccurgenetic]['id'] = $r->identifier;
                    $retArr[$r->idoccurgenetic]['name'] = $r->resourcename;
                    $retArr[$r->idoccurgenetic]['locus'] = $r->locus;
                    $retArr[$r->idoccurgenetic]['resourceurl'] = $r->resourceurl;
                    $retArr[$r->idoccurgenetic]['notes'] = $r->notes;
                }
                $result->free();
            }
        }
        return $retArr;
    }

    public function editGeneticResource($genArr): string
    {
        $sql = 'UPDATE omoccurgenetic SET '.
            'identifier = "'.Sanitizer::cleanInStr($genArr['identifier']).'", '.
            'resourcename = "'.Sanitizer::cleanInStr($genArr['resourcename']).'", '.
            'locus = '.($genArr['locus']?'"'.Sanitizer::cleanInStr($genArr['locus']).'"':'NULL').', '.
            'resourceurl = '.($genArr['resourceurl']?'"'.$genArr['resourceurl'].'"':'').', '.
            'notes = '.($genArr['notes']?'"'.Sanitizer::cleanInStr($genArr['notes']).'"':'NULL').' '.
            'WHERE idoccurgenetic = '.$genArr['genid'];
        if(!$this->conn->query($sql)){
            return 'ERROR editing genetic resource #'.$genArr['genid'].'.';
        }
        return 'Genetic resource editted successfully';
    }

    public function deleteGeneticResource($id): string
    {
        $sql = 'DELETE FROM omoccurgenetic WHERE idoccurgenetic = '.$id;
        if(!$this->conn->query($sql)){
            return 'ERROR deleting genetic resource #'.$id.'.';
        }
        return 'Genetic resource deleted successfully!';
    }

    public function addGeneticResource($genArr): string
    {
        $sql = 'INSERT INTO omoccurgenetic(occid, identifier, resourcename, locus, resourceurl, notes) '.
            'VALUES('.Sanitizer::cleanInStr($genArr['occid']).',"'.Sanitizer::cleanInStr($genArr['identifier']).'","'.
            Sanitizer::cleanInStr($genArr['resourcename']).'",'.
            ($genArr['locus']?'"'.Sanitizer::cleanInStr($genArr['locus']).'"':'NULL').','.
            ($genArr['resourceurl']?'"'.Sanitizer::cleanInStr($genArr['resourceurl']).'"':'NULL').','.
            ($genArr['notes']?'"'.Sanitizer::cleanInStr($genArr['notes']).'"':'NULL').')';
        if(!$this->conn->query($sql)){
            return 'ERROR Adding new genetic resource.';
        }
        return 'Genetic resource added successfully!';
    }

    public function getRawTextFragments(): array
    {
        $retArr = array();
        if($this->occid){
            $sql = 'SELECT r.prlid, r.imgid, r.rawstr, r.notes, r.source '.
                'FROM specprocessorrawlabels r INNER JOIN images i ON r.imgid = i.imgid '.
                'WHERE i.occid = '.$this->occid;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->imgid][$r->prlid]['raw'] = Sanitizer::cleanOutStr($r->rawstr);
                $retArr[$r->imgid][$r->prlid]['notes'] = Sanitizer::cleanOutStr($r->notes);
                $retArr[$r->imgid][$r->prlid]['source'] = Sanitizer::cleanOutStr($r->source);
            }
            $rs->free();
        }
        return $retArr;
    }

    public function insertTextFragment($imgId,$rawFrag,$notes,$source){
        $statusStr = '';
        if($imgId && $rawFrag){
            $sql = 'INSERT INTO specprocessorrawlabels(imgid,rawstr,notes,source) '.
                'VALUES ('.$imgId.',"'.$this->cleanRawFragment($rawFrag).'",'.
                ($notes?'"'.Sanitizer::cleanInStr($notes).'"':'NULL').','.
                ($source?'"'.Sanitizer::cleanInStr($source).'"':'NULL').')';
            //echo $sql;
            if($this->conn->query($sql)){
                $statusStr = $this->conn->insert_id;
            }
            else{
                $statusStr = 'ERROR: unable to INSERT text fragment.';
                $statusStr .= '; SQL = '.$sql;
            }
        }
        return $statusStr;
    }

    public function saveTextFragment($prlId,$rawFrag,$notes,$source): ?string
    {
        $statusStr = '';
        if($prlId && $rawFrag){
            $sql = 'UPDATE specprocessorrawlabels '.
                'SET rawstr = "'.$this->cleanRawFragment($rawFrag).'", '.
                'notes = '.($notes?'"'.Sanitizer::cleanInStr($notes).'"':'NULL').', '.
                'source = '.($source?'"'.Sanitizer::cleanInStr($source).'"':'NULL').' '.
                'WHERE (prlid = '.$prlId.')';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $statusStr = 'ERROR: unable to UPDATE text fragment.';
                $statusStr .= '; SQL = '.$sql;
            }
        }
        return $statusStr;
    }

    public function deleteTextFragment($prlId): ?string
    {
        $statusStr = '';
        if($prlId){
            $sql = 'DELETE FROM specprocessorrawlabels '.
                'WHERE (prlid = '.$prlId.')';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $statusStr = 'ERROR: unable DELETE text fragment.';
            }
        }
        return $statusStr;
    }

    public function getImageMap(): array
    {
        $imageMap = array();
        if($this->occid){
            $sql = 'SELECT imgid, url, thumbnailurl, originalurl, caption, photographer, photographeruid, '.
                'sourceurl, copyright, notes, occid, username, sortsequence, initialtimestamp '.
                'FROM images '.
                'WHERE (occid = '.$this->occid.') ORDER BY sortsequence';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $imgId = $row->imgid;
                $imageMap[$imgId]['url'] = $row->url;
                $imageMap[$imgId]['tnurl'] = $row->thumbnailurl;
                $imageMap[$imgId]['origurl'] = $row->originalurl;
                $imageMap[$imgId]['caption'] = Sanitizer::cleanOutStr($row->caption);
                $imageMap[$imgId]['photographer'] = Sanitizer::cleanOutStr($row->photographer);
                $imageMap[$imgId]['photographeruid'] = $row->photographeruid;
                $imageMap[$imgId]['sourceurl'] = $row->sourceurl;
                $imageMap[$imgId]['copyright'] = Sanitizer::cleanOutStr($row->copyright);
                $imageMap[$imgId]['notes'] = Sanitizer::cleanOutStr($row->notes);
                $imageMap[$imgId]['occid'] = $row->occid;
                $imageMap[$imgId]['username'] = Sanitizer::cleanOutStr($row->username);
                $imageMap[$imgId]['sortseq'] = $row->sortsequence;
                if(strpos($row->originalurl, 'api.idigbio.org') && strtotime($row->initialtimestamp) > strtotime('-2 days')) {
                    $headerArr = get_headers($row->originalurl,1);
                    if($headerArr && $headerArr['Content-Type'] === 'image/svg+xml') {
                        $imageMap[$imgId]['error'] = 'NOTICE: iDigBio image derivatives not yet available, it may take upto 24 hours before image processing is complete';
                    }
                }
            }
            $result->free();
        }
        return $imageMap;
    }

    public function getEditArr(): array
    {
        $retArr = array();
        $sql = 'SELECT e.ocedid, e.fieldname, e.fieldvalueold, e.fieldvaluenew, e.reviewstatus, e.appliedstatus, '.
            'CONCAT_WS(", ",u.lastname,u.firstname) as editor, e.initialtimestamp '.
            'FROM omoccuredits e INNER JOIN users u ON e.uid = u.uid '.
            'WHERE e.occid = '.$this->occid.' ORDER BY e.initialtimestamp DESC ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result){
            while($r = $result->fetch_object()){
                $k = substr($r->initialtimestamp,0,16);
                if($k){
                    if(!isset($retArr[$k]['editor'])){
                        $retArr[$k]['editor'] = $r->editor;
                        $retArr[$k]['ts'] = $r->initialtimestamp;
                        $retArr[$k]['reviewstatus'] = $r->reviewstatus;
                        $retArr[$k]['appliedstatus'] = $r->appliedstatus;
                    }
                    $retArr[$k]['edits'][$r->ocedid]['fieldname'] = $r->fieldname;
                    $retArr[$k]['edits'][$r->ocedid]['old'] = $r->fieldvalueold;
                    $retArr[$k]['edits'][$r->ocedid]['new'] = $r->fieldvaluenew;
                }
            }
            $result->free();
        }
        return $retArr;
    }

    public function getExternalEditArr(): array
    {
        $retArr = array();
        $sql = 'SELECT r.orid, r.oldvalues, r.newvalues, r.externalsource, r.externaleditor, r.reviewstatus, r.appliedstatus, '.
            'CONCAT_WS(", ",u.lastname,u.firstname) AS username, r.externaltimestamp, r.initialtimestamp '.
            'FROM omoccurrevisions r LEFT JOIN users u ON r.uid = u.uid '.
            'WHERE (r.occid = '.$this->occid.') ORDER BY r.initialtimestamp DESC ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $editor = $r->externaleditor;
            if($r->username) {
                $editor .= ' (' . $r->username . ')';
            }
            $retArr[$r->orid][$r->appliedstatus]['editor'] = $editor;
            $retArr[$r->orid][$r->appliedstatus]['source'] = $r->externalsource;
            $retArr[$r->orid][$r->appliedstatus]['reviewstatus'] = $r->reviewstatus;
            $retArr[$r->orid][$r->appliedstatus]['ts'] = $r->initialtimestamp;

            $oldValues = json_decode($r->oldvalues,true);
            $newValues = json_decode($r->newvalues,true);
            foreach($oldValues as $fieldName => $value){
                $retArr[$r->orid][$r->appliedstatus]['edits'][$fieldName]['old'] = $value;
                $retArr[$r->orid][$r->appliedstatus]['edits'][$fieldName]['new'] = ($newValues[$fieldName] ?? 'ERROR');
            }
        }
        $rs->free();
        return $retArr;
    }

    public function getLock(): bool
    {
        $isLocked = false;
        $delSql = 'DELETE FROM omoccureditlocks WHERE (ts < '.(time()-900).') OR (uid = '.$GLOBALS['SYMB_UID'].')';
        if(!$this->conn->query($delSql)) {
            return false;
        }
        $sql = 'INSERT INTO omoccureditlocks(occid,uid,ts) '.
            'VALUES ('.$this->occid.','.$GLOBALS['SYMB_UID'].','.time().')';
        if(!$this->conn->query($sql)){
            $isLocked = true;
        }
        return $isLocked;
    }

    public function isTaxonomicEditor(): int
    {
        $isEditor = 0;

        $udIdArr = array();
        if(array_key_exists('CollTaxon',$GLOBALS['USER_RIGHTS'])){
            foreach($GLOBALS['USER_RIGHTS']['CollTaxon'] as $vStr){
                $tok = explode(':',$vStr);
                if($tok && $tok[0] === $this->collId){
                    $udIdArr[] = $tok[1];
                }
            }
        }
        $editTidArr = array();
        $sqlut = 'SELECT idusertaxonomy, tid, geographicscope '.
            'FROM usertaxonomy '.
            'WHERE editorstatus = "OccurrenceEditor" AND uid = '.$GLOBALS['SYMB_UID'];
        //echo $sqlut;
        $rsut = $this->conn->query($sqlut);
        while($rut = $rsut->fetch_object()){
            if(in_array('all', $udIdArr, true) || in_array($rut->idusertaxonomy, $udIdArr, true)){
                $editTidArr[2][$rut->tid] = $rut->geographicscope;
            }
            else{
                $editTidArr[3][$rut->tid] = $rut->geographicscope;
            }
        }
        $rsut->free();
        if($editTidArr){
            $occTidArr = array();
            $tid = 0;
            $sciname = '';
            $family = '';
            if($this->occurrenceMap && $this->occurrenceMap['tidinterpreted']){
                $tid = $this->occurrenceMap['tidinterpreted'];
                $sciname = $this->occurrenceMap['sciname'];
                $family = $this->occurrenceMap['family'];
            }
            if(!$tid && !$sciname && !$family){
                $sql = 'SELECT tidinterpreted, sciname, family '.
                    'FROM omoccurrences '.
                    'WHERE occid = '.$this->occid;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $tid = $r->tidinterpreted;
                    $sciname = $r->sciname;
                    $family = $r->family;
                }
                $rs->free();
            }
            if($tid){
                $occTidArr[] = $tid;
                $rs2 = $this->conn->query('SELECT parenttid FROM taxaenumtree WHERE (taxauthid = 1) AND (tid = '.$tid.')');
                while($r2 = $rs2->fetch_object()){
                    $occTidArr[] = $r2->parenttid;
                }
                $rs2->free();
            }
            elseif($sciname || $family){
                $sqlWhere = '';
                if($sciname){
                    $taxon = $sciname;
                    $tok = explode(' ',$sciname);
                    if($tok && (count($tok) > 1) && strlen($tok[0]) > 2) {
                        $taxon = $tok[0];
                    }
                    $sqlWhere .= '(t.sciname = "'.Sanitizer::cleanInStr($taxon).'") ';
                }
                elseif($family){
                    $sqlWhere .= '(t.sciname = "'.Sanitizer::cleanInStr($family).'") ';
                }
                if($sqlWhere){
                    $sql2 = 'SELECT e.parenttid '.
                        'FROM taxaenumtree e INNER JOIN taxa t ON e.tid = t.tid '.
                        'WHERE e.taxauthid = 1 AND ('.$sqlWhere.')';
                    //echo $sql2;
                    $rs2 = $this->conn->query($sql2);
                    while($r2 = $rs2->fetch_object()){
                        $occTidArr[] = $r2->parenttid;
                    }
                    $rs2->free();
                }
            }
            if($occTidArr){
                if(array_key_exists(2,$editTidArr) && array_intersect(array_keys($editTidArr[2]),$occTidArr)){
                    $isEditor = 2;
                }
                if(!$isEditor && array_key_exists(3, $editTidArr) && array_intersect(array_keys($editTidArr[3]), $occTidArr)) {
                    $isEditor = 3;
                }
            }
        }
        return $isEditor;
    }

    public function getErrorStr(): ?string
    {
        if($this->errorArr) {
            return implode('; ', $this->errorArr);
        }

        return '';
    }

    public function getCollectionList(): array
    {
        $retArr = array();
        $collArr = array('0');
        $collEditorArr = array();
        if(isset($GLOBALS['USER_RIGHTS']['CollAdmin'])) {
            $collArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
        }
        $sql = 'SELECT collid, collectionname FROM omcollections '.
            'WHERE (collid IN('.implode(',',$collArr).')) ';
        if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
            $collEditorArr = $GLOBALS['USER_RIGHTS']['CollEditor'];
        }
        if($collEditorArr){
            $sql .= 'OR (collid IN('.implode(',',$collEditorArr).') AND colltype = "General Observations")';
        }
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->collid] = $r->collectionname;
        }
        $rs->free();
        asort($retArr);
        return $retArr;
    }

    public function getExsiccatiList(): array
    {
        $retArr = array();
        if($this->collId){
            $sql = 'SELECT DISTINCT t.ometid, t.title, t.abbreviation '.
                'FROM omexsiccatititles t INNER JOIN omexsiccatinumbers n ON t.ometid = n.ometid '.
                'INNER JOIN omexsiccatiocclink l ON n.omenid = l.omenid '.
                'INNER JOIN omoccurrences o ON l.occid = o.occid '.
                'WHERE (o.collid = '.$this->collId.') '.
                'ORDER BY t.title ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->ometid] = $r->title.($r->abbreviation?' ['.$r->abbreviation.']':'');
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getQuickHost($occId): array
    {
        $retArr = array();
        $sql = 'SELECT associd, verbatimsciname '.
            'FROM omoccurassociations '.
            'WHERE relationship = "host" AND occid = '.$occId.' ';
        //echo $sql; exit;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr['associd'] = $r->associd;
            $retArr['verbatimsciname'] = $r->verbatimsciname;
        }
        $rs->free();
        return $retArr;
    }

    private function encodeStrTargeted($inStr,$inCharset,$outCharset): string
    {
        if($inCharset === $outCharset) {
            return $inStr;
        }
        $retStr = $inStr;
        if($inCharset === 'latin' && $outCharset === 'utf8'){
            if(mb_detect_encoding($retStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
                $retStr = utf8_encode($retStr);
            }
        }
        elseif($inCharset === 'utf8' && $outCharset === 'latin'){
            if(mb_detect_encoding($retStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
                $retStr = utf8_decode($retStr);
            }
        }
        return $retStr;
    }

    protected function encodeStr($inStr): string
    {
        $retStr = $inStr;
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
        $replace = array("'","'",'"','"','*','-','-');
        $inStr= str_replace($search, $replace, $inStr);

        if($inStr){
            $lowerStr = strtolower($GLOBALS['CHARSET']);
            if($lowerStr === 'utf-8' || $lowerStr === 'utf8'){
                if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
                    $retStr = utf8_encode($inStr);
                }
            }
            elseif($lowerStr === 'iso-8859-1'){
                if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
                    $retStr = utf8_decode($inStr);
                }
            }
        }
        return $retStr;
    }

    protected function cleanOutArr($inArr): array
    {
        $outArr = array();
        foreach($inArr as $k => $v){
            $outArr[$k] = Sanitizer::cleanOutStr($v);
        }
        return $outArr;
    }

    private function cleanRawFragment($str): string
    {
        $newStr = trim($str);
        $newStr = $this->encodeStr($newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }
}
