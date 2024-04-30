const occurrenceCollectingEventListPopup = {
    props: {
        eventArr: {
            type: Array,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle">
                    
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        
        Vue.watch(contentRef, () => {
            setcontentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function setcontentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        return {
            contentRef,
            contentStyle,
            closePopup
        }
    }
};
