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
        'uid' => array('dataType' => 'number', 'length' => 10),
        'firstname' => array('dataType' => 'string', 'length' => 45),
        'middleinitial' => array('dataType' => 'string', 'length' => 2),
        'lastname' => array('dataType' => 'string', 'length' => 45),
        'username' => array('dataType' => 'string', 'length' => 45),
        'password' => array('dataType' => 'string', 'length' => 255),
        'title' => array('dataType' => 'string', 'length' => 150),
        'institution' => array('dataType' => 'string', 'length' => 200),
        'email' => array('dataType' => 'string', 'length' => 100),
        'guid' => array('dataType' => 'string', 'length' => 45),
        'validated' => array('dataType' => 'string', 'length' => 45),
        'lastlogindate' => array('dataType' => 'date', 'length' => 0),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
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
        $this->conn->close();
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
                $sql .= 'AND password = SHA2("' . SanitizerService::cleanInStr($this->conn, $password) . '", 224) OR password = SHA2("' . SanitizerService::cleanInStr($this->conn, $password) . '", 256) ';
            }
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $returnVal = 1;
                    $this->clearCookieSession();
                    $this->setUserParams($row);
                    (new Permissions)->setUserPermissions();
                    if($rememberMe){
                        $this->setTokenCookie($row['uid'], SanitizerService::cleanInStr($this->conn, $username), 2592000);
                    }
                    else{
                        $this->setTokenCookie($row['uid'], SanitizerService::cleanInStr($this->conn, $username), 10800);
                    }
                    $sql = 'UPDATE users SET lastlogindate = NOW() WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
                    $this->conn->query($sql);
                }
            }
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
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row && $row['uid']){
                    $returnVal = 1;
                    $this->clearCookieSession();
                    $this->setUserParams($row);
                    (new Permissions)->setUserPermissions();
                    $this->setTokenCookie($row['uid'], SanitizerService::cleanInStr($this->conn, $username), $cookieDuration);
                    $sql = 'UPDATE users SET lastlogindate = NOW() WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
                    $this->conn->query($sql);
                }
            }
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
                $sql .= 'UPDATE users SET password = SHA2("' . SanitizerService::cleanInStr($this->conn, $password) . '", 256) ';
            }
            $sql .= 'WHERE uid = ' . (int)$uid . ' ';
            if($this->conn->query($sql)){
                $success = 1;
            }
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
        $password = array_key_exists('password', $data) ? SanitizerService::cleanInStr($this->conn, $data['password']) : '';
        if($firstName && $lastName && $email && $username && $password){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'uid' && $field !== 'password' && $field !== 'validated' && array_key_exists($field, $data)){
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
                $fieldValueArr[] = 'PASSWORD("' . $password . '")';
            }
            else{
                $fieldValueArr[] = 'SHA2("' . $password . '", 256)';
            }
            $fieldNameArr[] = 'validated';
            $fieldValueArr[] = '0';
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

    public function deleteAllUnconfirmedUsers(): int
    {
        $retuenVal = 0;
        $sql = 'DELETE t.* FROM useraccesstokens AS t LEFT JOIN users AS u ON t.uid = u.uid WHERE u.validated <> 1 ';
        if($this->conn->query($sql)){
            $retuenVal = 1;
        }
        if($retuenVal){
            $sql = 'DELETE r.* FROM userroles AS r LEFT JOIN users AS u ON r.uid = u.uid WHERE u.validated <> 1 ';
            if(!$this->conn->query($sql)){
                $retuenVal = 0;
            }
        }
        if($retuenVal){
            $sql = 'DELETE FROM users WHERE validated <> 1 ';
            if(!$this->conn->query($sql)){
                $retuenVal = 0;
            }
        }
        return $retuenVal;
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
        if($row = $result->fetch_array(MYSQLI_ASSOC)){
            $cnt = $row['cnt'];
        }
        $result->free();
        return $cnt;
    }

    public function getUserArrByPermission($permission, $tablePk = null): array
    {
        $retArr = array();
        if($permission && $permission !== 'SuperAdmin'){
            $sql = 'SELECT u.uid, u.firstname, u.lastname, u.username '.
                'FROM users AS u LEFT JOIN userroles AS r ON u.uid = r.uid '.
                'WHERE r.role = "' . SanitizerService::cleanInStr($this->conn, $permission) . '" ';
            if((int)$tablePk > 0){
                $sql .= 'AND r.tablepk = ' . (int)$tablePk . ' ';
            }
            $sql .= 'ORDER BY u.lastname, u.firstname, u.username ';
            //echo "<div>".$sql."</div>";
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['uid'] = $row['uid'];
                    $nodeArr['firstname'] = $row['firstname'];
                    $nodeArr['lastname'] = $row['lastname'];
                    $nodeArr['username'] = $row['username'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getUserByEmail($email): array
    {
        $returnArr = array();
        if($email){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users WHERE email = "' . SanitizerService::cleanInStr($this->conn, $email) . '" ';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    foreach($fields as $val){
                        $name = $val->name;
                        $returnArr[$name] = $row[$name];
                    }
                }
            }
        }
        return $returnArr;
    }

    public function getUserByUid($uid): array
    {
        $returnArr = array();
        if($uid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users WHERE uid = ' . (int)$uid . ' ';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    foreach($fields as $val){
                        $name = $val->name;
                        $returnArr[$name] = $row[$name];
                    }
                }
            }
        }
        return $returnArr;
    }

    public function getUserByUsername($username): array
    {
        $returnArr = array();
        if($username){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM users WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    foreach($fields as $val){
                        $name = $val->name;
                        $returnArr[$name] = $row[$name];
                    }
                }
            }
        }
        return $returnArr;
    }

    public function getUsers($keyword, $userType): array
    {
        $this->clearOldUnregisteredUsers();
        $retArr = array();
        $whereArr = array();
        $sql = 'SELECT uid, firstname, middleinitial, lastname, username FROM users ';
        if($userType === 'confirmed'){
            $whereArr[] = 'validated = "1"';
        }
        elseif($userType === 'unconfirmed'){
            $whereArr[] = 'validated <> "1"';
        }
        elseif($userType === 'nonadmin'){
            $whereArr[] = 'uid NOT IN(SELECT uid FROM userroles WHERE role = "SuperAdmin")';
        }
        if($keyword){
            $whereArr[] = '(lastname LIKE "'. SanitizerService::cleanInStr($this->conn, $keyword) . '%" OR username LIKE "' . SanitizerService::cleanInStr($this->conn, $keyword) . '%")';
        }
        if(count($whereArr) > 0){
            $sql .= 'WHERE ' . implode(' AND ', $whereArr) . ' ';
        }
        $sql .= 'ORDER BY lastname, firstname';
        //echo "<div>".$sql."</div>";
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['uid'] = $row['uid'];
                $nodeArr['firstname'] = $row['firstname'];
                $nodeArr['middleinitial'] = $row['middleinitial'];
                $nodeArr['lastname'] = $row['lastname'];
                $nodeArr['username'] = $row['username'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function loginAsUser($username): int
    {
        $returnVal = 0;
        if($GLOBALS['IS_ADMIN']){
            unset($_SESSION['USER_RIGHTS'], $_SESSION['PARAMS_ARR']);
            if($username){
                $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
                $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                    'FROM users WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
                if($result = $this->conn->query($sql)){
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $result->free();
                    if($row && $row['uid']){
                        $returnVal = 1;
                        $this->clearCookieSession();
                        $this->setUserParams($row);
                        (new Permissions)->setUserPermissions();
                    }
                }
            }
        }
        return $returnVal;
    }

    public function resetPassword($username, $admin): string
    {
        $returnVal = '0';
        if($username && ($admin || $GLOBALS['EMAIL_CONFIGURED'])){
            $newPassword = $this->generateNewPassword();
            $sql = 'UPDATE users ';
            if($this->encryption === 'password'){
                $sql .= 'SET password = PASSWORD("' . SanitizerService::cleanInStr($this->conn, $newPassword) . '") ';
            }
            if($this->encryption === 'sha2'){
                $sql .= 'SET password = SHA2("' . SanitizerService::cleanInStr($this->conn, $newPassword) . '", 256) ';
            }
            $sql .= 'WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
            if($this->conn->query($sql)){
                if($admin){
                    $returnVal = $newPassword;
                }
                else{
                    $returnVal = '1';
                    $emailAddr = '';
                    $sql = 'SELECT email FROM users WHERE username = "' . SanitizerService::cleanInStr($this->conn, $username) . '" ';
                    $result = $this->conn->query($sql);
                    if($row = $result->fetch_array(MYSQLI_ASSOC)){
                        $emailAddr = $row['email'];
                    }
                    $result->free();
                    if($emailAddr){
                        $subject = 'Your password';
                        $bodyStr = 'Your ' . $GLOBALS['DEFAULT_TITLE'] . ' password has been reset to: ' . $newPassword . ' ';
                        $bodyStr .= '<br/><br/>After logging in, you can reset your password by clicking on <a href="' . SanitizerService::getFullUrlPathPrefix() . '/profile/viewprofile.php">View Profile</a> link and then click the View Profile tab.';
                        if($GLOBALS['ADMIN_EMAIL']){
                            $bodyStr .= '<br/>If you have problems with the new password, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                        }
                        (new MailerService)->sendEmail($emailAddr, $subject, $bodyStr);
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
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $email = $row['email'];
                $code = $row['guid'];
                if(!$code){
                    $code = UuidService::getUuidV4();
                    $sql = 'UPDATE users SET guid = "' . $code . '" WHERE uid = ' . (int)$uid . ' ';
                    $this->conn->query($sql);
                }
            }
            $result->free();
            if($email && $code){
                $confirmationLink = SanitizerService::getFullUrlPathPrefix() . '/profile/index.php?uid=' . (int)$uid . '&confirmationcode=' . $code;
                $subject = $GLOBALS['DEFAULT_TITLE'] . ' Confirmation';
                $bodyStr = 'Your ' . $GLOBALS['DEFAULT_TITLE'] . ' account has been created. ';
                $bodyStr .= '<br/><br/><a href="' . $confirmationLink . '">Please follow this link to confirm your new account.</a>';
                if($GLOBALS['ADMIN_EMAIL']){
                    $bodyStr .= '<br/>If you have trouble confirming your account, contact the System Administrator at ' . $GLOBALS['ADMIN_EMAIL'];
                }
                $mailerResult = (new MailerService)->sendEmail($email, $subject, $bodyStr);
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
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $username = $row['username'];
            }
            $result->free();
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
            $cookieExpire = time() + (int)$duration;
            $domainName = $_SERVER['HTTP_HOST'];
            setcookie('BioSurvCrumb', EncryptionService::encrypt(json_encode($tokenArr)), $cookieExpire, ($GLOBALS['CLIENT_ROOT'] ?: '/'), $domainName, false, true);
        }
    }

    private function setUserParams($user): void
    {
        $displayName = $user['firstname'];
        if(strlen($displayName) > 15) {
            $displayName = $user['username'];
        }
        if(strlen($displayName) > 15) {
            $displayName = substr($displayName, 0, 10) . '...';
        }
        $_SESSION['PARAMS_ARR']['un'] = $user['username'];
        $_SESSION['PARAMS_ARR']['dn'] = $displayName;
        $_SESSION['PARAMS_ARR']['uid'] = $user['uid'];
        $_SESSION['PARAMS_ARR']['valid'] = $user['validated'] ? (int)$user['validated'] : 0;
        $GLOBALS['PARAMS_ARR'] = $_SESSION['PARAMS_ARR'];
        $GLOBALS['USERNAME'] = $user['username'];
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

    public function validateAllUnconfirmedUsers(): int
    {
        $returnVal = 0;
        $sql = 'UPDATE users SET validated = 1 WHERE validated <> 1 ';
        if($this->conn->query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function validateFromConfirmationEmail($uid, $confirmationCode): int
    {
        $returnVal = 0;
        if($uid && $confirmationCode){
            $sql = 'SELECT guid, validated '.
                'FROM users WHERE uid = ' . (int)$uid . ' ';
            $result = $this->conn->query($sql);
            if(($row = $result->fetch_array(MYSQLI_ASSOC)) && (int)$row['validated'] !== 1 && $row['guid'] && $row['guid'] === $confirmationCode) {
                $sql = 'UPDATE users SET validated = 1 WHERE uid = ' . (int)$uid . ' ';
                $this->conn->query($sql);
                $returnVal = 1;
            }
            $result->free();
        }
        return $returnVal;
    }

    public function validateUser($userId): void
    {
        if($userId){
            $sql = 'UPDATE users SET validated = 1 WHERE uid = ' . (int)$userId . ' ';
            $this->conn->query($sql);
        }
    }
}
