const projectEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(checklistTaxaId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="details" label="Info" no-caps></q-tab>
                                <q-tab name="checklists" label="Checklist Management" no-caps></q-tab>
                                <q-tab name="admin" label="Admin" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="details">
                                    <project-field-module @close:popup="closePopup();"></project-field-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="checklists">
                                    
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="admin">
                                    
                                </q-tab-panel>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <project-field-module @close:popup="closePopup();"></project-field-module>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'project-field-module': projectFieldModule
    },
    setup(props, context) {
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const tab = Vue.ref('details');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
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
        });

        return {
            contentRef,
            contentStyle,
            tab,
            tabStyle,
            closePopup
        }
    }
};
