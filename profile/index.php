<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$uid = array_key_exists('uid',$_REQUEST)?(int)$_REQUEST['uid']:0;
$confirmationCode = array_key_exists('confirmationcode',$_REQUEST)?htmlspecialchars($_REQUEST['confirmationcode']):'';

$refUrl = '';
if(strpos($_SERVER['REQUEST_URI'], 'refurl=')){
    $fullRequest = str_replace('%22', '"',$_SERVER['REQUEST_URI']);
    $refUrl = substr($fullRequest, strpos($fullRequest, 'refurl=') + 7);
}
elseif(array_key_exists('refurl',$_REQUEST)){
    $refUrl = $_REQUEST['refurl'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Login</title>
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .login-container {
                width: 30%;
            }
        </style>
        <script type="text/javascript">
            const CONFIRMATION_CODE = '<?php echo $confirmationCode; ?>';
            const REF_URL = '<?php echo $refUrl; ?>';
            const UID = <?php echo $uid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="innertext">
            <div class="row justify-center q-mt-lg q-mb-xl">
                <q-card class="login-container">
                    <q-card-section class="bg-indigo-1 column">
                        <q-input outlined v-model="username" label="Username" bg-color="white" class="q-mb-sm" dense></q-input>
                        <q-input outlined v-model="password" type="password" label="Password" bg-color="white" class="q-mb-sm" dense></q-input>
                        <q-checkbox v-model="rememberMe" label="Remember me on this computer" class="q-mb-sm"></q-checkbox>
                        <div class="row justify-end q-pr-md">
                            <q-btn color="secondary" @click="processLogin();" label="Login" dense></q-btn>
                        </div>
                    </q-card-section>
                    <q-separator size="1px" color="grey-8"></q-separator>
                    <q-card-section class="column justify-center">
                        <div class="column justify-center q-mb-xs text-bold">
                            <span class="row justify-center">
                                Don't have an account?
                            </span>
                            <span class="row justify-center">
                                <a href="newprofile.php" class="anchor-class text-primary">Create an account here</a>
                            </span>
                        </div>
                        <template v-if="adminEmail !== ''">
                            <div class="column justify-center q-mb-xs text-bold">
                                <span class="row justify-center">
                                    Can't remember your password?
                                </span>
                                <template v-if="emailConfigured">
                                    <span class="row justify-center">
                                        <a class="anchor-class text-primary cursor-pointer" @click="resetPassword();">Reset password</a>
                                    </span>
                                </template>
                            </div>
                        </template>
                        <template v-if="adminEmail !== ''">
                            <template v-if="!retrieveUsernameWindow">
                                <div class="column justify-center q-mb-xs text-bold">
                                    <span class="row justify-center">
                                        Can't remember your username?
                                    </span>
                                    <template v-if="emailConfigured">
                                        <span class="row justify-center">
                                            <a class="anchor-class text-primary cursor-pointer" @click="retrieveUsernameWindow = !retrieveUsernameWindow">Retrieve username</a>
                                        </span>
                                    </template>
                                </div>
                            </template>
                            <template v-else>
                                <div class="column justify-center q-mb-xs text-bold">
                                    <q-input outlined v-model="email" label="Your Email" bg-color="white" class="q-mb-sm" dense></q-input>
                                    <div class="row justify-center">
                                        <q-btn color="secondary" @click="retrieveUsername();" label="Retrieve Username" dense></q-btn>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template v-if="!emailConfigured && adminEmail !== ''">
                            <div class="text-center text-bold text-red">
                                Contact the portal administrator at {{ adminEmail }} for assistance.
                            </div>
                        </template>
                    </q-card-section>
                </q-card>
            </div>
        </div>
        <?php
        include(__DIR__ . '/../footer.php');
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script type="text/javascript">
            const loginModule = Vue.createApp({
                setup() {
                    const { showNotification } = useCore();
                    const store = useBaseStore();
                    const adminEmail = store.getAdminEmail;
                    const confirmationCode = CONFIRMATION_CODE;
                    const email = Vue.ref(null);
                    const emailConfigured = store.getEmailConfigured;
                    const password = Vue.ref(null);
                    const refUrl = REF_URL;
                    const rememberMe = Vue.ref(false);
                    const retrieveUsernameWindow = Vue.ref(false);
                    const uid = UID;
                    const username = Vue.ref(null);

                    function checkCookiePermissions() {
                        if(!navigator.cookieEnabled){
                            showNotification('negative', 'Your browser cookies are disabled. To be able to login and access your profile correctly, they must be enabled for this domain.');
                        }
                    }

                    function processConfirmationCode() {
                        const formData = new FormData();
                        formData.append('uid', uid);
                        formData.append('confirmationCode', confirmationCode);
                        formData.append('action', 'processConfirmationCode');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                if(Number(res) === 1){
                                    showNotification('positive','Success! Your account has been confirmed. Please login to activate confirmation.');
                                }
                                else{
                                    showNotification('negative','There was a problem confirming your account. Please contact springsdata@springstewardship.org for assistance.');
                                }
                            });
                        });
                    }

                    function processLogin() {
                        if(username.value && password.value){
                            const formData = new FormData();
                            formData.append('username', username.value);
                            formData.append('password', password.value);
                            formData.append('remember', (rememberMe.value ? '1' : '0'));
                            formData.append('action', 'login');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        if(refUrl === '' || refUrl.startsWith('http') || refUrl.includes('newprofile.php')){
                                            window.location.href = CLIENT_ROOT + '/index.php';
                                        }
                                        else{
                                            window.location.href = refUrl;
                                        }
                                    }
                                    else{
                                        showNotification('negative','Your username and/or password were incorrect.');
                                    }
                                });
                            });
                        }
                        else{
                            showNotification('negative','Please enter your username and password to login.');
                        }
                    }

                    function resetPassword() {
                        if(username.value){
                            const formData = new FormData();
                            formData.append('username', username.value);
                            formData.append('action', 'resetPassword');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        showNotification('positive','Your new password has been emailed to the address associated with your account. Please check your junk folder if no email appears in your inbox.');
                                    }
                                    else{
                                        showNotification('negative','There was an error resetting your password.');
                                    }
                                });
                            });
                        }
                        else{
                            showNotification('negative','Please enter your username.');
                        }
                    }

                    function retrieveUsername() {
                        if(email.value){
                            const formData = new FormData();
                            formData.append('email', email.value);
                            formData.append('action', 'retrieveUsername');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        showNotification('positive','Your username has been emailed to you.');
                                    }
                                    else{
                                        showNotification('negative','There was an error sending your username to the email address you entered. Please ensure it is entered correctly.');
                                    }
                                    retrieveUsernameWindow.value = false;
                                });
                            });
                        }
                        else{
                            showNotification('negative','Please enter the email address that is associated with your account.');
                        }
                    }

                    Vue.onMounted(() => {
                        checkCookiePermissions();
                        if(Number(uid) > 0 && confirmationCode !== ''){
                            processConfirmationCode();
                        }
                    });
                    
                    return {
                        adminEmail,
                        email,
                        emailConfigured,
                        password,
                        rememberMe,
                        retrieveUsernameWindow,
                        username,
                        processLogin,
                        resetPassword,
                        retrieveUsername
                    }
                }
            });
            loginModule.use(Quasar, { config: {} });
            loginModule.use(Pinia.createPinia());
            loginModule.mount('#innertext');
        </script>
    </body>
</html>	
