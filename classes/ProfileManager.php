<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/Person.php');
include_once(__DIR__ . '/Encryption.php');
include_once(__DIR__ . '/Mailer.php');
include_once(__DIR__ . '/Sanitizer.php');

class ProfileManager extends Manager{

    private $rememberMe = false;
    private $uid;
    private $userName;
    private $displayName;
    private $token;
    private $authSql;
    private $errorStr;
    private $encryption;

    public function __construct(){
        parent::__construct();
        $connection = new DbConnection();
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
        setcookie('SymbiotaCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?:'/'),$domainName,false,true);
        setcookie('SymbiotaCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?:'/'));
        unset($_SESSION['userrights'], $_SESSION['userparams']);
    }

    public function authenticate($pwdStr = null): bool
    {
        $authStatus = false;
        unset($_SESSION['userrights'], $_SESSION['userparams']);
        if($this->userName){
            if(!$this->authSql){
                $this->authSql = 'SELECT u.uid, u.firstname, u.lastname '.
                    'FROM users AS u '.
                    'WHERE (u.username = "'.$this->userName.'") ';
                if($pwdStr) {
                    if($this->encryption === 'password'){
                        $this->authSql .= 'AND (u.password = PASSWORD("' . Sanitizer::cleanInStr($pwdStr) . '")) ';
                    }
                    if($this->encryption === 'sha2'){
                        $this->authSql .= 'AND (u.password = SHA2("' . Sanitizer::cleanInStr($pwdStr) . '", 224)) ';
                    }
                }
            }
            $result = $this->conn->query($this->authSql);
            if($row = $result->fetch_object()){
                $this->uid = $row->uid;
                $this->displayName = $row->firstname;
                if(strlen($this->displayName) > 15) {
                    $this->displayName = $this->userName;
                }
                if(strlen($this->displayName) > 15) {
                    $this->displayName = substr($this->displayName, 0, 10) . '...';
                }

                $authStatus = true;
                $this->reset();
                $this->setUserRights();
                $this->setUserParams();
                if($this->rememberMe){
                    $this->setTokenCookie();
                }

                $connection = new DbConnection();
                $conn = $connection->getConnection();
                $sql = 'UPDATE users SET lastlogindate = NOW() WHERE (username = "'.$this->userName.'")';
                $conn->query($sql);
                $conn->close();
            }
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
            setcookie('SymbiotaCrumb', Encryption::encrypt(json_encode($tokenArr, JSON_THROW_ON_ERROR)), $cookieExpire, ($GLOBALS['CLIENT_ROOT'] ?: '/'), $domainName, false, true);
        }
    }

    public function getPerson(): Person
    {
        $sqlStr = 'SELECT u.uid, u.firstname, ' .($this->checkFieldExists('users','middleinitial')?'u.middleinitial, ':''). 'u.lastname, u.title, u.institution, u.department, ' .
            'u.address, u.city, u.state, u.zip, u.country, u.phone, u.email, ' .
            'u.url, u.biography, u.ispublic, u.notes, u.username, u.lastlogindate ' .
            'FROM users u ' .
            'WHERE (u.uid = ' .$this->uid. ')';
        $person = new Person();
        //echo $sqlStr;
        $result = $this->conn->query($sqlStr);
        if($row = $result->fetch_object()){
            $person->setUid($row->uid);
            $person->setUserName($row->username);
            $person->setLastLoginDate($row->lastlogindate);
            $person->setFirstName($row->firstname);
            if(isset($row->middleinitial) && $row->middleinitial) {
                $person->setMiddleInitial($row->middleinitial);
            }
            $person->setLastName($row->lastname);
            $person->setTitle($row->title);
            $person->setInstitution($row->institution);
            $person->setDepartment($row->department);
            $person->setAddress($row->address);
            $person->setCity($row->city);
            $person->setState($row->state);
            $person->setZip($row->zip);
            $person->setCountry($row->country);
            $person->setPhone($row->phone);
            $person->setEmail($row->email);
            $person->setUrl($row->url);
            $person->setBiography($row->biography);
            $person->setIsPublic($row->ispublic);
            $this->setUserTaxonomy($person);
            while($row = $result->fetch_object()){
                if($row->lastlogindate && (!$person->getLastLoginDate() || $row->lastlogindate > $person->getLastLoginDate())){
                    $person->setUserName($row->username);
                    $person->setLastLoginDate($row->lastlogindate);
                }
            }
        }
        $result->free();
        return $person;
    }

    public function updateProfile($person){
        $success = false;
        $manager = new Manager();
        $middle = $manager->checkFieldExists('users','middleinitial');
        if($person){
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            $fields = 'UPDATE users SET ';
            $where = 'WHERE (uid = '.$person->getUid().')';
            $values = 'firstname = "'.Sanitizer::cleanInStr($person->getFirstName()).'"';
            if($middle) {
                $values = 'middleinitial = "' . Sanitizer::cleanInStr($person->getMiddleInitial()) . '"';
            }
            $values .= ', lastname= "'.Sanitizer::cleanInStr($person->getLastName()).'"';
            $values .= ', title= "'.Sanitizer::cleanInStr($person->getTitle()).'"';
            $values .= ', institution="'.Sanitizer::cleanInStr($person->getInstitution()).'"';
            $values .= ', department= "'.Sanitizer::cleanInStr($person->getDepartment()).'"';
            $values .= ', address= "'.Sanitizer::cleanInStr($person->getAddress()).'"';
            $values .= ', city="'.Sanitizer::cleanInStr($person->getCity()).'"';
            $values .= ', state="'.Sanitizer::cleanInStr($person->getState()).'"';
            $values .= ', zip="'.Sanitizer::cleanInStr($person->getZip()).'"';
            $values .= ', country= "'.Sanitizer::cleanInStr($person->getCountry()).'"';
            $values .= ', phone="'.Sanitizer::cleanInStr($person->getPhone()).'"';
            $values .= ', email="'.Sanitizer::cleanInStr($person->getEmail()).'"';
            $values .= ', url="'.Sanitizer::cleanInStr($person->getUrl()).'"';
            $values .= ', biography="'.Sanitizer::cleanInStr($person->getBiography()).'"';
            $values .= ', ispublic='.(Sanitizer::cleanInStr($person->getIsPublic())?1:0).' ';
            $sql = $fields. ' ' .$values. ' ' .$where;
            //echo $sql;
            $success = $editCon->query($sql);
            $editCon->close();
        }
        return $success;
    }

    public function deleteProfile($reset = null){
        $success = false;
        if($this->uid){
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            $sql = 'DELETE FROM users WHERE (uid = '.$this->uid.')';
            //echo $sql; Exit;
            $success = $editCon->query($sql);
            $editCon->close();
        }
        if($reset) {
            $this->reset();
        }
        return $success;
    }

    public function changePassword ($newPwd, $oldPwd = null, $isSelf = null): bool
    {
        $success = false;
        if($newPwd){
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            if($isSelf){
                $sqlTest = '';
                if($this->encryption === 'password'){
                    $sqlTest = 'SELECT u.uid FROM users u WHERE (u.uid = '.$this->uid.') '.
                        'AND (u.password = PASSWORD("'.Sanitizer::cleanInStr($oldPwd).'"))';
                }
                if($this->encryption === 'sha2'){
                    $sqlTest = 'SELECT u.uid FROM users u WHERE (u.uid = '.$this->uid.') '.
                        'AND (u.password = SHA2("'.Sanitizer::cleanInStr($oldPwd).'", 224))';
                }
                $rsTest = $editCon->query($sqlTest);
                if(!$rsTest->num_rows) {
                    return false;
                }
            }
            $sql = '';
            if($this->encryption === 'password'){
                $sql = 'UPDATE users SET password = PASSWORD("'.Sanitizer::cleanInStr($newPwd).'") '.
                    'WHERE (uid = '.$this->uid.')';
            }
            if($this->encryption === 'sha2'){
                $sql = 'UPDATE users SET password = SHA2("'.Sanitizer::cleanInStr($newPwd).'", 224) '.
                    'WHERE (uid = '.$this->uid.')';
            }
            $successCnt = $editCon->query($sql);
            $editCon->close();
            if($successCnt > 0) {
                $success = true;
            }
        }
        return $success;
    }

    public function resetPassword($un): string
    {
        if(isset($GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT']) && $GLOBALS['SMTP_HOST']){
            $newPassword = $this->generateNewPassword();
            $status = false;
            if($un){
                $connection = new DbConnection();
                $editCon = $connection->getConnection();
                $sql = '';
                if($this->encryption === 'password'){
                    $sql = 'UPDATE users SET password = PASSWORD("'.Sanitizer::cleanInStr($newPassword).'") '.
                        'WHERE (username = "'.Sanitizer::cleanInStr($un).'")';
                }
                if($this->encryption === 'sha2'){
                    $sql = 'UPDATE users SET password = SHA2("'.Sanitizer::cleanInStr($newPassword).'", 224) '.
                        'WHERE (username = "'.Sanitizer::cleanInStr($un).'")';
                }
                $status = $editCon->query($sql);
                $editCon->close();
            }
            if($status){
                $emailAddr = '';
                $sql = 'SELECT u.email FROM users u '.
                    'WHERE (u.username = "'.Sanitizer::cleanInStr($un).'")';
                $result = $this->conn->query($sql);
                if($row = $result->fetch_object()){
                    $emailAddr = $row->email;
                }
                $result->free();

                $subject = 'Your password';
                $bodyStr = 'Your ' .$GLOBALS['DEFAULT_TITLE']." (<a href='http://".$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT']."'>http://".$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT']. '</a>) password has been reset to: ' .$newPassword. ' ';
                $bodyStr .= "<br/><br/>After logging in, you can reset your password by clicking on <a href='http://".$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT']."/profile/viewprofile.php'>View Profile</a> link and then click the Edit Profile tab.";
                $bodyStr .= '<br/>If you have problems with the new password, contact the System Administrator ';
                if($GLOBALS['ADMIN_EMAIL']){
                    $bodyStr .= '<' .$GLOBALS['ADMIN_EMAIL']. '>';
                }
                (new Mailer)->sendEmail($emailAddr,$subject,$bodyStr);
                $returnStr = 'Your new password has been emailed to: ' .$emailAddr.' Please check your junk folder if no email appears in your inbox.';
            }
            else{
                $returnStr = 'Reset Failed! Contact Administrator';
            }
        }
        else{
            $returnStr = 'Reset Failed! Email has not been configured on this portal. Please contact portal admin.';
        }
        return $returnStr;
    }

    private function generateNewPassword(): string
    {
        $newPassword = '';
        $alphabet = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
        if($alphabet){
            for($i = 0; $i<8; $i++) {
                try {
                    $newPassword .= $alphabet[random_int(0, count($alphabet) - 1)];
                } catch (Exception $e) {}
            }
        }
        return $newPassword;
    }

    public function register($postArr): bool
    {
        $status = false;
        $manager = new Manager();
        $firstName = Sanitizer::cleanInStr($postArr['firstname']);
        $lastName = Sanitizer::cleanInStr($postArr['lastname']);
        $email = Sanitizer::cleanInStr($postArr['emailaddr']);
        if($firstName && $lastName && $email && $this->userName){
            $person = new Person();
            $person->setPassword($postArr['pwd']);
            $person->setUserName($this->userName);
            $person->setFirstName($firstName);
            $person->setMiddleInitial($postArr['middleinitial']);
            $person->setLastName($lastName);
            $person->setTitle($postArr['title']);
            $person->setInstitution($postArr['institution']);
            $person->setDepartment($postArr['department']);
            $person->setAddress($postArr['address']);
            $person->setCity($postArr['city']);
            $person->setState($postArr['state']);
            $person->setZip($postArr['zip']);
            $person->setCountry($postArr['country']);
            $person->setEmail($email);
            $person->setUrl($postArr['url']);
            $person->setBiography($postArr['biography']);
            $person->setIsPublic(isset($postArr['ispublic'])?1:0);

            $fields = 'INSERT INTO users (';
            $values = 'VALUES (';
            $fields .= 'firstname ';
            $values .= '"'.Sanitizer::cleanInStr($person->getFirstName()).'"';
            $fields .= ', middleinitial ';
            $values .= ', "'.Sanitizer::cleanInStr($person->getMiddleInitial()).'"';
            $fields .= ', lastname';
            $values .= ', "'.Sanitizer::cleanInStr($person->getLastName()).'"';
            $fields .= ', username';
            $values .= ', "'.Sanitizer::cleanInStr($person->getUserName()).'"';
            $fields .= ', password';
            if($this->encryption === 'password'){
                $values .= ', PASSWORD("'.Sanitizer::cleanInStr($person->getPassword()).'")';
            }
            if($this->encryption === 'sha2'){
                $values .= ', SHA2("'.Sanitizer::cleanInStr($person->getPassword()).'", 224)';
            }
            if($person->getTitle()){
                $fields .= ', title';
                $values .= ', "'.Sanitizer::cleanInStr($person->getTitle()).'"';
            }
            if($person->getInstitution()){
                $fields .= ', institution';
                $values .= ', "'.Sanitizer::cleanInStr($person->getInstitution()).'"';
            }
            if($person->getDepartment()){
                $fields .= ', department';
                $values .= ', "'.Sanitizer::cleanInStr($person->getDepartment()).'"';
            }
            if($person->getAddress()){
                $fields .= ', address';
                $values .= ', "'.Sanitizer::cleanInStr($person->getAddress()).'"';
            }
            if($person->getCity()){
                $fields .= ', city';
                $values .= ', "'.Sanitizer::cleanInStr($person->getCity()).'"';
            }
            $fields .= ', state';
            $values .= ', "'.Sanitizer::cleanInStr($person->getState()).'"';
            $fields .= ', country';
            $values .= ', "'.Sanitizer::cleanInStr($person->getCountry()).'"';
            if($person->getZip()){
                $fields .= ', zip';
                $values .= ', "'.Sanitizer::cleanInStr($person->getZip()).'"';
            }
            if($person->getPhone()){
                $fields .= ', phone';
                $values .= ', "'.Sanitizer::cleanInStr($person->getPhone()).'"';
            }
            if($person->getEmail()){
                $fields .= ', email';
                $values .= ', "'.Sanitizer::cleanInStr($person->getEmail()).'"';
            }
            if($person->getUrl()){
                $fields .= ', url';
                $values .= ', "'.$person->getUrl().'"';
            }
            if($person->getBiography()){
                $fields .= ', biography';
                $values .= ', "'.Sanitizer::cleanInStr($person->getBiography()).'"';
            }
            if($person->getIsPublic()){
                $fields .= ', ispublic';
                $values .= ', '.$person->getIsPublic();
            }

            $sql = $fields.') '.$values.')';
            //echo "SQL: ".$sql;
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            if($editCon->query($sql)){
                $person->setUid($editCon->insert_id);
                $this->uid = $person->getUid();
                $status = true;
                $this->userName = $person->getUserName();
                $this->displayName = $person->getFirstName();
                $this->reset();
                $this->authenticate();
            }
            else{
                $this->errorStr = 'FAILED: Unable to create user.<div style="margin-left:55px;">Please contact system administrator for assistance.</div>';
            }
            $editCon->close();
        }

        return $status;
    }

    public function lookupUserName($emailAddr): bool
    {
        $status = false;
        if(isset($GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT']) && $GLOBALS['SMTP_HOST']){
            if(!$this->validateEmailAddress($emailAddr)) {
                return false;
            }
            $loginStr = '';
            $sql = 'SELECT u.uid, u.username, concat_ws("; ",u.lastname,u.firstname) '.
                'FROM users u '.
                'WHERE (u.email = "'.$emailAddr.'")';
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
                $bodyStr = 'Your '.$GLOBALS['DEFAULT_TITLE'].' (<a href="http://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'">http://'.
                    $_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'</a>) login name is: '.$loginStr.' ';
                $bodyStr .= '<br/>If you continue to have login issues, contact the System Administrator ';
                if($GLOBALS['ADMIN_EMAIL']){
                    $bodyStr .= '<' .$GLOBALS['ADMIN_EMAIL']. '>';
                }
                $mailerResult = (new Mailer)->sendEmail($emailAddr,$subject,$bodyStr);
                if($mailerResult === 'Sent'){
                    $status = true;
                }
                else{
                    $this->errorStr = $mailerResult;
                }
            }
            else{
                $this->errorStr = 'There are no users registered to email address: '.$emailAddr;
            }
        }
        else{
            $this->errorStr = 'ERROR: email has not been configured on this portal. Please contact portal admin.';
        }

        return $status;
    }

    public function changeLogin($newLogin, $pwd = null): bool
    {
        $status = true;
        if($this->uid){
            $isSelf = true;
            if($this->uid !== $GLOBALS['SYMB_UID']) {
                $isSelf = false;
            }
            $newLogin = trim($newLogin);
            if(!$this->validateUserName($newLogin)) {
                return false;
            }

            $sqlTestLogin = 'SELECT uid FROM users WHERE (username = "'.$newLogin.'") ';
            $rs = $this->conn->query($sqlTestLogin);
            if($rs->num_rows){
                $this->errorStr = 'Login '.$newLogin.' is already being used by another user. Please try a new login.';
                $status = false;
            }
            $rs->free();

            if($status){
                $this->setUserName();
                if($isSelf && !$this->authenticate($pwd)) {
                    $this->errorStr = 'ERROR saving new login: incorrect password';
                    $status = false;
                }
                if($status){
                    $sql = 'UPDATE users '.
                        'SET username = "'.$newLogin.'" '.
                        'WHERE (uid = '.$this->uid.') AND (username = "'.$this->userName.'")';
                    //echo $sql;
                    $connection = new DbConnection();
                    $editCon = $connection->getConnection();
                    if($editCon->query($sql)){
                        if($isSelf){
                            $this->userName = $newLogin;
                            $this->authenticate();
                        }
                    }
                    else{
                        $this->errorStr = 'ERROR saving new login.';
                        $status = false;
                    }
                    $editCon->close();
                }
            }
        }
        return $status;
    }

    public function checkLogin($email): bool
    {
        if(!$this->validateEmailAddress($email)) {
            return false;
        }
        $status = true;
        $sql = 'SELECT u.email, u.username '.
            'FROM users u '.
            'WHERE (u.username = "'.$this->userName.'" OR u.email = "'.$email.'" )';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $status = false;
            if($row->username === $this->userName){
                $this->errorStr = 'login_exists';
                break;
            }

            $this->errorStr = 'email_registered';
        }
        $result->free();
        return $status;
    }

    public function getPersonalCollectionArr(): array
    {
        $retArr = array();
        if($this->uid){
            $cAdminArr = array();
            if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])) {
                $cAdminArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
            }
            $cArr = $cAdminArr;
            if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
                $cArr = array_merge($cArr, $GLOBALS['USER_RIGHTS']['CollEditor']);
            }
            if($cArr){
                $sql = 'SELECT collid, collectionname, colltype, CONCAT_WS(" ",institutioncode,collectioncode) AS instcode '.
                    'FROM omcollections WHERE collid IN('.implode(',',$cArr).') ORDER BY collectionname';
                //echo $sql;
                if($rs = $this->conn->query($sql)){
                    while($r = $rs->fetch_object()){
                        $retArr[$r->colltype][$r->collid] = $r->collectionname.($r->instcode?' ('.$r->instcode.')':'');
                    }
                    $rs->free();
                }
            }
        }
        return $retArr;
    }

    public function getPersonalOccurrenceCount($collId): int
    {
        $retCnt = 0;
        if($this->uid){
            $sql = 'SELECT count(*) AS reccnt FROM omoccurrences WHERE observeruid = '.$this->uid.' AND collid = '.$collId;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $retCnt = $r->reccnt;
                }
                $rs->close();
            }
        }
        return $retCnt;
    }

    public function unreviewedCommentsExist($collid): int
    {
        $retCnt = 0;
        $sql = 'SELECT count(c.comid) AS reccnt '.
            'FROM omoccurrences o INNER JOIN omoccurcomments c ON o.occid = c.occid '.
            'WHERE (o.observeruid = '.$GLOBALS['SYMB_UID'].') AND (o.collid = '.$collid.') AND (c.reviewstatus < 3)';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retCnt = $r->reccnt;
            }
            $rs->free();
        }
        return $retCnt;
    }

    private function setUserTaxonomy($person): void
    {
        $sql = 'SELECT ut.idusertaxonomy, t.tid, t.sciname, '.
            'ut.editorstatus, ut.geographicscope, ut.notes, ut.modifieduid, ut.modifiedtimestamp '.
            'FROM usertaxonomy ut INNER JOIN taxa t ON ut.tid = t.tid '.
            'WHERE ut.uid = ?';
        $id = 0;
        $tid = 0;
        $sciname = '';
        $editorStatus = 0;
        $geographicScope = '';
        $notes = '';
        $modifiedUid = 0;
        $modifiedtimestamp = '';
        $statement = $this->conn->prepare($sql);
        $uid = $person->getUid();
        $statement->bind_param('i', $uid);
        $statement->execute();
        $statement->bind_result($id, $tid, $sciname, $editorStatus, $geographicScope, $notes, $modifiedUid, $modifiedtimestamp);
        while($statement->fetch()){
            $person->addUserTaxonomy($editorStatus, $id,'sciname',$sciname);
            $person->addUserTaxonomy($editorStatus, $id,'tid',$tid);
            $person->addUserTaxonomy($editorStatus, $id,'geographicScope',$geographicScope);
            $person->addUserTaxonomy($editorStatus, $id,'notes',$notes);
        }
        $statement->close();
    }

    public function deleteUserTaxonomy($utid,$editorStatus = null): string
    {
        $statusStr = 'SUCCESS: Taxonomic relationship deleted';
        if(is_numeric($utid) || $utid === 'all'){
            $sql = 'DELETE FROM usertaxonomy ';
            if($utid === 'all'){
                $sql .= 'WHERE uid = '.$this->uid;
            }
            else{
                $sql .= 'WHERE idusertaxonomy = '.$utid;
            }
            if($editorStatus){
                $sql .= ' AND editorstatus = "'.$editorStatus.'" ';
            }
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            if($editCon->query($sql)){
                if($this->uid === $GLOBALS['SYMB_UID']){
                    $this->userName = $GLOBALS['USERNAME'];
                    $this->authenticate();
                }
            }
            else{
                $statusStr = 'ERROR deleting taxonomic relationship.';
            }
            $editCon->close();
        }
        return $statusStr;
    }

    public function addUserTaxonomy($taxon,$editorStatus,$geographicScope,$notes): string
    {
        $statusStr = 'SUCCESS adding taxonomic relationship';

        $tid = 0;
        $taxon = Sanitizer::cleanInStr($taxon);
        $editorStatus = Sanitizer::cleanInStr($editorStatus);
        $geographicScope = Sanitizer::cleanInStr($geographicScope);
        $notes = Sanitizer::cleanInStr($notes);
        $modDate = date('Y-m-d H:i:s');
        $sql1 = 'SELECT tid FROM taxa WHERE sciname = "'.$taxon.'"';
        $rs1 = $this->conn->query($sql1);
        while($r1 = $rs1->fetch_object()){
            $tid = $r1->tid;
        }
        $rs1->close();
        if($tid){
            $sql2 = 'INSERT INTO usertaxonomy(uid, tid, taxauthid, editorstatus, geographicScope, notes, modifiedUid, modifiedtimestamp) '.
                'VALUES('.$this->uid.','.$tid.',1,"'.$editorStatus.'","'.$geographicScope.'","'.$notes.'",'.$GLOBALS['SYMB_UID'].',"'.$modDate.'") ';
            //echo $sql;
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            if($editCon->query($sql2)){
                if($this->uid === $GLOBALS['SYMB_UID']){
                    $this->userName = $GLOBALS['USERNAME'];
                    $this->authenticate();
                }
            }
            else{
                $statusStr = 'ERROR adding taxonomic relationship.';
            }
            $editCon->close();
        }
        else{
            $statusStr = 'ERROR adding taxonomic relationship: unable to obtain tid for '.$taxon;
        }
        return $statusStr;
    }

    public function dlSpecBackup($collId, $characterSet, $zipFile){
        $tempPath = $this->getTempPath();
        $buFileName = $GLOBALS['PARAMS_ARR']['un'].'_'.time();
        $zipArchive = null;

        if($zipFile && class_exists('ZipArchive')){
            $zipArchive = new ZipArchive;
            $zipArchive->open($tempPath.$buFileName.'.zip', ZipArchive::CREATE);
        }

        $cSet = str_replace('-','',strtolower($GLOBALS['CHARSET']));
        echo '<li style="font-weight:bold;">Zip Archive created</li>';
        echo '<li style="font-weight:bold;">Adding occurrence records to archive...';
        flush();

        $fileName = $tempPath.$buFileName;
        $specFH = fopen($fileName.'_spec.csv', 'wb');

        $headerStr = 'occid,dbpk,basisOfRecord,otherCatalogNumbers,ownerInstitutionCode, '.
            'family,scientificName,sciname,tidinterpreted,genus,specificEpithet,taxonRank,infraspecificEpithet,scientificNameAuthorship, '.
            'taxonRemarks,identifiedBy,dateIdentified,identificationReferences,identificationRemarks,identificationQualifier, '.
            'typeStatus,recordedBy,recordNumber,associatedCollectors,eventDate,year,month,day,startDayOfYear,endDayOfYear, '.
            'verbatimEventDate,habitat,substrate,occurrenceRemarks,informationWithheld,associatedOccurrences, '.
            'dataGeneralizations,associatedTaxa,dynamicProperties,verbatimAttributes,reproductiveCondition, '.
            'cultivationStatus,establishmentMeans,lifeStage,sex,individualCount,country,stateProvince,county,municipality, '.
            'locality,localitySecurity,localitySecurityReason,decimalLatitude,decimalLongitude,geodeticDatum, '.
            'coordinateUncertaintyInMeters,verbatimCoordinates,georeferencedBy,georeferenceProtocol,georeferenceSources, '.
            'georeferenceVerificationStatus,georeferenceRemarks,minimumElevationInMeters,maximumElevationInMeters,verbatimElevation, '.
            'previousIdentifications,disposition,modified,language,processingstatus,recordEnteredBy,duplicateQuantity,dateLastModified ';
        fputcsv($specFH, explode(',',$headerStr));
        $sql = 'SELECT '.$headerStr.
            ' FROM omoccurrences '.
            'WHERE collid = '.$collId.' AND observeruid = '.$this->uid;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_row()){
                if($characterSet && $characterSet !== $cSet){
                    $this->encodeArr($r,$characterSet);
                }
                fputcsv($specFH, $r);
            }
            $rs->close();
        }
        fclose($specFH);
        if($zipFile && $zipArchive){
            $zipArchive->addFile($fileName.'_spec.csv');
            $zipArchive->renameName($fileName.'_spec.csv','occurrences.csv');

            echo 'Done!</li> ';
            flush();
            $fileUrl = str_replace($GLOBALS['SERVER_ROOT'],$GLOBALS['CLIENT_ROOT'],$tempPath.$buFileName.'.zip');
            $zipArchive->close();
            unlink($fileName.'_spec.csv');
        }
        else{
            $fileUrl = str_replace($GLOBALS['SERVER_ROOT'],$GLOBALS['CLIENT_ROOT'],$tempPath.$buFileName.'_spec.csv');
        }
        return $fileUrl;
    }

    public function setUid($uid): void
    {
        if(is_numeric($uid)){
            $this->uid = $uid;
        }
    }

    private function setUserRights(): void
    {
        if($this->uid){
            $userrights = array();
            $sql = 'SELECT role, tablepk FROM userroles WHERE (uid = '.$this->uid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $userrights[$r->role][] = $r->tablepk;
            }
            $rs->free();
            $_SESSION['userrights'] = $userrights;
            $GLOBALS['USER_RIGHTS'] = $userrights;
        }
    }

    private function setUserParams(): void
    {
        $_SESSION['userparams']['un'] = $this->userName;
        $_SESSION['userparams']['dn'] = $this->displayName;
        $_SESSION['userparams']['uid'] = $this->uid;
        $GLOBALS['PARAMS_ARR'] = $_SESSION['userparams'];
        $GLOBALS['USERNAME'] = $this->userName;
    }

    public function setTokenAuthSql(): void
    {
        $this->authSql = 'SELECT u.uid, u.firstname, u.lastname '.
            'FROM users AS u '.
            'INNER JOIN useraccesstokens AS ut ON u.uid = ut.uid '.
            'WHERE (u.username = "'.$this->userName.'") AND (ut.token = "'.$this->token.'") ';
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function setRememberMe($test): void
    {
        $this->rememberMe = $test;
    }

    public function setUserName($un = null): bool
    {
        if($un){
            if(!$this->validateUserName($un)) {
                return false;
            }
            $this->userName = $un;
        }
        else if($this->uid === $GLOBALS['SYMB_UID']){
            $this->userName = $GLOBALS['USERNAME'];
        }
        elseif($this->uid){
            $sql = 'SELECT username FROM users WHERE (uid = '.$this->uid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $this->userName = $r->username;
            }
            $rs->free();
        }
        return true;
    }

    public function getUserName($uId): string
    {
        $un = '';
        $sql = 'SELECT username FROM users WHERE uid = '.$uId.' ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $un = $r->username;
        }
        $rs->free();
        return $un;
    }

    private function getTempPath(): string
    {
        $tPath = $GLOBALS['SERVER_ROOT'];
        if(substr($tPath,-1) !== '/' && substr($tPath,-1) !== '\\') {
            $tPath .= '/';
        }
        $tPath .= 'temp/';
        if(file_exists($tPath. 'downloads/')){
            $tPath .= 'downloads/';
        }
        return $tPath;
    }

    public function getErrorStr(){
        return $this->errorStr;
    }

    public function validateEmailAddress($emailAddress): bool
    {
        if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
            $this->errorStr = 'email_invalid';
            return false;
        }
        return true;
    }

    private function validateUserName($un): bool
    {
        $status = true;
        if(!Sanitizer::cleanInStr($un)){
            $status = false;
        }
        if (preg_match('/^[0-9A-Za-z_!@#$\s.+\-]+$/', $un) === 0) {
            $status = false;
        }
        if (strncmp($un, ' ', 1) === 0) {
            $status = false;
        }
        if (substr($un,-1) === ' ') {
            $status = false;
        }
        if(!$status) {
            $this->errorStr = 'username not valid';
        }
        return $status;
    }

    private function encodeArr(&$inArr,$cSet): void
    {
        foreach($inArr as $k => $v){
            $inArr[$k] = $this->encodeStr($v,$cSet);
        }
    }

    private function encodeStr($inStr,$cSet){
        $retStr = $inStr;
        if($cSet === 'utf8'){
            if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
                $retStr = iconv('ISO-8859-1//TRANSLIT', 'UTF-8',$inStr);
            }
        }
        elseif($cSet === 'latin1'){
            if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
                $retStr = iconv('UTF-8', 'ISO-8859-1//TRANSLIT',$inStr);
            }
        }
        return $retStr;
    }

    public function generateTokenPacket(): array
    {
        $pkArr = array();
        $this->createToken();
        $person = $this->getPerson();
        if($this->token){
            $pkArr['uid'] = $this->uid;
            $pkArr['firstname'] = $person->getFirstName();
            $pkArr['lastname'] = $person->getLastName();
            $pkArr['email'] = $person->getEmail();
            $pkArr['token'] = $this->token;
        }
        return $pkArr;
    }

    public function generateAccessPacket(): array
    {
        $pkArr = array();
        $sql = 'SELECT ul.role, ul.tablename, ul.tablepk, c.CollectionName, c.CollectionCode, c.InstitutionCode, fc.`Name`, fp.projname '.
            'FROM userroles AS ul LEFT JOIN omcollections AS c ON ul.tablepk = c.CollID '.
            'LEFT JOIN fmchecklists AS fc ON ul.tablepk = fc.CLID '.
            'LEFT JOIN fmprojects AS fp ON ul.tablepk = fp.pid '.
            'WHERE ul.uid = '.$this->uid.' ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($r->role === 'CollAdmin' || $r->role === 'CollEditor' || $r->role === 'CollTaxon'){
                    $pkArr['collections'][$r->role][$r->tablepk]['CollectionName'] = $r->CollectionName;
                    $pkArr['collections'][$r->role][$r->tablepk]['CollectionCode'] = $r->CollectionCode;
                    $pkArr['collections'][$r->role][$r->tablepk]['InstitutionCode'] = $r->InstitutionCode;
                }
                elseif($r->role === 'ClAdmin'){
                    $pkArr['checklists'][$r->role][$r->tablepk]['ChecklistName'] = $r->Name;
                }
                elseif($r->role === 'ProjAdmin'){
                    $pkArr['projects'][$r->role][$r->tablepk]['ProjectName'] = $r->projname;
                }
                else{
                    $pkArr['portal'][] = $r->role;
                }
            }
            $rs->close();
        }
        if(in_array('SuperAdmin', $pkArr['portal'], true)){
            $pkArr['collections']['CollAdmin'] = $this->getCollectionArr();
            $pkArr['checklists']['ClAdmin'] = $this->getChecklistArr();
            $pkArr['projects']['ProjAdmin'] = $this->getProjectArr();
        }
        return $pkArr;
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
            $connection = new DbConnection();
            $editCon = $connection->getConnection();
            $sql = 'INSERT INTO useraccesstokens (uid,token) '.
                'VALUES ('.$this->uid.',"'.$token.'") ';
            if($editCon->query($sql)){
                $this->token = $token;
            }
            $editCon->close();
        }
    }

    public function getCollectionArr(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
        }
        if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
            $cArr = array_merge($cArr, $GLOBALS['USER_RIGHTS']['CollEditor']);
        }
        if(!$cArr) {
            return $retArr;
        }

        $sql = 'SELECT collid, institutioncode, collectioncode, collectionname, colltype FROM omcollections WHERE collid IN('.implode(',',$cArr).') ORDER BY collectionname';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->collid]['collectionname'] = $r->collectionname;
                $retArr[$r->collid]['collectioncode'] = $r->collectioncode;
                $retArr[$r->collid]['institutioncode'] = $r->institutioncode;
                $retArr[$r->collid]['colltype'] = $r->colltype;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getChecklistArr(): array
    {
        $retArr = array();
        $sql = 'SELECT CLID, `Name` FROM fmchecklists';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->CLID]['ChecklistName'] = $r->Name;
            }
            $rs->close();
        }

        return $retArr;
    }

    public function getProjectArr(): array
    {
        $retArr = array();
        $sql = 'SELECT pid, projname FROM fmprojects';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->pid]['ProjectName'] = $r->projname;
            }
            $rs->close();
        }

        return $retArr;
    }

    public function getTokenCnt(): int
    {
        $cnt = 0;
        $sql = 'SELECT COUNT(token) AS cnt FROM useraccesstokens WHERE uid = '.$this->uid;
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $cnt = $row->cnt;
            $result->close();
        }
        return $cnt;
    }

    public function getUid($un): int
    {
        $uid = 0;
        $sql = 'SELECT uid FROM users WHERE username = "'.$un.'"  ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $uid = (int)$row->uid;
            $result->close();
        }
        return $uid;
    }

    public function deleteToken($uid,$token): string
    {
        $sql = 'DELETE FROM useraccesstokens WHERE uid = '.$uid.' AND token = "'.$token.'" ';
        //echo $sql;
        $connection = new DbConnection();
        $editCon = $connection->getConnection();
        if($editCon->query($sql)){
            $statusStr = 'Access token cleared!';
        }
        else{
            $statusStr = 'ERROR clearing access token.';
        }
        $editCon->close();
        return $statusStr;
    }

    public function clearAccessTokens(): string
    {
        $sql = 'DELETE FROM useraccesstokens WHERE uid = '.$this->uid;
        //echo $sql;
        $connection = new DbConnection();
        $editCon = $connection->getConnection();
        if($editCon->query($sql)){
            $statusStr = 'Access tokens cleared!';
        }
        else{
            $statusStr = 'ERROR clearing access tokens.';
        }
        $editCon->close();
        return $statusStr;
    }
}
