const imageTagSelector = {
    props: {
        disable: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Image Tags'
        },
        tabindex: {
            type: Number,
            default: 1
        },
        tagArr: {
            type: Array
        }
    },
    template: `
        <q-select ref="inputRef" v-model="tagArr" outlined dense options-dense clearable multiple use-chips popup-content-class="z-max" behavior="menu" bg-color="white" :options="imageTagOptions" @clear="clearAction" @update:model-value="processChange" :label="label" :tabindex="tabindex" :disable="disable"></q-select>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();

        const clearInput = Vue.ref(false);
        const imageTagOptions = baseStore.getImageTagOptions;
        let inputRef = Vue.ref(null);

        function clearAction() {
            clearInput.value = true;
        }

        function processChange(tagArrVal) {
            context.emit('update:value', (tagArrVal ? tagArrVal : []));
        }

        return {
            imageTagOptions,
            inputRef,
            clearAction,
            processChange
        }
    }
};
