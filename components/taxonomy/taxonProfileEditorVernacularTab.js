const taxonProfileEditorVernacularTab = {
    template: `
        
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const taxaStore = useTaxaStore();



        return {

        }
    }
};
