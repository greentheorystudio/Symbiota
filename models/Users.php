<?php
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/EncryptionService.php');
include_once(__DIR__ . '/../services/MailerService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Users{

	private $conn;
    private $encryption;

    private $fields = array(
        "uid" => array("dataType" => "number", "length" => 10),
        "firstname" => array("dataType" => "string", "length" => 45),
        "middleinitial" => array("dataType" => "string", "length" => 2),
        "lastname" => array("dataType" => "string", "length" => 45),
        "username" => array("dataType" => "string", "length" => 45),
        "password" => array("dataType" => "string", "length" => 255),
        "title" => array("dataType" => "string", "length" => 150),
        "institution" => array("dataType" => "string", "length" => 200),
        "department" => array("dataType" => "string", "length" => 200),
        "address" => array("dataType" => "string", "length" => 255),
        "city" => array("dataType" => "string", "length" => 100),
        "state" => array("dataType" => "string", "length" => 50),
        "zip" => array("dataType" => "string", "length" => 15),
        "country" => array("dataType" => "string", "length" => 50),
        "email" => array("dataType" => "string", "length" => 100),
        "url" => array("dataType" => "string", "length" => 400),
        "biography" => array("dataType" => "string", "length" => 1500),
        "guid" => array("dataType" => "string", "length" => 45),
        "validated" => array("dataType" => "string", "length" => 45),
        "lastlogindate" => array("dataType" => "date", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
        $dbVersion = $connection->getVersion();
        if((strncmp($dbVersion, '5.', 2) === 0) || (strncmp($dbVersion, 'ma', 2) === 0)){
            $this->encryption = 'password';
        }
        else{
            $this->encryption = 'sha2';
        }
    }

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function authenticateUserFromPassword($username, $password, $rememberMe = false): int
    {
        $returnVal = 0;
        unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
        if($username && $password){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users '.
                'WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
            if($this->encryption === 'password'){
                $sql .= 'AND password = PASSWORD("' . SanitizerService::cleanInStr($this->conn, $password) . '") ';
            }
            if($this->encryption === 'sha2'){
                $sql .= 'AND password = SHA2("' . SanitizerService::cleanInStr($this->conn, $password) . '", 224) ';
            }
            $result = $this->conn->query($sql);
            if(($row = $result->fetch_object()) && $row->uid) {
                $returnVal = 1;
                $this->clearCookieSession();
                $this->setUserParams($row);
                (new Permissions)->setUserPermissions();
                if($rememberMe){
                    $this->setTokenCookie($row->uid, $username, 2592000);
                }
                else{
                    $this->setTokenCookie($row->uid, $username, 10800);
                }
                $sql = 'UPDATE users SET lastlogindate = NOW() WHERE username = "' . $username . '" ';
                $this->conn->query($sql);
            }
            $result->free();
        }
        return $returnVal;
    }

    public function authenticateUserFromToken($username, $token, $cookieDuration): int
    {
        $returnVal = 0;
        unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
        if($username && $token){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'u');
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users AS u LEFT JOIN useraccesstokens AS ut ON u.uid = ut.uid '.
                'WHERE u.username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" AND ut.token = "' . SanitizerService::cleanInStr($this->conn, $token) . '" ';
            $result = $this->conn->query($sql);
            if(($row = $result->fetch_object()) && $row->uid) {
                $returnVal = 1;
                $this->clearCookieSession();
                $this->setUserParams($row);
                (new Permissions)->setUserPermissions();
                $this->setTokenCookie($row->uid, $username, $cookieDuration);
                $sql = 'UPDATE users SET lastlogindate = NOW() WHERE username = "' . $username . '" ';
                $this->conn->query($sql);
            }
            $result->free();
        }
        return $returnVal;
    }

    public function changePassword($uid, $password): int
    {
        $success = 0;
        if($uid && $password){
            $sql = '';
            if($this->encryption === 'password'){
                $sql .= 'UPDATE users SET password = PASSWORD("' . SanitizerService::cleanInStr($this->conn, $password) . '") ';
            }
            if($this->encryption === 'sha2'){
                $sql .= 'UPDATE users SET password = SHA2("' . SanitizerService::cleanInStr($this->conn, $password) . '", 224) ';
            }
            $sql .= 'WHERE uid = ' . (int)$uid . ' ';
            if($this->conn->query($sql)){
                $success = 1;
            }
            $this->conn->close();
        }
        return $success;
    }

    public function clearAccessTokens($uid): int
    {
        $returnVal = 0;
        if($uid){
            $sql = 'DELETE FROM useraccesstokens WHERE uid = ' . (int)$uid;
            //echo $sql;
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
            $this->conn->close();
        }
        return $returnVal;
    }

    public function clearCookieSession(): void
    {
        $domainName = $_SERVER['HTTP_HOST'];
        setcookie('BioSurvCrumb', '', (time() - 3600), ($GLOBALS['CLIENT_ROOT'] ?: '/'), $domainName, false, true);
        unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
    }

    public function clearOldUnregisteredUsers(): void
    {
        $sql = 'DELETE ua.*, u.* '.
            'FROM users AS u LEFT JOIN useraccesstokens AS ua ON u.uid = ua.uid '.
            'WHERE u.initialtimestamp < DATE_SUB(NOW(), INTERVAL 30 DAY) AND (ISNULL(u.validated) OR u.validated <> 1) ';
        $this->conn->query($sql);
    }

    public function createToken($uid): string
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
        } catch (Exception $e) {}
        if($token){
            $sql = 'INSERT INTO useraccesstokens(uid, token) '.
                'VALUES (' . (int)$uid . ', "' . $token . '") ';
            $this->conn->query($sql);
        }
        return $token;
    }

    public function createUser($data): int
    {
        if($GLOBALS['EMAIL_CONFIGURED']){
            $this->clearOldUnregisteredUsers();
        }
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $firstName = array_key_exists('firstname', $data) ? SanitizerService::cleanInStr($this->conn, $data['firstname']) : '';
        $lastName = array_key_exists('lastname', $data) ? SanitizerService::cleanInStr($this->conn, $data['lastname']) : '';
        $email = array_key_exists('email', $data) ? SanitizerService::cleanInStr($this->conn, $data['email']) : '';
        $username = array_key_exists('username', $data) ? SanitizerService::cleanInStr($this->conn, $data['username']) : '';
        $password = array_key_exists('pwd', $data) ? SanitizerService::cleanInStr($this->conn, $data['pwd']) : '';
        if($firstName && $lastName && $email && $username && $password){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'uid' && array_key_exists($field, $data)){
                    if($field === 'state'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'password';
            if($this->encryption === 'password'){
                $fieldValueArr[] = 'PASSWORD("' . $password . '"))';
            }
            else{
                $fieldValueArr[] = 'SHA2("' . $password . '", 224))';
            }
            $sql = 'INSERT INTO users(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $this->clearCookieSession();
                $this->authenticateUserFromPassword($username, $password);
                if($GLOBALS['EMAIL_CONFIGURED']){
                    $this->sendConfirmationEmail($newID);
                }
            }
        }
        return $newID;
    }

    public function deleteAllUnconfirmedUsers(): void
    {
        $sql = 'SELECT uid FROM users WHERE validated <> 1 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sql = 'DELETE FROM useraccesstokens WHERE uid = ' . $r->uid . ' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM userroles WHERE uid = ' . $r->uid . ' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM usertaxonomy WHERE uid = ' . $r->uid . ' ';
            $this->conn->query($sql);
            $sql = 'DELETE FROM users WHERE uid = ' . $r->uid . ' ';
            $this->conn->query($sql);
        }
        $rs->free();
    }

    public function deleteToken($uid, $token): void
    {
        $sql = 'DELETE FROM useraccesstokens WHERE uid = ' . (int)$uid . ' AND token = "' . SanitizerService::cleanInStr($this->conn, $token) . '" ';
        $this->conn->query($sql);
    }

    public function deleteUser($uid): int
    {
        $retuenVal = 0;
        if($uid){
            $sql = 'DELETE FROM users WHERE uid = ' . (int)$uid . ' ';
            if($this->conn->query($sql)){
                $retuenVal = 1;
            }
        }
        $this->clearCookieSession();
        return $retuenVal;
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

    public function getTokenCnt($uid): int
    {
        $cnt = 0;
        $sql = 'SELECT COUNT(token) AS cnt FROM useraccesstokens WHERE uid = ' . (int)$uid;
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $cnt = $row->cnt;
            $result->free();
        }
        return $cnt;
    }

    public function getUserByEmail($email): array
    {
        $returnArr = array();
        if($email){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users '.
                'WHERE email = "' . SanitizerService::cleanInStr($this->conn, $email) . '" ';
            $rs = $this->conn->query($sql);
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $returnArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $returnArr;
    }

    public function getUserByUid($uid): array
    {
        $returnArr = array();
        if($uid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users '.
                'WHERE uid = ' . (int)$uid . ' ';
            $rs = $this->conn->query($sql);
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $returnArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $returnArr;
    }

    public function getUserByUsername($username): array
    {
        $returnArr = array();
        if($username){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users '.
                'WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
            $rs = $this->conn->query($sql);
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $returnArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $returnArr;
    }

    public function loginAsUser($username): int
    {
        $returnVal = 0;
        if($GLOBALS['IS_ADMIN']){
            unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
            if($username){
                $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
                $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                    'FROM users '.
                    'WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
                $result = $this->conn->query($sql);
                if(($row = $result->fetch_object()) && $row->uid) {
                    $returnVal = 1;
                    $this->clearCookieSession();
                    $this->setUserParams($row);
                    (new Permissions)->setUserPermissions();
                }
                $result->free();
            }
        }
        return $returnVal;
    }

    public function resetPassword($uid, $admin): string
    {
        $returnVal = 0;
        if($uid && ($admin || $GLOBALS['EMAIL_CONFIGURED'])){
            $newPassword = $this->generateNewPassword();
            $sql = 'UPDATE users ';
            if($this->encryption === 'password'){
                $sql .= 'SET password = PASSWORD("' . SanitizerService::cleanInStr($this->conn, $newPassword) . '") ';
            }
            if($this->encryption === 'sha2'){
                $sql .= 'SET password = SHA2("' . SanitizerService::cleanInStr($this->conn,$newPassword) . '", 224) ';
            }
            $sql .= 'WHERE uid = ' . (int)$uid . ' ';
            if($this->conn->query($sql)){
                if($admin){
                    $returnVal = $newPassword;
                }
                else{
                    $returnVal = 1;
                    $emailAddr = '';
                    $sql = 'SELECT email FROM users WHERE uid = ' . (int)$uid . ' ';
                    $result = $this->conn->query($sql);
                    if($row = $result->fetch_object()){
                        $emailAddr = $row->email;
                    }
                    if($emailAddr){
                        $subject = 'Your password';
                        $bodyStr = 'Your ' . $GLOBALS['DEFAULT_TITLE'] . ' password has been reset to: ' . $newPassword . ' ';
                        $bodyStr .= '<br/><br/>After logging in, you can reset your password by clicking on <a href="' . (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . $GLOBALS['CLIENT_ROOT'] . '/profile/viewprofile.php">View Profile</a> link and then click the View Profile tab.';
                        if($GLOBALS['ADMIN_EMAIL']){
                            $bodyStr .= '<br/>If you have problems with the new password, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                        }
                        (new MailerService)->sendEmail($emailAddr, $subject, $bodyStr);
                        $result->free();
                    }
                }
            }
        }
        return $returnVal;
    }

    public function sendConfirmationEmail($uid): int
    {
        $status = 0;
        if($GLOBALS['EMAIL_CONFIGURED']){
            $email = '';
            $code = '';
            $sql = 'SELECT email, guid FROM users WHERE uid = ' . (int)$uid . ' ';
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $email = $row->email;
                $code = $row->guid;
                if(!$code){
                    $code = UuidService::getUuidV4();
                    $sql = 'UPDATE users SET guid = "' . $code . '" WHERE uid = ' . (int)$uid . ' ';
                    $this->conn->query($sql);
                }
            }
            $result->free();
            if($email && $code){
                $confirmationLink = ((!empty($_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $GLOBALS['CLIENT_ROOT'];
                $confirmationLink .= '/profile/index.php?uid=' . (int)$uid . '&confirmationcode=' . $code;
                $subject = $GLOBALS['DEFAULT_TITLE'] . ' Confirmation';
                $bodyStr = 'Your ' . $GLOBALS['DEFAULT_TITLE'] . ' account has been created. ';
                $bodyStr .= '<br/><br/><a href="' . $confirmationLink . '">Please follow this link to confirm your new account.</a>';
                if($GLOBALS['ADMIN_EMAIL']){
                    $bodyStr .= '<br/>If you have trouble confirming your account, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                }
                $mailerResult = (new MailerService)->sendEmail($email,$subject,$bodyStr);
                if($mailerResult === 'Sent'){
                    $status = 1;
                }
            }
        }
        return $status;
    }

    public function sendUsernameEmail($email): int
    {
        $status = 0;
        if($GLOBALS['EMAIL_CONFIGURED']){
            $username = '';
            $sql = 'SELECT username FROM users '.
                'WHERE email = "' . SanitizerService::cleanInStr($this->conn, $email) . '" ';
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $username = $row->username;
            }
            if($username){
                $subject = $GLOBALS['DEFAULT_TITLE'] . ' Login Name';
                $bodyStr = 'Your ' . $GLOBALS['DEFAULT_TITLE'] . ' login name is: ' . $username . ' ';
                if(isset($GLOBALS['ADMIN_EMAIL'])){
                    $bodyStr .= '<br/>If you continue to have login issues, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                }
                $mailerResult = (new MailerService)->sendEmail($email, $subject, $bodyStr);
                if($mailerResult === 'Sent'){
                    $status = 1;
                }
            }
            $result->free();
        }
        return $status;
    }

    private function setTokenCookie($uid, $username, $duration): void
    {
        $tokenArr = array();
        $token = $this->createToken($uid);
        if($token){
            $tokenArr[] = $username;
            $tokenArr[] = $token;
            $tokenArr[] = $duration;
            $cookieExpire = time() + $duration;
            $domainName = $_SERVER['HTTP_HOST'];
            setcookie('BioSurvCrumb', EncryptionService::encrypt(json_encode($tokenArr)), $cookieExpire, ($GLOBALS['CLIENT_ROOT'] ?: '/'), $domainName, false, true);
        }
    }

    private function setUserParams($user): void
    {
        $displayName = $user->firstname;
        if(strlen($displayName) > 15) {
            $displayName = $user->username;
        }
        if(strlen($displayName) > 15) {
            $displayName = substr($displayName, 0, 10) . '...';
        }
        $_SESSION['PARAMS_ARR']['un'] = $user->username;
        $_SESSION['PARAMS_ARR']['dn'] = $displayName;
        $_SESSION['PARAMS_ARR']['uid'] = $user->uid;
        $_SESSION['PARAMS_ARR']['valid'] = $user->validated ? (int)$user->validated : 0;
        $GLOBALS['PARAMS_ARR'] = $_SESSION['PARAMS_ARR'];
        $GLOBALS['USERNAME'] = $user->username;
    }

    public function updateUser($userId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($userId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE users SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE uid = ' . (int)$userId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function validateAllUnconfirmedUsers(): void
    {
        $sql = 'SELECT uid FROM users WHERE validated <> 1 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sql = 'UPDATE users SET validated = 1 WHERE uid = ' . $r->uid . ' ';
            $this->conn->query($sql);
        }
        $rs->free();
    }

    public function validateFromConfirmationEmail($uid,$confirmationCode): int
    {
        $returnVal = 0;
        if($uid && $confirmationCode){
            $sql = 'SELECT guid, validated '.
                'FROM users '.
                'WHERE uid = ' . (int)$uid . ' ';
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                if((int)$row->validated !== 1 && $row->guid && $row->guid === $confirmationCode) {
                    $sql = 'UPDATE users SET validated = 1 WHERE uid = ' . (int)$uid . ' ';
                    $this->conn->query($sql);
                    $returnVal = 1;
                }
            }
            $result->free();
        }
        return $returnVal;
    }

    public function validateUser($userId): void
    {
        if($userId){
            $sql = 'UPDATE users SET validated = 1 WHERE uid = '.(int)$userId.' ';
            //echo $sql; Exit;
            $this->conn->query($sql);
        }
    }
}
