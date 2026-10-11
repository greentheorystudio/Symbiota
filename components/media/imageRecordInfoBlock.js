const imageRecordInfoBlock = {
    props: {
        collId: {
            type: Number,
            default: 0
        },
        imageData: {
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
                <div ref="contentContainerRef" class="row justify-between q-col-gutter-sm">
                    <div :style="imageSectionStyle" class="column no-wrap">
                        <div class="row justify-center">
                            <q-img :src="(imageData.url.startsWith('/') ? (clientRoot + imageData.url) : imageData.url)" :fit="contain"></q-img>
                        </div>
                        <div class="q-mt-xs row justify-center q-gutter-sm text-bold">
                            <span v-if="imageData.thumbnailurl">
                                <a :href="(imageData.thumbnailurl.startsWith('/') ? (clientRoot + imageData.thumbnailurl) : imageData.thumbnailurl)" target="_blank" aria-label="View thumbnail image - Opens in separate tab" tabindex="0">Thumbnail</a>
                            </span>
                            <span v-if="imageData.url">
                                <a :href="(imageData.url.startsWith('/') ? (clientRoot + imageData.url) : imageData.url)" target="_blank" aria-label="View web image - Opens in separate tab" tabindex="0">Web</a>
                            </span>
                        </div>
                        <div class="row justify-center q-gutter-sm text-bold">
                            <span v-if="imageData.originalurl">
                                <a :href="(imageData.originalurl.startsWith('/') ? (clientRoot + imageData.originalurl) : imageData.originalurl)" target="_blank" aria-label="View original image - Opens in separate tab" tabindex="0">Original</a>
                            </span>
                            <span v-if="imageData.sourceurl">
                                <a :href="(imageData.sourceurl.startsWith('/') ? (clientRoot + imageData.sourceurl) : imageData.sourceurl)" target="_blank" aria-label="View source image - Opens in separate tab" tabindex="0">Source</a>
                            </span>
                        </div>
                    </div>
                    <div :style="infoSectionStyle" class="q-pl-sm column no-wrap">
                        <template v-if="editor">
                            <div class="q-mb-xs row">
                                <div class="col-2">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="imageData.sortsequence" min-value="1" :clearable="false" @update:value="processSortSequenceChange"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="imageData.url" class="text-wrap">
                                <span class="text-bold">URL: </span>{{ imageData.url }}
                            </div>
                            <div v-if="imageData.thumbnailurl" class="text-wrap">
                                <span class="text-bold">Thumbnail URL: </span>{{ imageData.thumbnailurl }}
                            </div>
                            <div v-if="imageData.originalurl" class="text-wrap">
                                <span class="text-bold">Original URL: </span>{{ imageData.originalurl }}
                            </div>
                        </template>
                        <div v-if="imageData.sourceurl" class="text-wrap">
                            <span class="text-bold">Source URL: </span>{{ imageData.sourceurl }}
                        </div>
                        <div v-if="imageData.photographer" class="text-wrap">
                            <span class="text-bold">Photographer: </span>{{ imageData.photographer }}
                        </div>
                        <div v-if="imageData.caption" class="text-wrap">
                            <span class="text-bold">Caption: </span>{{ imageData.caption }}
                        </div>
                        <div v-if="imageData.alttext" class="text-wrap">
                            <span class="text-bold">Image Alt-Text: </span>{{ imageData.alttext }}
                        </div>
                        <div v-if="imageData.owner" class="text-wrap">
                            <span class="text-bold">Owner: </span>{{ imageData.owner }}
                        </div>
                        <div v-if="imageData.copyright" class="text-wrap">
                            <span class="text-bold">Copyright: </span>{{ imageData.copyright }}
                        </div>
                        <div v-if="imageData.rights" class="text-wrap">
                            <span class="text-bold">Rights: </span>{{ imageData.rights }}
                        </div>
                        <div v-if="imageData.accessrights" class="text-wrap">
                            <span class="text-bold">Access Rights: </span>{{ imageData.accessrights }}
                        </div>
                        <div v-if="imageData.locality" class="text-wrap">
                            <span class="text-bold">Locality: </span>{{ imageData.locality }}
                        </div>
                        <div v-if="imageData.notes" class="text-wrap">
                            <span class="text-bold">Notes: </span>{{ imageData.notes }}
                        </div>
                        <div v-if="imageData.anatomy" class="text-wrap">
                            <span class="text-bold">Anatomy: </span>{{ imageData.anatomy }}
                        </div>
                        <div v-if="imageData.dynamicproperties" class="text-wrap">
                            <span class="text-bold">Dynamic Properties: </span>{{ imageData.dynamicproperties }}
                        </div>
                        <div v-if="imageData.referenceurl" class="text-wrap">
                            <span class="text-bold">Reference URL: </span>{{ imageData.referenceurl }}
                        </div>
                        <div v-if="imageData.tagArr && imageData.tagArr.length > 0" class="text-wrap">
                            <span class="text-bold">Tags: </span>{{ JSON.stringify(imageData.tagArr) }}
                        </div>
                    </div>
                    <div v-if="editor" :style="buttonSectionStyle" class="row justify-end">
                        <div>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(imageData['imgid']);" icon="fas fa-edit" dense aria-label="Edit image record" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit image record
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const baseStore = useBaseStore();
        const imageStore = useImageStore();

        const clientRoot = baseStore.getClientRoot;
        const contentContainerRef = Vue.ref(null);
        const buttonSectionStyle = Vue.ref(null);
        const imageSectionStyle = Vue.ref(null);
        const infoSectionStyle = Vue.ref(null);

        Vue.watch(contentContainerRef, () => {
            if(contentContainerRef.value){
                setStyles();
            }
        });

        function openEditorPopup(id) {
            context.emit('open:image-editor', id);
        }

        function processSortSequenceChange(value) {
            if(props.editor){
                imageStore.updateImageSortSequence(props.collId, props.imageData['imgid'], value, (res) => {
                    if(res === 1){
                        context.emit('image:updated');
                    }
                });
            }
        }

        function setStyles() {
            buttonSectionStyle.value = null;
            imageSectionStyle.value = null;
            infoSectionStyle.value = null;
            if(contentContainerRef.value){
                const widthSection = Math.trunc((contentContainerRef.value.clientWidth) / 12);
                buttonSectionStyle.value = 'width:' + widthSection + 'px;';
                imageSectionStyle.value = 'width:' + (widthSection * 3) + 'px;';
                infoSectionStyle.value = 'width:' + (widthSection * 8) + 'px;';
            }
        }

        Vue.onMounted(() => {
            setStyles();
            window.addEventListener('resize', setStyles);
        });

        return {
            buttonSectionStyle,
            clientRoot,
            contentContainerRef,
            imageSectionStyle,
            infoSectionStyle,
            openEditorPopup,
            processSortSequenceChange
        }
    }
};
