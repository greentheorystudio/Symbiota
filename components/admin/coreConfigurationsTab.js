const coreConfigurationsTab = {
    template: `
        <div class="column q-gutter-sm">
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Server - <span class="text-red">Do Not Change Unless You Know What You're Doing</span>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Maximum Upload Filesize (MB): 
                                <q-btn color="primary" size="sm" @click="changeEditField('MAX_UPLOAD_FILESIZE');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'MAX_UPLOAD_FILESIZE'" :value="coreData.hasOwnProperty('MAX_UPLOAD_FILESIZE') ? coreData['MAX_UPLOAD_FILESIZE'] : null" @update:value="(value) => processUploadFilesizeChange(value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Server Path: 
                                <q-btn color="primary" size="sm" @click="changeEditField('SERVER_ROOT');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'SERVER_ROOT'" :value="coreData.hasOwnProperty('SERVER_ROOT') ? coreData['SERVER_ROOT'] : null" @update:value="(value) => processServerRootChange(value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Browser (Client) Path: 
                                <q-btn color="primary" size="sm" @click="changeEditField('CLIENT_ROOT');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'CLIENT_ROOT'" :value="coreData.hasOwnProperty('CLIENT_ROOT') ? coreData['CLIENT_ROOT'] : null" @update:value="(value) => processClientRootChange(value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Server Temp Directory Path: 
                                <q-btn color="primary" size="sm" @click="changeEditField('TEMP_DIR_ROOT');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'TEMP_DIR_ROOT'" :value="coreData.hasOwnProperty('TEMP_DIR_ROOT') ? coreData['TEMP_DIR_ROOT'] : null" @update:value="(value) => processServerWritePathChange('TEMP_DIR_ROOT', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Server Media Upload Path:   
                                <q-btn color="primary" size="sm" @click="changeEditField('IMAGE_ROOT_PATH');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'IMAGE_ROOT_PATH'" :value="coreData.hasOwnProperty('IMAGE_ROOT_PATH') ? coreData['IMAGE_ROOT_PATH'] : null" @update:value="(value) => processServerWritePathChange('IMAGE_ROOT_PATH', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Browser (Client) Media Path:   
                                <q-btn color="primary" size="sm" @click="changeEditField('IMAGE_ROOT_URL');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'IMAGE_ROOT_URL'" :value="coreData.hasOwnProperty('IMAGE_ROOT_URL') ? coreData['IMAGE_ROOT_URL'] : null" @update:value="(value) => processConfigurationChange('IMAGE_ROOT_URL', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Portal GUID:   
                                <q-btn color="primary" size="sm" @click="changeEditField('PORTAL_GUID');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'PORTAL_GUID'" :value="coreData.hasOwnProperty('PORTAL_GUID') ? coreData['PORTAL_GUID'] : null" @update:value="(value) => processConfigurationChange('PORTAL_GUID', value, true)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Security Key:   
                                <q-btn color="primary" size="sm" @click="changeEditField('SECURITY_KEY');" label="Edit" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="editField !== 'SECURITY_KEY'" :value="coreData.hasOwnProperty('SECURITY_KEY') ? coreData['SECURITY_KEY'] : null" @update:value="(value) => processConfigurationChange('SECURITY_KEY', value, true)"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Portal
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Portal Title:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('DEFAULT_TITLE') ? coreData['DEFAULT_TITLE'] : null" @update:value="(value) => processConfigurationChange('DEFAULT_TITLE', value, true)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Default Language:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <selector-input-element :options="languageOptionArr" :value="coreData.hasOwnProperty('DEFAULT_LANG') ? coreData['DEFAULT_LANG'] : null" @update:value="(value) => processConfigurationChange('DEFAULT_LANG', value, true)"></selector-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Admin Email:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('ADMIN_EMAIL') ? coreData['ADMIN_EMAIL'] : null" @update:value="(value) => processConfigurationChange('ADMIN_EMAIL', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="collectionCategoryOptionArr.length > 0" class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Default Collection Category:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <selector-input-element :options="collectionCategoryOptionArr" option-value="ccpk" option-label="category" :value="coreData.hasOwnProperty('DEFAULTCATID') ? coreData['DEFAULTCATID'] : null" @update:value="(value) => processConfigurationChange('DEFAULTCATID', value, false)"></selector-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Portal CSS Version:   
                                <q-btn color="primary" size="sm" @click="processUpdateCss();" label="Update" />
                            </div>   
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :disabled="true" :value="coreData.hasOwnProperty('CSS_VERSION_LOCAL') ? coreData['CSS_VERSION_LOCAL'] : null"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Email
                    </div>
                    <div class="q-pl-lg text-body1 text-bold no-wrap">
                        <checkbox-input-element label="Enable Email Encryption" :value="(coreData.hasOwnProperty('SMTP_ENCRYPTION') && Number(coreData['SMTP_ENCRYPTION']) === 1)" @update:value="(value) => processCheckboxConfigurationChange('SMTP_ENCRYPTION', value)"></checkbox-input-element>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Portal Email Address:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('PORTAL_EMAIL_ADDRESS') ? coreData['PORTAL_EMAIL_ADDRESS'] : null" @update:value="(value) => processConfigurationChange('PORTAL_EMAIL_ADDRESS', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                SMTP Host:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('SMTP_HOST') ? coreData['SMTP_HOST'] : null" @update:value="(value) => processConfigurationChange('SMTP_HOST', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                SMTP Port:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('SMTP_PORT') ? coreData['SMTP_PORT'] : null" @update:value="(value) => processConfigurationChange('SMTP_PORT', value, false, true)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Encryption Mechanism:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <selector-input-element :options="smtpEncryptionOptionArr" :value="coreData.hasOwnProperty('SMTP_ENCRYPTION_MECHANISM') ? coreData['SMTP_ENCRYPTION_MECHANISM'] : null" @update:value="(value) => processConfigurationChange('SMTP_ENCRYPTION_MECHANISM', value, false)"></selector-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Username:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('SMTP_USERNAME') ? coreData['SMTP_USERNAME'] : null" @update:value="(value) => processConfigurationChange('SMTP_USERNAME', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Password:   
                                <q-btn color="primary" size="sm" @click="showSmtpPassword = !showSmtpPassword" label="Show" />
                            </div>   
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :data-type="smtpPasswordDataType" :value="coreData.hasOwnProperty('SMTP_PASSWORD') ? coreData['SMTP_PASSWORD'] : null" @update:value="(value) => processConfigurationChange('SMTP_PASSWORD', value, false)"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Media/Images
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Web Image Width (px):
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('IMG_WEB_WIDTH') ? coreData['IMG_WEB_WIDTH'] : null" @update:value="(value) => processConfigurationChange('IMG_WEB_WIDTH', value, true, true)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Thumbnail Image Width (px):
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('IMG_TN_WIDTH') ? coreData['IMG_TN_WIDTH'] : null" @update:value="(value) => processConfigurationChange('IMG_TN_WIDTH', value, true, true)"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        SOLR
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                SOLR URL:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('SOLR_URL') ? coreData['SOLR_URL'] : null" @update:value="(value) => processConfigurationChange('SOLR_URL', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                SOLR Import Interval (hours):
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('SOLR_FULL_IMPORT_INTERVAL') ? coreData['SOLR_FULL_IMPORT_INTERVAL'] : null" @update:value="(value) => processConfigurationChange('SOLR_FULL_IMPORT_INTERVAL', value, false, true)"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        GBIF
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Organization Key:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('GBIF_ORG_KEY') ? coreData['GBIF_ORG_KEY'] : null" @update:value="(value) => processConfigurationChange('GBIF_ORG_KEY', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Username:
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :value="coreData.hasOwnProperty('GBIF_USERNAME') ? coreData['GBIF_USERNAME'] : null" @update:value="(value) => processConfigurationChange('GBIF_USERNAME', value, false)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                Password:   
                                <q-btn color="primary" size="sm" @click="showGbifPassword = !showGbifPassword" label="Show" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element debounce="2000" :data-type="gbifPasswordDataType" :value="coreData.hasOwnProperty('GBIF_PASSWORD') ? coreData['GBIF_PASSWORD'] : null" @update:value="(value) => processConfigurationChange('GBIF_PASSWORD', value, false)"></text-field-input-element>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="q-pa-sm column q-col-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Activate Optional Modules
                    </div>
                    <div class="q-pl-lg text-body1 text-bold no-wrap">
                        <checkbox-input-element label="Mobile Checklist App Enabled" :value="(coreData.hasOwnProperty('APP_ENABLED') && Number(coreData['APP_ENABLED']) === 1)" @update:value="(value) => processCheckboxConfigurationChange('APP_ENABLED', value)"></checkbox-input-element>
                    </div>
                    <div class="q-pl-lg text-body1 text-bold no-wrap">
                        <checkbox-input-element label="Activate Key Module" :value="(coreData.hasOwnProperty('KEY_MOD_IS_ACTIVE') && Number(coreData['KEY_MOD_IS_ACTIVE']) === 1)" @update:value="(value) => processCheckboxConfigurationChange('KEY_MOD_IS_ACTIVE', value)"></checkbox-input-element>
                    </div>
                    <div class="q-pl-lg text-body1 text-bold no-wrap">
                        <checkbox-input-element label="Activate Exsiccati Module" :value="(coreData.hasOwnProperty('ACTIVATE_EXSICCATI') && Number(coreData['ACTIVATE_EXSICCATI']) === 1)" @update:value="(value) => processCheckboxConfigurationChange('ACTIVATE_EXSICCATI', value)"></checkbox-input-element>
                    </div>
                </q-card-section>
            </q-card>
            <div class="q-mt-md column text-body1">
                <div><span class="text-bold">php version:</span> {{ phpVersion }}</div>
                <div><span class="text-bold">Database server:</span> {{ dbServerText }}</div>
                <div><span class="text-bold">Server max post size:</span> {{ maxPostSize }}MB</div>
                <div><span class="text-bold">Server max upload size:</span> {{ maxUploadSize }}MB</div>
            </div>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const collectionCategoryOptionArr = Vue.ref([]);
        const confirmationPopupRef = Vue.ref(null);
        const coreData = Vue.computed(() => configurationStore.getCoreConfigurationData);
        const dbServerText = Vue.computed(() => {
            return (serverData.value.hasOwnProperty('SERVER_DB_PROPS') && serverData.value['SERVER_DB_PROPS'].hasOwnProperty('db') && serverData.value['SERVER_DB_PROPS'].hasOwnProperty('ver')) ? (serverData.value['SERVER_DB_PROPS']['db'] + ' ' + serverData.value['SERVER_DB_PROPS']['ver']) : '';
        });
        const editField = Vue.ref(null);
        const gbifPasswordDataType = Vue.computed(() => {
            return showGbifPassword.value ? 'text' : 'password';
        });
        const languageOptionArr = Vue.ref([
            {value: 'en', label: 'English'}
        ]);
        const maxPostSize = Vue.computed(() => {
            return serverData.value.hasOwnProperty('SERVER_MAX_POST_SIZE') ? serverData.value['SERVER_MAX_POST_SIZE'] : 0;
        });
        const maxUploadSize = Vue.computed(() => {
            return serverData.value.hasOwnProperty('SERVER_MAX_UPLOAD_FILESIZE') ? serverData.value['SERVER_MAX_UPLOAD_FILESIZE'] : 0;
        });
        const phpVersion = Vue.computed(() => {
            return serverData.value.hasOwnProperty('SERVER_PHP_VERSION') ? serverData.value['SERVER_PHP_VERSION'] : '';
        });
        const serverData = Vue.computed(() => configurationStore.getServerData);
        const showGbifPassword = Vue.ref(false);
        const showSmtpPassword = Vue.ref(false);
        const smtpEncryptionOptionArr = Vue.ref([
            {value: 'STARTTLS', label: 'STARTTLS'},
            {value: 'SMTPS', label: 'SMTPS'}
        ]);
        const smtpPasswordDataType = Vue.computed(() => {
            return showSmtpPassword.value ? 'text' : 'password';
        });

        function changeEditField(field){
            if(editField.value === field){
                editField.value = null;
            }
            else{
                editField.value = field;
            }
        }

        function processCallbackResponse(res){
            if(res === 1){
                showNotification('positive','Saved and activated');
            }
            else{
                showNotification('negative', 'There was an error saving and activating the change');
            }
        }

        function processCheckboxConfigurationChange(configName, value){
            if(value){
                configurationStore.addConfigurationValue(configName, '1', (res) => {
                    processCallbackResponse(res);
                });
            }
            else{
                configurationStore.deleteConfigurationValue(configName, (res) => {
                    processCallbackResponse(res);
                });
            }
        }

        function processClientRootChange(value){
            configurationStore.validateClientPath(value, (res) => {
                if(res === 1){
                    processConfigurationChange('CLIENT_ROOT', value, false);
                }
                else{
                    showNotification('negative', 'The path entered is not a valid URL path to a portal');
                }
            });
        }

        function processConfigurationChange(configName, value, required, int = false){
            const confirmText = 'Do you want to save and activate this change?';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    if((int && Number(value) > 0) || (value && value !== '')){
                        if(coreData.value[configName]){
                            configurationStore.updateConfigurationValue(configName, value, (res) => {
                                processCallbackResponse(res);
                            });
                        }
                        else{
                            configurationStore.addConfigurationValue(configName, value, (res) => {
                                processCallbackResponse(res);
                            });
                        }
                    }
                    else if(required){
                        showNotification('negative', 'This value is required');
                    }
                    else{
                        configurationStore.deleteConfigurationValue(configName, (res) => {
                            processCallbackResponse(res);
                        });
                    }
                }
            }});
        }

        function processServerRootChange(value){
            if(value && value !== ''){
                configurationStore.validateServerPath(value, (res) => {
                    if(res === 1){
                        const confirmText = 'Do you want to save and activate this change?';
                        confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                            if(val){
                                configurationStore.updateConfigurationValue('SERVER_ROOT', value, (res) => {
                                    processCallbackResponse(res);
                                });
                            }
                        }});
                    }
                    else{
                        showNotification('negative', 'The path entered is not a valid path to a portal installation on the server');
                    }
                });
            }
            else{
                showNotification('negative', 'This value is required');
            }
        }

        function processServerWritePathChange(configName, value){
            if(value && value !== ''){
                configurationStore.validateServerWritePath(value, (res) => {
                    if(res === 1){
                        const confirmText = 'Do you want to save and activate this change?';
                        confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                            if(val){
                                configurationStore.updateConfigurationValue(configName, value, (res) => {
                                    processCallbackResponse(res);
                                });
                            }
                        }});
                    }
                    else{
                        showNotification('negative', 'The path entered is not a valid path to a portal installation on the server');
                    }
                });
            }
            else{
                showNotification('negative', 'This value is required');
            }
        }

        function processUpdateCss(){
            configurationStore.updateCssVersion((res) => {
                if(res === 1){
                    showNotification('positive','CSS version updated');
                }
                else{
                    showNotification('negative', 'There was an error updating the CSS version');
                }
            });
        }

        function processUploadFilesizeChange(value){
            if(Number(value) > 0){
                if(Number(value) <= Number(maxPostSize.value) && Number(value) <= Number(maxUploadSize.value)){
                    const confirmText = 'Do you want to save and activate this change?';
                    confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                        if(val){
                            configurationStore.updateConfigurationValue('MAX_UPLOAD_FILESIZE', value, (res) => {
                                processCallbackResponse(res);
                            });
                        }
                    }});
                }
                else{
                    showNotification('negative', ('Value can only be whole numbers and it must be less than or equal to the upload_max_filesize and post_max_size php settings on the server. The upload_max_filesize setting is currently set to ' + maxUploadSize.value.toString() + 'MB, and the post_max_size setting is currently set to ' + maxPostSize.value.toString() + 'MB on the server.'));
                }
            }
            else{
                showNotification('negative', 'This value is required');
            }
        }

        function setCollectionCategories() {
            const formData = new FormData();
            formData.append('action', 'getCollectionCategoryArr');
            fetch(collectionCategoryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((result) => {
                collectionCategoryOptionArr.value = result;
            });
        }

        Vue.onMounted(() => {
            setCollectionCategories();
        });

        return {
            collectionCategoryOptionArr,
            confirmationPopupRef,
            coreData,
            dbServerText,
            editField,
            gbifPasswordDataType,
            languageOptionArr,
            maxPostSize,
            maxUploadSize,
            phpVersion,
            showGbifPassword,
            showSmtpPassword,
            smtpEncryptionOptionArr,
            smtpPasswordDataType,
            changeEditField,
            processCheckboxConfigurationChange,
            processClientRootChange,
            processConfigurationChange,
            processServerRootChange,
            processServerWritePathChange,
            processUpdateCss,
            processUploadFilesizeChange
        }
    }
};
