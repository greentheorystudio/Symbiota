const checklistEditorAdminTab = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <q-card flat bordered>
                <q-card-section>
                    <user-permission-management-module permission-label="Manager" :permission="ClAdmin" :table-pk="checklistId" @update:user-list="(value) => managerUserArr = value"></user-permission-management-module>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-h6 text-bold">Delete checklist</div>
                    <div v-if="!deleteValid">
                        All managers (except yourself) must be removed before a checklist can be deleted
                    </div>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="negative" @click="deleteChecklist();" label="Delete" :disabled="!deleteValid" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'user-permission-management-module': userPermissionManagementModule
    },
    setup() {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();

        const checklistId = Vue.computed(() => checklistStore.getChecklistID);
        const clientRoot = baseStore.getClientRoot;
        const confirmationPopupRef = Vue.ref(null);
        const deleteValid = Vue.computed(() => {
            let returnVal = false;
            if((managerUserArr.value.length === 1 && Number(managerUserArr.value[0]['uid']) === Number(symbUid)) || managerUserArr.value.length === 0){
                returnVal = true;
            }
            return returnVal;
        });
        const managerUserArr = Vue.ref([]);
        const symbUid = baseStore.getSymbUid;

        function deleteChecklist() {
            const confirmText = 'Are you sure you want to delete this checklist? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    checklistStore.deleteChecklistRecord((res) => {
                        if(res === 1){
                            window.location.href = (clientRoot + '/checklists/index.php');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the checklist');
                        }
                    });
                }
            }});
        }

        return {
            checklistId,
            confirmationPopupRef,
            deleteValid,
            managerUserArr,
            deleteChecklist
        }
    }
};
