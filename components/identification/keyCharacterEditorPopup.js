const keyCharacterEditorPopup = {
    props: {
        characterId: {
            type: Number,
            default: 0
        },
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
        <q-dialog v-if="!showKeyCharacterStateEditorPopup" class="z-max" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(characterId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="details" label="Details" no-caps></q-tab>
                                <q-tab name="states" label="Character States" no-caps></q-tab>
                                <q-tab name="dependence" label="Dependence" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="details">
                                    <key-character-editor-info-tab :heading-id="headingId" @change:character="emitChange" @close:popup="closePopup();"></key-character-editor-info-tab>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="states">
                                    <key-character-editor-character-states-tab @open:character-state-popup="openKeyCharacterStateEditorPopup"></key-character-editor-character-states-tab>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="dependence">
                                    <key-character-editor-dependence-tab></key-character-editor-dependence-tab>
                                </q-tab-panel>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <key-character-editor-info-tab :heading-id="headingId" @change:character="emitChange" @close:popup="closePopup();"></key-character-editor-info-tab>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showKeyCharacterStateEditorPopup">
            <key-character-state-editor-popup
                :character-id="characterId"
                :state-id="editCharacterStateId"
                :show-popup="showKeyCharacterStateEditorPopup"
                @close:popup="showKeyCharacterStateEditorPopup = false"
            ></key-character-state-editor-popup>
        </template>
    `,
    components: {
        'key-character-editor-character-states-tab': keyCharacterEditorCharacterStatesTab,
        'key-character-editor-dependence-tab': keyCharacterEditorDependenceTab,
        'key-character-editor-info-tab': keyCharacterEditorInfoTab,
        'key-character-state-editor-popup': keyCharacterStateEditorPopup,
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const keyCharacterStore = useKeyCharacterStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editCharacterStateId = Vue.ref(0);
        const showKeyCharacterStateEditorPopup = Vue.ref(false);
        const tab = Vue.ref('details');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function emitChange() {
            context.emit('change:character');
        }

        function openKeyCharacterStateEditorPopup(stateid) {
            editCharacterStateId.value = stateid;
            showKeyCharacterStateEditorPopup.value = true;
        }

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            keyCharacterStore.setCurrentKeyCharacterRecord(props.headingId, props.characterId);
        });

        return {
            contentRef,
            contentStyle,
            editCharacterStateId,
            showKeyCharacterStateEditorPopup,
            tab,
            tabStyle,
            closePopup,
            emitChange,
            openKeyCharacterStateEditorPopup
        }
    }
};
