const tableColumnTogglePopup = {
    props: {
        fieldArr: {
            type: Array,
            default: []
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        visibleColumns: {
            type: Array,
            default: []
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="full-width q-pa-md">
                            <div class="column q-gutter-sm">
                                <template v-for="field in fieldArr">
                                    <div>
                                        <q-toggle v-model="visibleColumns" :val="field['name']" :label="field['label']" @update:model-value="processColumnSelectionChange"></q-toggle>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(_, context) {
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processColumnSelectionChange(value) {
            context.emit('update:visible-columns', value);
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
            closePopup,
            processColumnSelectionChange
        }
    }
};
