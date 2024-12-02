const occurrenceDataUploadModule = {
    props: {
        collid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="row q-mt-xs">
            <div class="col-grow">
                <file-picker-input-element :accepted-types="acceptedFileTypes" :value="selectedFile" :validate-file-size="false" @update:file="(value) => processUploadFile(value[0])"></file-picker-input-element>
            </div>
        </div>
    `,
    components: {
        'file-picker-input-element': filePickerInputElement
    },
    setup() {
        const { processCsvDownload } = useCore();
        const baseStore = useBaseStore();
        const collectionStore = useCollectionStore();

        const acceptedFileTypes = ['geojson'];
        const selectedFile = Vue.ref(null);

        function processUploadFile(file) {
            const fileReader = new FileReader();
            fileReader.onload = () => {
                const csvArr = [];
                const filename = 'rare_plant_upload.csv';
                const geoJSONFormat = new ol.format.GeoJSON();
                const wktFormat = new ol.format.WKT();
                const uploadData = JSON.parse(fileReader.result);
                const uploadFeatures = geoJSONFormat.readFeatures(uploadData);
                uploadFeatures.forEach((feature) => {
                    if(feature){
                        const featureData = {};
                        const featureProps = feature.getProperties();
                        const featureGeometry = feature.getGeometry();
                        const wktStr = wktFormat.writeGeometry(featureGeometry);
                        Object.keys(featureProps).forEach((prop) => {
                            if(prop !== 'geometry'){
                                if(featureProps[prop]){
                                    if(prop.toLowerCase().includes('date')){
                                        const date = new Date(featureProps[prop]);
                                        const year = date.getFullYear();
                                        const month = String(date.getMonth() + 1).padStart(2, '0');
                                        const day = String(date.getDate()).padStart(2, '0');
                                        featureData[prop.toLowerCase()] = `${year}-${month}-${day}`;
                                    }
                                    else{
                                        featureData[prop.toLowerCase()] = isNaN(featureProps[prop]) ? featureProps[prop].trim() : featureProps[prop];
                                    }
                                }
                                else{
                                    featureData[prop.toLowerCase()] = null;
                                }
                            }
                        });
                        featureData['footprintwkt'] = wktStr;
                        csvArr.push(featureData);
                    }
                });
                processCsvDownload(csvArr, filename);
            };
            fileReader.readAsText(file);
        }
        
        return {
            acceptedFileTypes,
            selectedFile,
            processUploadFile
        }
    }
};
