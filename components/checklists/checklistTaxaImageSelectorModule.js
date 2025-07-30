const checklistTaxaImageSelectorModule = {
    template: `
        <div ref="contentRef" class="fit">
            <template v-if="displayArr.length > 0">
                <q-scroll-area class="q-px-md" :style="scrollerStyle">
                    <div class="row no-wrap q-gutter-md q-pt-md">
                        <q-card v-for="image in displayArr" :key="image" class="q-ma-md" :style="cardStyle">
                            <q-img :src="image.url" :height="imageHeight" fit="scale-down" :title="image.caption" :alt="image.imgid"></q-img>
                            <div class="q-pa-sm">
                                <checkbox-input-element :value="taggedImageIdArr.includes(Number(image.imgid))" @update:value="(value) => processImageSelectionChange(image.imgid, value)"></checkbox-input-element>
                            </div>
                        </q-card>
                    </div>
                </q-scroll-area>
            </template>
            <template v-else>
                <div class="fit column justify-center">
                    <div class="text-body1 text-bold text-center">No images available for this taxon</div>
                </div>
            </template>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const checklistStore = useChecklistStore();

        const cardStyle = Vue.ref(null);
        const checklistTaxaImageOptionArr = Vue.computed(() => checklistStore.getChecklistTaxaImageOptionArr);
        const checklistTaxaTaggedImageArr = Vue.computed(() => checklistStore.getChecklistTaxaTaggedImageArr);
        const contentRef = Vue.ref(null);
        const displayArr = Vue.computed(() => {
            const returnArr = [];
            taggedImageIdArr.value.length = 0;
            checklistTaxaTaggedImageArr.value.forEach((image) => {
                taggedImageIdArr.value.push(Number(image['imgid']));
                returnArr.push(image);
            });
            checklistTaxaImageOptionArr.value.forEach((image) => {
                if(!taggedImageIdArr.value.includes(Number(image['imgid']))){
                    returnArr.push(image);
                }
            });
            return returnArr;
        });
        const imageHeight = Vue.ref(null);
        const scrollerStyle = Vue.ref(null);
        const taggedImageIdArr = Vue.ref([]);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function processImageSelectionChange(imgid, value) {
            if(Number(value) === 1){
                checklistStore.addCurrentChecklistTaxonImageTag(imgid, (res) => {
                    if(res !== 1){
                        showNotification('negative', 'There was an error selecting the image');
                    }
                });
            }
            else{
                checklistStore.deleteCurrentChecklistTaxonImageTag(imgid, (res) => {
                    if(res !== 1){
                        showNotification('negative', 'There was an error deselecting the image');
                    }
                });
            }
        }

        function setContentStyle() {
            scrollerStyle.value = null;
            cardStyle.value = null;
            imageHeight.value = null;
            if(contentRef.value){
                scrollerStyle.value = 'height: ' + contentRef.value.clientHeight + 'px;';
                cardStyle.value = 'height: ' + (contentRef.value.clientHeight - 60) + 'px;width: ' + (contentRef.value.clientHeight - 60) + 'px;';
                imageHeight.value = (contentRef.value.clientHeight - 100) + 'px';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            cardStyle,
            contentRef,
            displayArr,
            imageHeight,
            scrollerStyle,
            taggedImageIdArr,
            processImageSelectionChange
        }
    }
};
