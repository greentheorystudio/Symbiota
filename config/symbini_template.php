<?php
$GLOBALS['DEFAULT_LANG'] = 'en';
$GLOBALS['DEFAULT_PROJ_ID'] = 1;
$GLOBALS['DEFAULTCATID'] = 0;
$GLOBALS['DEFAULT_TITLE'] = '';
$GLOBALS['TID_FOCUS'] = '';
$GLOBALS['ADMIN_EMAIL'] = '';
$GLOBALS['CHARSET'] = '';					//ISO-8859-1 or UTF-8
$GLOBALS['MAX_UPLOAD_FILESIZE'] = 20000000;
$GLOBALS['PORTAL_GUID'] = '';				//Typically a UUID
$GLOBALS['SECURITY_KEY'] = '';				//Typically a UUID used to verify access to certain web service

$GLOBALS['CLIENT_ROOT'] = '';				//URL path to project root folder (relative path w/o domain, e.g. '/seinet')
$GLOBALS['SERVER_ROOT'] = '';				//Full path to Symbiota project root folder
$GLOBALS['LOG_PATH'] = $GLOBALS['SERVER_ROOT'].'/content/logs';					//Must be writable by Apache; will use <SYMBIOTA_ROOT>/temp/logs if not specified

$GLOBALS['SMTP_HOST'] = '';                                //SMTP Host
$GLOBALS['SMTP_PORT'] = 587;                               //SMTP Port
$GLOBALS['SMTP_ENCRYPTION'] = true;                        //Use email encryption
$GLOBALS['SMTP_ENCRYPTION_MECHANISM'] = 'STARTTLS';        //SMTP encryption mechanism - STARTTLS or SMTPS
$GLOBALS['SMTP_USERNAME'] = '';                            //SMTP Username
$GLOBALS['SMTP_PASSWORD'] = '';                            //SMTP Password

$GLOBALS['IMAGE_DOMAIN'] = '';				//Domain path to images, if different from portal
$GLOBALS['IMAGE_ROOT_URL'] = '/content/imglib';				//URL path to images
$GLOBALS['IMAGE_ROOT_PATH'] = $GLOBALS['SERVER_ROOT'].'/content/imglib';			//Writable path to images, especially needed for downloading images

$GLOBALS['IMG_WEB_WIDTH'] = 1400;
$GLOBALS['IMG_TN_WIDTH'] = 200;
$GLOBALS['IMG_LG_WIDTH'] = 3200;
$GLOBALS['IMG_FILE_SIZE_LIMIT'] = 300000;		//Files above this size limit and still within pixel width limits will still be resaved w/ some compression
$GLOBALS['IPLANT_IMAGE_IMPORT_PATH'] = '';		//Path used to map/import images uploaded to the iPlant image server (e.g. /home/shared/project-name/--INSTITUTION_CODE--/, the --INSTITUTION_CODE-- text will be replaced with collection's institution code)

//$GLOBALS['USE_IMAGE_MAGICK'] = 0;		//1 = ImageMagick resize images, given that it's installed (faster, less memory intensive)
$GLOBALS['TESSERACT_PATH'] = ''; 			//Needed for OCR function in the occurrence editor page

$GLOBALS['KEY_MOD_IS_ACTIVE'] = 1;

//Configurations for Apache SOLR integration
$GLOBALS['SOLR_URL'] = '';   // URL for SOLR instance indexing data for this portal
$GLOBALS['SOLR_FULL_IMPORT_INTERVAL'] = 0;   // Number of hours between full imports of SOLR index.

//Configurations for publishing to GBIF
$GLOBALS['GBIF_USERNAME'] = '';                //GBIF username which portal will use to publish
$GLOBALS['GBIF_PASSWORD'] = '';                //GBIF password which portal will use to publish
$GLOBALS['GBIF_ORG_KEY'] = '';                 //GBIF organization key for organization which is hosting this portal

//Misc variables
$GLOBALS['SPATIAL_INITIAL_CENTER'] = '';	    //Initial map center for Spatial Module. Default: '[-110.90713, 32.21976]'
$GLOBALS['SPATIAL_INITIAL_ZOOM'] = '';			//Initial zoom for Spatial Module. Default: 7
$GLOBALS['GEOREFERENCE_POLITICAL_DIVISIONS'] = false;			//Allow Batch Georeference module to georeference records without locality description, but with county
$GLOBALS['GOOGLE_ANALYTICS_KEY'] = '';			//Needed for setting up Google Analytics
$GLOBALS['GOOGLE_TAG_MANAGER_ID'] = '';			//Needed for setting up Google Tag Manager
$GLOBALS['EOL_KEY'] = '';						//Not required, but good to add a key if you plan to do a lot of EOL mapping
$GLOBALS['PORTAL_TAXA_DESC'] = '';				//Preferred taxa descriptions for the portal.
$GLOBALS['DYN_CHECKLIST_RADIUS'] = 10;			//Controls size of concentric rings that are sampled when building Dynamic Checklist
$GLOBALS['DISPLAY_COMMON_NAMES'] = 1;			//Display common names in species profile page and checklists displays
$GLOBALS['ACTIVATE_EXSICCATI'] = 0;			//Activates exsiccati fields within data entry pages; adding link to exsiccati search tools to portal menu is recommended
$GLOBALS['ACTIVATE_CHECKLIST_FG_EXPORT'] = 0;			//Activates checklist fieldguide export tool
$GLOBALS['GENBANK_SUB_TOOL_PATH'] = '';	//Path to GenBank Submission tool installation
$GLOBALS['ACTIVATE_GEOLOCATE_TOOLKIT'] = 0;	//Activates GeoLocate Toolkit located within the Processing Toolkit menu items

$GLOBALS['RIGHTS_TERMS'] = array(
    'CC0 1.0 (Public-domain)' => 'http://creativecommons.org/publicdomain/zero/1.0/',
    'CC BY (Attribution)' => 'http://creativecommons.org/licenses/by/4.0/',
    'CC BY-NC (Attribution-Non-Commercial)' => 'http://creativecommons.org/licenses/by-nc/4.0/',
    'CC BY-NC-ND 4.0 (Attribution-NonCommercial-NoDerivatives 4.0 International)' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/'
);
$GLOBALS['CSS_VERSION_LOCAL'] = '20170414';		//Changing this variable will force a refresh of main.css styles within users browser cache for all pages
