const searchCriteriaPopup = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        showSpatial: {
            type: Boolean,
            default: true
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div ref="topBarRef" class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                        <q-tab name="criteria" label="Criteria" no-caps></q-tab>
                        <q-tab v-if="!collectionId" name="collections" label="Collections" no-caps></q-tab>
                        <q-tab name="advanced" label="Advanced" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel class="q-pa-none" name="criteria">
                            <search-criteria-tab :collection-id="collectionId" :show-spatial="showSpatial"></search-criteria-tab>
                        </q-tab-panel>
                        <q-tab-panel v-if="!collectionId" name="collections">
                            <search-collections-tab></search-collections-tab>
                        </q-tab-panel>
                        <q-tab-panel name="advanced">
                            <search-advanced-tab></search-advanced-tab>
                        </q-tab-panel>
                    </q-tab-panels>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'search-advanced-tab': searchAdvancedTab,
        'search-collections-tab': searchCollectionsTab,
        'search-criteria-tab': searchCriteriaTab
    },
    setup(_, context) {
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const tab = Vue.ref('criteria');

        function closePopup() {
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            tab,
            closePopup
        }
    }
};
