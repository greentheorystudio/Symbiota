const searchCollectionsBlock = {
    template: `
        <div class="q-pa-md">
            <collection-checkbox-selector :value-arr="searchTerms.db" @update:value="(value) => updateSearchTerms('db', value)"></collection-checkbox-selector>
        </div>
    `,
    components: {
        'collection-checkbox-selector': collectionCheckboxSelector
    },
    setup() {
        const searchStore = useSearchStore();

        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);

        const updateSearchTerms = Vue.inject('updateSearchTerms');

        return {
            searchTerms,
            updateSearchTerms
        }
    }
};
