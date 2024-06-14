<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Configurations.php');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../../index.php');
}

$confManager = new Configurations();

$fullConfArr = $confManager->getConfigurationsArr();
$coreConfArr = $fullConfArr['core'];
$databaseProperties = $confManager->getDatabasePropArr();
?>
<div id="coreconfig">
    <fieldset style="margin: 10px 0;">
        <legend><b>Server - <span style="color:red;">Do Not Change Unless You Know What You're Doing</span></b></legend>
        <div class="field-block">
            <span class="field-label">Maximum Upload Filesize (Mb):  <button type="button" onclick="enableProtectedEditing('MAX_UPLOAD_FILESIZE');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="MAX_UPLOAD_FILESIZE" value="<?php echo (array_key_exists('MAX_UPLOAD_FILESIZE',$coreConfArr)?$coreConfArr['MAX_UPLOAD_FILESIZE']:''); ?>" style="width:600px;" onchange="processUploadFilesizeConfigurationChange('MAX_UPLOAD_FILESIZE','<?php echo (array_key_exists('MAX_UPLOAD_FILESIZE',$coreConfArr)?$coreConfArr['MAX_UPLOAD_FILESIZE']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Server Path:  <button type="button" onclick="enableProtectedEditing('SERVER_ROOT');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="SERVER_ROOT" value="<?php echo (array_key_exists('SERVER_ROOT',$coreConfArr)?$coreConfArr['SERVER_ROOT']:''); ?>" style="width:600px;" onchange="processServerPathConfigurationChange('SERVER_ROOT','<?php echo (array_key_exists('SERVER_ROOT',$coreConfArr)?$coreConfArr['SERVER_ROOT']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Browser (Client) Path:  <button type="button" onclick="enableProtectedEditing('CLIENT_ROOT');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="CLIENT_ROOT" value="<?php echo (array_key_exists('CLIENT_ROOT',$coreConfArr)?$coreConfArr['CLIENT_ROOT']:''); ?>" style="width:600px;" onchange="processClientPathConfigurationChange('CLIENT_ROOT','<?php echo (array_key_exists('CLIENT_ROOT',$coreConfArr)?$coreConfArr['CLIENT_ROOT']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Server Temp Directory Path:  <button type="button" onclick="enableProtectedEditing('TEMP_DIR_ROOT');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="TEMP_DIR_ROOT" value="<?php echo (array_key_exists('TEMP_DIR_ROOT',$coreConfArr)?$coreConfArr['TEMP_DIR_ROOT']:''); ?>" style="width:600px;" onchange="processServerWritePathConfigurationChange('TEMP_DIR_ROOT','<?php echo (array_key_exists('TEMP_DIR_ROOT',$coreConfArr)?$coreConfArr['TEMP_DIR_ROOT']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Server Log File Path:  <button type="button" onclick="enableProtectedEditing('LOG_PATH');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="LOG_PATH" value="<?php echo (array_key_exists('LOG_PATH',$coreConfArr)?$coreConfArr['LOG_PATH']:''); ?>" style="width:600px;" onchange="processServerWritePathConfigurationChange('LOG_PATH','<?php echo (array_key_exists('LOG_PATH',$coreConfArr)?$coreConfArr['LOG_PATH']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Server Media Upload Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_PATH');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="IMAGE_ROOT_PATH" value="<?php echo (array_key_exists('IMAGE_ROOT_PATH',$coreConfArr)?$coreConfArr['IMAGE_ROOT_PATH']:''); ?>" style="width:600px;" onchange="processServerWritePathConfigurationChange('IMAGE_ROOT_PATH','<?php echo (array_key_exists('IMAGE_ROOT_PATH',$coreConfArr)?$coreConfArr['IMAGE_ROOT_PATH']:''); ?>');" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Browser (Client) Media Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_URL');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="IMAGE_ROOT_URL" value="<?php echo (array_key_exists('IMAGE_ROOT_URL',$coreConfArr)?$coreConfArr['IMAGE_ROOT_URL']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('IMAGE_ROOT_URL','<?php echo (array_key_exists('IMAGE_ROOT_URL',$coreConfArr)?$coreConfArr['IMAGE_ROOT_URL']:''); ?>',false);" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Portal GUID:  <button type="button" onclick="enableProtectedEditing('PORTAL_GUID');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="PORTAL_GUID" value="<?php echo (array_key_exists('PORTAL_GUID',$coreConfArr)?$coreConfArr['PORTAL_GUID']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('PORTAL_GUID','<?php echo (array_key_exists('PORTAL_GUID',$coreConfArr)?$coreConfArr['PORTAL_GUID']:''); ?>',true);" disabled />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Security Key:  <button type="button" onclick="enableProtectedEditing('SECURITY_KEY');">Edit</button></span>
            <span class="field-elem">
                <input type="text" id="SECURITY_KEY" value="<?php echo (array_key_exists('SECURITY_KEY',$coreConfArr)?$coreConfArr['SECURITY_KEY']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('SECURITY_KEY','<?php echo (array_key_exists('SECURITY_KEY',$coreConfArr)?$coreConfArr['SECURITY_KEY']:''); ?>',true);" disabled />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>Portal</b></legend>
        <div class="field-block">
            <span class="field-label">Portal Title:</span>
            <span class="field-elem">
                <input type="text" id="DEFAULT_TITLE" value="<?php echo (array_key_exists('DEFAULT_TITLE',$coreConfArr)?$coreConfArr['DEFAULT_TITLE']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('DEFAULT_TITLE','<?php echo (array_key_exists('DEFAULT_TITLE',$coreConfArr)?$coreConfArr['DEFAULT_TITLE']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Default Language:</span>
            <span class="field-elem">
                <select id="DEFAULT_LANG" style="width:600px;" onchange="processTextConfigurationChange('DEFAULT_LANG','<?php echo (array_key_exists('DEFAULT_LANG',$coreConfArr)?$coreConfArr['DEFAULT_LANG']:''); ?>',false);" >
                    <option value="en">English</option>
                </select>
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Admin Email:</span>
            <span class="field-elem">
                <input type="text" id="ADMIN_EMAIL" value="<?php echo (array_key_exists('ADMIN_EMAIL',$coreConfArr)?$coreConfArr['ADMIN_EMAIL']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('ADMIN_EMAIL','<?php echo (array_key_exists('ADMIN_EMAIL',$coreConfArr)?$coreConfArr['ADMIN_EMAIL']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Google Analytics Key:</span>
            <span class="field-elem">
                <input type="text" id="GOOGLE_ANALYTICS_KEY" value="<?php echo (array_key_exists('GOOGLE_ANALYTICS_KEY',$coreConfArr)?$coreConfArr['GOOGLE_ANALYTICS_KEY']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('GOOGLE_ANALYTICS_KEY','<?php echo (array_key_exists('GOOGLE_ANALYTICS_KEY',$coreConfArr)?$coreConfArr['GOOGLE_ANALYTICS_KEY']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Default Collection Category:</span>
            <span class="field-elem">
                <select id="DEFAULTCATID" style="width:600px;" onchange="processTextConfigurationChange('DEFAULTCATID','<?php echo (array_key_exists('DEFAULTCATID',$coreConfArr)?$coreConfArr['DEFAULTCATID']:''); ?>',false);" >
                    <option value="">Select Collection Category</option>
                    <option value="">------------------------------------</option>
                    <?php
                    $collCatArr = $confManager->getCollectionCategoryArr();
                    foreach($collCatArr as $id => $collName){
                        echo '<option value="'.$id.'" '.(array_key_exists('DEFAULTCATID',$coreConfArr)&&(int)$coreConfArr['DEFAULTCATID'] === (int)$id?'selected':'').'>'.$collName.'</option>';
                    }
                    ?>
                </select>
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Dynamic Checklist Radius:</span>
            <span class="field-elem">
                <input type="text" id="DYN_CHECKLIST_RADIUS" value="<?php echo (array_key_exists('DYN_CHECKLIST_RADIUS',$coreConfArr)?$coreConfArr['DYN_CHECKLIST_RADIUS']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('DYN_CHECKLIST_RADIUS','<?php echo (array_key_exists('DYN_CHECKLIST_RADIUS',$coreConfArr)?$coreConfArr['DYN_CHECKLIST_RADIUS']:''); ?>',true);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Portal CSS Version:  <button type="button" onclick="processUpdateCss();">Update</button></span>
            <span class="field-elem">
                <input type="text" id="CSS_VERSION_LOCAL" value="<?php echo (array_key_exists('CSS_VERSION_LOCAL',$coreConfArr)?$coreConfArr['CSS_VERSION_LOCAL']:''); ?>" style="width:600px;" disabled />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>Email</b></legend>
        <div class="field-block">
            <span class="field-label">Portal Email Address:</span>
            <span class="field-elem">
                <input type="text" id="PORTAL_EMAIL_ADDRESS" value="<?php echo (array_key_exists('PORTAL_EMAIL_ADDRESS',$coreConfArr)?$coreConfArr['PORTAL_EMAIL_ADDRESS']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('PORTAL_EMAIL_ADDRESS','<?php echo (array_key_exists('PORTAL_EMAIL_ADDRESS',$coreConfArr)?$coreConfArr['PORTAL_EMAIL_ADDRESS']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Host:</span>
            <span class="field-elem">
                <input type="text" id="SMTP_HOST" value="<?php echo (array_key_exists('SMTP_HOST',$coreConfArr)?$coreConfArr['SMTP_HOST']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('SMTP_HOST','<?php echo (array_key_exists('SMTP_HOST',$coreConfArr)?$coreConfArr['SMTP_HOST']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Port:</span>
            <span class="field-elem">
                <input type="text" id="SMTP_PORT" value="<?php echo (array_key_exists('SMTP_PORT',$coreConfArr)?$coreConfArr['SMTP_PORT']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('SMTP_PORT','<?php echo (array_key_exists('SMTP_PORT',$coreConfArr)?$coreConfArr['SMTP_PORT']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Enable Email Encryption:</span>
            <span class="field-elem">
                <input type="checkbox" id="SMTP_ENCRYPTION" value="1" onchange="processCheckConfigurationChange('SMTP_ENCRYPTION');processTextConfigurationChange('SMTP_ENCRYPTION_MECHANISM','<?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)?$coreConfArr['SMTP_ENCRYPTION_MECHANISM']:''); ?>',false);" <?php echo (array_key_exists('SMTP_ENCRYPTION',$coreConfArr) && $coreConfArr['SMTP_ENCRYPTION']?'CHECKED':''); ?> />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Encryption Mechanism:</span>
            <span class="field-elem">
                <select id="SMTP_ENCRYPTION_MECHANISM" style="width:600px;" onchange="processTextConfigurationChange('SMTP_ENCRYPTION_MECHANISM','<?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)?$coreConfArr['SMTP_ENCRYPTION_MECHANISM']:''); ?>',false);" >
                    <option value="STARTTLS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)&&$coreConfArr['SMTP_ENCRYPTION_MECHANISM'] === 'STARTTLS'?'selected':''); ?>>STARTTLS</option>
                    <option value="SMTPS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)&&$coreConfArr['SMTP_ENCRYPTION_MECHANISM'] === 'SMTPS'?'selected':''); ?>>SMTPS</option>
                </select>
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Username:</span>
            <span class="field-elem">
                <input type="text" id="SMTP_USERNAME" value="<?php echo (array_key_exists('SMTP_USERNAME',$coreConfArr)?$coreConfArr['SMTP_USERNAME']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('SMTP_USERNAME','<?php echo (array_key_exists('SMTP_USERNAME',$coreConfArr)?$coreConfArr['SMTP_USERNAME']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Password:  <button type="button" onclick="showPassword('SMTP_PASSWORD');">Show</button></span>
            <span class="field-elem">
                <input type="password" id="SMTP_PASSWORD" value="<?php echo (array_key_exists('SMTP_PASSWORD',$coreConfArr)?$coreConfArr['SMTP_PASSWORD']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('SMTP_PASSWORD','<?php echo (array_key_exists('SMTP_PASSWORD',$coreConfArr)?$coreConfArr['SMTP_PASSWORD']:''); ?>',false);" autocomplete="new-password" />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>Media/Images</b></legend>
        <div class="field-block">
            <span class="field-label">Web Image Width (px):</span>
            <span class="field-elem">
                <input type="text" id="IMG_WEB_WIDTH" value="<?php echo (array_key_exists('IMG_WEB_WIDTH',$coreConfArr)?$coreConfArr['IMG_WEB_WIDTH']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('IMG_WEB_WIDTH','<?php echo (array_key_exists('IMG_WEB_WIDTH',$coreConfArr)?$coreConfArr['IMG_WEB_WIDTH']:''); ?>',true);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Thumbnail Image Width (px):</span>
            <span class="field-elem">
                <input type="text" id="IMG_TN_WIDTH" value="<?php echo (array_key_exists('IMG_TN_WIDTH',$coreConfArr)?$coreConfArr['IMG_TN_WIDTH']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('IMG_TN_WIDTH','<?php echo (array_key_exists('IMG_TN_WIDTH',$coreConfArr)?$coreConfArr['IMG_TN_WIDTH']:''); ?>',true);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Large Image Width (px):</span>
            <span class="field-elem">
                <input type="text" id="IMG_LG_WIDTH" value="<?php echo (array_key_exists('IMG_LG_WIDTH',$coreConfArr)?$coreConfArr['IMG_LG_WIDTH']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('IMG_LG_WIDTH','<?php echo (array_key_exists('IMG_LG_WIDTH',$coreConfArr)?$coreConfArr['IMG_LG_WIDTH']:''); ?>',true);" />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>SOLR</b></legend>
        <div class="field-block">
            <span class="field-label">SOLR URL:</span>
            <span class="field-elem">
                <input type="text" id="SOLR_URL" value="<?php echo (array_key_exists('SOLR_URL',$coreConfArr)?$coreConfArr['SOLR_URL']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('SOLR_URL','<?php echo (array_key_exists('SOLR_URL',$coreConfArr)?$coreConfArr['SOLR_URL']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">SOLR Import Interval (hours):</span>
            <span class="field-elem">
                <input type="text" id="SOLR_FULL_IMPORT_INTERVAL" value="<?php echo (array_key_exists('SOLR_FULL_IMPORT_INTERVAL',$coreConfArr)?$coreConfArr['SOLR_FULL_IMPORT_INTERVAL']:''); ?>" style="width:600px;" onchange="processIntConfigurationChange('SOLR_FULL_IMPORT_INTERVAL','<?php echo (array_key_exists('SOLR_FULL_IMPORT_INTERVAL',$coreConfArr)?$coreConfArr['SOLR_FULL_IMPORT_INTERVAL']:''); ?>',false);" />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>GBIF</b></legend>
        <div class="field-block">
            <span class="field-label">Organization Key:</span>
            <span class="field-elem">
                <input type="text" id="GBIF_ORG_KEY" value="<?php echo (array_key_exists('GBIF_ORG_KEY',$coreConfArr)?$coreConfArr['GBIF_ORG_KEY']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('GBIF_ORG_KEY','<?php echo (array_key_exists('GBIF_ORG_KEY',$coreConfArr)?$coreConfArr['GBIF_ORG_KEY']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Username:</span>
            <span class="field-elem">
                <input type="text" id="GBIF_USERNAME" value="<?php echo (array_key_exists('GBIF_USERNAME',$coreConfArr)?$coreConfArr['GBIF_USERNAME']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('GBIF_USERNAME','<?php echo (array_key_exists('GBIF_USERNAME',$coreConfArr)?$coreConfArr['GBIF_USERNAME']:''); ?>',false);" />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Password:  <button type="button" onclick="showPassword('GBIF_PASSWORD');">Show</button></span>
            <span class="field-elem">
                <input type="password" id="GBIF_PASSWORD" value="<?php echo (array_key_exists('GBIF_PASSWORD',$coreConfArr)?$coreConfArr['GBIF_PASSWORD']:''); ?>" style="width:600px;" onchange="processTextConfigurationChange('GBIF_PASSWORD','<?php echo (array_key_exists('GBIF_PASSWORD',$coreConfArr)?$coreConfArr['GBIF_PASSWORD']:''); ?>',false);" autocomplete="new-password" />
            </span>
        </div>
    </fieldset>
    <fieldset style="margin: 10px 0;">
        <legend><b>Activate Optional Modules</b></legend>
        <div class="field-block">
            <span class="field-label">Activate Key Module:</span>
            <span class="field-elem">
                <input type="checkbox" id="KEY_MOD_IS_ACTIVE" value="1" onchange="processCheckConfigurationChange('KEY_MOD_IS_ACTIVE');" <?php echo (array_key_exists('KEY_MOD_IS_ACTIVE',$coreConfArr) && $coreConfArr['KEY_MOD_IS_ACTIVE']?'CHECKED':''); ?> />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Activate Exsiccati Module:</span>
            <span class="field-elem">
                <input type="checkbox" id="ACTIVATE_EXSICCATI" value="1" onchange="processCheckConfigurationChange('ACTIVATE_EXSICCATI');" <?php echo (array_key_exists('ACTIVATE_EXSICCATI',$coreConfArr) && $coreConfArr['ACTIVATE_EXSICCATI']?'CHECKED':''); ?> />
            </span>
        </div>
        <div class="field-block">
            <span class="field-label">Activate Checklist FieldGuide Export:</span>
            <span class="field-elem">
                <input type="checkbox" id="ACTIVATE_CHECKLIST_FG_EXPORT" value="1" onchange="processCheckConfigurationChange('ACTIVATE_CHECKLIST_FG_EXPORT');" <?php echo (array_key_exists('ACTIVATE_CHECKLIST_FG_EXPORT',$coreConfArr) && $coreConfArr['ACTIVATE_CHECKLIST_FG_EXPORT']?'CHECKED':''); ?> />
            </span>
        </div>
    </fieldset>
    <div style="margin-top:20px;">
        <b>php version:</b> <?php echo $confManager->getPhpVersion(); ?><br />
        <b>Database server:</b> <?php echo $databaseProperties['db'].' '.$databaseProperties['ver']; ?>
    </div>
</div>
