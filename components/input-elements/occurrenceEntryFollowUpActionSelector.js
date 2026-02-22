const occurrenceEntryFollowUpActionSelector = {
    props: {
        selectedAction: {
            type: String,
            default: 'remain'
        },
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="actionOptions" option-value="value" option-label="label" label="Follow Up Action" popup-content-class="z-top" @update:model-value="changeAction" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup(props, context) {
        const actionOptions = [
            {value: 'remain', label: 'Remain on editing page'},
            {value: 'newrecord', label: 'Go to new record'},
            {value: 'newrecordlocation', label: 'Go to new record at same location'},
            {value: 'newrecordevent', label: 'Go to new record at same event'},
            {value: 'newrecordclone', label: 'Go to new cloned record'}
        ];
        const propsRefs = Vue.toRefs(props);
        const selectedOption = Vue.ref({});

        Vue.watch(propsRefs.selectedAction, () => {
            setSelectedOption();
        });

        function changeAction(val) {
            selectedOption.value = val;
            context.emit('change-occurrence-entry-follow-up-action', val.value);
        }

        function setSelectedOption() {
            if(props.selectedAction !== 'none'){
                selectedOption.value = actionOptions.find(opt => opt['value'] === props.selectedAction);
            }
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });

        return {
            actionOptions,
            selectedOption,
            changeAction
        }
    }
};
