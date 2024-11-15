const spatialSidePanelShowButton = {
    template: `
        <div class="z-top map-side-panel-link-container map-info-window-container control-panel column justify-center items-center cursor-pointer animate__animated animate__slow" :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelLeft) ? 'animate__slideInRight' : 'animate__slideOutLeft'" @click="updateMapSettings('showSidePanel', true);">
            <q-icon color="white" size="sm" name="fas fa-caret-right"></q-icon>
        </div>
    `,
    setup() {
        const inputWindowMode = Vue.inject('inputWindowMode');
        const mapSettings = Vue.inject('mapSettings');
        const windowWidth = Vue.inject('windowWidth');

        const updateMapSettings = Vue.inject('updateMapSettings');
        
        return {
            inputWindowMode,
            mapSettings,
            windowWidth,
            updateMapSettings
        }
    }
};
