const datePicker = {
    props: {
        inputValue: {
            type: String,
            default: null
        }
    },
    template: `
        <q-input outlined v-model="pickerValue" @update:model-value="processChange">
            <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale" class="z-max">
                        <q-date v-model="pickerValue" mask="YYYY-MM-DD" minimal>
                            <div class="row items-center justify-end">
                                <q-btn v-close-popup label="Close" color="primary" flat></q-btn>
                            </div>
                        </q-date>
                    </q-popup-proxy>
                </q-icon>
            </template>
        </q-input>
    `,
    setup(props, context) {
        const propsRefs = Vue.toRefs(props);
        const pickerValue = Vue.ref(null);

        Vue.watch(propsRefs.inputValue, () => {
            pickerValue.value = propsRefs.inputValue.value;
        });

        function processChange(value) {
            context.emit('date-picker-change', value);
        }

        Vue.onMounted(() => {
            pickerValue.value = propsRefs.inputValue.value;
        });

        return {
            pickerValue,
            processChange
        }
    }
};
