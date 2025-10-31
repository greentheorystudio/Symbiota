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
        editing: {
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
                                    <q-card role="link" flat bordered class="cursor-pointer" @click="openTaxaProfileTab(taxon['tid']);" :style="cardStyle" :aria-label="( taxon['sciname'] + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                        <template v-if="imageData.hasOwnProperty(taxon['tidaccepted']) && imageData[taxon['tidaccepted']].length > 0">
                                            <q-img class="rounded-borders" :height="imageHeight" :src="(imageData[taxon['tidaccepted']][0]['url'].startsWith('/') ? (clientRoot + imageData[taxon['tidaccepted']][0]['url']) : imageData[taxon['tidaccepted']][0]['url'])" fit="scale-down" :alt="(imageData[taxon['tidaccepted']][0]['alttext'] ? imageData[taxon['tidaccepted']][0]['alttext'] : taxon['sciname'])"></q-img>
                                        </template>
                                        <template v-else>
                                            <div class="column justify-center" :style="('height: ' + imageHeight + ';')">
                                                <div class="text-body1 text-bold text-center">Image not available</div>
                                            </div>
                                        </template>
                                        <q-card-section class="q-pa-sm">
                                            <div class="text-body1">
                                                <span class="text-bold text-italic">
                                                    {{ taxon['sciname'] }}
                                                </span>
                                                <template v-if="displayAuthors && taxon['author']">
                                                    <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                                </template>
                                                <template v-if="editing">
                                                    <span class="q-ml-sm">
                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openEditorPopup(taxon['cltlid']);" icon="far fa-edit" dense aria-label="Edit this taxon" tabindex="0">
                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                Edit this taxon
                                                            </q-tooltip>
                                                        </q-btn>
                                                    </span>
                                                </template>
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
                                                        <span role="button" class="cursor-pointer" @click="openRecordInfoWindow(voucher['occid']);" aria-label="See record details" tabindex="0">{{ voucher['label'] + '; ' }}</span>
                                                    </template>
                                                    <template v-if="voucherData[taxon['tid']].length > 10 && !expandedVouchers.includes(taxon['tid'])">
                                                        <span role="button" class="cursor-pointer" @click="addExpandedVoucher(taxon['tid']);" aria-label="Show more" tabindex="0">more...</span>
                                                    </template>
                                                    <template v-else-if="voucherData[taxon['tid']].length > 10 && expandedVouchers.includes(taxon['tid'])">
                                                        <span role="button" class="cursor-pointer" @click="removeExpandedVoucher(taxon['tid']);" aria-label="Show less" tabindex="0">less...</span>
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
                        <q-card role="link" flat bordered class="cursor-pointer" @click="openTaxaProfileTab(taxon['tid'])" :style="cardStyle" :aria-label="( taxon['sciname'] + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                            <template v-if="imageData.hasOwnProperty(taxon['tidaccepted']) && imageData[taxon['tidaccepted']].length > 0">
                                <q-img class="rounded-borders" :height="imageHeight" :src="(imageData[taxon['tidaccepted']][0]['url'].startsWith('/') ? (clientRoot + imageData[taxon['tidaccepted']][0]['url']) : imageData[taxon['tidaccepted']][0]['url'])" fit="scale-down" :alt="(imageData[taxon['tidaccepted']][0]['alttext'] ? imageData[taxon['tidaccepted']][0]['alttext'] : taxon['sciname'])"></q-img>
                            </template>
                            <template v-else>
                                <div class="column justify-center" :style="('height: ' + imageHeight + ';')">
                                    <div class="text-body1 text-bold text-center">Image not available</div>
                                </div>
                            </template>
                            <q-card-section class="q-pa-sm">
                                <div class="text-body1 text-black">
                                    <span class="text-bold text-italic">
                                        {{ taxon['sciname'] }}
                                    </span>
                                    <template v-if="displayAuthors && taxon['author']">
                                        <span class="q-ml-sm text-bold">{{ taxon['author'] }}</span>
                                    </template>
                                    <template v-if="editing">
                                        <span class="q-ml-sm">
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openEditorPopup(taxon['cltlid']);" icon="far fa-edit" dense aria-label="Edit this taxon" tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Edit this taxon
                                                </q-tooltip>
                                            </q-btn>
                                        </span>
                                    </template>
                                </div>
                                <template v-if="displayCommonNames && taxon['vernacularData'] && taxon['vernacularData'].length > 0">
                                    <div class="text-body1">{{ getVernacularStrFromArr(taxon['vernacularData']) }}</div>
                                </template>
                                <div v-if="displaySynonyms && taxon['synonymyData'] && taxon['synonymyData'].length > 0" class="text-italic">
                                    {{ getSynonymStrFromArr(taxon['synonymyData']) }}
                                </div>
                                <template v-if="displayVouchers">
                                    <div v-if="taxon['habitat'] || taxon['abundance'] || taxon['notes'] || taxon['source']" class="q-ml-md">
                                        <span v-if="taxon['habitat']">{{ taxon['habitat'] + ((taxon['abundance'] || taxon['notes'] || taxon['source']) ? ', ' : '') }}</span>
                                        <span v-if="taxon['abundance']">{{ taxon['abundance'] + ((taxon['notes'] || taxon['source']) ? ', ' : '') }}</span>
                                        <span v-if="taxon['notes']">{{ taxon['notes'] + (taxon['source'] ? ', ' : '') }}</span>
                                        <span v-if="taxon['source']"><span class="text-bold">Source: </span> {{ taxon['source'] }}</span>
                                    </div>
                                    <div v-if="voucherData.hasOwnProperty(taxon['tid']) && voucherData[taxon['tid']].length > 0">
                                        <template v-for="voucher in getAdjustedVoucherArr(taxon['tid'], voucherData[taxon['tid']])">
                                            <span role="button" class="cursor-pointer" @click="openRecordInfoWindow(voucher['occid']);" aria-label="See record details" tabindex="0">{{ voucher['label'] + '; ' }}</span>
                                        </template>
                                        <template v-if="voucherData[taxon['tid']].length > 10 && !expandedVouchers.includes(taxon['tid'])">
                                            <span role="button" class="cursor-pointer" @click="addExpandedVoucher(taxon['tid']);" aria-label="Show more" tabindex="0">more...</span>
                                        </template>
                                        <template v-else-if="voucherData[taxon['tid']].length > 10 && expandedVouchers.includes(taxon['tid'])">
                                            <span role="button" class="cursor-pointer" @click="removeExpandedVoucher(taxon['tid']);" aria-label="Show less" tabindex="0">less...</span>
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
    setup(_, context) {
        const baseStore = useBaseStore();

        const cardStyle = Vue.ref(null);
        const clientRoot = baseStore.getClientRoot;
        const containerRef = Vue.ref(null);
        const editorOpening = Vue.ref(false);
        const expandedVouchers = Vue.ref([]);
        const imageHeight = Vue.ref(null);
        const recordInfoWindowId = Vue.ref(null);
        const showRecordInfoWindow = Vue.ref(false);

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

        function openEditorPopup(id) {
            editorOpening.value = true;
            context.emit('open:checklist-taxa-editor', id);
        }

        function openRecordInfoWindow(id) {
            recordInfoWindowId.value = id;
            showRecordInfoWindow.value = true;
        }

        function openTaxaProfileTab(tid) {
            if(!editorOpening.value){
                window.open((clientRoot + '/taxa/index.php?taxon=' + tid), '_blank');
            }
            else{
                editorOpening.value = false;
            }
        }

        function removeExpandedVoucher(tid){
            const index = expandedVouchers.value.indexOf(tid);
            expandedVouchers.value.splice(index, 1);
        }

        function setContentStyle() {
            cardStyle.value = null;
            imageHeight.value = null;
            if(containerRef.value){
                let cardDim;
                if(containerRef.value.clientWidth > 900){
                    cardDim = (containerRef.value.clientWidth / 4) - 30;
                }
                else if(containerRef.value.clientWidth > 600){
                    cardDim = (containerRef.value.clientWidth / 3) - 30;
                }
                else if(containerRef.value.clientWidth > 400){
                    cardDim = (containerRef.value.clientWidth / 2) - 30;
                }
                else{
                    cardDim = containerRef.value.clientWidth - 30;
                }
                cardStyle.value = 'width: ' + cardDim + 'px;';
                imageHeight.value = cardDim + 'px';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            cardStyle,
            clientRoot,
            containerRef,
            expandedVouchers,
            imageHeight,
            recordInfoWindowId,
            showRecordInfoWindow,
            addExpandedVoucher,
            closeRecordInfoWindow,
            getAdjustedVoucherArr,
            getSynonymStrFromArr,
            getVernacularStrFromArr,
            openEditorPopup,
            openRecordInfoWindow,
            openTaxaProfileTab,
            removeExpandedVoucher
        }
    }
};
