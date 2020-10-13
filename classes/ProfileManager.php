<?php
include_once('DbConnection.php');
include_once('Manager.php');
include_once('Person.php');
include_once('Encryption.php');

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
        if((strpos($dbVersion, '5.') === 0) || (strpos($dbVersion, 'ma') === 0)){
            $this->encryption = 'password';
        }
        else{
            $this->encryption = 'sha2';
        }
	}

	public function reset(): void
	{
		global $CLIENT_ROOT;
	    $domainName = $_SERVER['HTTP_HOST'];
		if($domainName === 'localhost') {
			$domainName = false;
		}
        setcookie('SymbiotaCrumb', '', time() - 3600, ($CLIENT_ROOT?:'/'),$domainName,false,true);
        setcookie('SymbiotaCrumb', '', time() - 3600, ($CLIENT_ROOT?:'/'));
		unset($_SESSION['userrights'], $_SESSION['userparams']);
	}

	public function authenticate($pwdStr = ''): bool
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
                        $this->authSql .= 'AND (u.password = PASSWORD("' . $this->cleanInStr($pwdStr) . '")) ';
                    }
                    if($this->encryption === 'sha2'){
                        $this->authSql .= 'AND (u.password = SHA2("' . $this->cleanInStr($pwdStr) . '", 224)) ';
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
        global $CLIENT_ROOT;
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
            setcookie('SymbiotaCrumb', Encryption::encrypt(json_encode($tokenArr)), $cookieExpire, ($CLIENT_ROOT ?: '/'), $domainName, false, true);
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
			$values = 'firstname = "'.$this->cleanInStr($person->getFirstName()).'"';
			if($middle) {
				$values = 'middleinitial = "' . $this->cleanInStr($person->getMiddleInitial()) . '"';
			}
			$values .= ', lastname= "'.$this->cleanInStr($person->getLastName()).'"';
			$values .= ', title= "'.$this->cleanInStr($person->getTitle()).'"';
			$values .= ', institution="'.$this->cleanInStr($person->getInstitution()).'"';
			$values .= ', department= "'.$this->cleanInStr($person->getDepartment()).'"';
			$values .= ', address= "'.$this->cleanInStr($person->getAddress()).'"';
			$values .= ', city="'.$this->cleanInStr($person->getCity()).'"';
			$values .= ', state="'.$this->cleanInStr($person->getState()).'"';
			$values .= ', zip="'.$this->cleanInStr($person->getZip()).'"';
			$values .= ', country= "'.$this->cleanInStr($person->getCountry()).'"';
			$values .= ', phone="'.$this->cleanInStr($person->getPhone()).'"';
			$values .= ', email="'.$this->cleanInStr($person->getEmail()).'"';
			$values .= ', url="'.$this->cleanInStr($person->getUrl()).'"';
			$values .= ', biography="'.$this->cleanInStr($person->getBiography()).'"';
			$values .= ', ispublic='.($this->cleanInStr($person->getIsPublic())?1:0).' ';
			$sql = $fields. ' ' .$values. ' ' .$where;
			//echo $sql;
			$success = $editCon->query($sql);
			$editCon->close();
		}
		return $success;
	}

	public function deleteProfile($reset = 0){
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

	public function changePassword ($newPwd, $oldPwd = '', $isSelf = 0): bool
	{
		$success = false;
		if($newPwd){
			$connection = new DbConnection();
			$editCon = $connection->getConnection();
			if($isSelf){
                $sqlTest = '';
			    if($this->encryption === 'password'){
                    $sqlTest = 'SELECT u.uid FROM users u WHERE (u.uid = '.$this->uid.') '.
                        'AND (u.password = PASSWORD("'.$this->cleanInStr($oldPwd).'"))';
                }
                if($this->encryption === 'sha2'){
                    $sqlTest = 'SELECT u.uid FROM users u WHERE (u.uid = '.$this->uid.') '.
                        'AND (u.password = SHA2("'.$this->cleanInStr($oldPwd).'", 224))';
                }
				$rsTest = $editCon->query($sqlTest);
				if(!$rsTest->num_rows) {
					return false;
				}
			}
            $sql = '';
			if($this->encryption === 'password'){
                $sql = 'UPDATE users SET password = PASSWORD("'.$this->cleanInStr($newPwd).'") '.
                    'WHERE (uid = '.$this->uid.')';
            }
            if($this->encryption === 'sha2'){
                $sql = 'UPDATE users SET password = SHA2("'.$this->cleanInStr($newPwd).'", 224) '.
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
		global $DEFAULT_TITLE, $CLIENT_ROOT, $ADMIN_EMAIL;
	    $newPassword = $this->generateNewPassword();
		$status = false;
		if($un){
			$connection = new DbConnection();
			$editCon = $connection->getConnection();
            $sql = '';
			if($this->encryption === 'password'){
                $sql = 'UPDATE users SET password = PASSWORD("'.$this->cleanInStr($newPassword).'") '.
                    'WHERE (username = "'.$this->cleanInStr($un).'")';
            }
            if($this->encryption === 'sha2'){
                $sql = 'UPDATE users SET password = SHA2("'.$this->cleanInStr($newPassword).'", 224) '.
                    'WHERE (username = "'.$this->cleanInStr($un).'")';
            }
			$status = $editCon->query($sql);
			$editCon->close();
		}
		if($status){
			$emailAddr = '';
			$sql = 'SELECT u.email FROM users u '.
				'WHERE (u.username = "'.$this->cleanInStr($un).'")';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
                $emailAddr = $row->email;
			}
			$result->free();

			$subject = 'Your password';
			$bodyStr = 'Your ' .$DEFAULT_TITLE." (<a href='http://".$_SERVER['HTTP_HOST'].$CLIENT_ROOT."'>http://".$_SERVER['HTTP_HOST'].$CLIENT_ROOT. '</a>) password has been reset to: ' .$newPassword. ' ';
			$bodyStr .= "<br/><br/>After logging in, you can reset your password by clicking on <a href='http://".$_SERVER['HTTP_HOST'].$CLIENT_ROOT."/profile/viewprofile.php'>View Profile</a> link and then click the Edit Profile tab.";
			$bodyStr .= '<br/>If you have problems with the new password, contact the System Administrator ';
			if($ADMIN_EMAIL){
				$bodyStr .= '<' .$ADMIN_EMAIL. '>';
			}
			$fromAddr = $ADMIN_EMAIL;
            $headerStr = 'MIME-Version: 1.0' .
				'Content-type: text/html' .
				'To: ' .$emailAddr;
            $headerStr .= 'From: Admin <' .$fromAddr. '>';
            mail($emailAddr,$subject,$bodyStr,$headerStr);
			$returnStr = 'Your new password was just emailed to: ' .$emailAddr;
		}
		else{
			$returnStr = 'Reset Failed! Contact Administrator';
		}
		return $returnStr;
	}

	private function generateNewPassword(): string
    {
		$newPassword = '';
		$alphabet = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
		for($i = 0; $i<8; $i++) {
            try {
                $newPassword .= $alphabet[random_int(0, count($alphabet) - 1)];
            } catch (Exception $e) {
            }
        }
		return $newPassword;
	}

	public function register($postArr): bool
    {
		$status = false;
        $manager = new Manager();
        $middle = $manager->checkFieldExists('users','middleinitial');
		$firstName = $postArr['firstname'];
		if($middle) {
            $middle = $postArr['middleinitial'];
        }
		$lastName = $postArr['lastname'];
		if($postArr['institution'] && $postArr['title'] && preg_match('/[a-z]+[A-Z]+[a-z]+[A-Z]+/', $postArr['institution']) && preg_match('/[a-z]+[A-Z]+[a-z]+[A-Z]+/', $postArr['title']) && !trim(strpos($postArr['institution'], ' ')) && !trim(strpos($postArr['title'], ' '))) {
            return false;
        }

		$person = new Person();
		$person->setPassword($postArr['pwd']);
		$person->setUserName($this->userName);
		$person->setFirstName($firstName);
		if($middle) {
            $person->setMiddleInitial($middle);
        }
		$person->setLastName($lastName);
		$person->setTitle($postArr['title']);
		$person->setInstitution($postArr['institution']);
        $person->setDepartment($postArr['department']);
        $person->setAddress($postArr['address']);
		$person->setCity($postArr['city']);
		$person->setState($postArr['state']);
		$person->setZip($postArr['zip']);
		$person->setCountry($postArr['country']);
		$person->setEmail($postArr['emailaddr']);
		$person->setUrl($postArr['url']);
		$person->setBiography($postArr['biography']);
		$person->setIsPublic(isset($postArr['ispublic'])?1:0);

        $fields = 'INSERT INTO users (';
		$values = 'VALUES (';
		$fields .= 'firstname ';
		$values .= '"'.$this->cleanInStr($person->getFirstName()).'"';
		if($middle){
            $fields .= ', middleinitial ';
            $values .= ', "'.$this->cleanInStr($person->getMiddleInitial()).'"';
        }
		$fields .= ', lastname';
		$values .= ', "'.$this->cleanInStr($person->getLastName()).'"';
        $fields .= ', username';
        $values .= ', "'.$this->cleanInStr($person->getUserName()).'"';
        $fields .= ', password';
        if($this->encryption === 'password'){
            $values .= ', PASSWORD("'.$this->cleanInStr($person->getPassword()).'")';
        }
        if($this->encryption === 'sha2'){
            $values .= ', SHA2("'.$this->cleanInStr($person->getPassword()).'", 224)';
        }
		if($person->getTitle()){
			$fields .= ', title';
			$values .= ', "'.$this->cleanInStr($person->getTitle()).'"';
		}
		if($person->getInstitution()){
			$fields .= ', institution';
			$values .= ', "'.$this->cleanInStr($person->getInstitution()).'"';
		}
		if($person->getDepartment()){
			$fields .= ', department';
			$values .= ', "'.$this->cleanInStr($person->getDepartment()).'"';
		}
		if($person->getAddress()){
			$fields .= ', address';
			$values .= ', "'.$this->cleanInStr($person->getAddress()).'"';
		}
		if($person->getCity()){
			$fields .= ', city';
			$values .= ', "'.$this->cleanInStr($person->getCity()).'"';
		}
		$fields .= ', state';
		$values .= ', "'.$this->cleanInStr($person->getState()).'"';
		$fields .= ', country';
		$values .= ', "'.$this->cleanInStr($person->getCountry()).'"';
		if($person->getZip()){
			$fields .= ', zip';
			$values .= ', "'.$this->cleanInStr($person->getZip()).'"';
		}
		if($person->getPhone()){
			$fields .= ', phone';
			$values .= ', "'.$this->cleanInStr($person->getPhone()).'"';
		}
		if($person->getEmail()){
			$fields .= ', email';
			$values .= ', "'.$this->cleanInStr($person->getEmail()).'"';
		}
		if($person->getUrl()){
			$fields .= ', url';
			$values .= ', "'.$person->getUrl().'"';
		}
		if($person->getBiography()){
			$fields .= ', biography';
			$values .= ', "'.$this->cleanInStr($person->getBiography()).'"';
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

		return $status;
	}

	public function lookupUserName($emailAddr): bool
    {
        global $DEFAULT_TITLE, $CLIENT_ROOT, $ADMIN_EMAIL;
        $status = false;
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
			$subject = $DEFAULT_TITLE.' Login Name';
			$bodyStr = 'Your '.$DEFAULT_TITLE.' (<a href="http://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'">http://'.
                $_SERVER['HTTP_HOST'].$CLIENT_ROOT.'</a>) login name is: '.$loginStr.' ';
			$bodyStr .= '<br/>If you continue to have login issues, contact the System Administrator ';
			if($ADMIN_EMAIL){
				$bodyStr .= '<' .$ADMIN_EMAIL. '>';
			}
            $fromAddr = $ADMIN_EMAIL;
            $headerStr = 'MIME-Version: 1.0' .
                'Content-type: text/html' .
                'To: ' .$emailAddr;
            $headerStr .= 'From: Admin <' .$fromAddr. '>';
			if(mail($emailAddr,$subject,$bodyStr,$headerStr)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR sending email, mailserver might not be properly setup';
			}
		}
		else{
			$this->errorStr = 'There are no users registered to email address: '.$emailAddr;
		}

		return $status;
	}

	public function changeLogin($newLogin, $pwd = ''): bool
    {
		global $SYMB_UID;
        $status = true;
		if($this->uid){
			$isSelf = true;
			if($this->uid !== $SYMB_UID) {
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
						$this->errorStr = 'ERROR saving new login: '.$editCon->error;
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
		global $USER_RIGHTS;
		$retArr = array();
		if($this->uid){
			$cAdminArr = array();
			if(array_key_exists('CollAdmin',$USER_RIGHTS)) {
                $cAdminArr = $USER_RIGHTS['CollAdmin'];
            }
			$cArr = $cAdminArr;
			if(array_key_exists('CollEditor',$USER_RIGHTS)) {
                $cArr = array_merge($cArr, $USER_RIGHTS['CollEditor']);
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

	public function getPersonalOccurrenceCount($collId){
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

	public function deleteUserTaxonomy($utid,$editorStatus = ''): string
    {
		global $SYMB_UID, $USERNAME;
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
				if($this->uid === $SYMB_UID){
					$this->userName = $USERNAME;
					$this->authenticate();
				}
			}
			else{
				$statusStr = 'ERROR deleting taxonomic relationship: '.$editCon->error;
			}
			$editCon->close();
		}
		return $statusStr;
	}

	public function addUserTaxonomy($taxon,$editorStatus,$geographicScope,$notes): string
    {
		global $SYMB_UID, $USERNAME;
        $statusStr = 'SUCCESS adding taxonomic relationship';

		$tid = 0;
		$taxon = $this->cleanInStr($taxon);
		$editorStatus = $this->cleanInStr($editorStatus);
		$geographicScope = $this->cleanInStr($geographicScope);
		$notes = $this->cleanInStr($notes);
		$modDate = date('Y-m-d H:i:s');
		$sql1 = 'SELECT tid FROM taxa WHERE sciname = "'.$taxon.'"';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$tid = $r1->tid;
		}
		$rs1->close();
		if($tid){
			$sql2 = 'INSERT INTO usertaxonomy(uid, tid, taxauthid, editorstatus, geographicScope, notes, modifiedUid, modifiedtimestamp) '.
				'VALUES('.$this->uid.','.$tid.',1,"'.$editorStatus.'","'.$geographicScope.'","'.$notes.'",'.$SYMB_UID.',"'.$modDate.'") ';
			//echo $sql;
			$connection = new DbConnection();
			$editCon = $connection->getConnection();
			if($editCon->query($sql2)){
				if($this->uid === $SYMB_UID){
					$this->userName = $USERNAME;
					$this->authenticate();
				}
			}
			else{
				$statusStr = 'ERROR adding taxonomic relationship: '.$editCon->error;
			}
			$editCon->close();
		}
		else{
			$statusStr = 'ERROR adding taxonomic relationship: unable to obtain tid for '.$taxon;
		}
		return $statusStr;
	}

    public function dlSpecBackup($collId, $characterSet, $zipFile = 1){
		global $CHARSET, $PARAMS_ARR, $SERVER_ROOT, $CLIENT_ROOT;
        $tempPath = $this->getTempPath();
    	$buFileName = $PARAMS_ARR['un'].'_'.time();
 		$zipArchive = null;

    	if($zipFile && class_exists('ZipArchive')){
			$zipArchive = new ZipArchive;
			$zipArchive->open($tempPath.$buFileName.'.zip', ZipArchive::CREATE);
 		}

    	$cSet = str_replace('-','',strtolower($CHARSET));
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
			$fileUrl = str_replace($SERVER_ROOT,$CLIENT_ROOT,$tempPath.$buFileName.'.zip');
			$zipArchive->close();
			unlink($fileName.'_spec.csv');
		}
		else{
			$fileUrl = str_replace($SERVER_ROOT,$CLIENT_ROOT,$tempPath.$buFileName.'_spec.csv');
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
        global $USER_RIGHTS;
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
            $USER_RIGHTS = $userrights;
        }
	}

    private function setUserParams(): void
    {
        global $PARAMS_ARR, $USERNAME;
	    $_SESSION['userparams']['un'] = $this->userName;
        $_SESSION['userparams']['dn'] = $this->displayName;
        $_SESSION['userparams']['uid'] = $this->uid;
        $PARAMS_ARR = $_SESSION['userparams'];
        $USERNAME = $this->userName;
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

	public function setUserName($un = ''): bool
    {
		global $USERNAME, $SYMB_UID;
        if($un){
			if(!$this->validateUserName($un)) {
                return false;
            }
			$this->userName = $un;
		}
		else if($this->uid === $SYMB_UID){
            $this->userName = $USERNAME;
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

    public function getUserName($uId){
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
		global $SERVER_ROOT;
	    $tPath = $SERVER_ROOT;
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
		if (preg_match('/^[0-9A-Za-z_!@#$\s.+\-]+$/', $un) === 0) {
            $status = false;
        }
		if (strpos($un, ' ') === 0) {
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
        $sql = 'SELECT CollID, InstitutionCode, CollectionCode, CollectionName FROM omcollections';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->CollID]['CollectionName'] = $r->CollectionName;
                $retArr[$r->CollID]['CollectionCode'] = $r->CollectionCode;
                $retArr[$r->CollID]['InstitutionCode'] = $r->InstitutionCode;
            }
            $rs->close();
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

    public function getTokenCnt(){
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

    public function getUid($un){
        $uid = '';
        $sql = 'SELECT uid FROM users WHERE username = "'.$un.'"  ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $uid = $row->uid;
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
            $statusStr = 'ERROR clearing access token: '.$editCon->error;
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
            $statusStr = 'ERROR clearing access tokens: '.$editCon->error;
        }
        $editCon->close();
        return $statusStr;
    }
}
