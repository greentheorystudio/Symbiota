const occurrenceEditorImageTranscriberPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="xl-popup overflow-hidden">
                <div ref="topBarRef" class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="instructionRef" class="black-border q-pa-sm column q-gutter-xs">
                    <div class="text-body1 text-bold text-grey-8">
                        To pan and zoom, hover over the image on the right, and: scroll, double-click, or use the - and + keys to zoom; click and drag, or use the
                        arrow keys to pan.
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <div>
                            <q-btn-toggle v-model="selectedImageResolution" :options="imageResolutionOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Image Resolution" tabindex="0"></q-btn-toggle>
                        </div>
                        <div>
                            <q-btn-toggle v-model="selectedDataDisplay" :options="dataDisplayOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Data Display" tabindex="0"></q-btn-toggle>
                        </div>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="row justify-between">
                        <div class="full-height col-6 overflow-auto image-transcriber-data-panel z-max">
                            <template v-if="selectedDataDisplay === 'occurrence'">
                                <div class="q-pa-sm column q-gutter-sm">
                                    <div v-if="!transcriberHideFields.includes('catalognumber')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['catalognumber']" :label="occurrenceFieldDefinitions['catalognumber']['label']" :maxlength="occurrenceFieldDefinitions['catalognumber']['length']" :value="occurrenceData.catalognumber" @update:value="(value) => updateOccurrenceData('catalognumber', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('othercatalognumbers')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['othercatalognumbers']" :label="occurrenceFieldDefinitions['othercatalognumbers']['label']" :maxlength="occurrenceFieldDefinitions['othercatalognumbers']['length']" :value="occurrenceData.othercatalognumbers" @update:value="(value) => updateOccurrenceData('othercatalognumbers', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('recordedby')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['recordedby']" :label="occurrenceFieldDefinitions['recordedby']['label']" field="recordedby" :maxlength="occurrenceFieldDefinitions['recordedby']['length']" :value="occurrenceData.recordedby" @update:value="(value) => updateOccurrenceData('recordedby', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('recordnumber')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['recordnumber']" :label="occurrenceFieldDefinitions['recordnumber']['label']" :maxlength="occurrenceFieldDefinitions['recordnumber']['length']" :value="occurrenceData.recordnumber" @update:value="(value) => updateOccurrenceData('recordnumber', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('eventdate')">
                                        <date-input-element :definition="occurrenceFieldDefinitions['eventdate']" :label="occurrenceFieldDefinitions['eventdate']['label']" :value="occurrenceData.eventdate" @update:value="updateDateData"></date-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('eventtime')">
                                        <time-input-element :definition="occurrenceFieldDefinitions['eventtime']" :label="occurrenceFieldDefinitions['eventtime']['label']" :value="occurrenceData.eventtime" @update:value="(value) => updateOccurrenceData('eventtime', value)"></time-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('associatedcollectors')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['associatedcollectors']" :label="occurrenceFieldDefinitions['associatedcollectors']['label']" field="associatedcollectors" :maxlength="occurrenceFieldDefinitions['associatedcollectors']['length']" :value="occurrenceData.associatedcollectors" @update:value="(value) => updateOccurrenceData('associatedcollectors', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimeventdate')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimeventdate']" :label="occurrenceFieldDefinitions['verbatimeventdate']['label']" field="verbatimeventdate" :maxlength="occurrenceFieldDefinitions['verbatimeventdate']['length']" :value="occurrenceData.verbatimeventdate" @update:value="(value) => updateOccurrenceData('verbatimeventdate', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('minimumdepthinmeters')">
                                        <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['minimumdepthinmeters']" :label="occurrenceFieldDefinitions['minimumdepthinmeters']['label']" :maxlength="occurrenceFieldDefinitions['minimumdepthinmeters']['length']" :value="occurrenceData.minimumdepthinmeters" @update:value="(value) => updateOccurrenceData('minimumdepthinmeters', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('maximumdepthinmeters')">
                                        <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['maximumdepthinmeters']" :label="occurrenceFieldDefinitions['maximumdepthinmeters']['label']" :maxlength="occurrenceFieldDefinitions['maximumdepthinmeters']['length']" :value="occurrenceData.maximumdepthinmeters" @update:value="(value) => updateOccurrenceData('maximumdepthinmeters', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimdepth')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimdepth']" :label="occurrenceFieldDefinitions['verbatimdepth']['label']" :maxlength="occurrenceFieldDefinitions['verbatimdepth']['length']" :value="occurrenceData.verbatimdepth" @update:value="(value) => updateOccurrenceData('verbatimdepth', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('substrate')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['substrate']" :label="occurrenceFieldDefinitions['substrate']['label']" field="substrate" :value="occurrenceData.substrate" @update:value="(value) => updateOccurrenceData('substrate', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('fieldnumber')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['fieldnumber']" :label="occurrenceFieldDefinitions['fieldnumber']['label']" :maxlength="occurrenceFieldDefinitions['fieldnumber']['length']" :value="occurrenceData.fieldnumber" @update:value="(value) => updateOccurrenceData('fieldnumber', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('samplingprotocol')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['samplingprotocol']" :label="occurrenceFieldDefinitions['samplingprotocol']['label']" field="samplingprotocol" :maxlength="occurrenceFieldDefinitions['samplingprotocol']['length']" :value="occurrenceData.samplingprotocol" @update:value="(value) => updateOccurrenceData('samplingprotocol', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('samplingeffort')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['samplingeffort']" :label="occurrenceFieldDefinitions['samplingeffort']['label']" field="samplingeffort" :maxlength="occurrenceFieldDefinitions['samplingeffort']['length']" :value="occurrenceData.samplingeffort" @update:value="(value) => updateOccurrenceData('samplingeffort', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('fieldnotes')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['fieldnotes']" :label="occurrenceFieldDefinitions['fieldnotes']['label']" field="fieldnotes" :value="occurrenceData.fieldnotes" @update:value="(value) => updateOccurrenceData('fieldnotes', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('labelproject')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['labelproject']" :label="occurrenceFieldDefinitions['labelproject']['label']" field="labelproject" :maxlength="occurrenceFieldDefinitions['labelproject']['length']" :value="occurrenceData.labelproject" @update:value="(value) => updateOccurrenceData('labelproject', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('sciname')">
                                        <single-scientific-common-name-auto-complete :definition="occurrenceFieldDefinitions['sciname']" :sciname="occurrenceData.sciname" :label="occurrenceFieldDefinitions['sciname']['label']" :limit-to-options="limitIdsToThesaurus" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('scientificnameauthorship')">
                                        <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" :label="occurrenceFieldDefinitions['scientificnameauthorship']['label']" :maxlength="occurrenceFieldDefinitions['scientificnameauthorship']['length']" :value="((Number(occurrenceData.tid) > 0 && occurrenceData.hasOwnProperty('taxonData')) ? occurrenceData['taxonData'].author : occurrenceData.scientificnameauthorship)" @update:value="(value) => updateOccurrenceData('scientificnameauthorship', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('identificationqualifier')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identificationqualifier']" :label="occurrenceFieldDefinitions['identificationqualifier']['label']" field="identificationqualifier" :maxlength="occurrenceFieldDefinitions['identificationqualifier']['length']" :value="occurrenceData.identificationqualifier" @update:value="(value) => updateOccurrenceData('identificationqualifier', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('family')">
                                        <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['family']" :label="occurrenceFieldDefinitions['family']['label']" :maxlength="occurrenceFieldDefinitions['family']['length']" :value="(Number(occurrenceData.tid) > 0 ? occurrenceData['taxonData'].family : occurrenceData.family)" @update:value="(value) => updateOccurrenceData('family', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('identifiedby')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identifiedby']" :label="occurrenceFieldDefinitions['identifiedby']['label']" field="identifiedby" :maxlength="occurrenceFieldDefinitions['identifiedby']['length']" :value="occurrenceData.identifiedby" @update:value="(value) => updateOccurrenceData('identifiedby', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('dateidentified')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['dateidentified']" :label="occurrenceFieldDefinitions['dateidentified']['label']" field="dateidentified" :maxlength="occurrenceFieldDefinitions['dateidentified']['length']" :value="occurrenceData.dateidentified" @update:value="(value) => updateOccurrenceData('dateidentified', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimscientificname')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimscientificname']" :label="occurrenceFieldDefinitions['verbatimscientificname']['label']" :maxlength="occurrenceFieldDefinitions['verbatimscientificname']['length']" :value="occurrenceData.verbatimscientificname" @update:value="(value) => updateOccurrenceData('verbatimscientificname', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('identificationreferences')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identificationreferences']" :label="occurrenceFieldDefinitions['identificationreferences']['label']" field="identificationreferences" :maxlength="occurrenceFieldDefinitions['identificationreferences']['length']" :value="occurrenceData.identificationreferences" @update:value="(value) => updateOccurrenceData('identificationreferences', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('identificationremarks')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identificationremarks']" :label="occurrenceFieldDefinitions['identificationremarks']['label']" field="identificationremarks" :maxlength="occurrenceFieldDefinitions['identificationremarks']['length']" :value="occurrenceData.identificationremarks" @update:value="(value) => updateOccurrenceData('identificationremarks', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('taxonremarks')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['taxonremarks']" :label="occurrenceFieldDefinitions['taxonremarks']['label']" field="taxonremarks" :maxlength="occurrenceFieldDefinitions['taxonremarks']['length']" :value="occurrenceData.taxonremarks" @update:value="(value) => updateOccurrenceData('taxonremarks', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('country')">
                                        <single-country-auto-complete :definition="occurrenceFieldDefinitions['country']" :label="occurrenceFieldDefinitions['country']['label']" :maxlength="occurrenceFieldDefinitions['country']['length']" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', (value ? value.name : null))"></single-country-auto-complete>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('stateprovince')">
                                        <single-state-province-auto-complete :definition="occurrenceFieldDefinitions['stateprovince']" :label="occurrenceFieldDefinitions['stateprovince']['label']" :maxlength="occurrenceFieldDefinitions['stateprovince']['length']" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', (value ? value.name : null))" :country="occurrenceData.country"></single-state-province-auto-complete>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('county')">
                                        <single-county-auto-complete :definition="occurrenceFieldDefinitions['county']" :label="occurrenceFieldDefinitions['county']['label']" :maxlength="occurrenceFieldDefinitions['county']['length']" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)" :state-province="occurrenceData.stateprovince"></single-county-auto-complete>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('municipality')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" :label="occurrenceFieldDefinitions['municipality']['label']" field="municipality" :maxlength="occurrenceFieldDefinitions['municipality']['length']" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('locality')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locality']" :label="occurrenceFieldDefinitions['locality']['label']" field="locality" :value="occurrenceData.locality" @update:value="(value) => updateOccurrenceData('locality', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('decimallatitude')">
                                        <text-field-input-element data-type="number" :label="occurrenceFieldDefinitions['decimallatitude']['label']" :value="occurrenceData.decimallatitude" min-value="-90" max-value="90" @update:value="(value) => updateOccurrenceData('decimallatitude', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('decimallongitude')">
                                        <text-field-input-element data-type="number" :label="occurrenceFieldDefinitions['decimallongitude']['label']" :value="occurrenceData.decimallongitude" min-value="-180" max-value="180" @update:value="(value) => updateOccurrenceData('decimallongitude', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('coordinateuncertaintyinmeters')">
                                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['coordinateuncertaintyinmeters']" :label="occurrenceFieldDefinitions['coordinateuncertaintyinmeters']['label']" :value="occurrenceData.coordinateuncertaintyinmeters" min-value="0" @update:value="(value) => updateOccurrenceData('coordinateuncertaintyinmeters', value)" :state-province="occurrenceData.stateprovince"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('geodeticdatum')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['geodeticdatum']" :label="occurrenceFieldDefinitions['geodeticdatum']['label']" field="geodeticdatum" :maxlength="occurrenceFieldDefinitions['geodeticdatum']['length']" :value="occurrenceData.geodeticdatum" @update:value="(value) => updateOccurrenceData('geodeticdatum', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimcoordinates')">
                                        <occurrence-verbatim-coordinates-input-element :definition="occurrenceFieldDefinitions['verbatimcoordinates']" :label="occurrenceFieldDefinitions['verbatimcoordinates']['label']" :maxlength="occurrenceFieldDefinitions['verbatimcoordinates']['length']" :value="occurrenceData.verbatimcoordinates" :geodetic-datum="occurrenceData.geodeticdatum" :decimal-latitude="occurrenceData.decimallatitude" @update:value="(value) => updateOccurrenceData('verbatimcoordinates', value)" @update:decimal-coordinates="processRecalculatedDecimalCoordinates"></occurrence-verbatim-coordinates-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('minimumelevationinmeters')">
                                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['minimumelevationinmeters']" :label="occurrenceFieldDefinitions['minimumelevationinmeters']['label']" :maxlength="occurrenceFieldDefinitions['minimumelevationinmeters']['length']" :value="occurrenceData.minimumelevationinmeters" @update:value="(value) => updateOccurrenceData('minimumelevationinmeters', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('maximumelevationinmeters')">
                                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['maximumelevationinmeters']" :label="occurrenceFieldDefinitions['maximumelevationinmeters']['label']" :maxlength="occurrenceFieldDefinitions['maximumelevationinmeters']['length']" :value="occurrenceData.maximumelevationinmeters" @update:value="(value) => updateOccurrenceData('maximumelevationinmeters', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimelevation')">
                                        <occurrence-verbatim-elevation-input-element :definition="occurrenceFieldDefinitions['verbatimelevation']" :label="occurrenceFieldDefinitions['verbatimelevation']['label']" :maxlength="occurrenceFieldDefinitions['verbatimelevation']['length']" :value="occurrenceData.verbatimelevation" :minimum-elevation-in-meters="occurrenceData.minimumelevationinmeters" @update:value="(value) => updateOccurrenceData('verbatimelevation', value)" @update:elevation-values="processRecalculatedElevationValues"></occurrence-verbatim-elevation-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('continent')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['continent']" :label="occurrenceFieldDefinitions['continent']['label']" field="continent" :maxlength="occurrenceFieldDefinitions['continent']['length']" :value="occurrenceData.continent" @update:value="(value) => updateOccurrenceData('continent', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('island')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['island']" :label="occurrenceFieldDefinitions['island']['label']" field="island" :maxlength="occurrenceFieldDefinitions['island']['length']" :value="occurrenceData.island" @update:value="(value) => updateOccurrenceData('island', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('islandgroup')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['islandgroup']" :label="occurrenceFieldDefinitions['islandgroup']['label']" field="islandgroup" :maxlength="occurrenceFieldDefinitions['islandgroup']['length']" :value="occurrenceData.islandgroup" @update:value="(value) => updateOccurrenceData('islandgroup', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('waterbody')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['waterbody']" :label="occurrenceFieldDefinitions['waterbody']['label']" field="waterbody" :maxlength="occurrenceFieldDefinitions['waterbody']['length']" :value="occurrenceData.waterbody" @update:value="(value) => updateOccurrenceData('waterbody', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('georeferencedby')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['georeferencedby']" :label="occurrenceFieldDefinitions['georeferencedby']['label']" field="georeferencedby" :maxlength="occurrenceFieldDefinitions['georeferencedby']['length']" :value="occurrenceData.georeferencedby" @update:value="(value) => updateOccurrenceData('georeferencedby', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('georeferenceprotocol')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['georeferenceprotocol']" :label="occurrenceFieldDefinitions['georeferenceprotocol']['label']" field="georeferenceprotocol" :maxlength="occurrenceFieldDefinitions['georeferenceprotocol']['length']" :value="occurrenceData.georeferenceprotocol" @update:value="(value) => updateOccurrenceData('georeferenceprotocol', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('georeferenceverificationstatus')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['georeferenceverificationstatus']" :label="occurrenceFieldDefinitions['georeferenceverificationstatus']['label']" field="georeferenceverificationstatus" :maxlength="occurrenceFieldDefinitions['georeferenceverificationstatus']['length']" :value="occurrenceData.georeferenceverificationstatus" @update:value="(value) => updateOccurrenceData('georeferenceverificationstatus', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('georeferencesources')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['georeferencesources']" :label="occurrenceFieldDefinitions['georeferencesources']['label']" field="georeferencesources" :maxlength="occurrenceFieldDefinitions['georeferencesources']['length']" :value="occurrenceData.georeferencesources" @update:value="(value) => updateOccurrenceData('georeferencesources', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('georeferenceremarks')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['georeferenceremarks']" :label="occurrenceFieldDefinitions['georeferenceremarks']['label']" field="georeferenceremarks" :maxlength="occurrenceFieldDefinitions['georeferenceremarks']['length']" :value="occurrenceData.georeferenceremarks" @update:value="(value) => updateOccurrenceData('georeferenceremarks', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('locationremarks')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locationremarks']" :label="occurrenceFieldDefinitions['locationremarks']['label']" field="locationremarks" :value="occurrenceData.locationremarks" @update:value="(value) => updateOccurrenceData('locationremarks', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('habitat')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['habitat']" :label="occurrenceFieldDefinitions['habitat']['label']" field="habitat" :value="occurrenceData.habitat" @update:value="(value) => updateOccurrenceData('habitat', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('associatedtaxa')">
                                        <occurrence-associated-taxa-input-element :definition="occurrenceFieldDefinitions['associatedtaxa']" :label="occurrenceFieldDefinitions['associatedtaxa']['label']" :maxlength="occurrenceFieldDefinitions['associatedtaxa']['length']" :value="occurrenceData.associatedtaxa" @update:value="(value) => updateOccurrenceData('associatedtaxa', value)"></occurrence-associated-taxa-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('verbatimattributes')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['verbatimattributes']" :label="occurrenceFieldDefinitions['verbatimattributes']['label']" field="verbatimattributes" :maxlength="occurrenceFieldDefinitions['verbatimattributes']['length']" :value="occurrenceData.verbatimattributes" @update:value="(value) => updateOccurrenceData('verbatimattributes', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('lifestage')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['lifestage']" :label="occurrenceFieldDefinitions['lifestage']['label']" field="lifestage" :maxlength="occurrenceFieldDefinitions['lifestage']['length']" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('sex')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['sex']" :label="occurrenceFieldDefinitions['sex']['label']" field="sex" :maxlength="occurrenceFieldDefinitions['sex']['length']" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('individualcount')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['individualcount']" :label="occurrenceFieldDefinitions['individualcount']['label']" :maxlength="occurrenceFieldDefinitions['individualcount']['length']" :value="occurrenceData.individualcount" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('occurrenceremarks')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['occurrenceremarks']" :label="occurrenceFieldDefinitions['occurrenceremarks']['label']" field="occurrenceremarks" :value="occurrenceData.occurrenceremarks" @update:value="(value) => updateOccurrenceData('occurrenceremarks', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('typestatus')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['typestatus']" :label="occurrenceFieldDefinitions['typestatus']['label']" field="typestatus" :maxlength="occurrenceFieldDefinitions['typestatus']['length']" :value="occurrenceData.typestatus" @update:value="(value) => updateOccurrenceData('typestatus', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('reproductivecondition')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['reproductivecondition']" :label="occurrenceFieldDefinitions['reproductivecondition']['label']" field="reproductivecondition" :maxlength="occurrenceFieldDefinitions['reproductivecondition']['length']" :value="occurrenceData.reproductivecondition" @update:value="(value) => updateOccurrenceData('reproductivecondition', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('establishmentmeans')">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['establishmentmeans']" :label="occurrenceFieldDefinitions['establishmentmeans']['label']" field="establishmentmeans" :maxlength="occurrenceFieldDefinitions['establishmentmeans']['length']" :value="occurrenceData.establishmentmeans" @update:value="(value) => updateOccurrenceData('establishmentmeans', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('cultivationstatus')">
                                        <checkbox-input-element :definition="occurrenceFieldDefinitions['cultivationstatus']" :label="occurrenceFieldDefinitions['cultivationstatus']['label']" :value="occurrenceData.cultivationstatus" @update:value="updateCultivationStatusSetting"></checkbox-input-element>
                                    </div>
                                    <div v-if="!transcriberHideFields.includes('dynamicproperties')">
                                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['dynamicproperties']" :label="occurrenceFieldDefinitions['dynamicproperties']['label']" field="dynamicproperties" :maxlength="occurrenceFieldDefinitions['dynamicproperties']['length']" :value="occurrenceData.dynamicproperties" @update:value="(value) => updateOccurrenceData('dynamicproperties', value)"></text-field-input-element>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="q-pa-sm column q-gutter-sm">
                                    <template v-if="showDeterminationEditorBlock">
                                        <determination-editor-module :determination-id="editDeterminationId" :single-column="true" @close:popup="showDeterminationEditorBlock = false"></determination-editor-module>
                                    </template>
                                    <template v-else>
                                        <occurrence-editor-determination-history-block @open:determination-editor="openDeterminationEditorBlock"></occurrence-editor-determination-history-block>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="col-6 occurrence-editor-image-transcriber-image overflow-hidden">
                            <div class="fit">
                                <q-card>
                                    <q-carousel ref="carousel" swipeable animated v-model="currentImage" :arrows="(imageArr.length > 1)" control-color="black" infinite class="fit">
                                        <template v-for="image in imageArr" :key="image">
                                            <q-carousel-slide :name="image.imgid" class="fit row justify-center q-ma-none q-pa-none">
                                                <div :style="imageStyle" class="overflow-hidden">
                                                    <template v-if="image.originalurl && selectedImageResolution === 'high'">
                                                        <img :ref="(element) => imageElement = element" :src="(image.originalurl.startsWith('/') ? (clientRoot + image.originalurl) : image.originalurl)" @load="imageLoadPostProcessing();" />
                                                    </template>
                                                    <template v-else>
                                                        <img :ref="(element) => imageElement = element" :src="(image.url.startsWith('/') ? (clientRoot + image.url) : image.url)" @load="imageLoadPostProcessing();" />
                                                    </template>
                                                </div>
                                            </q-carousel-slide>
                                        </template>
                                    </q-carousel>
                                    <q-inner-loading :showing="imageLoading">
                                        <q-spinner color="primary" size="3em" :thickness="10"></q-spinner>
                                    </q-inner-loading>
                                </q-card>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'date-input-element': dateInputElement,
        'determination-editor-module': determinationEditorModule,
        'occurrence-associated-taxa-input-element': occurrenceAssociatedTaxaInputElement,
        'occurrence-editor-determination-history-block': occurrenceEditorDeterminationHistoryBlock,
        'occurrence-verbatim-coordinates-input-element': occurrenceVerbatimCoordinatesInputElement,
        'occurrence-verbatim-elevation-input-element': occurrenceVerbatimElevationInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'single-county-auto-complete': singleCountyAutoComplete,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete,
        'text-field-input-element': textFieldInputElement,
        'time-input-element': timeInputElement
    },
    setup(props, context) {
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();

        const clientRoot = baseStore.getClientRoot;
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const currentImage = Vue.ref(null);
        const dataDisplayOptions = [
            {label: 'Occurrence', value: 'occurrence'},
            {label: 'Determination', value: 'determination'}
        ];
        const editDeterminationId = Vue.ref(0);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const imageElement = Vue.ref(null);
        const imageLoading = Vue.ref(true);
        const imageResolutionOptions = [
            {label: 'High Res', value: 'high'},
            {label: 'Low Res', value: 'low'}
        ];
        const imageStyle = Vue.ref(null);
        const instructionRef = Vue.ref(null);
        const limitIdsToThesaurus = Vue.computed(() => occurrenceStore.getLimitIdsToThesaurus);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const panzoomInitialized = Vue.ref(false);
        const selectedDataDisplay = Vue.ref('occurrence');
        const selectedImageResolution = Vue.ref('high');
        const showDeterminationEditorBlock = Vue.ref(false);
        const topBarRef = Vue.ref(null);
        const transcriberHideFields = Vue.computed(() => occurrenceStore.getTranscriberHideFields);

        const validateCoordinates = Vue.inject('validateCoordinates');

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        Vue.watch(imageArr, () => {
            setCurrentImage();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function imageLoadPostProcessing() {
            const initialZoomVal = contentRef.value.clientHeight / imageElement.value.clientHeight;
            const initialZoomedWidth = imageElement.value.clientWidth * initialZoomVal;
            const initialXVal = ((contentRef.value.clientWidth / 2) - initialZoomedWidth) / 2;
            panzoom(imageElement.value, {
                initialX: initialXVal,
                initialZoom: initialZoomVal,
                excludeClass: 'image-transcriber-data-panel'
            });
            panzoomInitialized.value = true;
            imageLoading.value = false;
        }

        function openDeterminationEditorBlock(id) {
            editDeterminationId.value = id;
            showDeterminationEditorBlock.value = true;
        }

        function processRecalculatedDecimalCoordinates(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                updateOccurrenceData('decimallatitude', data['decimalLatitude']);
                updateOccurrenceData('decimallongitude', data['decimalLongitude']);
            }
        }

        function processRecalculatedElevationValues(data) {
            if(data.minimumElevationInMeters){
                updateOccurrenceData('minimumelevationinmeters', data['minimumElevationInMeters']);
                if(data.maximumElevationInMeters){
                    updateOccurrenceData('maximumelevationinmeters', data['maximumElevationInMeters']);
                }
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                const offset = topBarRef.value.clientHeight + instructionRef.value.clientHeight + 2;
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - offset) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                imageStyle.value = 'height: ' + (contentRef.value.clientHeight - offset) + 'px;';
            }
        }

        function setCurrentImage() {
            if(imageArr.value.length > 0){
                currentImage.value = imageArr.value[0]['imgid'];
            }
        }

        function updateCultivationStatusSetting(value) {
            if(Number(value) === 1){
                updateOccurrenceData('cultivationstatus', value);
            }
            else{
                updateOccurrenceData('cultivationstatus', '0');
            }
        }

        function updateDateData(dateData) {
            occurrenceStore.updateOccurrenceEditDataDate(dateData);
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
            if(key === 'decimallongitude' && occurrenceData.value['decimallatitude']){
                validateCoordinates();
            }
        }

        function updateScientificNameValue(taxon) {
            occurrenceStore.updateOccurrenceEditDataTaxon(taxon);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            setCurrentImage();
        });

        return {
            clientRoot,
            contentRef,
            contentStyle,
            currentImage,
            dataDisplayOptions,
            editDeterminationId,
            imageArr,
            imageElement,
            imageLoading,
            imageResolutionOptions,
            imageStyle,
            instructionRef,
            limitIdsToThesaurus,
            occurrenceData,
            occurrenceFieldDefinitions,
            selectedDataDisplay,
            selectedImageResolution,
            showDeterminationEditorBlock,
            topBarRef,
            transcriberHideFields,
            closePopup,
            imageLoadPostProcessing,
            openDeterminationEditorBlock,
            processRecalculatedDecimalCoordinates,
            processRecalculatedElevationValues,
            updateCultivationStatusSetting,
            updateDateData,
            updateOccurrenceData,
            updateScientificNameValue
        }
    }
};
