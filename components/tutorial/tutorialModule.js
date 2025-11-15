const tutorialModule = {
    props: {
        showTutorial: {
            type: Boolean,
            default: false
        },
        tutorial: {
            type: String,
            default: null
        }
    },
    template: `
        <q-dialog v-if="currentSlide" class="z-max" v-model="showTutorial" persistent seamless>
            <q-card class="tutorial-frame" style="width:40%;" flat>
                <h2>Mapping Configurations Tutorial</h2>
                    <p>Welcome to the mapping configurations manager tutorial! This tutorial will explain the different
                        settings that can be configured within this module. It will also explain how to upload and
                        configure map data layers, and configure map layer groups in the Layers Tab.</p>
                    <p>Use the red arrows located in
                        the bottom-right corner of this screen to progress forwards and backwards. The left and right arrow
                        keys on your keyboard can also be used for progression, however if anything is clicked outside
                        the tutorial windows on any slide, the red arrows will need to be used for the next progression.</p>
                    <p>On any topic slide there will be a Hide Tutorial link in the bottom-left corner of the screen,
                        which can be clicked to hide the tutorial content. Once clicked, a Show Tutorial link in the
                        same location can be clicked to show the tutorial content again.</p>
            </q-card>
            <q-card class="tutorial-frame fixed-bottom-right" style="width:40%;" flat square>
                test
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const currentSlide = Vue.computed(() => {
            return tutorialSlides.value.length > 0 ? tutorialSlides.value[currentSlideIndex.value] : null;
        });
        const currentSlideIndex = Vue.ref(0);
        const tutorialData = Vue.ref(null);
        const tutorialIndex = Vue.ref({});
        const tutorialSlides = Vue.computed(() => {
            let index = 0;
            const returnArr = [];
            if(tutorialData.value){
                if(tutorialData.value.hasOwnProperty('introHtml') && tutorialData.value['introHtml']){
                    returnArr.push({
                        type: 'intro',
                        title: (tutorialData.value.hasOwnProperty('title') && tutorialData.value['title']) ? tutorialData.value['title'] : null,
                        content: tutorialData.value['introHtml']
                    });
                    tutorialIndex['intro'] = index;
                    index++;
                }
                if(tutorialData.value.hasOwnProperty('sections') && tutorialData.value['sections'].length > 0) {
                    returnArr.push({
                        type: 'toc'
                    });
                    tutorialIndex['toc'] = index;
                    index++;
                    tutorialData.value['sections'].forEach((section) => {
                        if(section.hasOwnProperty('slides') && section['slides'].length > 0){
                            tutorialIndex[section['title']] = index;
                            index++;
                            section['slides'].forEach((slide) => {
                                returnArr.push({
                                    type: 'content',
                                    section: section['title'],
                                    header: (slide.hasOwnProperty('header') && slide['header']) ? slide['header'] : null,
                                    content: slide['html']
                                });
                            });
                        }
                    });
                }
            }
            return returnArr;
        });

        function goToNextSlide() {
            currentSlideIndex.value++;
        }

        function goToPreviousSlide() {
            currentSlideIndex.value--;
        }

        function goToSlide(index) {
            currentSlideIndex.value = index;
        }

        function setTutorialData() {
            baseStore.getGlobalConfigValue('CUSTOM_TUTORIAL_JSON', (dataStr) => {
                const customConfig = dataStr ? JSON.parse(dataStr) : null;
                if(customConfig && customConfig.hasOwnProperty(props.tutorial)){
                    tutorialData.value = customConfig[props.tutorial];
                }
                else{
                    baseStore.getGlobalConfigValue('DEFAULT_TUTORIAL_JSON', (dataStr) => {
                        const defaultConfig = dataStr ? JSON.parse(dataStr) : null;
                        if(defaultConfig && defaultConfig.hasOwnProperty(props.tutorial)){
                            const dataUrl = clientRoot + '/config/tutorial/' + defaultConfig[props.tutorial];
                            fetch(dataUrl)
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((data) => {
                                tutorialData.value = data;
                            });
                        }
                    });
                }
            });
        }

        Vue.onMounted(() => {
            setTutorialData();
        });

        return {
            currentSlide,
            currentSlideIndex,
            goToNextSlide,
            goToPreviousSlide,
            goToSlide
        }
    }
};
