const imageTagSelector = {
    props: {
        tagArr: {
            type: Array
        },
        label: {
            type: String,
            default: 'Image Tags'
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-select ref="inputRef" v-model="tagArr" outlined dense options-dense clearable multiple use-chips popup-content-class="z-max" bg-color="white" :options="imageTagOptions" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();

        const clearInput = Vue.ref(false);
        const imageTagOptions = baseStore.getImageTagOptions;
        let inputRef = Vue.ref(null);

        function clearAction() {
            clearInput.value = true;
        }

        function processChange(languageObj) {
            const newValArr = [];
            if(languageObj){
                languageObj.forEach((lang) => {
                    const existingObj = newValArr.find(obj => obj['name'] === lang['name']);
                    if(!existingObj){
                        newValArr.push(lang);
                    }
                });
            }
            context.emit('update:language', newValArr);
            inputRef.value.updateInputValue('');
        }

        return {
            imageTagOptions,
            inputRef,
            clearAction,
            processChange
        }
    }
};
