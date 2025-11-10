const spatialControlPanelTopShowButton = {
    template: `
        <template v-if="(windowWidth >= 875 && !inputWindowMode) || (windowWidth >= 600 && inputWindowMode)">
            <div role="button" class="z-max map-control-panel-link-container map-info-window-container cursor-pointer row justify-center  animate__animated animate__slow"  :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelTop) ? 'animate__slideInDown' : 'animate__slideOutUp'" @click="updateMapSettings('showControlPanelTop', true);" @keyup.enter="updateMapSettings('showControlPanelTop', true);" aria-role="Toggle control panel" tabindex="0">
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
