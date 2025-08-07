const checklistFieldModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="checklistId > 0 && editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="checklistId > 0">
                        <q-btn color="secondary" @click="saveChecklistEdits();" label="Save Edits" :disabled="!editsExist || !checklistValid" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="createChecklist();" label="Create" :disabled="!checklistValid" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Checklist Name" :value="checklistData['name']" maxlength="100" @update:value="(value) => updateChecklistData('name', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Authors" :value="checklistData['authors']" maxlength="250" @update:value="(value) => updateChecklistData('authors', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Locality" :value="checklistData['locality']" maxlength="500" @update:value="(value) => updateChecklistData('locality', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Publication" :value="checklistData['publication']" maxlength="500" @update:value="(value) => updateChecklistData('publication', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <wysiwyg-input-element label="Abstract" :value="checklistData['abstract']" @update:value="(value) => updateChecklistData('abstract', value)"></wysiwyg-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Notes" :value="checklistData['notes']" maxlength="500" @update:value="(value) => updateChecklistData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <selector-input-element :options="checklistOptions" label="Parent Checklist" :value="checklistData['parentclid']" option-value="clid" option-label="name" :clearable="true" @update:value="(value) => updateChecklistData('parentclid', value)"></selector-input-element>
                </div>
            </div>
            <div class="row justify-start q-gutter-sm no-wrap">
                <div class="self-center">
                    <div class="text-body1 text-bold">
                        Centroid
                    </div>
                </div>
                <div class="col-3">
                    <text-field-input-element data-type="number" label="Latitude" :value="checklistData['latcentroid']" @update:value="(value) => updateChecklistData('latcentroid', value)"></text-field-input-element>
                </div>
                <div class="col-3">
                    <text-field-input-element data-type="number" label="Longitude" :value="checklistData['longcentroid']" @update:value="(value) => updateChecklistData('longcentroid', value)"></text-field-input-element>
                </div>
                <div class="col-1 self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-point');" icon="fas fa-globe" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Mapping Aid
                        </q-tooltip>
                    </q-btn>
                </div>
            </div>
            <div class="row">
                <div class="col-grow row q-gutter-sm no-wrap">
                    <occurrence-footprint-wkt-input-element label="Footprint Polygon" :value="checklistData['footprintwkt']" @open:spatial-popup="openSpatialPopup" @update:value="(value) => updateChecklistData('footprintwkt', value)"></occurrence-footprint-wkt-input-element>
                </div>
            </div>
            <q-card flat bordered>
                <q-card-section class="column">
                    <div class="text-h6 text-bold">Default Display Settings</div>
                    <div>
                        <checkbox-input-element label="More Details" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('ddetails')) ? checklistData['defaultsettings']['ddetails'] : null" @update:value="(value) => updateDefaultSettingsData('ddetails', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Sort Taxa by Scientific Name" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('dalpha')) ? checklistData['defaultsettings']['dalpha'] : null" @update:value="(value) => updateDefaultSettingsData('dalpha', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Synonyms" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('showsynonyms')) ? checklistData['defaultsettings']['showsynonyms'] : null" @update:value="(value) => updateDefaultSettingsData('showsynonyms', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Common Names" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('dcommon')) ? checklistData['defaultsettings']['dcommon'] : null" @update:value="(value) => updateDefaultSettingsData('dcommon', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Images" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('dimages')) ? checklistData['defaultsettings']['dimages'] : null" @update:value="(value) => updateDefaultSettingsData('dimages', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Notes & Vouchers" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('dvouchers')) ? checklistData['defaultsettings']['dvouchers'] : null" @update:value="(value) => updateDefaultSettingsData('dvouchers', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Taxon Authors" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('dauthors')) ? checklistData['defaultsettings']['dauthors'] : null" @update:value="(value) => updateDefaultSettingsData('dauthors', value)"></checkbox-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Activate Identification Key" :value="(checklistData['defaultsettings'] && checklistData['defaultsettings'].hasOwnProperty('keyactive')) ? checklistData['defaultsettings']['keyactive'] : null" @update:value="(value) => updateDefaultSettingsData('keyactive', value)"></checkbox-input-element>
                    </div>
                </q-card-section>
            </q-card>
            <div v-if="canPublicPublish" class="row">
                <div class="col-grow">
                    <selector-input-element :options="accessOptions" label="Access" :value="checklistData['access']" @update:value="(value) => updateChecklistData('access', value)"></selector-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'occurrence-footprint-wkt-input-element': occurrenceFootprintWktInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement,
        'wysiwyg-input-element': wysiwygInputElement
    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();

        const accessOptions = [
            {value: 'private', label: 'Private'},
            {value: 'public', label: 'Public'}
        ];
        const canPublicPublish = Vue.ref(false);
        const checklistArr = Vue.ref([]);
        const checklistData = Vue.computed(() => checklistStore.getChecklistData);
        const checklistId = Vue.computed(() => checklistStore.getChecklistID);
        const checklistOptions = Vue.computed(() => {
            const returnArr = [];
            checklistArr.value.forEach((checklist) => {
                if(Number(checklist['collid']) !== checklistId.value){
                    returnArr.push(checklist);
                }
            });
            return returnArr;
        });
        const checklistValid = Vue.computed(() => checklistStore.getChecklistValid);
        const clientRoot = baseStore.getClientRoot;
        const editsExist = Vue.computed(() => checklistStore.getChecklistEditsExist);
        
        function createChecklist() {
            checklistStore.createChecklistRecord((newChecklistId) => {
                if(newChecklistId > 0){
                    window.location.href = (clientRoot + '/checklists/checklist.php?clid=' + newChecklistId);
                }
                else{
                    showNotification('negative', 'There was an error creating the checklist');
                }
            });
        }

        function openSpatialPopup(type) {
            context.emit('open:spatial-popup', type);
        }

        function saveChecklistEdits() {
            showWorking('Saving edits...');
            checklistStore.updateChecklistRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the checklist edits.');
                }
                context.emit('close:popup');
            });
        }

        function setChecklistArr() {
            const formData = new FormData();
            formData.append('action', 'getChecklistArr');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                if(resData && resData.length > 0){
                    checklistArr.value = resData;
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

        function updateChecklistData(key, value) {
            checklistStore.updateChecklistEditData(key, value);
        }

        function updateDefaultSettingsData(key, value) {
            checklistStore.updateChecklistEditDefaultSettingsData(key, value);
        }

        Vue.onMounted(() => {
            setChecklistArr();
            setPublicPermission();
        });

        return {
            accessOptions,
            canPublicPublish,
            checklistData,
            checklistId,
            checklistOptions,
            checklistValid,
            editsExist,
            createChecklist,
            openSpatialPopup,
            saveChecklistEdits,
            updateChecklistData,
            updateDefaultSettingsData
        }
    }
};
