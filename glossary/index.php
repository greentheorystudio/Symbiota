<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GlossaryManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$glossId = array_key_exists('glossid',$_REQUEST)?(int)$_REQUEST['glossid']:0;
$glossgrpId = array_key_exists('glossgrpid',$_REQUEST)?(int)$_REQUEST['glossgrpid']:0;
$language = array_key_exists('searchlanguage',$_REQUEST)?$_REQUEST['searchlanguage']:'';
$tid = array_key_exists('searchtaxa',$_REQUEST)?$_REQUEST['searchtaxa']:'';
$searchTerm = array_key_exists('searchterm',$_REQUEST)?$_REQUEST['searchterm']:'';
$deepSearch = array_key_exists('deepsearch',$_POST)?(int)$_POST['deepsearch']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

if(!$language) {
    $language = $GLOBALS['DEFAULT_LANG'];
}
if($language === 'en') {
    $language = 'English';
}
if($language === 'es') {
    $language = 'Spanish';
}

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$glosManager = new GlossaryManager();

$statusStr = '';
if($formSubmit){
	if($formSubmit === 'Add Source'){
		if(!$glosManager->addSource($_POST)) {
            $statusStr = $glosManager->getErrorStr();
        }
	}
	elseif($formSubmit === 'Edit Source'){
		if(!$glosManager->editSource($_POST)) {
            $statusStr = $glosManager->getErrorStr();
        }
	}
	elseif($formSubmit === 'Delete Source'){
		if(!$glosManager->deleteSource($_POST['tid'])) {
            $statusStr = $glosManager->getErrorStr();
        }
	}
}
$languageArr = $glosManager->getLanguageArr();
$langArr = $languageArr['all'];
unset($languageArr['all']);

$taxaArr = $glosManager->getTaxaGroupArr();
$taxonName = ($tid?$taxaArr[$tid]:'');

$addTermUrl = 'addterm.php';
$indTermUrl = 'individual.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary Index</title>
    <meta name="description" content="Glossary index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
		const langArr = {<?php
			$d = '';
			foreach($languageArr as $k => $v){ 
				echo $d.'"'.$k.'":['.$v.']';
				$d = ',';
			}
		?>};

		function verifySearchForm(f){
            const language = f.searchlanguage.value;
            const taxon = f.searchtaxa.value;
            if(!language || !taxon){
				alert("Please select a language and taxonomic group to see term list.");
				return false;
			}
			return true;
		}

		function verifyDownloadForm(f){
            const searchForm = document.searchform;
            f.searchlanguage.value = searchForm.searchlanguage.value;
			f.searchtaxa.value = searchForm.searchtaxa.value;
			f.searchterm.value = searchForm.searchterm.value;
			f.deepsearch.value = searchForm.deepsearch.value;
            const language = f.searchlanguage.value;
            const taxon = f.searchtaxa.value;
            if(!language || !taxon){
				alert("Please select a primary language and taxonomic group to download.");
				return false;
			}

            const downloadtype = f.exporttype.value;
            if(downloadtype === 'translation'){
                let numTranslations = 0;
                const e = f.getElementsByTagName("input");
                for(let i=0; i<e.length; i++){
					if(e[i].name === "language[]"){
						if(e[i].checked === true){
							numTranslations++;
						}
					}
				}
				if(numTranslations > 3){
					alert("Please select a maximum of three translations for the Translation Table. Please be sure to not select the primary language.");
					return false;
				}
				if(numTranslations === 0){
					alert("Please select at least one translation for the Translation Table. Please be sure to not select the primary language.");
					return false;
				}
			}
			return true;
		}

		function openNewTermPopup(){
            const urlStr = '<?php echo $addTermUrl; ?>?rellanguage=<?php echo $language; ?>& taxatid=<?php echo $tid; ?>&taxaname=<?php echo $taxonName; ?>';
            const newWindow = window.open(urlStr, 'addnewpopup', 'toolbar=1,status=1,scrollbars=1,width=1250,height=900,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
		}

		function openTermPopup(glossid){
            let urlStr = '<?php echo $indTermUrl; ?>?glossid=' + glossid;
            const newWindow = window.open(urlStr, 'popup', 'toolbar=1,status=1,scrollbars=1,width=800,height=750,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
			return false;
		}

	</script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/glossary.index.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
	<?php
    include(__DIR__ . '/../header.php');
    ?>
    <div id="breadcrumbs">
        <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
        <a href='index.php'> <b>Glossary</b></a>
    </div>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<?php 
		if($statusStr){
			?>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php 
		}
		if(isset($GLOSSARY_BANNER) && $GLOSSARY_BANNER){
			$bannerUrl = $GLOSSARY_BANNER;
			if(strncmp($bannerUrl, 'http', 4) !== 0 && strncmp($bannerUrl, '/', 1) !== 0){
				$bannerUrl = '../images/layout/'.$bannerUrl;
			}
			echo '<div id="glossaryBannerDiv"><img src="'.$bannerUrl.'" /></div>';
		}
		if(isset($GLOSSARY_DESCRIPTION) && $GLOSSARY_DESCRIPTION){
			echo '<div id="glossaryDescriptionDiv">'.$GLOSSARY_DESCRIPTION.'</div><div style="clear:both;"></div>';
		}
		?>
		<div style="float:right;width:360px;position:relative;">
			<div style="float:right;position:relative">
				<?php 
				if($isEditor){
					?>
					<div>
						<a href="#" onclick="openNewTermPopup();">
							Create New Term
						</a>
					</div>
					<div>
						<a href='glossaryloader.php'>Batch Upload Terms</a>
					</div>
					<?php 
				}
				?>
				<div>
					<a href="#" title="Show download options" onclick="toggle('downloadoptionsdiv');return false;">
						Download Options
					</a>
				</div>
			</div>
			<div id="downloadoptionsdiv" style="display:none;clear:both;position:absolute;right:0;margin-top:45px;background-color:white;">
				<form name="downloadform" action="glossdocexport.php" method="post" onsubmit="return verifyDownloadForm(this);">
					<fieldset style="padding:8px">
						<legend><b>Download Options</b></legend>
						<div style="margin-bottom:8px;">
							Primary language will be language selected to the left.
						</div>
						<div style="margin-bottom:8px;">
							<div>
								<input name="exporttype" type="radio" value="singlelanguage" checked /> Single Language
							</div>
							<div style="margin-left:25px;">
								<input name="images" type="checkbox" value="images" /> Include Images
							</div>
						</div>
						<div>
							<div>
								<input name="exporttype" type="radio" value="translation" /> Translation Table
							</div>
							<div style="float:left;margin-left:25px;">
								<b>Translations</b><br />
								<?php
								foreach($langArr as $k => $v){
									echo '<input name="language[]" type="checkbox" value="'.$v.'" /> '.$v.'<br />';
								}
								?>
							</div>
							<div style="float:left;margin-left:15px;padding-top:1.1em;">
								<input name="definitions" type="radio" value="nodef" checked /> Without Definitions<br />
								<input name="definitions" type="radio" value="onedef" /> Primary Definition Only<br />
								<input name="definitions" type="radio" value="alldef" /> All Definitions
							</div>
						</div>
						<div style="clear:both;padding:15px">
							<input name="searchlanguage" type="hidden" value="" />
							<input name="searchtaxa" type="hidden" value="" />
							<input name="searchterm" type="hidden" value="" />
							<input name="deepsearch" type="hidden" value="" />
							<button name="formsubmit" type="submit" value="Download">Download</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<h2>Search/Browse Glossary</h2>
		<div style="float:left;">
			<form id="searchform" name="searchform" action="index.php" method="post" onsubmit="return verifySearchForm(this);">
				<div style="height:25px;">
					<?php 
					if(count($taxaArr) > 1){
						?>
						<div style="float:left;">
							<b>Taxonomic Group:</b>
							<select id="searchtaxa" name="searchtaxa" style="margin-top:2px;width:300px;" onchange="resetLanguageSelect(this.form)">
								<?php 
								foreach($taxaArr as $k => $v){
									echo '<option value="'.$k.'" '.($k === $tid?'SELECTED':'').'>'.$v.'</option>';
								}
								?>
							</select>
						</div>
						<?php
					}
					else{
						echo '<input name="searchtaxa" type="hidden" value="'.key($taxaArr).'" />';
					}
					if(count($langArr) > 1){
						?>
						<div style="float:left;margin-left:10px;">
							<b>Language:</b>
							<select id="searchlanguage" name="searchlanguage" style="margin-top:2px;" onchange="">
								<?php 
								foreach($langArr as $k => $v){
									echo '<option value="'.$v.'" '.($v === $language || $k === $language?'SELECTED':'').'>'.$v.'</option>';
								}
								?>
							</select>
						</div>
						<?php
					}
					else{
						echo '<input name="searchlanguage" type="hidden" value="'.reset($langArr).'" />';
					}
					?>
				</div>
				<div style="clear:both;">
					<b>Search Term:</b> 
					<input type="text" autocomplete="off" name="searchterm" size="25" value="<?php echo $searchTerm; ?>" />
				</div>
				<div style="margin-left:40px">
					<input name="deepsearch" type="checkbox" value="1" <?php echo $deepSearch?'checked':''; ?> /> 
					<b>search within definitions</b> 
				</div>
				<div style="margin:20px">
					<button name="formsubmit" type="submit" value="Search Terms">Search/Browse Terms</button>
				</div>
			</form>
		</div>
		<div>
			<div style="min-height:200px;clear:both">
				<?php
				if($searchTerm || $tid){
					$termList = $glosManager->getTermSearch($searchTerm,$language,$tid,$deepSearch);
					if($termList){
						$title = 'Terms for '.$taxonName.' in '.$language;
						if($searchTerm){
							$title .= ' and with a keyword of '.$searchTerm;
						}
						?>
						<div>
							<?php 
							echo '<div style="float:left;font-weight:bold;">'.$title.'</div>';
							$sourceArrFull = $glosManager->getTaxonSources($tid);
                            $sourceArr = current($sourceArrFull);
							if($sourceArr){
								?>
								<div style="float:left;margin-left:5px;">
									<div style="" onclick="toggle('sourcesdiv');return false;">
										<a href="#">(Display Sources)</a>
									</div>
								</div>
								<?php
							}
							else if($isEditor){
                                ?>
                                <div style="float:left;margin-left:5px;">
                                    <a href="sources.php?emode=1&tid=<?php echo $tid.'&searchterm='.$searchTerm.'&language='.$language.'&taxa='.$tid; ?>">(Add Sources)</a>
                                </div>
                                <?php
                            }
							?>
						</div>
						<?php
						if($sourceArr){
							?>
							<div id="sourcesdiv" style="clear:both;display:none;padding:5px">
								<fieldset style="margin:15px;padding:20px;">
									<legend><b>Contributors for Taxonomic Group</b></legend>
									<?php
									if($isEditor){
										?>
										<div style="float:right;">
											<a href="sources.php?emode=1&tid=<?php echo $tid.'&searchterm='.$searchTerm.'&language='.$language.'&taxa='.$tid; ?>"><i style="height:20px;width:20px;" class="far fa-edit"></i></a>
										</div>
										<?php
									}
									if($sourceArr['contributorTerm']){
										?>
										<div style="">
											<b>Terms and Definitions contributed by:</b> <?php echo $sourceArr['contributorTerm']; ?>
										</div>
										<?php
									}
									if($sourceArr['contributorImage']){
										?>
										<div style="margin-top:8px;">
											<b>Images contributed by:</b> <?php echo $sourceArr['contributorImage']; ?>
										</div>
										<?php
									}
									if($sourceArr['translator']){
										?>
										<div style="margin-top:8px;">
											<b>Translations by:</b> <?php echo $sourceArr['translator']; ?>
										</div>
										<?php
									}
									if($sourceArr['additionalSources']){
										?>
										<div style="margin-top:8px;">
											<b>Translations and images were also sourced from the following references:</b> <?php echo $sourceArr['additionalSources']; ?>
										</div>
										<?php
									}
									?>
								</fieldset>
							</div>
							<?php 
						}
						echo '<div style="clear:both;padding:10px;"><ul>';
						foreach($termList as $glossId => $termName){
							echo '<li>';
							echo '<a href="#" onclick="openTermPopup('.$glossId.');return false;"><b>'.$termName.'</b></a>';
							echo '</li>';
						}
						echo '</ul></div>';
					}
					elseif($formSubmit){
						echo '<div style="margin-top:10px;font-weight:bold;">There are no terms matching your criteria</div>';
					}
				}
				?>
			</div>
		</div>
	</div>
	<?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
