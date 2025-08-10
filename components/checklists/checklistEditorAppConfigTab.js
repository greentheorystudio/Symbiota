const checklistEditorAppConfigTab = {
    template: `
        
    `,
    setup() {
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);

        return {
            checklistData
        }
    }
};
