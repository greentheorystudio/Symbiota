const taxaProfileTaxonNativeStatus = {
    template: `
        <template v-if="nativeStatus">
            <div class="text-weight-bold text-red">
                {{ nativeStatus }}
            </div>
        </template>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const nativeStatus = Vue.ref(null);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        Vue.watch(taxon, () => {
            getNativeStatus();
        });

        function getNativeStatus() {
            const apiUrl = clientRoot + '/api/custom/IRLController.php';
            const formData = new FormData();
            formData.append('tid', taxon.value['tid']);
            formData.append('action', 'getNativeStatus');
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                nativeStatus.value = res;
            });
        }

        Vue.onMounted(() => {
            if(Number(taxon.value['tid']) > 0){
                getNativeStatus();
            }
        });

        return {
            nativeStatus,
            taxon
        }
    }
};
