const glossaryDownloadOptionsPopup = {
    props: {
        selectedLanguage: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="text-h6 text-bold">Download Glossary</div>
                    <template v-if="glossaryLanguageArr.length > 1">
                        <div v-if="!selectedLanguage" class="q-pa-md text-bold text-red">
                            Please close this window and select a language in the Language drop-down in order to proceed with the download.
                        </div>
                        <div class="row">
                            <div class="col-grow">
                                <selector-input-element label="Download Format" :options="downloadFormatOptions" :value="selectedDownloadFormat" @update:value="(value) => selectedDownloadFormat = value"></selector-input-element>
                            </div>
                        </div>
                        <template v-if="selectedDownloadFormat === 'singlelanguage'">
                            <div class="row">
                                <div class="col-grow q-pl-md">
                                    <checkbox-input-element label="Include Images" :value="includeImages" @update:value="(value) => includeImages = value"></checkbox-input-element>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="row">
                                <div class="col-5">
                                    <div class="text-body1 text-bold">Translations</div>
                                </div>
                                <div class="col-7 column">
                                    <template v-for="language in glossaryLanguageArr">
                                        <checkbox-input-element v-if="language !== selectedLanguage" :label="language" :value="selectedLanguages.includes(language)" @update:value="(value) => processTranslationLanguageChange(language, value)"></checkbox-input-element>
                                    </template>
                                </div>
                            </div>
                            <div class="q-mt-sm row justify-start">
                                <div>
                                    <q-option-group v-model="selectedDefinitionOption" :options="definitionOptions" color="primary" dense aria-label="Definition options" tabindex="0"></q-option-group>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="row">
                            <div class="col-grow q-pl-md">
                                <checkbox-input-element label="Include Images" :value="includeImages" @update:value="(value) => includeImages = value"></checkbox-input-element>
                            </div>
                        </div>
                    </template>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="primary" size="md" @click="downloadData();" label="Download" dense :disabled="!primaryLanguage" tabindex="0" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement
    },
    setup(props, context) {
        const baseStore = useBaseStore();
        const glossaryStore = useGlossaryStore();

        const clientRoot = baseStore.getClientRoot;
        const definitionOptions = Vue.ref([
            {value: 'nodef', label: 'Without Definitions'},
            {value: 'onedef', label: 'Primary Definition Only'},
            {value: 'alldef', label: 'All Definitions'}
        ]);
        const downloadFormatOptions = Vue.ref([
            {value: 'singlelanguage', label: 'Single Language'},
            {value: 'translation', label: 'Translation Table'}
        ]);
        const glossaryLanguageArr = Vue.computed(() => glossaryStore.getGlossaryLanguageArr);
        const includeImages = Vue.ref(false);
        const primaryLanguage = Vue.computed(() => {
            let returnVal = null;
            if(glossaryLanguageArr.value.length > 1){
                returnVal = props.selectedLanguage;
            }
            else if(glossaryLanguageArr.value.length > 0){
                returnVal = glossaryLanguageArr.value[0];
            }
            return returnVal;
        });
        const selectedDefinitionOption = Vue.ref('nodef');
        const selectedDownloadFormat = Vue.ref('singlelanguage');
        const selectedLanguages = Vue.ref([]);

        function closePopup() {
            context.emit('close:popup');
        }

        function downloadData() {

        }

        function processTranslationLanguageChange(language, value) {
            if(Number(value) === 1){
                selectedLanguages.value.push(language);
            }
            else{
                const index = selectedLanguages.value.indexOf(language);
                selectedLanguages.value.splice(index, 1);
            }
        }

        return {
            clientRoot,
            definitionOptions,
            downloadFormatOptions,
            glossaryLanguageArr,
            includeImages,
            primaryLanguage,
            selectedDefinitionOption,
            selectedDownloadFormat,
            selectedLanguages,
            closePopup,
            downloadData,
            processTranslationLanguageChange
        }
    }
};
