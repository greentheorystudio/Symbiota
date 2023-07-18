const viewProfileAccountModule = {
    props: {
        accountInfo: {
            type: Object,
            default: null
        },
        checklistArr: {
            type: Array,
            default: []
        },
        projectArr: {
            type: Array,
            default: []
        },
        uid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="row justify-center q-mt-md">
            <template v-if="accountInfo.validated !== 1">
                <q-card class="create-account-container">
                    <q-card-section>
                        <div class="row justify-between">
                            <div class="row col-8 text-red text-bold">
                                You have one more step to confirm your account. A confirmation email was sent to the email address for your account.
                                Please follow the instructions in that email to confirm your account.
                            </div>
                            <div class="row col-3 self-center justify-end">
                                <q-btn color="secondary" @click="resendConfirmationEmail();" label="Resend Confirmation Email" dense />
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </template>
            <q-card class="create-account-container q-mt-md">
                <q-card-section>
                    <div class="text-h6 q-mb-md">Edit Account Information</div>
                    <div class="row justify-start q-gutter-md">
                        <q-input outlined v-model="accountInfo.username" label="Username" bg-color="white" class="col-4" dense disable></q-input>
                    </div>
                    <account-information-form ref="accountInformationFormRef" :user="accountInfo" @update:account-information="updateAccountObj"></account-information-form>
                    <div class="row justify-end q-gutter-md q-mt-xs">
                        <q-btn color="secondary" @click="editeAccount();" label="Save Edits" dense />
                    </div>
                </q-card-section>
            </q-card>
            <q-card class="create-account-container q-mt-md">
                <q-card-section>
                    <div class="text-h6 q-mb-md">Change Password</div>
                    <div class="row justify-between">
                        <div class="row justify-start q-gutter-md col-8">
                            <password-input ref="passwordInputRef" :password="newPassword" @update:password="updatePassword"></password-input>
                        </div>
                        <div class="row col-3 self-center justify-end">
                            <q-btn color="secondary" @click="changePassword();" label="Change Password" dense />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <template v-if="accessTokenCnt > 0">
                <q-card class="create-account-container q-mt-md">
                    <q-card-section>
                        <div class="text-h6 q-mb-md">Manage Access Tokens</div>
                        <div class="row justify-between">
                            <div class="row inline justify-start q-gutter-md col-8">
                                <div>
                                    You currently have <span class="text-bold">{{ accessTokenCnt }}</span> access tokens linked to your account.
                                    Tokens are created when you select "Remember Me" when logging in, or accessing the portal
                                    from an external app. If the number of access tokens you have seems high, or if you would like to log out on all devices,
                                    please click on the Clear Tokens button to clear the access tokens linked to your account.
                                </div>
                            </div>
                            <div class="row col-3 self-center justify-end">
                                <q-btn color="secondary" @click="clearAccessTokens();" label="Clear Tokens" dense />
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </template>
            <q-card class="create-account-container q-mt-md">
                <q-card-section>
                    <template v-if="checklistArr.length > 0 || projectArr.length > 0">
                        <div class="row justify-center q-mb-md">
                            <div class="row col-8 text-center text-red text-bold">
                                Your account cannot be deleted until all checklists and projects associated with the account are removed.
                            </div>
                        </div>
                    </template>
                    <div class="row justify-center">
                        <q-btn color="red" @click="deleteConfirmation = true" label="Delete Account" :disable="checklistArr.length > 0 || projectArr.length > 0" dense />
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <q-dialog v-model="deleteConfirmation" persistent>
            <q-card>
                <q-card-section class="row items-center">
                    <span class="q-ml-sm">You are about to delete your account. This action cannot be undone. Are you certain that you want to continue?</span>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Yes, Delete Account" color="primary" @click="deleteAccount();" v-close-popup></q-btn>
                    <q-btn flat label="No, Cancel" color="primary" v-close-popup></q-btn>
                </q-card-actions>
            </q-card>
        </q-dialog>
    `,
    data() {
        return {
            deleteConfirmation: Vue.ref(false),
            newPassword: Vue.ref(null)
        }
    },
    components: {
        'account-information-form': accountInformationForm,
        'password-input': passwordInput
    },
    setup () {
        const $q = useQuasar();
        const passwordInputRef = Vue.ref(null);
        const accountInformationFormRef = Vue.ref(null);
        return {
            passwordInputRef,
            accountInformationFormRef,
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
        if(Number(this.uid) > 0){
            this.setAccessTokenCnt();
        }
    },
    methods: {
        changePassword(){
            this.$refs.passwordInputRef.validateForm();
            if(!this.$refs.passwordInputRef.formHasErrors()) {
                const formData = new FormData();
                formData.append('uid', this.uid);
                formData.append('pwd', this.newPassword);
                formData.append('action', 'changePassword');
                fetch(profileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) === 1){
                            this.showNotification('positive','Your password has been changed.');
                        }
                        else{
                            this.showNotification('negative','An error occurred changing your password.');
                        }
                    });
                });
            }
            else{
                this.showNotification('negative','Please correct the errors noted in red to change your password.');
            }
        },
        clearAccessTokens(){
            const formData = new FormData();
            formData.append('uid', this.uid);
            formData.append('action', 'clearAccessTokens');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(Number(res) === 1){
                        this.showNotification('positive','Your access tokens have been cleared and you have been logged out of all devices.');
                    }
                    else{
                        this.showNotification('negative','An error occurred clearing your access tokens.');
                    }
                });
            });
        },
        deleteAccount(){
            const formData = new FormData();
            formData.append('uid', this.uid);
            formData.append('action', 'deleteAccount');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(Number(res) === 1){
                        window.location.href = CLIENT_ROOT + '/index.php';
                    }
                    else{
                        this.showNotification('negative','An error occurred deleting your account.');
                    }
                });
            });
        },
        editeAccount(){
            if(!this.$refs.accountInformationFormRef.formHasErrors()) {
                const formData = new FormData();
                formData.append('user', JSON.stringify(this.accountInfo));
                formData.append('action', 'editAccount');
                fetch(profileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) === 1){
                            this.showNotification('positive','The edits to your account have been saved.');
                        }
                        else{
                            this.showNotification('negative','An error occurred saving the edits to your account.');
                        }
                    });
                });
            }
            else{
                this.showNotification('negative','Please correct the errors noted in red to save the edits to your account.');
            }
        },
        resendConfirmationEmail(){
            const formData = new FormData();
            formData.append('uid', this.uid);
            formData.append('action', 'sendConfirmationEmail');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(Number(res) === 1){
                        this.showNotification('positive','Your confirmation email has been sent.');
                    }
                    else{
                        this.showNotification('negative','There was an error sending your confirmation email.');
                    }
                });
            });
        },
        setAccessTokenCnt(){
            const formData = new FormData();
            formData.append('uid', this.uid);
            formData.append('action', 'getAccessTokenCnt');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
                .then((response) => {
                    response.text().then((res) => {
                        this.accessTokenCnt = Number(res);
                    });
                });
        },
        updatePassword(val) {
            this.newPassword = val;
        },
        updateAccountObj(obj) {
            this.$emit('update:account-information', obj);
        }
    }
};
