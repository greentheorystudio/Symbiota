const organismOfTheDay = {
    props: {
        checklistId: {
            type: Number,
            default: 0
        },
        title: {
            type: String,
            default: 'Organism of the Day'
        },
        type: {
            type: String,
            default: 'organism'
        }
    },
    template: `
        <div ref="cardContainerRef" class="full-width row justify-center">
            <q-card class="cursor-pointer" @click="showPopup = true">
                <q-card-section v-if="imageData && taxonData && imageData.hasOwnProperty(taxonData['tidaccepted']) && imageData[taxonData['tidaccepted']].length > 0" class="q-pa-md column">
                    <div class="full-width text-h6 text-bold row justify-center">
                        {{ title }}
                    </div>
                    <div class="row justify-center">
                        <div :style="cardStyle">
                            <q-img :height="cardImageHeight" :src="(imageData[taxonData['tidaccepted']][0]['url'].startsWith('/') ? (clientRoot + imageData[taxonData['tidaccepted']][0]['url']) : imageData[taxonData['tidaccepted']][0]['url'])" fit="scale-down" :no-native-menu="true"></q-img>
                        </div>
                    </div>
                    <div class="text-body1 text-bold row justify-center">
                        What is thie {{ type }}?
                    </div>
                    <div class="text-body1 text-bold text-blue row justify-center">
                        Click here to test your knowledge
                    </div>
                </q-card-section>
                <q-inner-loading :showing="loading">
                    <q-spinner color="primary" size="3em" :thickness="10"></q-spinner>
                </q-inner-loading>
            </q-card>
        </div>
        <template v-if="showPopup">
            <q-dialog class="z-top" v-model="showPopup" persistent>
                <q-card class="lg-popup overflow-hidden">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showPopup = false"></q-btn>
                        </div>
                    </div>
                    <div ref="containerRef" class="fit overflow-auto">
                        <div :style="containerStyle">
                            <div class="row justify-center">
                                <div class="q-mt-md column q-gutter-sm">
                                    <div class="full-width text-h5 text-bold row justify-center">
                                        {{ title }}
                                    </div>
                                    <q-img class="rounded-borders" :width="popupCardImageWidth" :height="popupCardImageWidth" :src="(currentImage['url'].startsWith('/') ? (clientRoot + currentImage['url']) : currentImage['url'])" fit="scale-down" :no-native-menu="true"></q-img>
                                    <div class="row justify-between">
                                        <div>
                                            
                                        </div>
                                        <div class="row justify-end q-gutter-xs">
                                            <q-btn round dense color="primary" text-color="white" icon="arrow_left" @click="currentImageIndex--" :disabled="currentImageIndex === 0">
                                                <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Previous image
                                                </q-tooltip>
                                            </q-btn>
                                            <q-btn round dense color="primary" text-color="white" icon="arrow_right" @click="currentImageIndex++" :disabled="(currentImageIndex + 1) === imageData[taxonData['tidaccepted']].length">
                                                <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Next image
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div class="text-h6 text-bold row justify-center">
                                        Name that {{ type }}!
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <single-scientific-common-name-auto-complete :sciname="(familyAnswer ? familyAnswer.sciname : null)" :options="familyAnswerOptions" label="Family" limit-to-options="true" @update:sciname="processFamilyAnswerChange"></single-scientific-common-name-auto-complete>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <single-scientific-common-name-auto-complete :sciname="(scinameAnswer ? scinameAnswer.sciname : null)" :options="scinameAnswerOptions" label="Scientific Name" limit-to-options="true" @update:sciname="processScinameAnswerChange"></single-scientific-common-name-auto-complete>
                                        </div>
                                    </div>
                                    <div class="row justify-between">
                                        <div>
                                            <q-btn color="negative" @click="showCurrentTaxon();" label="I give up!" />
                                        </div>
                                        <div>
                                            <q-btn color="primary" @click="checkAnswers();" label="Check Answer" :disabled="!scinameAnswer && !familyAnswer" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup(props) {
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();
        const imageStore = useImageStore();

        const cardContainerRef = Vue.ref(null);
        const cardImageHeight = Vue.ref(null);
        const cardStyle = Vue.ref(null);
        const configData = Vue.ref(null);
        const containerRef = Vue.ref(null);
        const containerStyle = Vue.ref(null);
        const currentImage = Vue.computed(() => {
            return (taxonData.value && imageData.value && imageData.value.hasOwnProperty(taxonData.value['tidaccepted'])) ? imageData.value[taxonData.value['tidaccepted']][Number(currentImageIndex.value)] : null;
        });
        const currentImageIndex = Vue.ref(0);
        const error = Vue.ref(false);
        const imageData = Vue.computed(() => imageStore.getChecklistImageData);
        const loading = Vue.ref(true);
        const newConfigData = Vue.computed(() => {
            const today = new Date();
            return {
                date: today.toDateString(),
                tid: newTaxonTid.value
            };
        });
        const newTaxonTid = Vue.ref(null);
        const popupCardImageWidth = Vue.ref(null);
        const showPopup = Vue.ref(false);
        const taxonData = Vue.ref(null);
        const taxaDataArr = Vue.computed(() => checklistStore.getChecklistFlashcardTaxaArr);

        Vue.watch(imageData, () => {
            loading.value = false;
        });

        Vue.watch(cardContainerRef, () => {
            setCardStyle();
        });

        Vue.watch(containerRef, () => {
            setContentStyle();
        });

        function saveNewConfigData() {
            let action;
            if(configData.value){
                action = 'updateOotdConfigJson';
            }
            else{
                action = 'addOotdConfigJson';
            }
            const formData = new FormData();
            formData.append('jsonVal', JSON.stringify(newConfigData.value));
            formData.append('action', action.toString());
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    configData.value = Object.assign({}, newConfigData.value);
                    setTaxonData();
                }
                else{
                    error.value = true;
                }
            });
        }

        function setCardStyle() {
            cardStyle.value = null;
            cardImageHeight.value = null;
            if(cardContainerRef.value){
                const cardDim = cardContainerRef.value.clientWidth * 0.9;
                cardStyle.value = 'width: ' + cardDim + 'px;';
                cardImageHeight.value = cardDim + 'px';
            }
        }

        function setConfigData() {
            baseStore.getGlobalConfigValue('OOTD_CONFIG_JSON', (dataStr) => {
                configData.value = dataStr ? JSON.parse(dataStr) : null;
                if(configData.value && configData.value.hasOwnProperty('date') && configData.value.hasOwnProperty('tid') && configData.value['date'] && configData.value['tid']){
                    validateConfigDate();
                }
                else{
                    setNewConfigData();
                }
            });
        }

        function setContentStyle() {
            containerStyle.value = null;
            popupCardImageWidth.value = null;
            if(containerRef.value){
                let cardDim;
                if(containerRef.value.clientWidth > 900){
                    cardDim = (containerRef.value.clientWidth * 0.5);
                }
                else if(containerRef.value.clientWidth > 600){
                    cardDim = (containerRef.value.clientWidth * 0.6);
                }
                else if(containerRef.value.clientWidth > 400){
                    cardDim = (containerRef.value.clientWidth * 0.7);
                }
                else{
                    cardDim = containerRef.value.clientWidth * 0.8;
                }
                containerStyle.value = 'height: ' + (containerRef.value.clientHeight + (Math.floor(cardDim) / 2)) + 'px;width: ' + containerRef.value.clientWidth + 'px;';
                popupCardImageWidth.value = Math.floor(cardDim) + 'px';
            }
        }

        function setNewConfigData() {
            if(Number(props.checklistId) > 0){
                checklistStore.setChecklist(props.checklistId, (clid) => {
                    if(Number(clid) > 0){
                        checklistStore.setChecklistTaxaArr(false, false, false, () => {
                            if((taxaDataArr.value.length > 0 && !configData.value) || (taxaDataArr.value.length > 1 && configData.value)){
                                do {
                                    const randomIndex = Math.floor(Math.random() * taxaDataArr.value.length);
                                    newTaxonTid.value = taxaDataArr.value[randomIndex] ? taxaDataArr.value[randomIndex]['tid'] : null;
                                } while (!newTaxonTid.value || (configData.value && Number(newTaxonTid.value) === Number(configData.value['tid'])));
                                saveNewConfigData();
                            }
                            else{
                                error.value = true;
                            }
                        });
                    }
                });
            }
        }

        function setTaxonData() {
            const formData = new FormData();
            formData.append('full', '0');
            formData.append('tid', configData.value['tid'].toString());
            formData.append('action', 'getTaxonFromTid');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                taxonData.value = Object.assign({}, data);
                if(Number(taxonData.value['tid']) > 0){
                    setTaxonImages();
                }
                else{
                    error.value = true;
                }
            });
        }

        function setTaxonImages() {
            imageStore.clearChecklistImageData();
            imageStore.setChecklistTaggedImageData([props.checklistId], 5, () => {
                if(!imageData.value.hasOwnProperty(taxonData.value['tidaccepted'])){
                    imageStore.setChecklistImageData([taxonData.value['tidaccepted']], 5);
                }
            }, [taxonData.value['tidaccepted']]);
        }

        function validateConfigDate() {
            const configDate = new Date(configData.value['date']);
            configDate.setHours(0, 0, 0, 0);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if(configDate.getTime() === today.getTime()){
                setTaxonData();
            }
            else{
                setNewConfigData();
            }
        }

        Vue.onMounted(() => {
            setConfigData();
            window.addEventListener('resize', setCardStyle);
        });

        return {
            cardContainerRef,
            cardImageHeight,
            cardStyle,
            containerRef,
            containerStyle,
            currentImage,
            currentImageIndex,
            error,
            imageData,
            loading,
            popupCardImageWidth,
            showPopup,
            taxonData
        }
    }
};
