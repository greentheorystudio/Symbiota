const computedValueInputElement = {
    props: {
        label: {
            type: String,
            default: ''
        },
        tabindex: {
            type: Number,
            default: 0
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-input outlined v-model="value" :label="label" bg-color="white" :readonly="true" :tabindex="tabindex" dense></q-input>
    `
};
