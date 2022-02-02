<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!isset($GLOBALS['IS_ADMIN']) || !$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$confManager = new ConfigurationManager();

$confArr = $confManager->getConfigurationsArr();
$coreConfArr = $confManager->getCoreConfigurationsArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
    <style type="text/css">
        .ui-tabs .ui-tabs-nav li { width:32%; }
        .ui-tabs .ui-tabs-nav li a { margin-left:10px;}
        fieldset {
            background-color: #f9f9f9;
            padding:15px;
        }
        legend {
            font-weight: bold;
            font-size: 16px;
        }
        .field-block {
            margin: 5px 0px;
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tabs').tabs({
                active: <?php echo $tabIndex; ?>,
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
        });

        function enableProtectedEditing(id){
            const disabled = document.getElementById(id).disabled;
            if(disabled){
                document.getElementById(id).name = id;
                document.getElementById(id).disabled = false;
            }
            else{
                document.getElementById(id).disabled = true;
                document.getElementById(id).removeAttribute('name');
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
            <li><a href='#portalconfig'>Portal Configurations</a></li>
            <li><a href="#spatialconfig">Spatial Configurations</a></li>
        </ul>

        <div id="portalconfig">
            <form name="portalconfform" action="configurationManager.php" method="post">
                <fieldset style="margin: 10px 0;">
                    <legend><b>Server - <span style="color:red;">Do Not Change Unless You Know What You're Doing</span></b></legend>
                    <div class="field-block">
                        <span class="field-label">Portal Character Set:  <button type="button" onclick="enableProtectedEditing('CHARSET');">Edit</button></span>
                        <span class="field-elem">
                            <select id="CHARSET" style="width:600px;" disabled>
                                <option value="UTF-8" <?php echo (array_key_exists('CHARSET',$confArr)&&$confArr['CHARSET'] === 'UTF-8'?'selected':''); ?>>UTF-8</option>
                                <option value="ISO-8859-1" <?php echo (array_key_exists('CHARSET',$confArr)&&$confArr['CHARSET'] === 'ISO-8859-1'?'selected':''); ?>>ISO-8859-1</option>
                            </select>
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Maximum Upload Filesize (Mb):  <button type="button" onclick="enableProtectedEditing('MAX_UPLOAD_FILESIZE');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="MAX_UPLOAD_FILESIZE" value="<?php echo (array_key_exists('MAX_UPLOAD_FILESIZE',$confArr)?$confArr['MAX_UPLOAD_FILESIZE']:$confManager->getServerMaxFilesize()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Server Path:  <button type="button" onclick="enableProtectedEditing('SERVER_ROOT');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="SERVER_ROOT" value="<?php echo (array_key_exists('SERVER_ROOT',$confArr)?$confArr['SERVER_ROOT']:$confManager->getServerRootPath()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Browser (Client) Path:  <button type="button" onclick="enableProtectedEditing('CLIENT_ROOT');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="CLIENT_ROOT" value="<?php echo (array_key_exists('CLIENT_ROOT',$confArr)?$confArr['CLIENT_ROOT']:$confManager->getClientRootPath()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Server Log File Path:  <button type="button" onclick="enableProtectedEditing('LOG_PATH');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="LOG_PATH" value="<?php echo (array_key_exists('LOG_PATH',$confArr)?$confArr['LOG_PATH']:$confManager->getServerLogFilePath()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Server Media Upload Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_PATH');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="IMAGE_ROOT_PATH" value="<?php echo (array_key_exists('IMAGE_ROOT_PATH',$confArr)?$confArr['IMAGE_ROOT_PATH']:$confManager->getServerMediaUploadPath()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Browser (Client) Media Path:  <button type="button" onclick="enableProtectedEditing('IMAGE_ROOT_URL');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="IMAGE_ROOT_URL" value="<?php echo (array_key_exists('IMAGE_ROOT_URL',$confArr)?$confArr['IMAGE_ROOT_URL']:$confManager->getClientMediaRootPath()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Maximum Media Upload Filesize (Mb):  <button type="button" onclick="enableProtectedEditing('IMG_FILE_SIZE_LIMIT');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="IMG_FILE_SIZE_LIMIT" value="<?php echo (array_key_exists('IMG_FILE_SIZE_LIMIT',$confArr)?$confArr['IMG_FILE_SIZE_LIMIT']:$confManager->getServerMaxFilesize()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Portal GUID:  <button type="button" onclick="enableProtectedEditing('PORTAL_GUID');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="PORTAL_GUID" value="<?php echo (array_key_exists('PORTAL_GUID',$confArr)?$confArr['PORTAL_GUID']:$confManager->getGUID()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Security Key:  <button type="button" onclick="enableProtectedEditing('SECURITY_KEY');">Edit</button></span>
                        <span class="field-elem">
                            <input type="text" id="SECURITY_KEY" value="<?php echo (array_key_exists('SECURITY_KEY',$confArr)?$confArr['SECURITY_KEY']:$confManager->getGUID()); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>Portal</b></legend>
                    <div class="field-block">
                        <span class="field-label">Portal Title:</span>
                        <span class="field-elem">
                            <input type="text" name="DEFAULT_TITLE" value="<?php echo (array_key_exists('DEFAULT_TITLE',$confArr)?$confArr['DEFAULT_TITLE']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Default Language:</span>
                        <span class="field-elem">
                            <select name="DEFAULT_LANG" style="width:600px;">
                                <option value="en">English</option>
                            </select>
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Admin Email:</span>
                        <span class="field-elem">
                            <input type="text" name="ADMIN_EMAIL" value="<?php echo (array_key_exists('ADMIN_EMAIL',$confArr)?$confArr['ADMIN_EMAIL']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Google Analytics Key:</span>
                        <span class="field-elem">
                            <input type="text" name="GOOGLE_ANALYTICS_KEY" value="<?php echo (array_key_exists('GOOGLE_ANALYTICS_KEY',$confArr)?$confArr['GOOGLE_ANALYTICS_KEY']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Default Collection Category:</span>
                        <span class="field-elem">
                            <select name="DEFAULTCATID" style="width:600px;">
                                <option value="">Select Collection Category</option>
                                <option value="">------------------------------------</option>
                                <?php
                                $collCatArr = $confManager->getCollectionCategoryArr();
                                foreach($collCatArr as $id => $collName){
                                    echo '<option value="'.$id.'" '.(array_key_exists('DEFAULTCATID',$confArr)&&(int)$confArr['DEFAULTCATID'] === (int)$id?'selected':'').'>'.$collName.'</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Display Common Names:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="DISPLAY_COMMON_NAMES" value="1" <?php echo (array_key_exists('DISPLAY_COMMON_NAMES',$confArr) && $confArr['DISPLAY_COMMON_NAMES']?'CHECKED':''); ?> />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Dynamic Checklist Radius:</span>
                        <span class="field-elem">
                            <input type="text" name="DYN_CHECKLIST_RADIUS" value="<?php echo (array_key_exists('DYN_CHECKLIST_RADIUS',$confArr)?$confArr['DYN_CHECKLIST_RADIUS']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Portal CSS Version:  <button type="button">Update</button></span>
                        <span class="field-elem">
                            <input type="text" name="CSS_VERSION_LOCAL" value="<?php echo (array_key_exists('CSS_VERSION_LOCAL',$confArr)?$confArr['CSS_VERSION_LOCAL']:''); ?>" style="width:600px;" disabled />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>Email</b></legend>
                    <div class="field-block">
                        <span class="field-label">Host:</span>
                        <span class="field-elem">
                            <input type="text" name="SMTP_HOST" value="<?php echo (array_key_exists('SMTP_HOST',$confArr)?$confArr['SMTP_HOST']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Port:</span>
                        <span class="field-elem">
                            <input type="text" name="SMTP_PORT" value="<?php echo (array_key_exists('SMTP_PORT',$confArr)?$confArr['SMTP_PORT']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Enable Email Encryption:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="SMTP_ENCRYPTION" value="1" <?php echo (array_key_exists('SMTP_ENCRYPTION',$confArr) && $confArr['SMTP_ENCRYPTION']?'CHECKED':''); ?> />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Encryption Mechanism:</span>
                        <span class="field-elem">
                            <select name="SMTP_ENCRYPTION_MECHANISM" style="width:600px;">
                                <option value="STARTTLS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$confArr)&&$confArr['SMTP_ENCRYPTION_MECHANISM'] === 'STARTTLS'?'selected':''); ?>>STARTTLS</option>
                                <option value="SMTPS" <?php echo (array_key_exists('SMTP_ENCRYPTION_MECHANISM',$confArr)&&$confArr['SMTP_ENCRYPTION_MECHANISM'] === 'SMTPS'?'selected':''); ?>>SMTPS</option>
                            </select>
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Username:</span>
                        <span class="field-elem">
                            <input type="text" name="SMTP_USERNAME" value="<?php echo (array_key_exists('SMTP_USERNAME',$confArr)?$confArr['SMTP_USERNAME']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Password:  <button type="button" onclick="showPassword('SMTP_PASSWORD');">Show</button></span>
                        <span class="field-elem">
                            <input type="password" id="SMTP_PASSWORD" name="SMTP_PASSWORD" value="<?php echo (array_key_exists('SMTP_PASSWORD',$confArr)?$confArr['SMTP_PASSWORD']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>Media/Images</b></legend>
                    <div class="field-block">
                        <span class="field-label">External Media Domain:</span>
                        <span class="field-elem">
                            <input type="text" name="IMAGE_DOMAIN" value="<?php echo (array_key_exists('IMAGE_DOMAIN',$confArr)?$confArr['IMAGE_DOMAIN']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Web Image Width (px):</span>
                        <span class="field-elem">
                            <input type="text" name="IMG_WEB_WIDTH" value="<?php echo (array_key_exists('IMG_WEB_WIDTH',$confArr)?$confArr['IMG_WEB_WIDTH']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Thumbnail Image Width (px):</span>
                        <span class="field-elem">
                            <input type="text" name="IMG_TN_WIDTH" value="<?php echo (array_key_exists('IMG_TN_WIDTH',$confArr)?$confArr['IMG_TN_WIDTH']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Large Image Width (px):</span>
                        <span class="field-elem">
                            <input type="text" name="IMG_LG_WIDTH" value="<?php echo (array_key_exists('IMG_LG_WIDTH',$confArr)?$confArr['IMG_LG_WIDTH']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>SOLR</b></legend>
                    <div class="field-block">
                        <span class="field-label">SOLR URL:</span>
                        <span class="field-elem">
                            <input type="text" name="SOLR_URL" value="<?php echo (array_key_exists('SOLR_URL',$confArr)?$confArr['SOLR_URL']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">SOLR Import Interval (hours):</span>
                        <span class="field-elem">
                            <input type="text" name="SOLR_FULL_IMPORT_INTERVAL" value="<?php echo (array_key_exists('SOLR_FULL_IMPORT_INTERVAL',$confArr)?$confArr['SOLR_FULL_IMPORT_INTERVAL']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>GBIF</b></legend>
                    <div class="field-block">
                        <span class="field-label">Organization Key:</span>
                        <span class="field-elem">
                            <input type="text" name="GBIF_ORG_KEY" value="<?php echo (array_key_exists('GBIF_ORG_KEY',$confArr)?$confArr['GBIF_ORG_KEY']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Username:</span>
                        <span class="field-elem">
                            <input type="text" name="GBIF_USERNAME" value="<?php echo (array_key_exists('GBIF_USERNAME',$confArr)?$confArr['GBIF_USERNAME']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Password:  <button type="button" onclick="showPassword('GBIF_PASSWORD');">Show</button></span>
                        <span class="field-elem">
                            <input type="password" id="GBIF_PASSWORD" name="GBIF_PASSWORD" value="<?php echo (array_key_exists('GBIF_PASSWORD',$confArr)?$confArr['GBIF_PASSWORD']:''); ?>" style="width:600px;" />
                        </span>
                    </div>
                </fieldset>
                <fieldset style="margin: 10px 0;">
                    <legend><b>Activate Optional Modules</b></legend>
                    <div class="field-block">
                        <span class="field-label">Activate Key Module:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="KEY_MOD_IS_ACTIVE" value="1" <?php echo (array_key_exists('KEY_MOD_IS_ACTIVE',$confArr) && $confArr['KEY_MOD_IS_ACTIVE']?'CHECKED':''); ?> />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Activate Exsiccati Module:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="ACTIVATE_EXSICCATI" value="1" <?php echo (array_key_exists('ACTIVATE_EXSICCATI',$confArr) && $confArr['ACTIVATE_EXSICCATI']?'CHECKED':''); ?> />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Activate Checklist FieldGuide Export:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="ACTIVATE_CHECKLIST_FG_EXPORT" value="1" <?php echo (array_key_exists('ACTIVATE_CHECKLIST_FG_EXPORT',$confArr) && $confArr['ACTIVATE_CHECKLIST_FG_EXPORT']?'CHECKED':''); ?> />
                        </span>
                    </div>
                    <div class="field-block">
                        <span class="field-label">Activate GeoLocate Toolkit:</span>
                        <span class="field-elem">
                            <input type="checkbox" name="ACTIVATE_GEOLOCATE_TOOLKIT" value="1" <?php echo (array_key_exists('ACTIVATE_GEOLOCATE_TOOLKIT',$confArr) && $confArr['ACTIVATE_GEOLOCATE_TOOLKIT']?'CHECKED':''); ?> />
                        </span>
                    </div>
                </fieldset>
            </form>
        </div>

        <div id="spatialconfig">

        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
