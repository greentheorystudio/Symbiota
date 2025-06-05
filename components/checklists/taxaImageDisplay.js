const taxaImageDisplay = {
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
        imageData: {
            type: Object,
            default: {}
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
        <div ref="containerRef" class="fit q-pa-sm">
            <template v-if="sortBy === 'family'">
                <div class="column no-wrap q-gutter-sm">
                    <template v-for="family in taxaArr">
                        <div class="full-width column">
                            <div class="full-width text-h6 text-bold">
                                {{ family['familyName'] }}
                            </div>
                            <div class="full-width row q-gutter-sm">
                                <template v-for="taxon in family['taxa']">
                                    <q-card flat bordered class="col-12 col-md-4 col-lg-2 col-xl-1">
                                        <template v-if="imageData.hasOwnProperty(taxon['tidaccepted']) && imageData[taxon['tidaccepted']].length > 0">
                                            <q-img class="rounded-borders" :src="(imageData[taxon['tidaccepted']][0]['url'].startsWith('/') ? (clientRoot + imageData[taxon['tidaccepted']][0]['url']) : imageData[taxon['tidaccepted']][0]['url'])" fit="fill"></q-img>
                                        </template>
                                        <template v-else>
                                            <div class="q-pa-md text-body1 text-bold text-center">Image not available</div>
                                        </template>
                                        <q-card-section class="q-pa-sm">
                                            <div class="text-body1">
                                                <a class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">
                                                    <span class="text-bold text-italic">
                                                        {{ taxon['sciname'] }}
                                                    </span>
                                                    <template v-if="displayAuthors && taxon['author']">
                                                        <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                                    </template>
                                                </a>
                                            </div>
                                            <template v-if="displayCommonNames && taxon['vernacularData'] && taxon['vernacularData'].length > 0">
                                                <div class="text-body1">{{ getVernacularStrFromArr(taxon['vernacularData']) }}</div>
                                            </template>
                                            <div v-if="displaySynonyms && taxon['synonymyData'] && taxon['synonymyData'].length > 0" class="text-italic">
                                                {{ getSynonymStrFromArr(taxon['synonymyData']) }}
                                            </div>
                                            <template v-if="displayVouchers && (taxon['notes'] || (voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0))">
                                                <div v-if="taxon['notes']">
                                                    {{ taxon['notes'] }}
                                                </div>
                                                <div v-if="voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0">
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
                                        </q-card-section>
                                    </q-card>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <template v-else>
                <div class="full-width row q-gutter-sm">
                    <template v-for="taxon in taxaArr">
                        <q-card flat bordered class="col-12 col-md-4 col-lg-2 col-xl-1">
                            <template v-if="imageData.hasOwnProperty(taxon['tidaccepted']) && imageData[taxon['tidaccepted']].length > 0">
                                <q-img class="rounded-borders" :src="(imageData[taxon['tidaccepted']][0]['url'].startsWith('/') ? (clientRoot + imageData[taxon['tidaccepted']][0]['url']) : imageData[taxon['tidaccepted']][0]['url'])" fit="fill"></q-img>
                            </template>
                            <template v-else>
                                <div class="q-pa-md text-body1 text-bold text-center">Image not available</div>
                            </template>
                            <q-card-section class="q-pa-sm">
                                <div class="text-body1">
                                    <a class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">
                                        <span class="text-bold text-italic">
                                            {{ taxon['sciname'] }}
                                        </span>
                                        <template v-if="displayAuthors && taxon['author']">
                                            <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                        </template>
                                    </a>
                                </div>
                                <template v-if="displayCommonNames && taxon['vernacularData'] && taxon['vernacularData'].length > 0">
                                    <div class="text-body1">{{ getVernacularStrFromArr(taxon['vernacularData']) }}</div>
                                </template>
                                <div v-if="displaySynonyms && taxon['synonymyData'] && taxon['synonymyData'].length > 0" class="text-italic">
                                    {{ getSynonymStrFromArr(taxon['synonymyData']) }}
                                </div>
                                <template v-if="displayVouchers && (taxon['notes'] || (voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0))">
                                    <div v-if="taxon['notes']">
                                        {{ taxon['notes'] }}
                                    </div>
                                    <div v-if="voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0">
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
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
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
        const containerRef = Vue.ref(null);
        const expandedVouchers = Vue.ref([]);
        const recordInfoWindowId = Vue.ref(null);
        const showRecordInfoWindow = Vue.ref(false);
        const styleStr = Vue.ref(null);

        Vue.watch(containerRef, () => {
            setContentStyle();
        });

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
            return nameArr.length > 0 ? nameArr.join(', ') : '';
        }

        function openRecordInfoWindow(id) {
            recordInfoWindowId.value = id;
            showRecordInfoWindow.value = true;
        }

        function removeExpandedVoucher(tid){
            const index = expandedVouchers.value.indexOf(tid);
            expandedVouchers.value.splice(index, 1);
        }

        function setContentStyle() {
            styleStr.value = null;
            if(containerRef.value){
                styleStr.value = 'height: ' + (containerRef.value.clientHeight - 30) + 'px;width: ' + containerRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            clientRoot,
            containerRef,
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
