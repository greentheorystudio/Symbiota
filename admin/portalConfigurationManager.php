<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!isset($GLOBALS['IS_ADMIN']) || !$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$confManager = new ConfigurationManager();

$fullConfArr = $confManager->getConfigurationsArr();
$coreConfArr = $fullConfArr['core'];
$additionalConfArr = $fullConfArr['additional'];
$coreConfNames = $confManager->getCoreConfigurationsArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
    <style type="text/css">
        fieldset {
            background-color: #f9f9f9;
            padding:15px;
        }
        legend {
            font-weight: bold;
            font-size: 16px;
        }
        .field-block {
            margin: 5px 0;
            display:flex;
            justify-content: space-between;
        }
        .field-elem {
            width: 600px;
            display:flex;
            justify-content: left;
        }
        .field-label {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
    <script src="../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/jquery.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/jquery-ui.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/symb/shared.js?ver=20211227"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tabs').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
        });

        function enableProtectedEditing(id){
            const disabled = document.getElementById(id).disabled;
            if(disabled){
                document.getElementById(id).disabled = false;
            }
            else{
                document.getElementById(id).disabled = true;
            }
        }

        function showPassword(id){
            document.getElementById(id).type = 'text';
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='#coreconfig'>Core Configurations</a></li>
            <li><a href="#additionalconfig">Additional Configurations</a></li>
        </ul>

        <div id="coreconfig">
            <fieldset style="margin: 10px 0;">
                <legend><b>Server - <span style="color:red;">Do Not Change Unless You Know What You're Doing</span></b></legend>
                <div class="field-block">
                    <span class="field-label">Portal Character Set:  <button type="button" onclick="enableProtectedEditing('CHARSET');">Edit</button></span>
                    <span class="field-elem">
                        <select id="CHARSET" style="width:600px;" disabled>
                            <option value="UTF-8" <?php echo (array_key_exists('CHARSET',$coreConfArr)&&$coreConfArr['CHARSET'] === 'UTF-8'?'selected':''); ?>>UTF-8</option>
                            <option value="ISO-8859-1" <?php echo (array_key_exists('CHARSET',$coreConfArr)&&$coreConfArr['CHARSET'] === 'ISO-8859-1'?'selected':''); ?>>ISO-8859-1</option>
                        </select>
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Maximum Upload Filesize (Mb):  <button type="button" onclick="enableProtectedEditing('MAX_UPLOAD_FILESIZE');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="MAX_UPLOAD_FILESIZE" value="<?php echo (array_key_exists('MAX_UPLOAD_FILESIZE',$coreConfArr)?$coreConfArr['MAX_UPLOAD_FILESIZE']:$confManager->getServerMaxFilesize()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Server Path:  <button type="button" onclick="enableProtectedEditing('SERVER_ROOT');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="SERVER_ROOT" value="<?php echo (array_key_exists('SERVER_ROOT',$coreConfArr)?$coreConfArr['SERVER_ROOT']:$confManager->getServerRootPath()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Browser (Client) Path:  <button type="button" onclick="enableProtectedEditing('CLIENT_ROOT');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="CLIENT_ROOT" value="<?php echo (array_key_exists('CLIENT_ROOT',$coreConfArr)?$coreConfArr['CLIENT_ROOT']:$confManager->getClientRootPath()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Server Log File Path:  <button type="button" onclick="enableProtectedEditing('LOG_PATH');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="LOG_PATH" value="<?php echo (array_key_exists('LOG_PATH',$coreConfArr)?$coreConfArr['LOG_PATH']:$confManager->getServerLogFilePath()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Server Media Upload Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_PATH');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="IMAGE_ROOT_PATH" value="<?php echo (array_key_exists('IMAGE_ROOT_PATH',$coreConfArr)?$coreConfArr['IMAGE_ROOT_PATH']:$confManager->getServerMediaUploadPath()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Browser (Client) Media Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_URL');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="IMAGE_ROOT_URL" value="<?php echo (array_key_exists('IMAGE_ROOT_URL',$coreConfArr)?$coreConfArr['IMAGE_ROOT_URL']:$confManager->getClientMediaRootPath()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Maximum Media Upload Filesize (Mb):  <button type="button" onclick="enableProtectedEditing('IMG_FILE_SIZE_LIMIT');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="IMG_FILE_SIZE_LIMIT" value="<?php echo (array_key_exists('IMG_FILE_SIZE_LIMIT',$coreConfArr)?$coreConfArr['IMG_FILE_SIZE_LIMIT']:$confManager->getServerMaxFilesize()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Portal GUID:  <button type="button" onclick="enableProtectedEditing('PORTAL_GUID');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="PORTAL_GUID" value="<?php echo (array_key_exists('PORTAL_GUID',$coreConfArr)?$coreConfArr['PORTAL_GUID']:$confManager->getGUID()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Security Key:  <button type="button" onclick="enableProtectedEditing('SECURITY_KEY');">Edit</button></span>
                    <span class="field-elem">
                        <input type="text" id="SECURITY_KEY" value="<?php echo (array_key_exists('SECURITY_KEY',$coreConfArr)?$coreConfArr['SECURITY_KEY']:$confManager->getGUID()); ?>" style="width:600px;" disabled />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Portal</b></legend>
                <div class="field-block">
                    <span class="field-label">Portal Title:</span>
                    <span class="field-elem">
                        <input type="text" id="DEFAULT_TITLE" value="<?php echo (array_key_exists('DEFAULT_TITLE',$coreConfArr)?$coreConfArr['DEFAULT_TITLE']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Default Language:</span>
                    <span class="field-elem">
                        <select id="DEFAULT_LANG" style="width:600px;">
                            <option value="en">English</option>
                        </select>
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Admin Email:</span>
                    <span class="field-elem">
                        <input type="text" id="ADMIN_EMAIL" value="<?php echo (array_key_exists('ADMIN_EMAIL',$coreConfArr)?$coreConfArr['ADMIN_EMAIL']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Google Analytics Key:</span>
                    <span class="field-elem">
                        <input type="text" id="GOOGLE_ANALYTICS_KEY" value="<?php echo (array_key_exists('GOOGLE_ANALYTICS_KEY',$coreConfArr)?$coreConfArr['GOOGLE_ANALYTICS_KEY']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Default Collection Category:</span>
                    <span class="field-elem">
                        <select id="DEFAULTCATID" style="width:600px;">
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
                    <span class="field-label">Display Common Names:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="DISPLAY_COMMON_NAMES" value="1" <?php echo (array_key_exists('DISPLAY_COMMON_NAMES',$coreConfArr) && $coreConfArr['DISPLAY_COMMON_NAMES']?'CHECKED':''); ?> />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Dynamic Checklist Radius:</span>
                    <span class="field-elem">
                        <input type="text" id="DYN_CHECKLIST_RADIUS" value="<?php echo (array_key_exists('DYN_CHECKLIST_RADIUS',$coreConfArr)?$coreConfArr['DYN_CHECKLIST_RADIUS']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Portal CSS Version:  <button type="button">Update</button></span>
                    <span class="field-elem">
                        <input type="text" id="CSS_VERSION_LOCAL" value="<?php echo (array_key_exists('CSS_VERSION_LOCAL',$coreConfArr)?$coreConfArr['CSS_VERSION_LOCAL']:''); ?>" style="width:600px;" disabled />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Email</b></legend>
                <div class="field-block">
                    <span class="field-label">Host:</span>
                    <span class="field-elem">
                        <input type="text" id="SMTP_HOST" value="<?php echo (array_key_exists('SMTP_HOST',$coreConfArr)?$coreConfArr['SMTP_HOST']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Port:</span>
                    <span class="field-elem">
                        <input type="text" id="SMTP_PORT" value="<?php echo (array_key_exists('SMTP_PORT',$coreConfArr)?$coreConfArr['SMTP_PORT']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Enable Email Encryption:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="SMTP_ENCRYPTION" value="1" <?php echo (array_key_exists('SMTP_ENCRYPTION',$coreConfArr) && $coreConfArr['SMTP_ENCRYPTION']?'CHECKED':''); ?> />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Encryption Mechanism:</span>
                    <span class="field-elem">
                        <select id="SMTP_ENCRYPTION_MECHANISM" style="width:600px;">
                            <option value="STARTTLS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)&&$coreConfArr['SMTP_ENCRYPTION_MECHANISM'] === 'STARTTLS'?'selected':''); ?>>STARTTLS</option>
                            <option value="SMTPS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$coreConfArr)&&$coreConfArr['SMTP_ENCRYPTION_MECHANISM'] === 'SMTPS'?'selected':''); ?>>SMTPS</option>
                        </select>
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Username:</span>
                    <span class="field-elem">
                        <input type="text" id="SMTP_USERNAME" value="<?php echo (array_key_exists('SMTP_USERNAME',$coreConfArr)?$coreConfArr['SMTP_USERNAME']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Password:  <button type="button" onclick="showPassword('SMTP_PASSWORD');">Show</button></span>
                    <span class="field-elem">
                        <input type="password" id="SMTP_PASSWORD" value="<?php echo (array_key_exists('SMTP_PASSWORD',$coreConfArr)?$coreConfArr['SMTP_PASSWORD']:''); ?>" style="width:600px;" />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Media/Images</b></legend>
                <div class="field-block">
                    <span class="field-label">External Media Domain:</span>
                    <span class="field-elem">
                        <input type="text" id="IMAGE_DOMAIN" value="<?php echo (array_key_exists('IMAGE_DOMAIN',$coreConfArr)?$coreConfArr['IMAGE_DOMAIN']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Web Image Width (px):</span>
                    <span class="field-elem">
                        <input type="text" id="IMG_WEB_WIDTH" value="<?php echo (array_key_exists('IMG_WEB_WIDTH',$coreConfArr)?$coreConfArr['IMG_WEB_WIDTH']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Thumbnail Image Width (px):</span>
                    <span class="field-elem">
                        <input type="text" id="IMG_TN_WIDTH" value="<?php echo (array_key_exists('IMG_TN_WIDTH',$coreConfArr)?$coreConfArr['IMG_TN_WIDTH']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Large Image Width (px):</span>
                    <span class="field-elem">
                        <input type="text" id="IMG_LG_WIDTH" value="<?php echo (array_key_exists('IMG_LG_WIDTH',$coreConfArr)?$coreConfArr['IMG_LG_WIDTH']:''); ?>" style="width:600px;" />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>SOLR</b></legend>
                <div class="field-block">
                    <span class="field-label">SOLR URL:</span>
                    <span class="field-elem">
                        <input type="text" id="SOLR_URL" value="<?php echo (array_key_exists('SOLR_URL',$coreConfArr)?$coreConfArr['SOLR_URL']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">SOLR Import Interval (hours):</span>
                    <span class="field-elem">
                        <input type="text" id="SOLR_FULL_IMPORT_INTERVAL" value="<?php echo (array_key_exists('SOLR_FULL_IMPORT_INTERVAL',$coreConfArr)?$coreConfArr['SOLR_FULL_IMPORT_INTERVAL']:''); ?>" style="width:600px;" />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>GBIF</b></legend>
                <div class="field-block">
                    <span class="field-label">Organization Key:</span>
                    <span class="field-elem">
                        <input type="text" id="GBIF_ORG_KEY" value="<?php echo (array_key_exists('GBIF_ORG_KEY',$coreConfArr)?$coreConfArr['GBIF_ORG_KEY']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Username:</span>
                    <span class="field-elem">
                        <input type="text" id="GBIF_USERNAME" value="<?php echo (array_key_exists('GBIF_USERNAME',$coreConfArr)?$coreConfArr['GBIF_USERNAME']:''); ?>" style="width:600px;" />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Password:  <button type="button" onclick="showPassword('GBIF_PASSWORD');">Show</button></span>
                    <span class="field-elem">
                        <input type="password" id="GBIF_PASSWORD" value="<?php echo (array_key_exists('GBIF_PASSWORD',$coreConfArr)?$coreConfArr['GBIF_PASSWORD']:''); ?>" style="width:600px;" />
                    </span>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Activate Optional Modules</b></legend>
                <div class="field-block">
                    <span class="field-label">Activate Key Module:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="KEY_MOD_IS_ACTIVE" value="1" <?php echo (array_key_exists('KEY_MOD_IS_ACTIVE',$coreConfArr) && $coreConfArr['KEY_MOD_IS_ACTIVE']?'CHECKED':''); ?> />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Activate Exsiccati Module:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="ACTIVATE_EXSICCATI" value="1" <?php echo (array_key_exists('ACTIVATE_EXSICCATI',$coreConfArr) && $coreConfArr['ACTIVATE_EXSICCATI']?'CHECKED':''); ?> />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Activate Checklist FieldGuide Export:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="ACTIVATE_CHECKLIST_FG_EXPORT" value="1" <?php echo (array_key_exists('ACTIVATE_CHECKLIST_FG_EXPORT',$coreConfArr) && $coreConfArr['ACTIVATE_CHECKLIST_FG_EXPORT']?'CHECKED':''); ?> />
                    </span>
                </div>
                <div class="field-block">
                    <span class="field-label">Activate GeoLocate Toolkit:</span>
                    <span class="field-elem">
                        <input type="checkbox" id="ACTIVATE_GEOLOCATE_TOOLKIT" value="1" <?php echo (array_key_exists('ACTIVATE_GEOLOCATE_TOOLKIT',$coreConfArr) && $coreConfArr['ACTIVATE_GEOLOCATE_TOOLKIT']?'CHECKED':''); ?> />
                    </span>
                </div>
            </fieldset>
        </div>

        <div id="additionalconfig">
            <div style="display:flex;justify-content:right;margin:10px;" title="Add Configuration" onclick="toggle('addconfdiv')">
                <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
            </div>
            <div id="addconfdiv" style="display:none">
                <fieldset>
                    <legend><b>Add Configuration</b></legend>
                    <div class="field-block">
                        <span class="field-label">New Configuration Name:</span>
                        <span class="field-elem">
                            <input type="text" id="newConfName" value="" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">New Configuration Value:</span>
                        <span class="field-elem">
                            <input type="text" id="newConfValue" value="" style="width:600px;" />
                        </span>
                    </div>
                    <div style="margin-top:12px;width:98%;display:flex;justify-content:right;">
                        <button type="button">Add Configuration</button>
                    </div>
                </fieldset>
            </div>
            <fieldset style="margin: 10px 0;">
                <legend><b>Additional Configurations</b></legend>
                <?php
                if($additionalConfArr){
                    foreach($additionalConfArr as $confName => $confValue){
                        ?>
                        <div class="field-block">
                            <span class="field-label"><?php echo $confName; ?>:  <button type="button" onclick="deleteConfiguration('<?php echo $confName; ?>');">Delete</button></span>
                            <span class="field-elem">
                                <input type="text" id="<?php echo $confName; ?>" value="<?php echo $confValue; ?>" style="width:600px;" onchange="saveConfigurationChange('<?php echo $confName; ?>');" />
                            </span>
                        </div>
                        <?php
                    }
                }
                else{
                    ?>
                    <div class="field-block">
                        <span class="field-label">No additional configurations set</span>
                        <span class="field-elem"></span>
                    </div>
                    <?php
                }
                ?>
            </fieldset>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
