const keyCharacterStateEditorPopup = {
    props: {
        characterId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        stateId: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
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
                                    <template v-if="Number(stateId) > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="Number(stateId) > 0">
                                        <q-btn color="secondary" @click="saveStateEdits();" label="Save Character State Edits" :disabled="!editsExist || !stateValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addState();" label="Add Character State" :disabled="!stateValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Character State" :value="stateData['characterstatename']" @update:value="(value) => updateStateData('characterstatename', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Description" :value="stateData['description']" @update:value="(value) => updateStateData('description', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="URL" :value="stateData['infourl']" @update:value="(value) => updateStateData('infourl', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <single-language-auto-complete label="Language" :language="stateData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="stateData['sortsequence']" min-value="1" :clearable="false" @update:value="(value) => updateStateData('sortsequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(stateId) > 0">
                                <q-card flat bordered>
                                    <q-card-section class="column q-gutter-sm">
                                        <div class="text-subtitle1 text-bold">Associate with different character</div>
                                        <div class="row justify-between q-col-gutter-sm no-wrap">
                                            <div class="col-4">
                                                <key-character-auto-complete label="Character" :value="selectedCharacterName" @update:value="processCharacterSelection"></key-character-auto-complete>
                                            </div>
                                            <div>
                                                <q-btn color="negative" @click="reassociateCharacterState();" label="Associate" :disabled="!selectedCharacterName || Number(selectedCharacterId) === Number(stateData['cid'])" aria-label="Associate" tabindex="0" />
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div v-if="Number(stateId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteState();" label="Delete Character State" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'key-character-auto-complete': keyCharacterAutoComplete,
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const keyCharacterStore = useKeyCharacterStore();
        const keyCharacterStateStore = useKeyCharacterStateStore();

        const characterId = Vue.computed(() => keyCharacterStore.getKeyCharacterID);
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => keyCharacterStateStore.getKeyCharacterStateEditsExist);
        const selectedCharacterId = Vue.ref(null);
        const selectedCharacterName = Vue.ref(null);
        const stateData = Vue.computed(() => keyCharacterStateStore.getKeyCharacterStateData);
        const stateValid = Vue.computed(() => keyCharacterStateStore.getKeyCharacterStateValid);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addState() {
            updateStateData('cid', props.characterId);
            keyCharacterStateStore.createKeyCharacterStateRecord((newStateId) => {
                if(newStateId > 0){
                    keyCharacterStateStore.setKeyCharacterStateArr(characterId.value);
                    showNotification('positive','Character state added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new character state.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteState() {
            const confirmText = 'Are you sure you want to delete this character state? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    keyCharacterStateStore.deleteKeyCharacterStateRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Character state has been deleted.');
                            context.emit('close:popup');
                            keyCharacterStateStore.setKeyCharacterStateArr(characterId.value);
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the character state.');
                        }
                    });
                }
            }});
        }

        function processCharacterSelection(charObj) {
            if(charObj){
                selectedCharacterId.value = charObj['cid'];
                selectedCharacterName.value = charObj['charactername'];
            }
            else{
                selectedCharacterId.value = null;
                selectedCharacterName.value = null;
            }
        }

        function processLanguageChange(langObj) {
            if(langObj){
                updateStateData('language', langObj['name']);
                updateStateData('langid', langObj['id']);
            }
            else{
                updateStateData('language', null);
                updateStateData('langid', null);
            }
        }

        function reassociateCharacterState() {
            updateStateData('cid', selectedCharacterId.value);
            if(keyCharacterStateStore.getKeyCharacterStateEditsExist){
                saveStateEdits();
            }
        }

        function saveStateEdits() {
            showWorking('Saving edits...');
            keyCharacterStateStore.updateKeyCharacterStateRecord((res) => {
                hideWorking();
                if(res === 1){
                    keyCharacterStateStore.setKeyCharacterStateArr(characterId.value);
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the character state edits.');
                }
                context.emit('close:popup');
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateStateData(key, value) {
            keyCharacterStateStore.updateKeyCharacterStateEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            keyCharacterStateStore.setCurrentKeyCharacterStateRecord(props.stateId);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            selectedCharacterId,
            selectedCharacterName,
            stateData,
            stateValid,
            addState,
            closePopup,
            deleteState,
            processCharacterSelection,
            processLanguageChange,
            reassociateCharacterState,
            saveStateEdits,
            updateStateData
        }
    }
};
