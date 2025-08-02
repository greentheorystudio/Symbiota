const occurrenceSelectorInfoBlock = {
    props: {
        occurrenceData: {
            type: Object,
            default: null
        }
    },
    template: `
        <template v-if="occurrenceData">
            <div class="column">
                <div class="text-bold">
                    <span v-if="occurrenceData['collectionname']">{{ occurrenceData['collectionname'] + ' ' }}</span>
                    <span v-if="occurrenceData['institutioncode'] || occurrenceData['collectioncode']">
                        [{{ (occurrenceData['institutioncode'] ? occurrenceData['institutioncode'] : '') + ((occurrenceData['institutioncode'] && occurrenceData['collectioncode']) ? ':' : '') + (occurrenceData['collectioncode'] ? occurrenceData['collectioncode'] : '') }}]
                    </span>
                </div>
                <div class="text-italic">
                    {{ occurrenceData['sciname'] }}
                </div>
                <div v-if="occurrenceData['catalognumber'] || occurrenceData['othercatalognumbers']">
                    <span v-if="occurrenceData['catalognumber']">{{ occurrenceData['catalognumber'] }}</span>
                    <span v-if="occurrenceData['othercatalognumbers']">
                        {{ (occurrenceData['catalognumber'] ? '; ' : '') + occurrenceData['othercatalognumbers'] }}
                    </span>
                </div>
                <div v-if="occurrenceData['recordedby'] || occurrenceData['recordnumber'] || occurrenceData['eventdate']">
                    {{ (occurrenceData['recordedby'] ? occurrenceData['recordedby'] : 's.n.') + ' ' }}
                    <span v-if="occurrenceData['recordnumber']">
                        {{ occurrenceData['recordnumber'] + ' ' }}
                    </span>
                    <span v-if="occurrenceData['eventdate']">
                        {{ occurrenceData['eventdate'] }}
                    </span>
                </div>
                <div v-if="occurrenceData['country'] || occurrenceData['stateprovince'] || occurrenceData['county']">
                    <span v-if="occurrenceData['country']">
                        {{ occurrenceData['country'] }}
                    </span>
                    <span v-if="occurrenceData['stateprovince']">
                        {{ (occurrenceData['country'] ? ', ' : '') + occurrenceData['stateprovince'] }}
                    </span>
                    <span v-if="occurrenceData['county']">
                        {{ ((occurrenceData['country'] || occurrenceData['stateprovince']) ? ', ' : '') + occurrenceData['county'] }}
                    </span>
                </div>
                <div v-if="occurrenceData['locality']">
                    {{ occurrenceData['locality'] }}
                </div>
                <div v-if="occurrenceData['decimallatitude'] && occurrenceData['decimallongitude']">
                    {{ occurrenceData['decimallatitude'] + ', ' + occurrenceData['decimallongitude'] }}
                </div>
            </div>
        </template>
    `
};
