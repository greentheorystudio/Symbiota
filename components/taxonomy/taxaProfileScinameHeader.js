const taxaProfileScinameHeader = {
    props: [
        'taxon',
        'style-class',
        'parent-link'
    ],
    template: `
        <div id="scinamecontainer">
            <span id="sciname" :class="styleClass">{{ taxon.sciName }}</span> <span id="sciname-author">{{ taxon.author }}</span>
            <a :href="parentLink" id="parentlink" title="Go to Parent">
                <q-icon name="fas fa-level-up-alt" size="15px" class="cursor-pointer" />
            </a>
            <template v-if="taxon.submittedTid !== taxon.tid">
                <span class="redirected-from"> (redirected from: <span class="text-italic">{{ taxon.submittedSciName }}</span>)</span>
            </template>
        </div>
    `
};
