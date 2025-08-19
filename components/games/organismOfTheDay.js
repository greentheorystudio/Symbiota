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
                    <div class="full-width text-h5 text-bold row justify-center">
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
                <q-inner-loading :showing="loading && !error">
                    <q-spinner color="primary" size="3em" :thickness="10"></q-spinner>
                </q-inner-loading>
                <q-inner-loading :showing="error">
                    <q-icon name="warning" color="negative" size="3em"></q-icon>
                </q-inner-loading>
            </q-card>
        </div>
        <template v-if="showPopup">
            <q-dialog class="z-top" v-model="showPopup" persistent>
                <q-card class="md-tall-popup overflow-hidden">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showPopup = false"></q-btn>
                        </div>
                    </div>
                    <div ref="containerRef" class="fit overflow-auto">
                        <div :style="containerStyle">
                            <div ref="balloonWindowRef" class="fit" :class="showBalloonDiv ? '' : 'hidden'"></div>
                            <template v-if="!showAnswerResponse">
                                <div class="row justify-center">
                                    <div class="full-width q-pa-md column q-gutter-sm">
                                        <div class="row justify-center q-gutter-sm">
                                            <div class="text-h6 text-bold">
                                                Name that {{ type }}!
                                            </div>
                                            <div>
                                                <q-btn size="md" icon="far fa-question-circle" stretch flat dense ripple="false" @click="displayInstructionsPopup = true">
                                                    <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Show instructions
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div class="full-width" :style="popupCardImageWidth">
                                            <image-carousel :image-arr="imageData[taxonData['tidaccepted']]"></image-carousel>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <single-scientific-common-name-auto-complete :sciname="(familyAnswer ? familyAnswer.sciname : null)" label="Family" rank-limit="140" limit-to-options="true" @update:sciname="processFamilyAnswerChange"></single-scientific-common-name-auto-complete>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <single-scientific-common-name-auto-complete :sciname="(scinameAnswer ? scinameAnswer.sciname : null)" label="Scientific Name" rank-low="220" limit-to-options="true" @update:sciname="processScinameAnswerChange"></single-scientific-common-name-auto-complete>
                                            </div>
                                        </div>
                                        <div class="row justify-between">
                                            <div>
                                                <q-btn color="negative" @click="showAnswer();" label="I give up!" />
                                            </div>
                                            <div>
                                                <q-btn color="primary" @click="checkAnswers();" label="Check Answer" :disabled="!scinameAnswer && !familyAnswer" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="q-pa-md fit column justify-center" :class="showBalloonDiv ? 'hidden' : ''">
                                    <div class="row justify-center">
                                        <div class="column">
                                            <template v-if="answerCorrect === 'complete'">
                                                <div class="z-max q-mb-lg text-h3 text-bold text-center">
                                                    Congratulations!
                                                </div>
                                                <div class="z-max q-mb-lg text-h3 text-bold text-center">
                                                    That is correct!
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="z-max q-mb-lg text-h3 text-bold text-center">
                                                    {{ answerHeader }}
                                                </div>
                                            </template>
                                            <template v-if="showCorrectAnswer">
                                                <div v-if="answerCorrect !== 'complete'" class="z-max text-h5 text-bold text-center">
                                                    The correct answer is
                                                </div>
                                                <div v-if="answerCorrect !== 'complete'" class="z-max text-h5 text-bold text-italic text-center">
                                                    {{ taxonData['sciname'] }}
                                                </div>
                                                <div v-if="answerCorrect !== 'complete'" class="text-h5 text-bold text-center">
                                                    {{ taxonData['family'] }}
                                                </div>
                                                <div class="q-my-md text-h6 text-bold text-blue cursor-pointer text-center" @click="showTaxonProfile">
                                                    Click here to learn more about this {{ type }}
                                                </div>
                                                <div class="text-h6 text-bold text-center">
                                                    Thank you for playing!
                                                </div>
                                                <div class="text-h6 text-bold text-center">
                                                    Check back tomorrow for a new {{ type }}!
                                                </div>
                                            </template>
                                            <template v-else>
                                                <template v-if="answerCorrect === 'none'">
                                                    <div class="text-h5 text-center">
                                                        <span class="text-bold">Hint:</span> The family is <span class="text-bold">not</span> {{ familyAnswer['sciname'] }}
                                                    </div>
                                                </template>
                                                <template v-else-if="answerCorrect === 'family'">
                                                    <div class="text-h5 text-center">
                                                        On the bright side, <span class="text-bold">you did get the family right</span>, but the scientific name is not {{ scinameAnswer['sciname'] }}
                                                    </div>
                                                </template>
                                                <template v-else-if="answerCorrect === 'sciname'">
                                                    <div class="text-h5 text-center">
                                                        On the bright side, <span class="text-bold">you did get the scientific name right</span>, but the family is not {{ familyAnswer['sciname'] }}
                                                    </div>
                                                </template>
                                                <template v-else-if="answerCorrect === 'genus'">
                                                    <div class="text-h5 text-center">
                                                        On the bright side, <span class="text-bold">you did get the genus right</span>, but the scientific name is not {{ scinameAnswer['sciname'] }} and the family is not {{ familyAnswer['sciname'] }}
                                                    </div>
                                                </template>
                                                <template v-else-if="answerCorrect === 'genusfamily'">
                                                    <div class="text-h5 text-center">
                                                        On the bright side, <span class="text-bold">you did get the genus and family right</span>, but the scientific name is not {{ scinameAnswer['sciname'] }}
                                                    </div>
                                                </template>
                                                <div class="text-h6 text-bold text-blue cursor-pointer text-center" @click="showAnswerResponse = false">
                                                    Click here to try again
                                                </div>
                                                <div class="text-h5 text-bold text-center">
                                                    OR
                                                </div>
                                                <div class="text-h6 text-bold text-blue cursor-pointer text-center" @click="showCorrectAnswer = true">
                                                    Click here reveal what the answer is
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </q-card>
                <q-dialog class="z-top" v-model="displayInstructionsPopup" persistent>
                    <q-card class="sm-popup q-pa-md column q-gutter-sm">
                        <div class="text-body1">
                            Look at the picture, and see if you can figure out what the {{ type }} is. If you get completely stumped, you can
                            click the "I give up" button. A new {{ type }} is updated daily, so make sure you check back every day to test your knowledge!
                        </div>
                        <div class="row justify-end">
                            <div>
                                <q-btn color="primary" @click="displayInstructionsPopup = false" label="Ok" />
                            </div>
                        </div>
                    </q-card>
                </q-dialog>
            </q-dialog>
        </template>
    `,
    components: {
        'image-carousel': imageCarousel,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup(props) {
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();
        const imageStore = useImageStore();

        const answerCorrect = Vue.ref(null);
        const answerHeader = Vue.ref(null);
        const balloonWindowRef = Vue.ref(null);
        const cardContainerRef = Vue.ref(null);
        const cardImageHeight = Vue.ref(null);
        const cardStyle = Vue.ref(null);
        const clientRoot = baseStore.getClientRoot;
        const configData = Vue.ref(null);
        const containerRef = Vue.ref(null);
        const containerStyle = Vue.ref(null);
        const correctGenus = Vue.computed(() => {
            let returnVal = null;
            if(taxonData.value && taxonData.value['sciname']){
                const nameParts = taxonData.value['sciname'].split(' ');
                returnVal = nameParts[0];
            }
            return returnVal;
        });
        const currentImage = Vue.computed(() => {
            return (taxonData.value && imageData.value && imageData.value.hasOwnProperty(taxonData.value['tidaccepted'])) ? imageData.value[taxonData.value['tidaccepted']][Number(currentImageIndex.value)] : null;
        });
        const currentImageIndex = Vue.ref(0);
        const displayInstructionsPopup = Vue.ref(false);
        const error = Vue.ref(false);
        const familyAnswer = Vue.ref(null);
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
        const scinameAnswer = Vue.ref(null);
        const showAnswerResponse = Vue.ref(false);
        const showBalloonDiv = Vue.ref(false);
        const showCorrectAnswer = Vue.ref(false);
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

        function checkAnswers() {
            if(scinameAnswer.value && scinameAnswer.value['sciname'] === taxonData.value['sciname']){
                if(familyAnswer.value && familyAnswer.value['sciname'] === taxonData.value['family']){
                    answerCorrect.value = 'complete';
                    showCorrectAnswer.value = true;
                    showBalloonDiv.value = true;
                }
                else{
                    answerCorrect.value = 'sciname';
                    answerHeader.value = 'Sorry, that is not correct';
                }
            }
            else{
                const scinameAnswerParts = scinameAnswer.value['sciname'].split(' ');
                const genusAnswer = scinameAnswerParts[0];
                if(scinameAnswer.value && genusAnswer === correctGenus.value){
                    if(familyAnswer.value && familyAnswer.value['sciname'] === taxonData.value['family']){
                        answerCorrect.value = 'genusfamily';
                        answerHeader.value = 'Sorry, that is not correct';
                    }
                    else{
                        answerCorrect.value = 'genus';
                        answerHeader.value = 'Sorry, that is not correct';
                    }
                }
                else if(familyAnswer.value && familyAnswer.value['sciname'] === taxonData.value['family']){
                    answerCorrect.value = 'family';
                    answerHeader.value = 'Sorry, that is not correct';
                }
                else{
                    answerCorrect.value = 'none';
                }
            }
            showAnswerResponse.value = true;
            if(answerCorrect.value === 'complete'){
                createBalloons(1);
            }
        }

        function createBalloons(num) {
            showBalloonDiv.value = true;
            let i;
            for(i = num; i < 1000; i *= 2) {
                let balloon = document.createElement('div');
                balloon.className = 'ootd-balloon';
                balloon.style.cssText = getRandomStyles();
                balloonWindowRef.value.append(balloon);
            }
            setTimeout(() => {
                showBalloonDiv.value = false;
            }, 5000);
        }

        function getRandomStyles() {
            var r = random(255);
            var g = random(255);
            var b = random(255);
            var ml = random(containerRef.value.clientWidth);
            var dur = random(5) + 5;
            return `
                background-color: rgba(${r},${g},${b},0.7);
                color: rgba(${r},${g},${b},0.7); 
                box-shadow: inset -7px -3px 10px rgba(${r - 10},${g - 10},${b - 10},1.0);
                margin: 0 0 0 ${ml}px;
                animation: ootd-float ${dur}s ease-in infinite
            `;
        }

        function processFamilyAnswerChange(value) {
            familyAnswer.value = value;
        }

        function processScinameAnswerChange(value) {
            scinameAnswer.value = value;
        }

        function processWindowResize() {
            setCardStyle();
            setContentStyle();
        }

        function random(num) {
            return Math.floor(Math.random() * num);
        }

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
                containerStyle.value = 'height: ' + containerRef.value.clientHeight + 'px;width: ' + containerRef.value.clientWidth + 'px;';
                popupCardImageWidth.value = 'height: ' + (Math.floor(cardDim) + 100) + 'px;';
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

        function showAnswer() {
            answerCorrect.value = 'giveup';
            answerHeader.value = 'Too bad!';
            showCorrectAnswer.value = true;
            showAnswerResponse.value = true;
        }

        function showTaxonProfile() {
            window.open((clientRoot + '/taxa/index.php?taxon=' + taxonData.value['tid']), '_blank');
            showPopup.value = false;
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
            window.addEventListener('resize', processWindowResize);
        });

        return {
            answerCorrect,
            answerHeader,
            balloonWindowRef,
            cardContainerRef,
            cardImageHeight,
            cardStyle,
            containerRef,
            containerStyle,
            currentImage,
            currentImageIndex,
            displayInstructionsPopup,
            error,
            familyAnswer,
            imageData,
            loading,
            popupCardImageWidth,
            scinameAnswer,
            showAnswerResponse,
            showBalloonDiv,
            showCorrectAnswer,
            showPopup,
            taxonData,
            checkAnswers,
            processFamilyAnswerChange,
            processScinameAnswerChange,
            showAnswer,
            showTaxonProfile
        }
    }
};
