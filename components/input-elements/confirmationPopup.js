const confirmationPopup = {
    props: {
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="q-dialog-plugin q-pa-lg">
                {{ popupText }}
                <q-card-actions align="right">
                    <q-btn color="primary" :label="trueButtonText" @click="processTrueClick" :tabindex="tabindex" />
                    <q-btn v-if="cancelOption" color="primary" :label="falseButtonText" @click="processFalseClick" :tabindex="tabindex" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const callbackFunction = Vue.ref(null);
        const cancelOption = Vue.ref(false);
        const falseButtonText = Vue.ref('Cancel');
        const popupText = Vue.ref(null);
        const showPopup = Vue.ref(false);
        const trueButtonText = Vue.ref('OK');

        function openPopup(text, options = null) {
            if(text){
                popupText.value = text;
                if(options){
                    if(options.hasOwnProperty('cancel')){
                        cancelOption.value = options.cancel;
                    }
                    if(options.hasOwnProperty('falseText')){
                        falseButtonText.value = options['falseText'];
                    }
                    if(options.hasOwnProperty('trueText')){
                        trueButtonText.value = options['trueText'];
                    }
                    if(options.hasOwnProperty('callback')){
                        callbackFunction.value = options['callback'];
                    }
                }
                showPopup.value = true;
            }
        }

        function processFalseClick() {
            if(callbackFunction.value){
                callbackFunction.value(false);
            }
            else{
                context.emit('confirmation:click', false);
            }
            showPopup.value = false;
        }

        function processTrueClick() {
            if(callbackFunction.value){
                callbackFunction.value(true);
            }
            else{
                context.emit('confirmation:click', true);
            }
            showPopup.value = false;
        }

        return {
            cancelOption,
            falseButtonText,
            popupText,
            showPopup,
            trueButtonText,
            openPopup,
            processFalseClick,
            processTrueClick
        }
    }
};
