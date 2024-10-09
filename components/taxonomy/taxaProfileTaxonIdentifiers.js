const taxaProfileTaxonIdentifiers = {
    props: {
        identifiers: {
            type: Array,
            default: []
        }
    },
    template: `
        <template v-if="identifiers.length">
            <div>
                {{ identifierStr }}
            </div>
        </template>
    `,
    setup(props) {
        const baseStore = useBaseStore();

        const identifierStr = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const taxonomicTags = baseStore.getTaxonomicTags;

        Vue.watch(propsRefs.identifiers, () => {
            processIdentifiers();
        });

        function processIdentifiers() {
            const strPartArr = [];
            identifierStr.value = '';
            if(props.identifiers.length > 0){
                props.identifiers.forEach((identifier) => {
                    if(taxonomicTags.hasOwnProperty(identifier.name)){
                        const idStr = taxonomicTags[identifier.name] + ': ' + identifier.identifier;
                        strPartArr.push(idStr);
                    }
                });
                identifierStr.value = strPartArr.join('; ') + ';';
            }
        }

        Vue.onMounted(() => {
            processIdentifiers();
        });

        return {
            identifierStr
        }
    }
};
