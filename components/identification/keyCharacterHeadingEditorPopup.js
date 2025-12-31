const keyCharacterHeadingEditorPopup = {
    props: {
        headingId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
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
                                    <template v-if="headingId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="headingId > 0">
                                        <q-btn color="secondary" @click="saveHeadingEdits();" label="Save Heading Edits" :disabled="!editsExist || !headingValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addHeading();" label="Add Heading" :disabled="!headingValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Heading" :value="headingData['headingname']" @update:value="(value) => updateHeadingData('headingname', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <single-language-auto-complete label="Language" :language="headingData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="headingData['sortsequence']" min-value="1" :clearable="false" @update:value="(value) => updateHeadingData('sortsequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(headingId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteHeading();" label="Delete Heading" :disabled="charactersExist" tabindex="0" />
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
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const keyCharacterStore = useKeyCharacterStore();
        const keyCharacterHeadingStore = useKeyCharacterHeadingStore();

        const characterData = Vue.computed(() => keyCharacterStore.getKeyCharacterArrData);
        const charactersExist = Vue.computed(() => {
            return characterData.value.hasOwnProperty(props.headingId) && characterData.value[props.headingId].length > 0;
        });
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => keyCharacterHeadingStore.getKeyCharacterHeadingEditsExist);
        const headingData = Vue.computed(() => keyCharacterHeadingStore.getKeyCharacterHeadingData);
        const headingValid = Vue.computed(() => keyCharacterHeadingStore.getKeyCharacterHeadingValid);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addHeading() {
            keyCharacterHeadingStore.createKeyCharacterHeadingRecord((newHeadingId) => {
                if(newHeadingId > 0){
                    showNotification('positive','Heading added successfully.');
                    context.emit('change:heading');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new heading.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteHeading() {
            const confirmText = 'Are you sure you want to delete this heading? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    keyCharacterHeadingStore.deleteKeyCharacterHeadingRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Heading has been deleted.');
                            context.emit('change:heading');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the heading.');
                        }
                    });
                }
            }});
        }

        function processLanguageChange(langObj) {
            if(langObj){
                updateHeadingData('language', langObj['name']);
                updateHeadingData('langid', langObj['id']);
            }
            else{
                updateHeadingData('language', null);
                updateHeadingData('langid', null);
            }
        }

        function saveHeadingEdits() {
            showWorking('Saving edits...');
            keyCharacterHeadingStore.updateKeyCharacterHeadingRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('change:heading');
                }
                else{
                    showNotification('negative', 'There was an error saving the heading edits.');
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

        function updateHeadingData(key, value) {
            keyCharacterHeadingStore.updateKeyCharacterHeadingEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            keyCharacterHeadingStore.setCurrentKeyCharacterHeadingRecord(props.headingId);
        });

        return {
            charactersExist,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            headingData,
            headingValid,
            addHeading,
            closePopup,
            deleteHeading,
            processLanguageChange,
            saveHeadingEdits,
            updateHeadingData
        }
    }
};
