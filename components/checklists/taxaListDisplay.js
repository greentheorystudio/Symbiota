const taxaListDisplay = {
    props: {
        displayAuthors: {
            type: Boolean,
            default: false
        },
        displayCommonNames: {
            type: Boolean,
            default: false
        },
        displaySynonyms: {
            type: Boolean,
            default: false
        },
        displayVouchers: {
            type: Boolean,
            default: false
        },
        sortBy: {
            type: String,
            default: 'family'
        },
        taxaArr: {
            type: Array,
            default: []
        },
        voucherData: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="fit q-pa-md column q-gutter-sm">
            <template v-if="sortBy === 'family'">
                <template v-for="family in taxaArr">
                    <div class="full-width column">
                        <div class="full-width text-h6 text-bold">
                            {{ family['familyName'] }}
                        </div>
                        <template v-for="taxon in family['taxa']">
                            <div class="q-pl-sm q-mb-xs full-width column">
                                <div class="text-body1">
                                    <a class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">
                                        <span class="text-bold text-italic">
                                            {{ taxon['sciname'] }}
                                        </span>
                                        <template v-if="displayAuthors && taxon['author']">
                                            <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                        </template>
                                    </a>
                                    <template v-if="displayCommonNames && taxon['vernacularData'] && taxon['vernacularData'].length > 0">
                                        <span>{{ getVernacularStrFromArr(taxon['vernacularData']) }}</span>
                                    </template>
                                </div>
                                <div v-if="displaySynonyms && taxon['synonymyData'] && taxon['synonymyData'].length > 0" class="q-ml-md text-italic">
                                    {{ getSynonymStrFromArr(taxon['synonymyData']) }}
                                </div>
                                <template v-if="displayVouchers && (taxon['notes'] || (voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0))">
                                    <div v-if="taxon['notes']" class="q-ml-md">
                                        {{ taxon['notes'] }}
                                    </div>
                                    <div v-if="voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0" class="q-ml-md">
                                        <template v-for="voucher in getAdjustedVoucherArr(taxon['tid'], voucherData[taxon['tid']])">
                                            <span class="cursor-pointer" @click="openRecordInfoWindow(voucher['occid']);">{{ voucher['label'] + '; ' }}</span>
                                        </template>
                                        <template v-if="voucherData[taxon['tid']].length > 10 && !expandedVouchers.includes(taxon['tid'])">
                                            <span class="cursor-pointer" @click="addExpandedVoucher(taxon['tid']);">more...</span>
                                        </template>
                                        <template v-else-if="voucherData[taxon['tid']].length > 10 && expandedVouchers.includes(taxon['tid'])">
                                            <span class="cursor-pointer" @click="removeExpandedVoucher(taxon['tid']);">less...</span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </template>
            <template v-else>
                <template v-for="taxon in taxaArr">
                    <div class="q-pl-sm q-mb-xs full-width column">
                        <div class="text-body1">
                            <a class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">
                                <span class="text-bold text-italic">
                                    {{ taxon['sciname'] }}
                                </span>
                                <template v-if="displayAuthors && taxon['author']">
                                    <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                </template>
                            </a>
                            <template v-if="displayCommonNames && taxon['vernacularData'] && taxon['vernacularData'].length > 0">
                                <span>{{ getVernacularStrFromArr(taxon['vernacularData']) }}</span>
                            </template>
                        </div>
                        <div v-if="displaySynonyms && taxon['synonymyData'] && taxon['synonymyData'].length > 0" class="q-ml-md text-italic">
                            {{ getSynonymStrFromArr(taxon['synonymyData']) }}
                        </div>
                        <template v-if="displayVouchers && (taxon['notes'] || (voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0))">
                            <div v-if="taxon['notes']" class="q-ml-md">
                                {{ taxon['notes'] }}
                            </div>
                            <div v-if="voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0" class="q-ml-md">
                                <template v-for="voucher in getAdjustedVoucherArr(taxon['tid'], voucherData[taxon['tid']])">
                                    <span class="cursor-pointer" @click="openRecordInfoWindow(voucher['occid']);">{{ voucher['label'] + '; ' }}</span>
                                </template>
                                <template v-if="voucherData[taxon['tid']].length > 10 && !expandedVouchers.includes(taxon['tid'])">
                                    <span class="cursor-pointer" @click="addExpandedVoucher(taxon['tid']);">more...</span>
                                </template>
                                <template v-else-if="voucherData[taxon['tid']].length > 10 && expandedVouchers.includes(taxon['tid'])">
                                    <span class="cursor-pointer" @click="removeExpandedVoucher(taxon['tid']);">less...</span>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </template> 
        </div>
        <template v-if="recordInfoWindowId">
            <occurrence-info-window-popup :occurrence-id="recordInfoWindowId" :show-popup="showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
        </template>
    `,
    components: {
        'occurrence-info-window-popup': occurrenceInfoWindowPopup
    },
    setup() {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const expandedVouchers = Vue.ref([]);
        const recordInfoWindowId = Vue.ref(null);
        const showRecordInfoWindow = Vue.ref(false);

        function addExpandedVoucher(tid){
            expandedVouchers.value.push(tid);
        }

        function closeRecordInfoWindow(){
            recordInfoWindowId.value = null;
            showRecordInfoWindow.value = false;
        }

        function getAdjustedVoucherArr(tid, voucherArr) {
            if(voucherArr.length > 10 && !expandedVouchers.value.includes(tid)){
                return voucherArr.slice(0, 10);
            }
            else{
                return voucherArr;
            }
        }

        function getSynonymStrFromArr(synonymArr) {
            const nameArr = [];
            synonymArr.forEach(synonym => {
                if(synonym['sciname']){
                    nameArr.push(synonym['sciname']);
                }
            });
            return nameArr.length > 0 ? ('[' + nameArr.join(', ') + ']') : '';
        }

        function getVernacularStrFromArr(vernacularArr) {
            const nameArr = [];
            vernacularArr.forEach(vernacular => {
                if(vernacular['vernacularname']){
                    nameArr.push(vernacular['vernacularname']);
                }
            });
            return nameArr.length > 0 ? (' - ' + nameArr.join(', ')) : '';
        }

        function openRecordInfoWindow(id) {
            recordInfoWindowId.value = id;
            showRecordInfoWindow.value = true;
        }

        function removeExpandedVoucher(tid){
            const index = expandedVouchers.value.indexOf(tid);
            expandedVouchers.value.splice(index, 1);
        }

        return {
            clientRoot,
            expandedVouchers,
            recordInfoWindowId,
            showRecordInfoWindow,
            addExpandedVoucher,
            closeRecordInfoWindow,
            getAdjustedVoucherArr,
            getSynonymStrFromArr,
            getVernacularStrFromArr,
            openRecordInfoWindow,
            removeExpandedVoucher
        }
    }
};
