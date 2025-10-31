const spatialControlPanelLeftShowButton = {
    template: `
        <template v-if="inputWindowMode || windowWidth < 875">
            <div role="button" class="map-side-panel-link-container map-info-window-container control-panel column justify-center items-center cursor-pointer animate__animated animate__slow" :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelLeft) ? 'animate__slideInRight' : 'animate__slideOutLeft'" @click="updateMapSettings('showControlPanelLeft', true);" @keyup.enter="updateMapSettings('showControlPanelLeft', true);" aria-role="Toggle control panel" tabindex="0">
                <q-icon color="white" size="sm" name="fas fa-caret-right"></q-icon>
            </div>
        </template>
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
