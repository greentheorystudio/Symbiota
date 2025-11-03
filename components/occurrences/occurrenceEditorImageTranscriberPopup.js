const occurrenceEditorImageTranscriberPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div ref="topBarRef" class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="instructionRef" class="black-border q-pa-sm text-body1 text-bold text-grey-8">
                    To pan and zoom, hover over the image on the right, and: scroll, double-click, or use the - and + keys to zoom; click and drag, or use the
                    arrow keys to pan.
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="row justify-between">
                        <div class="full-height col-6 overflow-auto image-transcriber-data-panel z-max">
                            <div class="q-pa-sm column q-gutter-sm">
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['catalognumber']" label="Catalog Number" :maxlength="occurrenceFields['catalognumber'] ? occurrenceFields['catalognumber']['length'] : 0" :value="occurrenceData.catalognumber" @update:value="(value) => updateOccurrenceData('catalognumber', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['othercatalognumbers']" label="Other Catalog Numbers" :maxlength="occurrenceFields['othercatalognumbers'] ? occurrenceFields['othercatalognumbers']['length'] : 0" :value="occurrenceData.othercatalognumbers" @update:value="(value) => updateOccurrenceData('othercatalognumbers', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['recordedby']" label="Collector/Observer" :maxlength="occurrenceFields['recordedby'] ? occurrenceFields['recordedby']['length'] : 0" :value="occurrenceData.recordedby" @update:value="(value) => updateOccurrenceData('recordedby', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['recordnumber']" label="Number" :maxlength="occurrenceFields['recordnumber'] ? occurrenceFields['recordnumber']['length'] : 0" :value="occurrenceData.recordnumber" @update:value="(value) => updateOccurrenceData('recordnumber', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <date-input-element :definition="occurrenceFieldDefinitions['eventdate']" label="Date" :value="occurrenceData.eventdate" @update:value="updateDateData"></date-input-element>
                                </div>
                                <div>
                                    <time-input-element :definition="occurrenceFieldDefinitions['eventtime']" label="Time" :value="occurrenceData.eventtime" @update:value="(value) => updateOccurrenceData('eventtime', value)"></time-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['associatedcollectors']" label="Associated Collectors" :maxlength="occurrenceFields['associatedcollectors'] ? occurrenceFields['associatedcollectors']['length'] : 0" :value="occurrenceData.associatedcollectors" @update:value="(value) => updateOccurrenceData('associatedcollectors', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimeventdate']" label="Verbatim Date" :maxlength="occurrenceFields['verbatimeventdate'] ? occurrenceFields['verbatimeventdate']['length'] : 0" :value="occurrenceData.verbatimeventdate" @update:value="(value) => updateOccurrenceData('verbatimeventdate', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['minimumdepthinmeters']" label="Minimum Depth (m)" :maxlength="occurrenceFields['minimumdepthinmeters'] ? occurrenceFields['minimumdepthinmeters']['length'] : 0" :value="occurrenceData.minimumdepthinmeters" @update:value="(value) => updateOccurrenceData('minimumdepthinmeters', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['maximumdepthinmeters']" label="Maximum Depth (m)" :maxlength="occurrenceFields['maximumdepthinmeters'] ? occurrenceFields['maximumdepthinmeters']['length'] : 0" :value="occurrenceData.maximumdepthinmeters" @update:value="(value) => updateOccurrenceData('maximumdepthinmeters', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimdepth']" label="Verbatim Depth" :maxlength="occurrenceFields['verbatimdepth'] ? occurrenceFields['verbatimdepth']['length'] : 0" :value="occurrenceData.verbatimdepth" @update:value="(value) => updateOccurrenceData('verbatimdepth', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['habitat']" label="Habitat" :value="occurrenceData.habitat" @update:value="(value) => updateOccurrenceData('habitat', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['substrate']" label="Substrate" :value="occurrenceData.substrate" @update:value="(value) => updateOccurrenceData('substrate', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['fieldnumber']" label="Field Number" :maxlength="occurrenceFields['fieldnumber'] ? occurrenceFields['fieldnumber']['length'] : 0" :value="occurrenceData.fieldnumber" @update:value="(value) => updateOccurrenceData('fieldnumber', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['samplingprotocol']" label="Sampling Protocol" :maxlength="occurrenceFields['samplingprotocol'] ? occurrenceFields['samplingprotocol']['length'] : 0" :value="occurrenceData.samplingprotocol" @update:value="(value) => updateOccurrenceData('samplingprotocol', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['samplingeffort']" label="Sampling Effort" :maxlength="occurrenceFields['samplingeffort'] ? occurrenceFields['samplingeffort']['length'] : 0" :value="occurrenceData.samplingeffort" @update:value="(value) => updateOccurrenceData('samplingeffort', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['samplingeffort']" label="Sampling Effort" :maxlength="occurrenceFields['samplingeffort'] ? occurrenceFields['samplingeffort']['length'] : 0" :value="occurrenceData.samplingeffort" @update:value="(value) => updateOccurrenceData('samplingeffort', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['fieldnotes']" label="Field Notes" :value="occurrenceData.fieldnotes" @update:value="(value) => updateOccurrenceData('fieldnotes', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['labelproject']" label="Label Project" :maxlength="occurrenceFields['labelproject'] ? occurrenceFields['labelproject']['length'] : 0" :value="occurrenceData.labelproject" @update:value="(value) => updateOccurrenceData('labelproject', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <single-scientific-common-name-auto-complete :definition="occurrenceFieldDefinitions['sciname']" :sciname="occurrenceData.sciname" label="Scientific Name" :limit-to-options="limitIdsToThesaurus" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                                </div>
                                <div>
                                    <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" label="Author" :maxlength="occurrenceFields['scientificnameauthorship'] ? occurrenceFields['scientificnameauthorship']['length'] : 0" :value="((Number(occurrenceData.tid) > 0 && occurrenceData.hasOwnProperty('taxonData')) ? occurrenceData['taxonData'].author : occurrenceData.scientificnameauthorship)" @update:value="(value) => updateOccurrenceData('scientificnameauthorship', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['identificationqualifier']" label="ID Qualifier" :maxlength="occurrenceFields['identificationqualifier'] ? occurrenceFields['identificationqualifier']['length'] : 0" :value="occurrenceData.identificationqualifier" @update:value="(value) => updateOccurrenceData('identificationqualifier', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['family']" label="Family" :maxlength="occurrenceFields['family'] ? occurrenceFields['family']['length'] : 0" :value="(Number(occurrenceData.tid) > 0 ? occurrenceData['taxonData'].family : occurrenceData.family)" @update:value="(value) => updateOccurrenceData('family', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['identifiedby']" label="Identified By" :maxlength="occurrenceFields['identifiedby'] ? occurrenceFields['identifiedby']['length'] : 0" :value="occurrenceData.identifiedby" @update:value="(value) => updateOccurrenceData('identifiedby', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['dateidentified']" label="Date Identified" :maxlength="occurrenceFields['dateidentified'] ? occurrenceFields['dateidentified']['length'] : 0" :value="occurrenceData.dateidentified" @update:value="(value) => updateOccurrenceData('dateidentified', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimscientificname']" label="Verbatim Scientific Name" :maxlength="occurrenceFields['verbatimscientificname'] ? occurrenceFields['verbatimscientificname']['length'] : 0" :value="occurrenceData.verbatimscientificname" @update:value="(value) => updateOccurrenceData('verbatimscientificname', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['identificationreferences']" label="ID References" :maxlength="occurrenceFields['identificationreferences'] ? occurrenceFields['identificationreferences']['length'] : 0" :value="occurrenceData.identificationreferences" @update:value="(value) => updateOccurrenceData('identificationreferences', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['identificationremarks']" label="ID Remarks" :maxlength="occurrenceFields['identificationremarks'] ? occurrenceFields['identificationremarks']['length'] : 0" :value="occurrenceData.identificationremarks" @update:value="(value) => updateOccurrenceData('identificationremarks', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['taxonremarks']" label="Taxon Remarks" :maxlength="occurrenceFields['taxonremarks'] ? occurrenceFields['taxonremarks']['length'] : 0" :value="occurrenceData.taxonremarks" @update:value="(value) => updateOccurrenceData('taxonremarks', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <single-country-auto-complete :definition="occurrenceFieldDefinitions['country']" label="Country" :maxlength="occurrenceFields['country'] ? occurrenceFields['country']['length'] : 0" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', value)"></single-country-auto-complete>
                                </div>
                                <div>
                                    <single-state-province-auto-complete :definition="occurrenceFieldDefinitions['stateprovince']" label="State/Province" :maxlength="occurrenceFields['stateprovince'] ? occurrenceFields['stateprovince']['length'] : 0" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', value)" :country="occurrenceData.country"></single-state-province-auto-complete>
                                </div>
                                <div>
                                    <single-county-auto-complete :definition="occurrenceFieldDefinitions['county']" label="County" :maxlength="occurrenceFields['county'] ? occurrenceFields['county']['length'] : 0" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)" :state-province="occurrenceData.stateprovince"></single-county-auto-complete>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" label="Municipality" :maxlength="occurrenceFields['municipality'] ? occurrenceFields['municipality']['length'] : 0" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locality']" label="Locality" :value="occurrenceData.locality" @update:value="(value) => updateOccurrenceData('locality', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="number" label="Latitude" :value="occurrenceData.decimallatitude" min-value="-90" max-value="90" @update:value="(value) => updateOccurrenceData('decimallatitude', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="number" label="Longitude" :value="occurrenceData.decimallongitude" min-value="-180" max-value="180" @update:value="(value) => updateOccurrenceData('decimallongitude', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['coordinateuncertaintyinmeters']" label="Uncertainty" :value="occurrenceData.coordinateuncertaintyinmeters" min-value="0" @update:value="(value) => updateOccurrenceData('coordinateuncertaintyinmeters', value)" :state-province="occurrenceData.stateprovince"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['geodeticdatum']" label="Datum" :maxlength="occurrenceFields['geodeticdatum'] ? occurrenceFields['geodeticdatum']['length'] : 0" :value="occurrenceData.geodeticdatum" @update:value="(value) => updateOccurrenceData('geodeticdatum', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <occurrence-verbatim-coordinates-input-element :definition="occurrenceFieldDefinitions['verbatimcoordinates']" label="Verbatim Coordinates" :maxlength="occurrenceFields['verbatimcoordinates'] ? occurrenceFields['verbatimcoordinates']['length'] : 0" :value="occurrenceData.verbatimcoordinates" :geodetic-datum="occurrenceData.geodeticdatum" :decimal-latitude="occurrenceData.decimallatitude" @update:value="(value) => updateOccurrenceData('verbatimcoordinates', value)" @update:decimal-coordinates="processRecalculatedDecimalCoordinates"></occurrence-verbatim-coordinates-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['minimumelevationinmeters']" label="Minimum Elevation (m)" :maxlength="occurrenceFields['minimumelevationinmeters'] ? occurrenceFields['minimumelevationinmeters']['length'] : 0" :value="occurrenceData.minimumelevationinmeters" @update:value="(value) => updateOccurrenceData('minimumelevationinmeters', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['maximumelevationinmeters']" label="Maximum Elevation (m)" :maxlength="occurrenceFields['maximumelevationinmeters'] ? occurrenceFields['maximumelevationinmeters']['length'] : 0" :value="occurrenceData.maximumelevationinmeters" @update:value="(value) => updateOccurrenceData('maximumelevationinmeters', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <occurrence-verbatim-elevation-input-element :definition="occurrenceFieldDefinitions['verbatimelevation']" label="Verbatim Elevation" :maxlength="occurrenceFields['verbatimelevation'] ? occurrenceFields['verbatimelevation']['length'] : 0" :value="occurrenceData.verbatimelevation" :minimum-elevation-in-meters="occurrenceData.minimumelevationinmeters" @update:value="(value) => updateOccurrenceData('verbatimelevation', value)" @update:elevation-values="processRecalculatedElevationValues"></occurrence-verbatim-elevation-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['continent']" label="Continent" :maxlength="occurrenceFields['continent'] ? occurrenceFields['continent']['length'] : 0" :value="occurrenceData.continent" @update:value="(value) => updateOccurrenceData('continent', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['island']" label="Island" :maxlength="occurrenceFields['island'] ? occurrenceFields['island']['length'] : 0" :value="occurrenceData.island" @update:value="(value) => updateOccurrenceData('island', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['islandgroup']" label="Island Group" :maxlength="occurrenceFields['islandgroup'] ? occurrenceFields['islandgroup']['length'] : 0" :value="occurrenceData.islandgroup" @update:value="(value) => updateOccurrenceData('islandgroup', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['waterbody']" label="Water Body" :maxlength="occurrenceFields['waterbody'] ? occurrenceFields['waterbody']['length'] : 0" :value="occurrenceData.waterbody" @update:value="(value) => updateOccurrenceData('waterbody', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['georeferencedby']" label="Georeferenced By" :maxlength="occurrenceFields['georeferencedby'] ? occurrenceFields['georeferencedby']['length'] : 0" :value="occurrenceData.georeferencedby" @update:value="(value) => updateOccurrenceData('georeferencedby', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['georeferenceprotocol']" label="Georeference Protocol" :maxlength="occurrenceFields['georeferenceprotocol'] ? occurrenceFields['georeferenceprotocol']['length'] : 0" :value="occurrenceData.georeferenceprotocol" @update:value="(value) => updateOccurrenceData('georeferenceprotocol', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['georeferenceverificationstatus']" label="Georeference Verification Status" :maxlength="occurrenceFields['georeferenceverificationstatus'] ? occurrenceFields['georeferenceverificationstatus']['length'] : 0" :value="occurrenceData.georeferenceverificationstatus" @update:value="(value) => updateOccurrenceData('georeferenceverificationstatus', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['georeferencesources']" label="Georeference Sources" :maxlength="occurrenceFields['georeferencesources'] ? occurrenceFields['georeferencesources']['length'] : 0" :value="occurrenceData.georeferencesources" @update:value="(value) => updateOccurrenceData('georeferencesources', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['georeferenceremarks']" label="Georeference Remarks" :maxlength="occurrenceFields['georeferenceremarks'] ? occurrenceFields['georeferenceremarks']['length'] : 0" :value="occurrenceData.georeferenceremarks" @update:value="(value) => updateOccurrenceData('georeferenceremarks', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locationremarks']" label="Location Remarks" :value="occurrenceData.locationremarks" @update:value="(value) => updateOccurrenceData('locationremarks', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <occurrence-associated-taxa-input-element :definition="occurrenceFieldDefinitions['associatedtaxa']" label="Associated Taxa" :maxlength="occurrenceFields['associatedtaxa'] ? occurrenceFields['associatedtaxa']['length'] : 0" :value="occurrenceData.associatedtaxa" @update:value="(value) => updateOccurrenceData('associatedtaxa', value)"></occurrence-associated-taxa-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" :maxlength="occurrenceFields['lifestage'] ? occurrenceFields['lifestage']['length'] : 0" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" :maxlength="occurrenceFields['sex'] ? occurrenceFields['sex']['length'] : 0" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['occurrenceremarks']" label="Occurrence Remarks" :value="occurrenceData.occurrenceremarks" @update:value="(value) => updateOccurrenceData('occurrenceremarks', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['typestatus']" label="Type Status" :maxlength="occurrenceFields['typestatus'] ? occurrenceFields['typestatus']['length'] : 0" :value="occurrenceData.typestatus" @update:value="(value) => updateOccurrenceData('typestatus', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['reproductivecondition']" label="Reproductive Condition" :maxlength="occurrenceFields['reproductivecondition'] ? occurrenceFields['reproductivecondition']['length'] : 0" :value="occurrenceData.reproductivecondition" @update:value="(value) => updateOccurrenceData('reproductivecondition', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['establishmentmeans']" label="Establishment Means" :maxlength="occurrenceFields['establishmentmeans'] ? occurrenceFields['establishmentmeans']['length'] : 0" :value="occurrenceData.establishmentmeans" @update:value="(value) => updateOccurrenceData('establishmentmeans', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <checkbox-input-element :definition="occurrenceFieldDefinitions['cultivationstatus']" label="Cultivated" :value="occurrenceData.cultivationstatus" @update:value="updateCultivationStatusSetting"></checkbox-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['dynamicproperties']" label="Dynamic Properties" :maxlength="occurrenceFields['dynamicproperties'] ? occurrenceFields['dynamicproperties']['length'] : 0" :value="occurrenceData.dynamicproperties" @update:value="(value) => updateOccurrenceData('dynamicproperties', value)"></text-field-input-element>
                                </div>
                                <div>
                                    <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['verbatimattributes']" label="Verbatim Attributes" :maxlength="occurrenceFields['verbatimattributes'] ? occurrenceFields['verbatimattributes']['length'] : 0" :value="occurrenceData.verbatimattributes" @update:value="(value) => updateOccurrenceData('verbatimattributes', value)"></text-field-input-element>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 occurrence-editor-image-transcriber-image overflow-hidden">
                            <div class="fit">
                                <q-card>
                                    <q-carousel ref="carousel" swipeable animated v-model="currentImage" :arrows="(imageArr.length > 1)" control-color="black" infinite class="fit">
                                        <template v-for="image in imageArr" :key="image">
                                            <q-carousel-slide :name="image.imgid" class="fit row justify-center q-ma-none q-pa-none">
                                                <div :style="imageStyle" class="overflow-hidden">
                                                    <template v-if="image.originalurl">
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
        'occurrence-associated-taxa-input-element': occurrenceAssociatedTaxaInputElement,
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
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const imageElement = Vue.ref(null);
        const imageLoading = Vue.ref(true);
        const imageStyle = Vue.ref(null);
        const instructionRef = Vue.ref(null);
        const limitIdsToThesaurus = Vue.computed(() => occurrenceStore.getLimitIdsToThesaurus);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const panzoomInitialized = Vue.ref(false);
        const topBarRef = Vue.ref(null);

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
            imageArr,
            imageElement,
            imageLoading,
            imageStyle,
            instructionRef,
            limitIdsToThesaurus,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            topBarRef,
            closePopup,
            imageLoadPostProcessing,
            processRecalculatedDecimalCoordinates,
            processRecalculatedElevationValues,
            updateCultivationStatusSetting,
            updateDateData,
            updateOccurrenceData,
            updateScientificNameValue
        }
    }
};
