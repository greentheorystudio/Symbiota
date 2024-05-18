const configuredDataFieldModule = {
    props: {
        data: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        fields: {
            type: Object,
            default: null
        },
        fieldDefinitions: {
            type: Object,
            default: null
        }
    },
    template: `
        
    `,
    components: {
        'date-input-element': dateInputElement,
        'text-field-input-element': textFieldInputElement,
        'time-input-element': timeInputElement
    },
    setup(props, context) {

        return {

        }
    }
};
