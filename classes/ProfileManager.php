<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Encryption.php');
include_once(__DIR__ . '/Mailer.php');
include_once(__DIR__ . '/Sanitizer.php');
include_once(__DIR__ . '/UuidFactory.php');

class ProfileManager{

    private $conn;
    private $rememberMe = false;
    private $uid;
    private $userName;
    private $displayName;
    private $validated;
    private $token;
    private $authSql;
    private $encryption;

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
        $dbVersion = $connection->getVersion();
        if((strncmp($dbVersion, '5.', 2) === 0) || (strncmp($dbVersion, 'ma', 2) === 0)){
            $this->encryption = 'password';
        }
        else{
            $this->encryption = 'sha2';
        }
    }

    public function reset(): void
    {
        $domainName = $_SERVER['HTTP_HOST'];
        if($domainName === 'localhost') {
            $domainName = false;
        }
        setcookie('BioSurvCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?:'/'),$domainName,false,true);
        setcookie('BioSurvCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?:'/'));
        unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
    }

    public function clearOldUnregisteredUsers(): void
    {
        $sql = 'DELETE ua.*, u.* '.
            'FROM users AS u LEFT JOIN useraccesstokens AS ua ON u.uid = ua.uid '.
            'WHERE u.InitialTimeStamp < DATE_SUB(NOW(), INTERVAL 30 DAY) AND (ISNULL(u.validated) OR u.validated <> 1) ';
        $this->conn->query($sql);
    }

    public function authenticate($pwdStr = null): int
    {
        $authStatus = 0;
        unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
        if($this->userName){
            if(!$this->authSql){
                $this->authSql = 'SELECT uid, firstname, lastname, validated '.
                    'FROM users '.
                    'WHERE username = "'.$this->userName.'" ';
                if($pwdStr) {
                    if($this->encryption === 'password'){
                        $this->authSql .= 'AND password = PASSWORD("' . Sanitizer::cleanInStr($this->conn,$pwdStr) . '") ';
                    }
                    if($this->encryption === 'sha2'){
                        $this->authSql .= 'AND password = SHA2("' . Sanitizer::cleanInStr($this->conn,$pwdStr) . '", 224) ';
                    }
                }
            }
            $result = $this->conn->query($this->authSql);
            if($row = $result->fetch_object()){
                $this->uid = $row->uid;
                if($this->uid){
                    $this->validated = $row->validated ? (int)$row->validated : 0;
                    $this->displayName = $row->firstname;
                    if(strlen($this->displayName) > 15) {
                        $this->displayName = $this->userName;
                    }
                    if(strlen($this->displayName) > 15) {
                        $this->displayName = substr($this->displayName, 0, 10) . '...';
                    }

                    $authStatus = 1;
                    $this->reset();
                    $this->setUserRights($this->uid);
                    $this->setUserParams();
                    if($this->rememberMe){
                        $this->setTokenCookie();
                    }
                    $sql = 'UPDATE users SET lastlogindate = NOW() WHERE username = "'.$this->userName.'" ';
                    $this->conn->query($sql);
                }
            }
            $result->free();
        }
        return $authStatus;
    }

    private function setTokenCookie(): void
    {
        $tokenArr = array();
        if(!$this->token){
            $this->createToken();
        }
        if($this->token){
            $tokenArr[] = $this->userName;
            $tokenArr[] = $this->token;
            $cookieExpire = time() + 60 * 60 * 24 * 30;
            $domainName = $_SERVER['HTTP_HOST'];
            if ($domainName === 'localhost') {
                $domainName = false;
            }
            setcookie('BioSurvCrumb', Encryption::encrypt(json_encode($tokenArr)), $cookieExpire, ($GLOBALS['CLIENT_ROOT'] ?: '/'), $domainName, false, true);
        }
    }

    public function getAccountInfoByUid($uid): array
    {
        $retArr = array();
        $sqlStr = 'SELECT uid, firstname, middleinitial, lastname, title, institution, department, ' .
            'address, city, state, zip, country, phone, email, ' .
            'url, biography, ispublic, notes, username, lastlogindate, validated ' .
            'FROM users ' .
            'WHERE uid = ' .(int)$uid. ' ';
        //echo $sqlStr;
        $result = $this->conn->query($sqlStr);
        if($row = $result->fetch_object()){
            $retArr['uid'] = $row->uid;
            $retArr['username'] = $row->username;
            $retArr['lastlogindate'] = $row->lastlogindate;
            $retArr['firstname'] = $row->firstname;
            $retArr['middleinitial'] = $row->middleinitial;
            $retArr['lastname'] = $row->lastname;
            $retArr['title'] = $row->title;
            $retArr['institution'] = $row->institution;
            $retArr['department'] = $row->department;
            $retArr['address'] = $row->address;
            $retArr['city'] = $row->city;
            $retArr['state'] = $row->state;
            $retArr['zip'] = $row->zip;
            $retArr['country'] = $row->country;
            $retArr['email'] = $row->email;
            $retArr['url'] = $row->url;
            $retArr['biography'] = $row->biography;
            $retArr['validated'] = (int)$row->validated;
        }
        $result->free();
        return $retArr;
    }

    public function updateAccountInfo($personArr): int
    {
        $success = 0;
        if($personArr){
            $sql = 'UPDATE users SET ';
            if(array_key_exists('firstname',$personArr) && Sanitizer::cleanInStr($this->conn,$personArr['firstname'])){
                $sql .= 'firstname = "'.Sanitizer::cleanInStr($this->conn,$personArr['firstname']).'", ';
            }
            if(array_key_exists('middleinitial',$personArr)){
                $sql .= 'middleinitial = '.(Sanitizer::cleanInStr($this->conn,$personArr['middleinitial'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['middleinitial']) . '"': 'NULL').', ';
            }
            if(array_key_exists('lastname',$personArr) && Sanitizer::cleanInStr($this->conn,$personArr['lastname'])){
                $sql .= 'lastname = "'.Sanitizer::cleanInStr($this->conn,$personArr['lastname']).'", ';
            }
            if(array_key_exists('title',$personArr)){
                $sql .= 'title = '.(Sanitizer::cleanInStr($this->conn,$personArr['title'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['title']) . '"': 'NULL').', ';
            }
            if(array_key_exists('institution',$personArr)){
                $sql .= 'institution = '.(Sanitizer::cleanInStr($this->conn,$personArr['institution'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['institution']) . '"': 'NULL').', ';
            }
            if(array_key_exists('department',$personArr)){
                $sql .= 'department = '.(Sanitizer::cleanInStr($this->conn,$personArr['department'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['department']) . '"': 'NULL').', ';
            }
            if(array_key_exists('address',$personArr)){
                $sql .= 'address = '.(Sanitizer::cleanInStr($this->conn,$personArr['address'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['address']) . '"': 'NULL').', ';
            }
            if(array_key_exists('city',$personArr)){
                $sql .= 'city = '.(Sanitizer::cleanInStr($this->conn,$personArr['city'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['city']) . '"': 'NULL').', ';
            }
            if(array_key_exists('state',$personArr)){
                $sql .= 'state = '.(Sanitizer::cleanInStr($this->conn,$personArr['state'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['state']) . '"': 'NULL').', ';
            }
            if(array_key_exists('zip',$personArr)){
                $sql .= 'zip = '.(Sanitizer::cleanInStr($this->conn,$personArr['zip'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['zip']) . '"': 'NULL').', ';
            }
            if(array_key_exists('country',$personArr)){
                $sql .= 'country = '.(Sanitizer::cleanInStr($this->conn,$personArr['country'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['country']) . '"': 'NULL').', ';
            }
            if(array_key_exists('email',$personArr) && Sanitizer::cleanInStr($this->conn,$personArr['email'])){
                $sql .= 'email = "'.Sanitizer::cleanInStr($this->conn,$personArr['email']).'", ';
            }
            if(array_key_exists('url',$personArr)){
                $sql .= 'url = '.(Sanitizer::cleanInStr($this->conn,$personArr['url'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['url']) . '"': 'NULL').', ';
            }
            if(array_key_exists('biography',$personArr)){
                $sql .= 'biography = '.(Sanitizer::cleanInStr($this->conn,$personArr['biography'])?'"' . Sanitizer::cleanInStr($this->conn,$personArr['biography']) . '"': 'NULL').', ';
            }
            $sql = substr($sql, 0, -2) . ' ';
            $sql .= 'WHERE uid = '.(int)$personArr['uid'].' ';
            //echo $sql;
            if($this->conn->query($sql)){
                $success = 1;
            }
            $this->conn->close();
        }
        return $success;
    }

    public function validateUser($userId): void
    {
        if($userId){
            $sql = 'UPDATE users SET validated = 1 WHERE uid = '.(int)$userId.' ';
            //echo $sql; Exit;
            $this->conn->query($sql);
            $this->conn->close();
        }
    }

    public function deleteProfile($uid): int
    {
        $success = 0;
        if($uid){
            $sql = 'DELETE FROM users WHERE uid = '.(int)$uid.' ';
            //echo $sql; Exit;
            if($this->conn->query($sql)){
                $success = 1;
            }
            $this->conn->close();
        }
        $this->reset();
        return $success;
    }

    public function changePassword($uid, $newPwd): int
    {
        $success = 0;
        if($newPwd){
            $sql = '';
            if($this->encryption === 'password'){
                $sql .= 'UPDATE users SET password = PASSWORD("'.Sanitizer::cleanInStr($this->conn,$newPwd).'") ';
            }
            if($this->encryption === 'sha2'){
                $sql .= 'UPDATE users SET password = SHA2("'.Sanitizer::cleanInStr($this->conn,$newPwd).'", 224) ';
            }
            $sql .= 'WHERE uid = '.(int)$uid.' ';
            if($this->conn->query($sql)){
                $success = 1;
            }
            $this->conn->close();
        }
        return $success;
    }

    public function resetPassword($uid,$admin): int
    {
        $returnVal = 0;
        if($uid && ($admin || $GLOBALS['EMAIL_CONFIGURED'])){
            $newPassword = $this->generateNewPassword();
            $sql = '';
            if($this->encryption === 'password'){
                $sql = 'UPDATE users SET password = PASSWORD("'.Sanitizer::cleanInStr($this->conn,$newPassword).'") '.
                    'WHERE uid = '.(int)$uid.' ';
            }
            if($this->encryption === 'sha2'){
                $sql = 'UPDATE users SET password = SHA2("'.Sanitizer::cleanInStr($this->conn,$newPassword).'", 224) '.
                    'WHERE uid = '.(int)$uid.' ';
            }
            $status = $this->conn->query($sql);
            if($status){
                if($admin){
                    $returnVal = $newPassword;
                }
                else{
                    $emailAddr = '';
                    $sql = 'SELECT email FROM users WHERE uid = '.(int)$uid.' ';
                    $result = $this->conn->query($sql);
                    if($row = $result->fetch_object()){
                        $emailAddr = $row->email;
                    }
                    $subject = 'Your password';
                    $bodyStr = 'Your ' .$GLOBALS['DEFAULT_TITLE'].' password has been reset to: ' .$newPassword. ' ';
                    $bodyStr .= "<br/><br/>After logging in, you can reset your password by clicking on <a href='".(((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http')."://".$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT']."/profile/viewprofile.php'>View Profile</a> link and then click the View Profile tab.";
                    if($GLOBALS['ADMIN_EMAIL']){
                        $bodyStr .= '<br/>If you have problems with the new password, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                    }
                    (new Mailer)->sendEmail($emailAddr,$subject,$bodyStr);
                    $returnVal = 1;
                    $result->free();
                }
            }
        }
        return $returnVal;
    }

    private function generateNewPassword(): string
    {
        $newPassword = '';
        $alphabet = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
        if($alphabet){
            for($i = 0; $i<16; $i++) {
                try {
                    $newPassword .= $alphabet[random_int(0, count($alphabet) - 1)];
                } catch (Exception $e) {}
            }
        }
        return $newPassword;
    }

    public function register($personArr): int
    {
        if($GLOBALS['EMAIL_CONFIGURED']){
            $this->clearOldUnregisteredUsers();
        }
        $status = 0;
        $firstName = Sanitizer::cleanInStr($this->conn,$personArr['firstname']);
        $lastName = Sanitizer::cleanInStr($this->conn,$personArr['lastname']);
        $email = Sanitizer::cleanInStr($this->conn,$personArr['email']);
        $username = Sanitizer::cleanInStr($this->conn,$personArr['username']);
        $password = Sanitizer::cleanInStr($this->conn,$personArr['pwd']);
        if($firstName && $lastName && $email && $username && $password){
            $sql = 'INSERT INTO users (firstname,middleinitial,lastname,guid,title,institution,department,'.
                'address,city,`state`,zip,country,email,url,biography,username,password) '.
                'VALUES ("'.$firstName.'",'.
                (Sanitizer::cleanInStr($this->conn,$personArr['middleinitial']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['middleinitial']).'"' : 'NULL').','.
                '"'.$lastName.'",'.
                '"'.UuidFactory::getUuidV4().'",'.
                (Sanitizer::cleanInStr($this->conn,$personArr['title']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['title']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['institution']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['institution']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['department']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['department']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['address']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['address']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['city']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['city']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['state']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['state']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['zip']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['zip']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['country']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['country']).'"' : 'NULL').','.
                '"'.$email.'",'.
                (Sanitizer::cleanInStr($this->conn,$personArr['url']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['url']).'"' : 'NULL').','.
                (Sanitizer::cleanInStr($this->conn,$personArr['biography']) ? '"'.Sanitizer::cleanInStr($this->conn,$personArr['biography']).'"' : 'NULL').','.
                '"'.$username.'",';
            if($this->encryption === 'password'){
                $sql .= 'PASSWORD("'.$password.'"))';
            }
            elseif($this->encryption === 'sha2'){
                $sql .= 'SHA2("'.$password.'", 224))';
            }
            //echo "<div>SQL: ".$sql.'</div>';
            if($this->conn->query($sql)){
                $this->uid = $this->conn->insert_id;
                $status = 1;
                $this->userName = $username;
                $this->displayName = $firstName;
                $this->reset();
                $this->authenticate();
                if($GLOBALS['EMAIL_CONFIGURED']){
                    $this->sendConfirmationEmail($this->uid);
                }
            }
        }
        return $status;
    }

    public function sendConfirmationEmail($uid): int
    {
        $status = 0;
        if($GLOBALS['EMAIL_CONFIGURED']){
            $email = '';
            $code = '';
            $sql = 'SELECT email, guid FROM users WHERE uid = '.(int)$uid.' ';
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $email = $row->email;
                $code = $row->guid;
                if(!$code){
                    $code = UuidFactory::getUuidV4();
                    $sql = 'UPDATE users SET guid = "'.$code.'" WHERE uid = '.(int)$uid.' ';
                    $this->conn->query($sql);
                }
            }
            $result->free();
            if($email && $code){
                $confirmationLink = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http')."://".$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'];
                $confirmationLink .= '/profile/index.php?uid='.$uid.'&confirmationcode='.$code;
                $subject = $GLOBALS['DEFAULT_TITLE'].' Confirmation';
                $bodyStr = 'Your '.$GLOBALS['DEFAULT_TITLE'].' account has been created. ';
                $bodyStr .= "<br/><br/><a href='".$confirmationLink."'>Please follow this link to confirm your new account.</a>";
                if($GLOBALS['ADMIN_EMAIL']){
                    $bodyStr .= '<br/>If you have trouble confirming your account, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                }
                $mailerResult = (new Mailer)->sendEmail($email,$subject,$bodyStr);
                if($mailerResult === 'Sent'){
                    $status = 1;
                }
            }
        }
        return $status;
    }

    public function lookupUserName($emailAddr): int
    {
        $status = 0;
        if($GLOBALS['EMAIL_CONFIGURED']){
            $loginStr = '';
            $sql = 'SELECT uid, username, concat_ws("; ",lastname,firstname) FROM users '.
                'WHERE email = "'.Sanitizer::cleanInStr($this->conn,$emailAddr).'" ';
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                if($loginStr) {
                    $loginStr .= '; ';
                }
                $loginStr .= $row->username;
            }
            $result->free();
            if($loginStr){
                $subject = $GLOBALS['DEFAULT_TITLE'].' Login Name';
                $bodyStr = 'Your '.$GLOBALS['DEFAULT_TITLE'].' login name is: '.$loginStr.' ';
                if(isset($GLOBALS['ADMIN_EMAIL'])){
                    $bodyStr .= '<br/>If you continue to have login issues, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                }
                $mailerResult = (new Mailer)->sendEmail($emailAddr,$subject,$bodyStr);
                if($mailerResult === 'Sent'){
                    $status = 1;
                }
            }
        }
        return $status;
    }

    public function getPersonalOccurrenceCount($collId): int
    {
        $retCnt = 0;
        if($GLOBALS['SYMB_UID']){
            $sql = 'SELECT count(occid) AS reccnt FROM omoccurrences WHERE observeruid = '.$GLOBALS['SYMB_UID'].' AND collid = '.$collId;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $retCnt = $r->reccnt;
                }
                $rs->free();
            }
        }
        return $retCnt;
    }

    public function unreviewedCommentsExist($collid): int
    {
        $retCnt = 0;
        $sql = 'SELECT count(c.comid) AS reccnt '.
            'FROM omoccurrences AS o INNER JOIN omoccurcomments AS c ON o.occid = c.occid '.
            'WHERE o.observeruid = '.$GLOBALS['SYMB_UID'].' AND o.collid = '.$collid.' AND c.reviewstatus < 3 ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retCnt = $r->reccnt;
            }
            $rs->free();
        }
        return $retCnt;
    }

    public function getPersonalOccurrencesCsvData($collId){
        $returnArr = array();
        $headerArr = array('occid','dbpk','basisOfRecord','otherCatalogNumbers','ownerInstitutionCode','family','verbatimScientificName','sciname','tid',
            'taxonRemarks','identifiedBy','dateIdentified','identificationReferences','identificationRemarks','identificationQualifier',
            'typeStatus','recordedBy','recordNumber','associatedCollectors','eventDate','year','month','day','startDayOfYear','endDayOfYear',
            'verbatimEventDate','habitat','substrate','occurrenceRemarks','informationWithheld','associatedOccurrences',
            'dataGeneralizations','associatedTaxa','dynamicProperties','verbatimAttributes','reproductiveCondition',
            'cultivationStatus','establishmentMeans','lifeStage','sex','individualCount','country','stateProvince','county','municipality',
            'locality','localitySecurity','localitySecurityReason','decimalLatitude','decimalLongitude','geodeticDatum',
            'coordinateUncertaintyInMeters','verbatimCoordinates','georeferencedBy','georeferenceProtocol','georeferenceSources',
            'georeferenceVerificationStatus','georeferenceRemarks','minimumElevationInMeters','maximumElevationInMeters','verbatimElevation',
            'disposition','modified','language','processingstatus','recordEnteredBy','duplicateQuantity','dateLastModified');
        $returnArr[] = $headerArr;
        $sql = 'SELECT ' . implode(',',$headerArr) . ' FROM omoccurrences WHERE collid = '.$collId.' AND observeruid = '.$GLOBALS['SYMB_UID'];
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_row()){
                $returnArr[] = $r;
            }
            $rs->free();
        }
        return $returnArr;
    }

    public function setUid($uid): void
    {
        if(is_numeric($uid)){
            $this->uid = $uid;
        }
    }

    public function setUserRights($uId): void
    {
        if($uId){
            $userrights = array();
            $sql = 'SELECT role, tablepk FROM userroles WHERE uid = '.$uId.' ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                if($r->tablepk){
                    $userrights[$r->role][] = (int)$r->tablepk;
                }
                else{
                    $userrights[$r->role] = true;
                }
            }
            $rs->free();
            $_SESSION['USER_RIGHTS'] = $userrights;
            $GLOBALS['USER_RIGHTS'] = $userrights;
        }
    }

    private function setUserParams(): void
    {
        $_SESSION['PARAMS_ARR']['un'] = $this->userName;
        $_SESSION['PARAMS_ARR']['dn'] = $this->displayName;
        $_SESSION['PARAMS_ARR']['uid'] = $this->uid;
        $_SESSION['PARAMS_ARR']['valid'] = $this->validated;
        $GLOBALS['PARAMS_ARR'] = $_SESSION['PARAMS_ARR'];
        $GLOBALS['USERNAME'] = $this->userName;
    }

    public function setTokenAuthSql(): void
    {
        $this->authSql = 'SELECT u.uid, u.firstname, u.lastname, u.validated '.
            'FROM users AS u INNER JOIN useraccesstokens AS ut ON u.uid = ut.uid '.
            'WHERE u.username = "'.$this->userName.'" AND ut.token = "'.$this->token.'" ';
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function setRememberMe($val): void
    {
        $this->rememberMe = $val;
    }

    public function setUserName($un = null): bool
    {
        if($un){
            $this->userName = $un;
        }
        return true;
    }

    public function createToken(): void
    {
        $token = '';
        try {
            $token = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                random_int(0, 0xffff), random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0x0fff) | 0x4000,
                random_int(0, 0x3fff) | 0x8000,
                random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
            );
        } catch (Exception $e) {
        }
        if($token){
            $sql = 'INSERT INTO useraccesstokens(uid,token) '.
                'VALUES ('.$this->uid.',"'.$token.'") ';
            if($this->conn->query($sql)){
                $this->token = $token;
            }
        }
    }

    public function getAccountCollectionArr(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
        }
        if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
            $cArr = array_merge($cArr, $GLOBALS['USER_RIGHTS']['CollEditor']);
        }
        if($cArr){
            $sql = 'SELECT collid, institutioncode, collectioncode, collectionname, colltype FROM omcollections WHERE collid IN('.implode(',',$cArr).') ORDER BY collectionname';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $collCode = '';
                    if($r->institutioncode){
                        $collCode .= $r->institutioncode;
                    }
                    if($r->collectioncode){
                        $collCode .= ($collCode?'-':'') . $r->collectioncode;
                    }
                    $collid = (int)$r->collid;
                    $nodeArr = array();
                    $nodeArr['accesslevel'] = (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) ? 'admin' : 'editor';
                    $nodeArr['collid'] = $collid;
                    $nodeArr['label'] = $r->collectionname.($collCode ? ' ('.$collCode.')' : '');
                    $nodeArr['colltype'] = $r->colltype;
                    $nodeArr['occCount'] = $nodeArr['colltype'] === 'HumanObservation' ? $this->getPersonalOccurrenceCount($collid) : 0;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getTokenCnt($uid): int
    {
        $cnt = 0;
        $sql = 'SELECT COUNT(token) AS cnt FROM useraccesstokens WHERE uid = '.(int)$uid;
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $cnt = $row->cnt;
            $result->free();
        }
        return $cnt;
    }

    public function getUidFromUsername($un): int
    {
        $uid = 0;
        $sql = 'SELECT uid FROM users WHERE username = "'.Sanitizer::cleanInStr($this->conn,$un).'"  ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $uid = (int)$row->uid;
            $result->free();
        }
        return $uid;
    }

    public function getUidFromEmail($email): string
    {
        $returnVal = 0;
        $sql = 'SELECT uid FROM users WHERE email = "'.Sanitizer::cleanInStr($this->conn,$email).'"  ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if(($row = $result->fetch_object()) && $row->uid) {
            $returnVal = $row->uid;
        }
        $result->free();
        return $returnVal;
    }

    public function deleteToken($uid,$token): string
    {
        $sql = 'DELETE FROM useraccesstokens WHERE uid = '.(int)$uid.' AND token = "'.$token.'" ';
        //echo $sql;
        if($this->conn->query($sql)){
            $statusStr = 'Access token cleared!';
        }
        else{
            $statusStr = 'ERROR clearing access token.';
        }
        $this->conn->close();
        return $statusStr;
    }

    public function clearAccessTokens($uid): int
    {
        $returnVal = 0;
        if($uid){
            $sql = 'DELETE FROM useraccesstokens WHERE uid = '.(int)$uid;
            //echo $sql;
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
            $this->conn->close();
        }
        return $returnVal;
    }

    public function validateFromConfirmationEmail($uid,$confirmationCode): int
    {
        $returnVal = 0;
        if($uid && $confirmationCode){
            $sql = 'SELECT guid, validated '.
                'FROM users '.
                'WHERE uid = '.(int)$uid.' ';
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                if((int)$row->validated !== 1 && $row->guid && $row->guid === $confirmationCode) {
                    $sql = 'UPDATE users SET validated = 1 WHERE uid = '.(int)$uid.' ';
                    $this->conn->query($sql);
                    $returnVal = 1;
                }
            }
            $result->free();
        }
        return $returnVal;
    }

    public function validateAllUnconfirmedUsers(): void
    {
        $sql = 'SELECT uid FROM users WHERE validated <> 1 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sql = 'UPDATE users SET validated = 1 WHERE uid = '.$r->uid.' ';
            $this->conn->query($sql);
        }
        $rs->free();
    }

    public function deleteAllUnconfirmedUsers(): void
    {
        $sql = 'SELECT uid FROM users WHERE validated <> 1 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sql = 'DELETE FROM useraccesstokens WHERE uid = '.$r->uid.' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM userroles WHERE uid = '.$r->uid.' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM usertaxonomy WHERE uid = '.$r->uid.' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM users WHERE uid = '.$r->uid.' ';
            $this->conn->query($sql);
        }
        $rs->free();
    }
}
