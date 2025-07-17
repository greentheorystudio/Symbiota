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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> New Account</title>
        <meta name="description" content="Create a new account for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer" class="q-pa-md">
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
                            <password-input ref="passwordInputRef" :password="newAccount.password" @update:password="(value) => updateAccountData({key: 'password', value: value})"></password-input>
                        </div>
                    </q-card-section>
                </q-card>
                <q-card class="create-account-container q-mt-md">
                    <q-card-section>
                        <div class="text-h6 q-mb-md">Account Details</div>
                        <account-information-form ref="accountInformationFormRef" @update:account-information="updateAccountData"></account-information-form>
                    </q-card-section>
                </q-card>
                <q-card class="create-account-container q-mt-md" :class="(termsAgreeError === true ? 'error-border' : '')">
                    <q-card-section class="q-pa-lg column q-gutter-sm">
                        <div class="row justify-start q-gutter-md no-wrap">
                            <div>
                                <q-checkbox v-model="agreeCheck" dense @update:model-value="processTermsAgreeChange"></q-checkbox>
                            </div>
                            <div>
                                I have read and agree to the terms and policies described in the <a :href="(clientRoot + '/misc/usagepolicy.php')" class="text-bold" target="_blank">Terms of Use, Policies, and Guidelines page</a>
                            </div>
                        </div>
                        <div v-if="termsAgreeError" class="text-negative text-bold">
                            Required
                        </div>
                    </q-card-section>
                </q-card>
                <q-card class="create-account-container q-mt-md">
                    <q-card-section>
                        <human-validator ref="humanValidationInputRef"></human-validator>
                        <div class="row justify-end q-mt-md">
                            <q-btn color="primary" @click="createAccount();" label="Create Account" dense />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include_once(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/collection.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/user.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
                    const baseStore = useBaseStore();
                    const userStore = useUserStore();

                    const accountInformationFormRef = Vue.ref(null);
                    const adminEmail = baseStore.getAdminEmail;
                    const agreeCheck = Vue.ref(false);
                    const clientRoot = baseStore.getClientRoot;
                    const humanValidationInputRef = Vue.ref(null);
                    const newAccount = Vue.computed(() => userStore.getUserData);
                    const passwordInputRef = Vue.ref(null);
                    const termsAgreeError = Vue.ref(null);
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
                                return response.ok ? response.json() : null;
                            })
                            .then((resObj) => {
                                resolve((resObj && (!resObj.hasOwnProperty('uid') || Number(resObj['uid']) === 0)) || 'Username is already associated with another account');
                            });
                        });
                    };
                    const usernameRef = Vue.ref(null);
                    const usernameRegex = /^[0-9A-Za-z_!@#$\s.+\-]+$/;

                    function createAccount() {
                        usernameRef.value.validate();
                        validateTermsAgree();
                        passwordInputRef.value.validateForm();
                        accountInformationFormRef.value.validateForm();
                        humanValidationInputRef.value.validateForm();
                        if(
                            !usernameRef.value.hasError &&
                            !termsAgreeError.value &&
                            !passwordInputRef.value.formHasErrors() &&
                            !accountInformationFormRef.value.formHasErrors() &&
                            !humanValidationInputRef.value.formHasErrors()
                        ) {
                            userStore.createUserRecord((res) => {
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
                        }
                        else{
                            showNotification('negative','Please correct the errors noted in red to create a new account.');
                        }
                    }

                    function processTermsAgreeChange(val) {
                        agreeCheck.value = val;
                        validateTermsAgree();
                    }

                    function updateAccountData(data) {
                        userStore.updateUserEditData(data.key, data.value);
                    }

                    function validateTermsAgree() {
                        termsAgreeError.value = agreeCheck.value === false;
                    }

                    Vue.onMounted(() => {
                        userStore.setUser(0);
                    });
                    
                    return {
                        accountInformationFormRef,
                        agreeCheck,
                        clientRoot,
                        humanValidationInputRef,
                        newAccount,
                        passwordInputRef,
                        termsAgreeError,
                        usernameRef,
                        usernameRules: [
                            val => (val !== null && val !== '') || 'Required',
                            val => usernameRegex.test(val) || 'Please enter a valid username',
                            val => usernameExists(val)
                        ],
                        createAccount,
                        processTermsAgreeChange,
                        updateAccountData
                    }
                }
            });
            createAccountModule.use(Quasar, { config: {} });
            createAccountModule.use(Pinia.createPinia());
            createAccountModule.mount('#mainContainer');
        </script>
    </body>
</html>
