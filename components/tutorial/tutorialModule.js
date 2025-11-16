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
            <div v-if="currentSlide.type === 'intro' || currentSlide.type === 'toc'" class="tutorial-center-frame-container">
                <q-card class="tutorial-frame tutorial-content q-pa-md fit" flat>
                    <template v-if="currentSlide.type === 'intro'">
                        <div v-if="currentSlide.title" class="heading title">{{ currentSlide.title }}</div>
                        <div v-html="currentSlide.content"></div>
                    </template>
                    <template v-else>
                        
                    </template>
                </q-card>
            </div>
            <q-card class="tutorial-frame fixed-bottom-right" flat square>
                <q-card-section class="q-pa-sm row q-gutter-sm no-wrap">
                    <q-btn round color="negative" icon="close" size="lg" glossy dense></q-btn>
                    <q-btn round color="green" icon="chevron_left" size="lg" glossy dense></q-btn>
                    <q-btn round color="green" icon="chevron_right" size="lg" glossy dense></q-btn>
                </q-card-section>
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
                                console.log(data);
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
