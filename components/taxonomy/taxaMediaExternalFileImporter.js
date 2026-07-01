const taxaMediaExternalFileImporter = {
    template: `
        <div class="q-pa-md">
            <div class="text-h5 text-bold">Batch Uploader</div>
            <div>
                <div class="q-mb-md">
                    To batch upload taxa image, audio, and video files either click the Add files button to select the files to be uploaded or drag and
                    drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
                    <a :href="(clientRoot + '/templates/batchTaxaImageData.csv')" aria-label="Download image template csv" tabindex="0"><span class="text-bold">Use this template for the csv spreadsheet for image files. </span></a>
                    <a :href="(clientRoot + '/templates/batchTaxaMediaData.csv')" aria-label="Download media template csv" tabindex="0"><span class="text-bold">Use this template for the csv spreadsheet for audio and video files.</span></a>
                    Data for image files can be combined with data for audio and video files in the same csv spreadsheet. For each
                    row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
                </div>
                <div class="q-mt-md">
                    <media-file-upload-input-element></media-file-upload-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'media-file-upload-input-element': mediaFileUploadInputElement,
    },
    setup() {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;

        return {
            clientRoot,
        }
    }
};
