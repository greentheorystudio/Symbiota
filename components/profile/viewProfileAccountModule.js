const viewProfileAccountModule = {
    template: `
        <div class="row justify-center q-mt-md">
            <template v-if="Number(accountInfo.validated) !== 1">
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
                    <account-information-form ref="accountInformationFormRef" @update:account-information="updateAccountData"></account-information-form>
                    <div class="row justify-between q-mt-md">
                        <div>
                            <template v-if="editsExist">
                                <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                            </template>
                        </div>
                        <div class="row justify-end">
                            <q-btn color="secondary" @click="editAccount();" label="Save Edits" :disabled="!editsExist || !userValid" dense />
                        </div>
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
    components: {
        'account-information-form': accountInformationForm,
        'password-input': passwordInput
    },
    setup(props) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const userStore = useUserStore();
        
        const accessTokenCnt = Vue.computed(() => userStore.getTokenCnt);
        const accountInfo = Vue.computed(() => userStore.getUserData);
        const accountInformationFormRef = Vue.ref(null);
        const checklistArr = Vue.computed(() => userStore.getChecklistArr);
        const clientRoot = baseStore.getClientRoot;
        const deleteConfirmation = Vue.ref(false);
        const editsExist = Vue.computed(() => userStore.getUserEditsExist);
        const newPassword = Vue.ref(null);
        const passwordInputRef = Vue.ref(null);
        const projectArr = Vue.computed(() => userStore.getProjectArr);
        const uid = Vue.computed(() => userStore.getUserID);
        const userValid = Vue.computed(() => userStore.getUserValid);

        function changePassword() {
            passwordInputRef.value.validateForm();
            if(!passwordInputRef.value.formHasErrors()) {
                userStore.updateUserPassword(newPassword.value, (res) => {
                    if(Number(res) === 1){
                        showNotification('positive','Your password has been changed.');
                    }
                    else{
                        showNotification('negative','An error occurred changing your password.');
                    }
                });
            }
            else{
                showNotification('negative','Please correct the errors noted in red to change your password.');
            }
        }

        function clearAccessTokens() {
            userStore.clearUserAccessTokens((res) => {
                if(Number(res) === 1){
                    showNotification('positive','Your access tokens have been cleared and you have been logged out of all devices.');
                }
                else{
                    showNotification('negative','An error occurred clearing your access tokens.');
                }
            });
        }

        function deleteAccount() {
            userStore.deleteUserRecord(uid.value, (res) => {
                if(Number(res) === 1){
                    window.location.href = clientRoot + '/index.php';
                }
                else{
                    showNotification('negative','An error occurred deleting your account.');
                }
            });
        }

        function editAccount() {
            if(!accountInformationFormRef.value.formHasErrors()) {
                userStore.updateUserRecord((res) => {
                    if(Number(res) === 1){
                        showNotification('positive','The edits to your account have been saved.');
                    }
                    else{
                        showNotification('negative','An error occurred saving the edits to your account.');
                    }
                });
            }
            else{
                showNotification('negative','Please correct the errors noted in red to save the edits to your account.');
            }
        }

        function resendConfirmationEmail() {
            userStore.resendConfirmationEmail((res) => {
                if(Number(res) === 1){
                    showNotification('positive','Your confirmation email has been sent.');
                }
                else{
                    showNotification('negative','There was an error sending your confirmation email.');
                }
            });
        }

        function updateAccountData(data) {
            userStore.updateUserEditData(data.key, data.value);
        }

        function updatePassword(val) {
            newPassword.value = val;
        }

        return {
            accessTokenCnt,
            accountInfo,
            accountInformationFormRef,
            checklistArr,
            deleteConfirmation,
            editsExist,
            newPassword,
            passwordInputRef,
            projectArr,
            uid,
            userValid,
            changePassword,
            clearAccessTokens,
            deleteAccount,
            editAccount,
            resendConfirmationEmail,
            updateAccountData,
            updatePassword
        }
    }
};
