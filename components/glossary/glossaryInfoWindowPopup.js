const glossaryInfoWindowPopup = {
    props: {
        isEditor: {
            type: Boolean,
            default: false
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        termData: {
            type: Object,
            default: {}
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentContainerRef" class="fit">
                    <q-card flat bordered :style="tabCardStyle">
                        <q-tabs v-model="selectedTab" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab class="bg-grey-3" label="Details" name="details" no-caps />
                            <template v-if="imageArr.length > 0">
                                <q-tab class="bg-grey-3" label="Images" name="images" no-caps />
                            </template>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="selectedTab" animated>
                            <q-tab-panel name="details" :style="tabPanelStyle">
                                <div class="row justify-between q-gutter-md">
                                    <div class="text-h6 text-bold">
                                        {{ termData['term']  }}
                                    </div>
                                    <div>
                                        <q-btn v-if="isEditor" color="grey-4" text-color="black" class="black-border cursor-pointer" size="sm" @click="openTermEditorPopup(termData['glossid']);" icon="far fa-edit" aria-label="Open term editor" dense tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open term editor
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                                <div class="q-mt-sm row justify-between q-pb-md">
                                    <div class="q-pl-md column">
                                        <div v-if="termData['definition']">
                                            <span class="text-bold">Definition:</span>
                                            <span class="q-ml-sm">{{ termData['definition'] }}</span>
                                        </div>
                                        <div v-if="termData['author']">
                                            <span class="text-bold">Author:</span>
                                            <span class="q-ml-sm">{{ termData['author'] }}</span>
                                        </div>
                                        <div v-if="termData['translator']">
                                            <span class="text-bold">Translator:</span>
                                            <span class="q-ml-sm">{{ termData['translator'] }}</span>
                                        </div>
                                        <div v-if="synonymArr.length > 0">
                                            <span class="text-bold">Synonyms:</span>
                                            <span class="q-ml-sm">{{ getSynonymStrFromArr(synonymArr) }}</span>
                                        </div>
                                        <div v-if="translationArr.length > 0">
                                            <span class="text-bold">Translations:</span>
                                            <span class="q-ml-sm">{{ getTranslationStrFromArr(translationArr) }}</span>
                                        </div>
                                        <div v-if="termData['notes']">
                                            <span class="text-bold">Notes:</span>
                                            <span class="q-ml-sm">{{ termData['notes'] }}</span>
                                        </div>
                                        <div v-if="termData['resourceurl']">
                                            <span class="text-bold">Resource URL:</span>
                                            <span class="q-ml-sm"><a :href="termData['resourceurl']" target="_blank" tabindex="0">{{ termData['resourceurl'] }}</a></span>
                                        </div>
                                        <div v-if="termData['source']">
                                            <span class="text-bold">Source:</span>
                                            <span class="q-ml-sm">{{ termData['source'] }}</span>
                                        </div>
                                        <div v-if="relevantTaxaArr.length > 0">
                                            <span class="text-bold">Relevant Taxa:</span>
                                            <span class="q-ml-sm">{{ getRelevantTaxaStrFromArr(relevantTaxaArr) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </q-tab-panel>
                            <template v-if="imageArr.length > 0">
                                <q-tab-panel name="images" :style="tabPanelStyle">
                                    <div class="q-mt-sm q-pb-sm column q-gutter-sm">
                                        <div class="text-h6 text-bold">Images</div>
                                        <template v-for="image in imageArr">
                                            <q-card class="q-ma-md" :style="cardStyle">
                                                <q-img :src="(image.url.startsWith('/') ? (clientRoot + image.url) : image.url)" :width="imageWidth" fit="scale-down"></q-img>
                                                <div class="q-pa-sm row q-gutter-sm">
                                                    <div v-if="image['createdby']">
                                                        <span class="text-bold">Image courtesy of:</span>
                                                        <span class="q-ml-sm">{{ image['createdby'] }}</span>
                                                    </div>
                                                    <div v-if="image['structures']">
                                                        <span class="text-bold">Structures:</span>
                                                        <span class="q-ml-sm">{{ image['structures'] }}</span>
                                                    </div>
                                                    <div v-if="image['notes']">
                                                        <span class="text-bold">Notes:</span>
                                                        <span class="q-ml-sm">{{ image['notes'] }}</span>
                                                    </div>
                                                </div>
                                            </q-card>
                                        </template>
                                    </div>
                                </q-tab-panel>
                            </template>
                        </q-tab-panels>
                    </q-card>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {

    },
    setup(props, context) {
        const baseStore = useBaseStore();
        const glossaryStore = useGlossaryStore();

        const clientRoot = baseStore.getClientRoot;
        const containerWidth = Vue.ref(0);
        const contentContainerRef = Vue.ref(null);
        const glossaryTaxaArr = Vue.computed(() => glossaryStore.getGlossaryTaxaArr);
        const imageArr = Vue.ref({});
        const imageCardStyle = Vue.ref('');
        const imageWidth = Vue.ref(null);
        const relatedTermData = Vue.ref({});
        const relevantTaxaArr = Vue.computed(() => {
            let returnArr = [];
            if(props.termData['tidArr'].length > 0){
                props.termData['tidArr'].forEach(tid => {
                    const relTaxon = glossaryTaxaArr.value.find(taxon => Number(taxon['tid']) === Number(tid));
                    if(relTaxon){
                        returnArr.push(relTaxon);
                    }
                });
                returnArr.sort((a, b) => {
                    return a['sciname'].localeCompare(b['sciname']);
                });
            }
            return returnArr;
        });
        const selectedTab = Vue.ref('details');
        const synonymArr = Vue.computed(() => {
            let returnArr = [];
            if(props.termData['groupIdArr'].length > 0){
                props.termData['groupIdArr'].forEach(group => {
                    if(group['relationshiptype'] === 'synonym' && relatedTermData.value.hasOwnProperty(group['glossgrpid'])){
                        const newArr = returnArr.concat(relatedTermData.value[group['glossgrpid']]);
                        returnArr = newArr.slice();
                    }
                });
                returnArr.sort((a, b) => {
                    return a['term'].localeCompare(b['term']);
                });
            }
            return returnArr;
        });
        const tabCardStyle = Vue.ref('');
        const tabPanelStyle = Vue.ref('');
        const translationArr = Vue.computed(() => {
            let returnArr = [];
            if(props.termData['groupIdArr'].length > 0){
                props.termData['groupIdArr'].forEach(group => {
                    if(group['relationshiptype'] === 'translation' && relatedTermData.value.hasOwnProperty(group['glossgrpid'])){
                        const newArr = returnArr.concat(relatedTermData.value[group['glossgrpid']]);
                        returnArr = newArr.slice();
                    }
                });
                returnArr.sort((a, b) => {
                    return a['language'].localeCompare(b['language']) || a['term'].localeCompare(b['term']);
                });
            }
            return returnArr;
        });

        Vue.watch(contentContainerRef, () => {
            if(contentContainerRef.value){
                setTabPanelHeights();
            }
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function getRelevantTaxaStrFromArr(relTaxaArr) {
            const nameArr = [];
            relTaxaArr.forEach(synonym => {
                nameArr.push(synonym['sciname']);
            });
            return nameArr.length > 0 ? nameArr.join(', ') : '';
        }

        function getSynonymStrFromArr(synonymArr) {
            const nameArr = [];
            synonymArr.forEach(synonym => {
                nameArr.push(synonym['term']);
            });
            return nameArr.length > 0 ? nameArr.join(', ') : '';
        }

        function getTranslationStrFromArr(translationArr) {
            const nameArr = [];
            translationArr.forEach(translation => {
                nameArr.push((translation['term'] + ' (' + translation['language'] + ')'));
            });
            return nameArr.length > 0 ? nameArr.join(', ') : '';
        }

        function openTermEditorPopup(glossid) {
            context.emit('open:term-editor-popup', glossid);
        }

        function setImageArr() {
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify([props.termData['glossid']]));
            formData.append('action', 'getGlossaryImageDataFromGlossidArr');
            fetch(glossaryImageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                imageArr.value = resData.hasOwnProperty(props.termData['glossid']) ? resData[props.termData['glossid']] : [];
            });
        }

        function setRelatedTermData() {
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify([props.termData['glossid']]));
            formData.append('action', 'getGlossaryRelatedTermsDataFromGlossidArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                relatedTermData.value = Object.assign({}, resData);
            });
        }

        function setTabPanelHeights() {
            if(contentContainerRef.value){
                containerWidth.value = contentContainerRef.value.clientWidth;
                const clientHeight = contentContainerRef.value.clientHeight;
                tabCardStyle.value = 'height: ' + clientHeight + 'px;';
                imageCardStyle.value = 'width: ' + (containerWidth.value - 60) + 'px;';
                imageWidth.value = (containerWidth.value - 100) + 'px';
            }
        }

        Vue.onMounted(() => {
            window.addEventListener('resize', setTabPanelHeights);
            setImageArr();
            setRelatedTermData();
        });

        return {
            clientRoot,
            contentContainerRef,
            imageArr,
            imageCardStyle,
            imageWidth,
            relevantTaxaArr,
            selectedTab,
            synonymArr,
            tabCardStyle,
            tabPanelStyle,
            translationArr,
            closePopup,
            getRelevantTaxaStrFromArr,
            getSynonymStrFromArr,
            getTranslationStrFromArr,
            openTermEditorPopup
        }
    }
};
