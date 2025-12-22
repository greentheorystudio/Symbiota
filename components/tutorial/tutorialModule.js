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
        <q-dialog v-if="currentSlide" class="z-top" v-model="showTutorial" @keydown.left="goToPreviousSlide" @keydown.right="goToNextSlide" persistent seamless>
            <div v-if="!hideTutorial && currentSlide.type === 'intro'" class="tutorial-intro-frame-container">
                <q-card class="tutorial-frame tutorial-content q-pa-md fit">
                    <div v-if="currentSlide.title" class="heading title">{{ currentSlide.title }}</div>
                    <div v-html="currentSlide.content"></div>
                </q-card>
            </div>
            <div v-if="!hideTutorial && currentSlide.type === 'toc'" class="tutorial-toc-frame-container" :style="tocOuterStyle">
                <q-card class="tutorial-frame tutorial-content q-pa-md fit">
                    <div class="heading title">Index of Topics</div>
                    <div class="q-ml-md column wrap" :style="tocInnerStyle">
                        <template v-for="topic in Object.keys(tutorialIndex)">
                            <div v-if="topic !== 'intro' && topic !== 'toc'">
                                <a class="cursor-pointer tutorial-link q-mr-md" @click="goToSection(tutorialIndex[topic].title.toLowerCase());" :aria-label="('Go to ' + tutorialIndex[topic].title + ' section')" tabindex="0">{{ tutorialIndex[topic].title }}</a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </div>
            <div v-if="!hideTutorial && currentSlide.type === 'content'" class="tutorial-content-frame-container">
                <q-card class="tutorial-frame tutorial-content q-pa-md q-mt-md q-mr-md fixed-top-right tutorial-content-frame-container">
                    <p v-if="currentSlide.header" class="heading header">{{ currentSlide.header }}</p>
                    <div v-html="currentSlide.content"></div>
                </q-card>
            </div>
            <q-card v-if="!hideTutorial" class="tutorial-frame fixed-bottom-right">
                <q-card-section class="q-pa-sm row q-gutter-sm no-wrap">
                    <q-btn round color="negative" icon="close" size="md" glossy dense @click="closeTutorial();" aria-label="Close tutorial" tabindex="0">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Close tutorial
                        </q-tooltip>
                    </q-btn>
                    <q-btn v-if="currentSlideIndex > 0" round color="green" icon="chevron_left" size="md" glossy dense @click="goToPreviousSlide();" aria-label="Go to previous slide" tabindex="0">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Go to previous slide
                        </q-tooltip>
                    </q-btn>
                    <q-btn v-if="currentSlideIndex < (tutorialSlides.length - 1)" round color="green" icon="chevron_right" size="md" glossy dense @click="goToNextSlide();" aria-label="Go to next slide" tabindex="0">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Go to next slide
                        </q-tooltip>
                    </q-btn>
                </q-card-section>
            </q-card>
            <q-card v-if="currentSlide.type !== 'intro' && currentSlide.type !== 'toc'" class="tutorial-frame fixed-bottom-left">
                <q-card-section class="q-pa-sm">
                    <template v-if="hideTutorial">
                        <a class="cursor-pointer toggle-tutorial tutorial-link text-bold" @click="hideTutorial = false" aria-label="Show tutorial" tabindex="0">Show Tutorial</a>
                    </template>
                    <template v-else>
                        <a class="cursor-pointer toggle-tutorial tutorial-link text-bold" @click="hideTutorial = true" aria-label="Hide tutorial" tabindex="0">Hide Tutorial</a>
                    </template>
                </q-card-section>
            </q-card>
            <div v-if="!hideTutorial && currentSlide.type !== 'intro' && currentSlide.type !== 'toc'" class="tutorial-bottom-nav-container q-mb-sm">
                <q-card class="tutorial-frame tutorial-content">
                    <q-card-section class="q-pa-sm column q-gutter-sm">
                        <div class="row justify-center">
                            <div v-if="currentSlide.section" class="heading title text-center">{{ currentSlide.section }}</div>
                        </div>
                        <div class="row justify-between">
                            <div>
                                <a class="cursor-pointer tutorial-link" @click="goToPreviousTopic();" aria-label="Go to previous topic" tabindex="0">Previous Topic</a>
                            </div>
                            <div>
                                <a class="cursor-pointer tutorial-link" @click="goToSection('toc');" aria-label="Go to Index of Topics" tabindex="0">Index of Topics</a>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </q-dialog>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const currentSectionIndex = Vue.computed(() => {
            return currentSlide.value ? Object.keys(tutorialIndex.value).indexOf(currentSlide.value['section'].toLowerCase()) : 0;
        });
        const currentSlide = Vue.computed(() => {
            return tutorialSlides.value.length > 0 ? tutorialSlides.value[currentSlideIndex.value] : null;
        });
        const currentSlideIndex = Vue.ref(0);
        const hideTutorial = Vue.ref(false);
        const jsVersion = baseStore.getJsVersion;
        const tocInnerStyle = Vue.ref(null);
        const tocOuterStyle = Vue.ref(null);
        const tutorialData = Vue.ref(null);
        const tutorialIndex = Vue.ref({});
        const tutorialSlides = Vue.computed(() => {
            let index = 0;
            const returnArr = [];
            if(tutorialData.value){
                if(tutorialData.value.hasOwnProperty('introHtml') && tutorialData.value['introHtml']){
                    returnArr.push({
                        type: 'intro',
                        section: 'intro',
                        title: (tutorialData.value.hasOwnProperty('title') && tutorialData.value['title']) ? tutorialData.value['title'] : null,
                        content: tutorialData.value['introHtml']
                    });
                    tutorialIndex.value['intro'] = {title: 'Intro', index: index};
                    index++;
                }
                if(tutorialData.value.hasOwnProperty('sections') && tutorialData.value['sections'].length > 0) {
                    returnArr.push({
                        type: 'toc',
                        section: 'toc'
                    });
                    tutorialIndex.value['toc'] = {title: 'TOC', index: index};
                    index++;
                    tutorialData.value['sections'].forEach((section) => {
                        if(section.hasOwnProperty('slides') && section['slides'].length > 0){
                            tutorialIndex.value[section['title'].toLowerCase()] = {title: section['title'], index: index};
                            section['slides'].forEach((slide) => {
                                returnArr.push({
                                    type: 'content',
                                    section: section['title'],
                                    header: (slide.hasOwnProperty('header') && slide['header']) ? slide['header'] : null,
                                    content: slide['html']
                                });
                                index++;
                            });
                        }
                    });
                }
            }
            return returnArr;
        });
        const windowWidth = Vue.ref(0);

        Vue.watch(tutorialSlides, () => {
            setStyling();
        });

        function closeTutorial() {
            context.emit('close:tutorial');
        }

        function goToNextSlide() {
            if(currentSlideIndex.value < (tutorialSlides.value.length - 1)){
                currentSlideIndex.value++;
            }
        }

        function goToPreviousSlide() {
            if(currentSlideIndex.value > 0){
                currentSlideIndex.value--;
            }
        }

        function goToPreviousTopic() {
            goToSection(Object.keys(tutorialIndex.value)[currentSectionIndex.value - 1]);
        }

        function goToSection(section) {
            currentSlideIndex.value = tutorialIndex.value[section.toLowerCase()]['index'];
        }

        function setStyling() {
            tocInnerStyle.value = null;
            tocOuterStyle.value = null;
            windowWidth.value = window.innerWidth;
            if(tutorialSlides.value.length > 10 && windowWidth.value >= 1220){
                if(tutorialSlides.value.length > 30){
                    tocOuterStyle.value = 'width: 90%;height: 80%;';
                    tocInnerStyle.value = 'width: 100%;height: 90%;overflow: scroll;';
                }
            }
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
                            const dataUrl = clientRoot + '/config/tutorial/' + defaultConfig[props.tutorial] + '?ver=' + jsVersion;
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
            window.addEventListener('resize', setStyling);
            setStyling();
        });

        return {
            currentSlide,
            currentSlideIndex,
            hideTutorial,
            tocInnerStyle,
            tocOuterStyle,
            tutorialIndex,
            tutorialSlides,
            closeTutorial,
            goToNextSlide,
            goToPreviousSlide,
            goToPreviousTopic,
            goToSection
        }
    }
};
