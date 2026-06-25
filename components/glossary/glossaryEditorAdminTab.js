const glossaryEditorAdminTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-h6 text-bold">Delete glossary term</div>
                    <div v-if="imageArr.length > 0" class="text-red">
                        All images associated with this term must be removed before it can be deleted
                    </div>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="negative" @click="deleteGlossaryTerm();" label="Delete" :disabled="imageArr.length > 0" aria-label="Delete term" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup
    },
    setup(_, context) {
        const { showNotification } = useCore();
        const glossaryStore = useGlossaryStore();

        const confirmationPopupRef = Vue.ref(null);
        const imageArr = Vue.computed(() => glossaryStore.getGlossaryImageArr);
        
        function deleteGlossaryTerm() {
            const confirmText = 'Are you sure you want to delete this term? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    glossaryStore.deleteGlossaryRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Term deleted successfully.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the term');
                        }
                    });
                }
            }});
        }

        return {
            confirmationPopupRef,
            imageArr,
            deleteGlossaryTerm
        }
    }
};
