const keyCharacterEditorInfoTab = {
    props: {
        headingId: {
            type: Number,
            default: 0
        }
    },
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="Number(characterId) > 0 && editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="Number(characterId) > 0">
                        <q-btn color="secondary" @click="saveCharacterEdits();" label="Save Character Edits" :disabled="!editsExist || !characterValid" tabindex="0" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="addCharacter();" label="Add Character State" :disabled="!characterValid" tabindex="0" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Character" :value="characterData['charactername']" @update:value="(value) => updateCharacterData('charactername', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Description" :value="characterData['description']" @update:value="(value) => updateCharacterData('description', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="URL" :value="characterData['infourl']" @update:value="(value) => updateCharacterData('infourl', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <single-language-auto-complete label="Language" :language="characterData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="int" label="Sort Sequence" :value="characterData['sortsequence']" min-value="1" :clearable="false" @update:value="(value) => updateCharacterData('sortsequence', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="Number(characterId) > 0">
                <q-card flat bordered>
                    <q-card-section class="column q-gutter-sm">
                        <div class="text-subtitle1 text-bold">Associate with different heading</div>
                        <div class="row justify-between q-col-gutter-sm no-wrap">
                            <div class="col-4">
                                <key-character-heading-auto-complete label="Heading" :value="selectedHeadingName" @update:value="processHeadingSelection"></key-character-heading-auto-complete>
                            </div>
                            <div>
                                <q-btn color="negative" @click="reassociateCharacter();" label="Associate" :disabled="!selectedHeadingName || Number(selectedHeadingId) === Number(characterData['chid'])" aria-label="Associate" tabindex="0" />
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div v-if="Number(characterId) > 0" class="row justify-end q-gutter-md">
                <div>
                    <q-btn color="negative" @click="deleteCharacter();" label="Delete Character" :disabled="characterStateArr.length > 0" tabindex="0" />
                </div>
            </div>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'key-character-heading-auto-complete': keyCharacterHeadingAutoComplete,
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const keyCharacterStore = useKeyCharacterStore();
        const keyCharacterStateStore = useKeyCharacterStateStore();

        const characterData = Vue.computed(() => keyCharacterStore.getKeyCharacterData);
        const characterId = Vue.computed(() => keyCharacterStore.getKeyCharacterID);
        const characterStateArr = Vue.computed(() => keyCharacterStateStore.getKeyCharacterStateArr);
        const characterValid = Vue.computed(() => keyCharacterStore.getKeyCharacterValid);
        const confirmationPopupRef = Vue.ref(null);
        const editsExist = Vue.computed(() => keyCharacterStore.getKeyCharacterEditsExist);
        const selectedHeadingId = Vue.ref(null);
        const selectedHeadingName = Vue.ref(null);
        
        function addCharacter() {
            updateCharacterData('chid', props.headingId);
            keyCharacterStore.createKeyCharacterRecord((newCharId) => {
                if(newCharId > 0){
                    keyCharacterStore.setCurrentKeyCharacterRecord(props.headingId, newCharId);
                    showNotification('positive','Character added successfully.');
                    context.emit('change:character');
                }
                else{
                    showNotification('negative', 'There was an error adding the new character.');
                }
            });
        }

        function deleteCharacter() {
            const confirmText = 'Are you sure you want to delete this character? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    keyCharacterStore.deleteKeyCharacterRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Character has been deleted.');
                            context.emit('change:character');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the character.');
                        }
                    });
                }
            }});
        }

        function processHeadingSelection(headingObj) {
            if(headingObj){
                selectedHeadingId.value = headingObj['chid'];
                selectedHeadingName.value = headingObj['headingname'];
            }
            else{
                selectedHeadingId.value = null;
                selectedHeadingName.value = null;
            }
        }

        function processLanguageChange(langObj) {
            if(langObj){
                updateCharacterData('language', langObj['name']);
                updateCharacterData('langid', langObj['id']);
            }
            else{
                updateCharacterData('language', null);
                updateCharacterData('langid', null);
            }
        }

        function reassociateCharacter() {
            updateCharacterData('chid', selectedHeadingId.value);
            if(keyCharacterStore.getKeyCharacterEditsExist){
                saveCharacterEdits();
            }
        }

        function saveCharacterEdits() {
            showWorking('Saving edits...');
            keyCharacterStore.updateKeyCharacterRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('change:character');
                }
                else{
                    showNotification('negative', 'There was an error saving the character edits.');
                }
            });
        }

        function updateCharacterData(key, value) {
            keyCharacterStore.updateKeyCharacterEditData(key, value);
        }

        return {
            characterData,
            characterId,
            characterStateArr,
            characterValid,
            confirmationPopupRef,
            editsExist,
            selectedHeadingId,
            selectedHeadingName,
            addCharacter,
            deleteCharacter,
            processHeadingSelection,
            processLanguageChange,
            reassociateCharacter,
            saveCharacterEdits,
            updateCharacterData
        }
    }
};
