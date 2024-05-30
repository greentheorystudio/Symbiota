const taxaProfileTaxonNativeStatus = {
    props: [
        'taxon'
    ],
    watch: {
        taxon: function(){
            this.getNativeStatus();
        }
    },
    template: `
        <template v-if="nativeStatus">
            <div class="text-weight-bold text-red">
                {{ nativeStatus }}
            </div>
        </template>
    `,
    data() {
        return {
            nativeStatus: Vue.ref(null)
        }
    },
    mounted(){
        this.getNativeStatus();
    },
    methods: {
        getNativeStatus() {
            const apiUrl = CLIENT_ROOT + '/api/custom/IRLController.php';
            const formData = new FormData();
            formData.append('tid', this.taxon['tid']);
            formData.append('action', 'getNativeStatus');
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        this.nativeStatus = res;
                    });
                }
            });
        }
    }
};
