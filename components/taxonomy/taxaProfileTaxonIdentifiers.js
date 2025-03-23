const taxaProfileTaxonIdentifiers = {
    template: `
        <template v-if="taxaIdentifiers.length > 0">
            <div>
                {{ identifierStr }}
            </div>
        </template>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const identifierStr = Vue.computed(() => {
            let identifierStr = '';
            const strPartArr = [];
            if(taxaIdentifiers.value.length > 0){
                taxaIdentifiers.value.forEach((identifier) => {
                    if(taxonomicTags.hasOwnProperty(identifier.name)){
                        const idStr = taxonomicTags[identifier.name] + ': ' + identifier.identifier;
                        strPartArr.push(idStr);
                    }
                });
                identifierStr = strPartArr.join('; ') + ';';
            }
            return identifierStr;
        });
        const taxaIdentifiers = Vue.computed(() => taxaStore.getTaxaIdentifiers);
        const taxonomicTags = baseStore.getTaxonomicTags;

        return {
            identifierStr,
            taxaIdentifiers
        }
    }
};
