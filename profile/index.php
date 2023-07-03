<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
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
                            <q-btn :loading="loading" color="secondary" @click="processLogin();" label="Login" dense></q-btn>
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
                                        <q-btn :loading="loading" color="secondary" @click="retrieveUsername();" label="Retrieve Username" dense></q-btn>
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
        <script>
            const loginModule = Vue.createApp({
                data() {
                    return {
                        adminEmail: ADMIN_EMAIL,
                        confirmationCode: Vue.ref('<?php echo $confirmationCode; ?>'),
                        email: Vue.ref(null),
                        emailConfigured: EMAIL_CONFIGURED,
                        loading: Vue.ref(false),
                        password: Vue.ref(null),
                        refUrl: Vue.ref('<?php echo $refUrl; ?>'),
                        rememberMe: Vue.ref(false),
                        retrieveUsernameWindow: Vue.ref(false),
                        uid: Vue.ref(<?php echo $uid; ?>),
                        username: Vue.ref(null)
                    }
                },
                setup () {
                    const $q = useQuasar();
                    return {
                        showNotification(type, text){
                            $q.notify({
                                type: type,
                                icon: null,
                                message: text,
                                multiLine: true,
                                position: 'top',
                                timeout: 5000
                            });
                        }
                    }
                },
                mounted() {
                    this.checkCookiePermissions();
                    if(Number(this.uid) > 0 && this.confirmationCode !== ''){
                        this.processConfirmationCode();
                    }
                },
                methods: {
                    checkCookiePermissions(){
                        if(!navigator.cookieEnabled){
                            this.showNotification('negative','Your browser cookies are disabled. To be able to login and access your profile correctly, they must be enabled for this domain.');
                        }
                    },
                    processConfirmationCode(){
                        const formData = new FormData();
                        formData.append('uid', this.uid);
                        formData.append('confirmationCode', this.confirmationCode);
                        formData.append('action', 'processConfirmationCode');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                if(Number(res) === 1){
                                    this.showNotification('positive','Success! Your account has been confirmed. Please login to activate confirmation.');
                                }
                                else{
                                    this.showNotification('negative','There was a problem confirming your account. Please contact springsdata@springstewardship.org for assistance.');
                                }
                            });
                        });
                    },
                    processLogin(){
                        if(this.username && this.password){
                            const formData = new FormData();
                            formData.append('username', this.username);
                            formData.append('password', this.password);
                            formData.append('remember', (this.rememberMe ? '1' : '0'));
                            formData.append('action', 'login');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        if(this.refUrl === '' || this.refUrl.startsWith('http') || this.refUrl.includes('newprofile.php')){
                                            window.location.href = CLIENT_ROOT + '/index.php';
                                        }
                                        else{
                                            window.location.href = this.refUrl;
                                        }
                                    }
                                    else{
                                        this.showNotification('negative','Your username and/or password were incorrect.');
                                    }
                                });
                            });
                        }
                        else{
                            this.showNotification('negative','Please enter your username and password to login.');
                        }
                    },
                    resetPassword(){
                        if(this.username){
                            const formData = new FormData();
                            formData.append('username', this.username);
                            formData.append('action', 'resetPassword');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        this.showNotification('positive','Your new password has been emailed to the address associated with your account. Please check your junk folder if no email appears in your inbox.');
                                    }
                                    else{
                                        this.showNotification('negative','There was an error resetting your password.');
                                    }
                                });
                            });
                        }
                        else{
                            this.showNotification('negative','Please enter your username.');
                        }
                    },
                    retrieveUsername(){
                        if(this.email){
                            const formData = new FormData();
                            formData.append('email', this.email);
                            formData.append('action', 'retrieveUsername');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) === 1){
                                        this.showNotification('positive','Your username has been emailed to you.');
                                    }
                                    else{
                                        this.showNotification('negative','There was an error sending your username to the email address you entered. Please ensure it is entered correctly.');
                                    }
                                    this.retrieveUsernameWindow = false;
                                });
                            });
                        }
                        else{
                            this.showNotification('negative','Please enter the email address that is associated with your account.');
                        }
                    }
                }
            });
            loginModule.use(Quasar, { config: {} });
            loginModule.mount('#innertext');
        </script>
    </body>
</html>	
