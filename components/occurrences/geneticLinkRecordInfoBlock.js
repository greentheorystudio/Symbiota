const geneticLinkRecordInfoBlock = {
    props: {
        geneticLinkageData: {
            type: Object,
            default: null
        },
        editor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card>
            <q-card-section>
                <div class="column">
                    <q-resize-observer @resize="setLineStyle" />
                    <div class="row justify-end">
                        <div v-if="editor" class="row justify-end">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(geneticLinkageData['idoccurgenetic']);" icon="fas fa-edit" dense aria-label="Edit genetic record linkage" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit genetic record linkage
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <div v-if="geneticLinkageData['sourcename']">
                        <span class="text-bold q-mr-sm">Source Name:</span>{{ geneticLinkageData['sourcename'] }}
                    </div>
                    <div v-if="geneticLinkageData['sourceidentifier']">
                        <span class="text-bold q-mr-sm">Source Identifier:</span>{{ geneticLinkageData['sourceidentifier'] }}
                    </div>
                    <div v-if="geneticLinkageData['description']">
                        <span class="text-bold q-mr-sm">Description:</span>{{ geneticLinkageData['description'] }}
                    </div>
                    <div v-if="geneticLinkageData['targetgene']">
                        <span class="text-bold q-mr-sm">Target Gene (Locus):</span>{{ geneticLinkageData['targetgene'] }}
                    </div>
                    <div v-if="geneticLinkageData['targetsubfragment']">
                        <span class="text-bold q-mr-sm">Target Subfragment:</span>{{ geneticLinkageData['targetsubfragment'] }}
                    </div>
                    <div v-if="geneticLinkageData['dnasequence']" class="inner-text-wrap" :style="lineStyle">
                        <span class="text-bold q-mr-sm">DNA Sequence:</span>{{ geneticLinkageData['dnasequence'] }}
                    </div>
                    <div v-if="geneticLinkageData['url']">
                        <span class="text-bold q-mr-sm">URL:</span> <a :href="geneticLinkageData['url']" target="_blank" aria-label="External link: View resource - Opens in separate tab" tabindex="0">{{ geneticLinkageData['url'] }}</a>
                    </div>
                    <div v-if="geneticLinkageData['notes']">
                        <span class="text-bold q-mr-sm">Notes:</span>{{ geneticLinkageData['notes'] }}
                    </div>
                    <div v-if="geneticLinkageData['authors']">
                        <span class="text-bold q-mr-sm">Authors:</span>{{ geneticLinkageData['authors'] }}
                    </div>
                    <div v-if="geneticLinkageData['authorinstitution']">
                        <span class="text-bold q-mr-sm">Author Institution:</span>{{ geneticLinkageData['authorinstitution'] }}
                    </div>
                    <div v-if="geneticLinkageData['reference']">
                        <span class="text-bold q-mr-sm">Reference:</span>{{ geneticLinkageData['reference'] }}
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup(_, context) {
        const containerWidth = Vue.inject('containerWidth');
        const lineStyle = Vue.ref(null);

        function openEditorPopup(id) {
            context.emit('open:genetic-link-editor', id);
        }

        function setLineStyle() {
            lineStyle.value = 'width: ' + (containerWidth.value - 200) + 'px;';
        }

        return {
            lineStyle,
            openEditorPopup,
            setLineStyle
        }
    }
};
