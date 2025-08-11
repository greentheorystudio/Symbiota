const projectEditorAdminTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <q-card flat bordered>
                <q-card-section>
                    <user-permission-management-module permission-label="Manager" permission="ProjAdmin" :table-pk="projectId" @update:user-list="(value) => managerUserArr = value"></user-permission-management-module>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-h6 text-bold">Delete project</div>
                    <div v-if="!deleteValid">
                        All managers (except yourself) must be removed before a project can be deleted
                    </div>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="negative" @click="deleteProject();" label="Delete" :disabled="!deleteValid" />
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
        const projectStore = useProjectStore();

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
        const projectId = Vue.computed(() => projectStore.getProjectID);
        const symbUid = baseStore.getSymbUid;

        function deleteProject() {
            const confirmText = 'Are you sure you want to delete this project? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    projectStore.deleteProjectRecord((res) => {
                        if(res === 1){
                            window.location.href = (clientRoot + '/projects/index.php');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the project');
                        }
                    });
                }
            }});
        }

        return {
            confirmationPopupRef,
            deleteValid,
            managerUserArr,
            projectId,
            deleteProject
        }
    }
};
