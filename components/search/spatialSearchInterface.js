const spatialSearchInterface = {
    template: `
        <div id="map-container" class="fullscreen">
            <spatial-analysis-module window-type="analysis" :load-records-completed="loadRecordsCompleted" @open:query-popup="openQueryPopupDisplay" @open:record-info-window="openRecordInfoWindow"></spatial-analysis-module>
        </div>
    `,
    components: {
        'spatial-analysis-module': spatialAnalysisModule
    },
    setup(_, context) {
        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        function openQueryPopupDisplay() {
            context.emit('open:query-popup');
        }

        function openRecordInfoWindow(id){
            context.emit('open:record-info-window', id);
        }

        return {
            loadRecordsCompleted,
            openQueryPopupDisplay,
            openRecordInfoWindow
        }
    }
};
