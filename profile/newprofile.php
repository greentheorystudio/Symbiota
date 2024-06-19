<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> - New User Profile</title>
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .human-validator-canvas {
                border: 1px solid #000;
                height: 50px;
                width: 400px;
            }
            .create-account-container {
                width: 90%;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="innertext">
            <h1>Create New Account</h1>
            <div class="row justify-center q-mt-md">
                <q-card class="create-account-container">
                    <q-card-section>
                        <div class="text-h6 q-mb-md">Login Credentials</div>
                        <div class="row justify-start q-gutter-md">
                            <q-input ref="usernameRef" outlined bottom-slots v-model="newAccount.username" label="Username" bg-color="white" class="col-4" dense lazy-rules :rules="usernameRules">
                                <template v-slot:hint>
                                    Required
                                </template>
                            </q-input>
                        </div>
                        <div class="row justify-start q-gutter-md q-mt-xs">
                            <password-input ref="passwordInputRef" :password="newAccount.pwd" @update:password="updatePassword"></password-input>
                        </div>
                    </q-card-section>
                </q-card>
                <q-card class="create-account-container q-mt-md">
                    <q-card-section>
                        <div class="text-h6 q-mb-md">Account Details</div>
                        <account-information-form ref="accountInformationFormRef" :user="newAccount" @update:account-information="updateAccountObj"></account-information-form>
                    </q-card-section>
                </q-card>
                <q-card class="create-account-container q-mt-md">
                    <q-card-section>
                        <human-validator ref="humanValidationInputRef"></human-validator>
                        <div class="row justify-end q-mt-md">
                            <q-btn color="secondary" @click="createAccount();" label="Create Account" dense />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../footer.php');
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/pwdInput.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationForm.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/humanValidator.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const createAccountModule = Vue.createApp({
                components: {
                    'account-information-form': accountInformationForm,
                    'human-validator': humanValidator,
                    'password-input': passwordInput
                },
                setup() {
                    const { showNotification } = useCore();
                    const store = useBaseStore();
                    const accountInformationFormRef = Vue.ref(null);
                    const adminEmail = store.getAdminEmail;
                    const clientRoot = store.getClientRoot;
                    const humanValidationInputRef = Vue.ref(null);
                    const newAccount = Vue.ref({
                        uid: null,
                        firstname: null,
                        middleinitial: null,
                        lastname: null,
                        title: null,
                        institution: null,
                        department: null,
                        address: null,
                        city: null,
                        state: null,
                        zip: null,
                        country: null,
                        email: null,
                        url: null,
                        biography: null,
                        username: null,
                        pwd: null
                    });
                    const passwordInputRef = Vue.ref(null);
                    const usernameExists = (val) => {
                        return new Promise((resolve) => {
                            const formData = new FormData();
                            formData.append('username', val);
                            formData.append('action', 'getUserFromUsername');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.json().then((resObj) => {
                                    resolve((!resObj.hasOwnProperty('uid') || Number(resObj['uid']) === 0) || 'Username is already associated with another account');
                                });
                            });
                        });
                    };
                    const usernameRef = Vue.ref(null);
                    const usernameRegex = /^[0-9A-Za-z_!@#$\s.+\-]+$/;

                    function createAccount() {
                        usernameRef.value.validate();
                        passwordInputRef.value.validateForm();
                        accountInformationFormRef.value.validateForm();
                        humanValidationInputRef.value.validateForm();
                        if(
                            !usernameRef.value.hasError &&
                            !passwordInputRef.value.formHasErrors() &&
                            !accountInformationFormRef.value.formHasErrors() &&
                            !humanValidationInputRef.value.formHasErrors()
                        ) {
                            const formData = new FormData();
                            formData.append('user', JSON.stringify(newAccount.value));
                            formData.append('action', 'createAccount');
                            fetch(profileApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(Number(res) > 0){
                                        window.location.href = clientRoot + '/profile/viewprofile.php';
                                    }
                                    else{
                                        let errorText = 'An error occurred creating the account. ';
                                        if(adminEmail !== ''){
                                            errorText += 'Please contact system administrator at ' + adminEmail + ' for assistance.';
                                        }
                                        showNotification('negative',errorText);
                                    }
                                });
                            });
                        }
                        else{
                            showNotification('negative','Please correct the errors noted in red to create a new account.');
                        }
                    }

                    function updatePassword(val) {
                        newAccount.value.pwd = val;
                    }

                    function updateAccountObj(obj) {
                        newAccount.value = Object.assign({}, obj);
                    }
                    
                    return {
                        accountInformationFormRef,
                        humanValidationInputRef,
                        newAccount,
                        passwordInputRef,
                        usernameRef,
                        usernameRules: [
                            val => (val !== null && val !== '') || 'Required',
                            val => usernameRegex.test(val) || 'Please enter a valid username',
                            val => usernameExists(val)
                        ],
                        createAccount,
                        updatePassword,
                        updateAccountObj
                    }
                }
            });
            createAccountModule.use(Quasar, { config: {} });
            createAccountModule.use(Pinia.createPinia());
            createAccountModule.mount('#innertext');
        </script>
    </body>
</html>
