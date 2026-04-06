const spatialSearchInterface = {
    template: `
        <div id="map-container" class="fullscreen">
            <spatial-analysis-module window-type="analysis" :load-records-completed="loadRecordsCompleted" @open:query-popup="openQueryPopupDisplay"></spatial-analysis-module>
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

        return {
            loadRecordsCompleted,
            openQueryPopupDisplay
        }
    }
};
