const uploadDataTableViewerPopup = {
    props: {
        columns: {
            type: Array,
            default: []
        },
        data: {
            type: Array,
            default: []
        },
        loadCount: {
            type: Number,
            default: null
        },
        pageNumber: {
            type: Number,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        totalRecords: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit overflow-auto">
                    <q-table flat bordered class="sticky-header-table" :style="contentStyle" :columns="columns" :rows="data" row-key="upspid" :loading="tableLoading" v-model:pagination="pagination" separator="cell" @request="changePage" :rows-per-page-options="[0]" wrap-cells dense virtual-scroll-sticky-size-start="40">
                        <template v-slot:top="scope">
                            <div class="full-width row justify-end">
                                <div class="self-center text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>
                            
                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage"></q-btn>
                                
                                <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage"></q-btn>
                                
                                <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage"></q-btn>
                                
                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage"></q-btn>
                            </div>
                        </template>
                        <template v-slot:header="props">
                        <q-tr :props='props' class="bg-blue-grey-2">
                            <q-th v-for="col in props.cols" :key="col.name" :props="props" class="content-center">
                                <div class="fit text-center text-lowercase text-bold">
                                    <div>{{ col.label }}</div>
                                </div>
                            </q-th>
                        </q-tr>
                    </template>
                        <template v-slot:pagination="scope"></template>
                        <template v-slot:no-data>
                            <div class="text-bold">Loading...</div>
                        </template>
                        <template v-slot:loading>
                            <q-inner-loading showing color="primary"></q-inner-loading>
                        </template>
                    </q-table>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const paginationFirstRecordNumber = Vue.computed(() => {
            let recordNumber = 1;
            if(Number(props.pageNumber) > 1){
                recordNumber += ((Number(props.pageNumber) - 1) * Number(props.loadCount));
            }
            return recordNumber;
        });
        const paginationLastPageNumber = Vue.computed(() => {
            let lastPage = 1;
            if(Number(props.totalRecords) > Number(props.loadCount)){
                lastPage = Math.floor(Number(props.totalRecords) / Number(props.loadCount));
            }
            if(Number(props.totalRecords) % Number(props.loadCount)){
                lastPage++;
            }
            return lastPage;
        });
        const paginationLastRecordNumber = Vue.computed(() => {
            let recordNumber = (Number(props.totalRecords) > Number(props.loadCount)) ? Number(props.loadCount) : Number(props.totalRecords);
            if(Number(props.totalRecords) > Number(props.loadCount) && Number(props.pageNumber) > 1){
                if(Number(props.pageNumber) === Number(paginationLastPageNumber.value)){
                    recordNumber = (Number(props.totalRecords) % Number(props.loadCount)) + ((Number(props.pageNumber) - 1) * Number(props.loadCount));
                }
                else{
                    recordNumber = Number(props.pageNumber) * Number(props.loadCount);
                }
            }
            return recordNumber;
        });
        const pagination = Vue.computed(() => {
            return {
                page: props.pageNumber,
                lastPage: paginationLastPageNumber.value,
                rowsPerPage: props.loadCount,
                firstRowNumber: paginationFirstRecordNumber.value,
                lastRowNumber: paginationLastRecordNumber.value,
                rowsNumber: Number(props.totalRecords)
            };
        });
        const propsRefs = Vue.toRefs(props);
        const tableLoading = Vue.ref(false);

        Vue.watch(propsRefs.data, () => {
            tableLoading.value = false;
        });

        function changePage(props) {
            tableLoading.value = true;
            context.emit('update:page-number', props.pagination.page);
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            pagination,
            tableLoading,
            changePage,
            closePopup
        }
    }
};
