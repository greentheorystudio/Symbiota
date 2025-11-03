const projectFieldModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="projectId > 0 && editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="projectId > 0">
                        <q-btn color="secondary" @click="saveProjectEdits();" label="Save Edits" :disabled="!editsExist || !projectValid" tabindex="0" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="createProject();" label="Create" :disabled="!projectValid" aria-label="Create project" tabindex="0" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Project Name" :value="projectData['projname']" maxlength="45" @update:value="(value) => updateProjectData('projname', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Managers" :value="projectData['managers']" maxlength="150" @update:value="(value) => updateProjectData('managers', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <wysiwyg-input-element label="Description" :value="projectData['fulldescription']" @update:value="(value) => updateProjectData('fulldescription', value)"></wysiwyg-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Notes" :value="projectData['notes']" maxlength="250" @update:value="(value) => updateProjectData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="canPublicPublish" class="row">
                <div class="col-grow">
                    <selector-input-element :options="accessOptions" label="Access" :value="Number(projectData['ispublic']).toString()" @update:value="(value) => updateProjectData('ispublic', value)"></selector-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement,
        'wysiwyg-input-element': wysiwygInputElement
    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const projectStore = useProjectStore();

        const accessOptions = [
            {value: '0', label: 'Private'},
            {value: '1', label: 'Public'}
        ];
        const canPublicPublish = Vue.ref(false);
        const clientRoot = baseStore.getClientRoot;
        const projectData = Vue.computed(() => projectStore.getProjectData);
        const projectId = Vue.computed(() => projectStore.getProjectID);
        const projectValid = Vue.computed(() => projectStore.getProjectValid);
        const editsExist = Vue.computed(() => projectStore.getProjectEditsExist);
        
        function createProject() {
            projectStore.createProjectRecord((newProjectId) => {
                if(newProjectId > 0){
                    window.location.href = (clientRoot + '/projects/project.php?pid=' + newProjectId);
                }
                else{
                    showNotification('negative', 'There was an error creating the project');
                }
            });
        }

        function saveProjectEdits() {
            showWorking('Saving edits...');
            projectStore.updateProjectRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the project edits.');
                }
            });
        }

        function setPublicPermission() {
            const formData = new FormData();
            formData.append('permission', 'PublicChecklist');
            formData.append('action', 'validatePermission');
            fetch(permissionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                canPublicPublish.value = resData.includes('PublicChecklist');
            });
        }

        function updateProjectData(key, value) {
            projectStore.updateProjectEditData(key, value);
        }

        Vue.onMounted(() => {
            setPublicPermission();
        });

        return {
            accessOptions,
            canPublicPublish,
            projectData,
            projectId,
            projectValid,
            editsExist,
            createProject,
            saveProjectEdits,
            updateProjectData
        }
    }
};
