const spatialControlPanelTopShowButton = {
    template: `
        <template v-if="(windowWidth >= 875 && !inputWindowMode) || (windowWidth >= 600 && inputWindowMode)">
            <div class="z-top map-control-panel-link-container map-info-window-container cursor-pointer row justify-center  animate__animated animate__slow"  :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelTop) ? 'animate__slideInDown' : 'animate__slideOutUp'" @click="updateMapSettings('showControlPanelTop', true);">
                <q-icon color="white" size="sm" name="fas fa-caret-down"></q-icon>
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
