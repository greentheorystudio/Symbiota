const spatialSideButtonTray = {
    template: `
        <div class="map-side-panel-button-tray column justify-around side-panel-button-tray">
            <template v-if="windowWidth < 875 || inputWindowMode">
                <spatial-control-panel-left-show-button></spatial-control-panel-left-show-button>
            </template>
            <spatial-side-panel-show-button></spatial-side-panel-show-button>
        </div>
    `,
    components: {
        'spatial-control-panel-left-show-button': spatialControlPanelLeftShowButton,
        'spatial-side-panel-show-button': spatialSidePanelShowButton
    },
    setup() {
        const inputWindowMode = Vue.inject('inputWindowMode');
        const windowWidth = Vue.inject('windowWidth');

        return {
            inputWindowMode,
            windowWidth
        }
    }
};
