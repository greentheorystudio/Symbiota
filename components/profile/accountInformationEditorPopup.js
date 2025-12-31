const accountInformationEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="editAccount();" label="Save Edits" :disabled="!editsExist || !userValid" dense tabindex="0" />
                                </div>
                            </div>
                            <account-information-form ref="accountInformationFormRef" @update:account-information="updateAccountData"></account-information-form>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'account-information-form': accountInformationForm
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const userStore = useUserStore();

        const accountInformationFormRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => userStore.getUserEditsExist);
        const userValid = Vue.computed(() => userStore.getUserValid);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            if(editsExist.value){
                userStore.revertUserEditData();
            }
            context.emit('close:popup');
        }

        function editAccount() {
            if(!accountInformationFormRef.value.formHasErrors()) {
                userStore.updateUserRecord((res) => {
                    if(Number(res) === 1){
                        showNotification('positive','The edits to your account have been saved.');
                        context.emit('account:edit');
                        context.emit('close:popup');
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

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateAccountData(data) {
            userStore.updateUserEditData(data.key, data.value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            accountInformationFormRef,
            contentRef,
            contentStyle,
            editsExist,
            userValid,
            closePopup,
            editAccount,
            updateAccountData
        }
    }
};
