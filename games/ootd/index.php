<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/GamesManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$pid = array_key_exists('pid',$_REQUEST)?(int)$_REQUEST['pid']:0;
$submitAction = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$oodID = array_key_exists('oodid',$_REQUEST)?(int)$_REQUEST['oodid']:1;
$ootdGameChecklist = array_key_exists('cl',$_REQUEST)?(int)$_REQUEST['cl']:0;
$ootdGameTitle = array_key_exists('title',$_REQUEST)?$_REQUEST['title']:'';
$ootdGameType = array_key_exists('type',$_REQUEST)?$_REQUEST['type']:'';

$gameManager = new GamesManager();
$clArr = $gameManager->getChecklistArr($pid);

$gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
$imageArr = $gameInfo['images'];
$cacheRefresh = date('Ydm');

$genusAnswer = '';

foreach($imageArr as $k => $imgValue){
	$imageArr[$k] = $imgValue.'?ver='.$cacheRefresh;
}

if($submitAction){
	$scinameAnswerArr = explode(' ',trim($_POST['sciname_answer']));
	if($scinameAnswerArr){
        $genusAnswer = strtolower($scinameAnswerArr[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> <?php echo ($ootdGameTitle ?? 'Organism Of The Day Game'); ?></title>
    <meta name="description" content="<?php echo ($ootdGameTitle ?? 'Organism of the day game'); ?> for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/games.ootd.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
		$(function() {
            const dialogArr = new Array("game");
            let dialogStr = "";
            for(let i=0;i<dialogArr.length;i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "center top", at: "right bottom", of: "#"+dialogStr }
				});

				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}

		});

		function toggleById(target){
            const obj = document.getElementById(target);
            if(obj.style.display === "none"){
				obj.style.display="block";
			}
			else {
				obj.style.display="none";
			}
		}

        let ImgNum = 0;
        let NewImg = <?php echo json_encode($imageArr); ?>;
        const familyAnswer = document.getElementById('family_answer');
        const scinameAnswer = document.getElementById('sciname_answer');
        const delay = 3000;
        const lock = false;
        let run;

        function chgImg(direction){
            const ImgLength = NewImg.length - 1;
            if (document.images) {
				ImgNum = ImgNum + direction;
				if (ImgNum > ImgLength) {
				    ImgNum = 0;
				}
				if (ImgNum < 0) {
				    ImgNum = ImgLength;
				}
				document.getElementById('slideshow').src = NewImg[ImgNum];
			}
		}

        function familyOnFocus(value){
            if(value === 'Family') {
                familyAnswer.value = '';
                familyAnswer.style.color = 'black';
                familyAnswer.style.fontWeight = 'normal';
            }
        }

        function familyOnBlur(value){
            if(value === '') {
                familyAnswer.value = 'Family';
                familyAnswer.style.color = '#888';
                familyAnswer.style.fontWeight = 'bold';
            }
        }

        function genusOnFocus(value){
            if(value === 'Genus species') {
                scinameAnswer.value = '';
                scinameAnswer.style.color = 'black';
                scinameAnswer.style.fontWeight = 'normal';
            }
        }

        function genusOnBlur(value){
            if(value === '') {
                scinameAnswer.value = 'Genus species';
                scinameAnswer.style.color = '#888';
                scinameAnswer.style.fontWeight = 'bold';
            }
        }
	</script>
</head>

<body>
    <?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<div style="width:80%;margin-left:auto;margin-right:auto;">
			<div style="text-align:center;margin-bottom:20px;">
				<h1><?php echo ($ootdGameTitle ?? 'Organism of the Day'); ?></h1>
			</div>
			<?php
			if(!$submitAction){
				?>
				<div style="z-index:1;width:500px;margin-left:auto;margin-right:auto;" >
					<div class="dailypicture" style="text-align:center">
						<div>
							<div style="vertical-align:middle;">
								<a onclick="chgImg(1);"><img src="../../temp/ootd/<?php echo $oodID; ?>_organism300_1.jpg?ver=<?php echo $cacheRefresh; ?>" id="slideshow" style="width:500px;" ></a><br />
							</div><br />
							<a onclick="chgImg(-1);">Previous</a> &nbsp;|&nbsp;
							<a onclick="chgImg(1);">Next</a>
						</div>
					</div>
					<div style="text-align:center;margin: 20px auto;" >
						<b>Name that <?php echo ($ootdGameType ?? 'organism'); ?>!</b>
						<a id="gameinfo" href="#" onclick="return false" title="How to Play?">
							<i style="width:15px;height:15px;" class="far fa-question-circle"></i>
						</a>
						<div id="gameinfodialog" title="How to Play">
							Look at the picture, and see if you can figure out what the <?php echo ($ootdGameType ?? 'organism'); ?> is. If you get completely stumped, you can
							click the "I give up" button. A new <?php echo ($ootdGameType ?? 'organism'); ?> is updated daily, so make sure you check back every day to test your knowledge!
						</div>
					</div>
					<div>
						<form name="answers" id="answers" method="post" action="index.php" class="asholder">
							<div style="width:500px;margin-left:auto;margin-right:auto;" >
								<div style="float:left;" >
									<div style="float:left;" >
										<b>Family:</b> <input type="text" id="family_answer" name="family_answer" style="width:200px;color:#888;font-weight:bold;" value="Family" onfocus="familyOnFocus(this.value);" onblur="familyOnBlur(this.value);" />
									</div>
									<div style="margin-top:20px;float:left;clear:left;" >
										<b>Scientific name:</b> <input type="text" id="sciname_answer" style="width:200px;color:#888;font-weight:bold;" name="sciname_answer" value="Genus species" onfocus="genusOnFocus(this.value);" onblur="genusOnBlur(this.value);" />
									</div>
								</div>
								<div style="float:right;margin-bottom:15px;" >
									<div style="float:right;" >
										<input name="submitaction" type="submit" value="Submit" style="height:7em; width:10em;"/>
									</div>
									<div style="margin-top:20px;float:right;clear:right;" >
										<button name="submitaction" type="submit" value="giveup" style="height:2em; width:8em;" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >I give up!</button>
									</div>
								</div>
								<div>
									<input name="oodid" type="hidden" value="<?php echo $oodID; ?>" />
									<input name="cl" type="hidden" value="<?php echo $ootdGameChecklist; ?>" />
									<input name="title" type="hidden" value="<?php echo $ootdGameTitle; ?>" />
									<input name="type" type="hidden" value="<?php echo $ootdGameType; ?>" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php
			}
			elseif((strtolower($_POST['family_answer']) === strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) === strtolower($gameInfo['sciname']))){
				?>
				<div id="correct" style="">
					<div style="width:700px;margin-top:20px;margin-left:auto;margin-right:auto;clear:both;text-align:center;display:table;">
						<div style="display:table-row;" >
							<div style="width:160px;float:left;display:table-cell;" >
								<img src = "../../images/games/ootd/balloons-150.png">
							</div>
							<div style="width:350px;float:left;margin-top:50px;display:table-cell;" >
								<b>Congratulations! That is<br />correct!</b>
							</div>
							<div style="width:160px;float:right;display:table-cell;" >
								<img src = "../../images/games/ootd/balloons-150.png">
							</div>
						</div>
					</div>
					<div style="width:670px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div><b><?php echo $gameInfo['family']; ?></b><br />
							<i><?php echo $gameInfo['sciname']; ?></i>
						</div>
						<div style="margin-top:30px;" >
							<a href = "#" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here to learn more about this <?php echo ($ootdGameType ?? 'organism'); ?>-</a>
						</div>
					</div>
				</div>
				<?php
			}

			elseif(($submitAction !== 'giveup') && ($genusAnswer !== strtolower($gameInfo['genus'])) && (strtolower($_POST['family_answer']) !== strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) !== strtolower($gameInfo['sciname']))){
				?>
				<div id="incorrect_both">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Sorry, that is not correct</b>
						</div>
						<div style="margin-top:25px;" >
							<b>Hint:</b> The family is <u>not</u>
							<?php echo $_POST['family_answer']; ?>.
						</div>
						<div style="margin-top:40px;" >
							<a href = "index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>');">Click Here to try again!</a>
							<br /><br />
							OR
							<br /><br />
							<a href = "index.php?submitaction=giveup?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here reveal what the <?php echo ($ootdGameType ?? 'organism'); ?> was-</a>
						</div>
					</div>
				</div>
				<?php
			}

			elseif(($submitAction !== 'giveup') && ($genusAnswer !== strtolower($gameInfo['genus'])) && (strtolower($_POST['family_answer']) === strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) !== strtolower($gameInfo['sciname']))){
				?>
				<div id="incorrect_sciname">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Sorry, that is not correct</b>
						</div>
						<div style="margin-top:25px;" >
							On the bright side, <b>you did get the family right</b>; it's
							<?php echo $gameInfo['family']; ?>.
						</div>
						<div style="margin-top:40px;" >
							<a href = "index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">Click Here to try again!</a>
							<br /><br />
							OR
							<br /><br />
							<a href = "index.php?submitaction=giveup?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here reveal what the <?php echo ($ootdGameType ?? 'organism'); ?> was-</a>
						</div>
					</div>
				</div>
				<?php
			}

			elseif(($submitAction !== 'giveup') && (strtolower($_POST['family_answer']) !== strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) === strtolower($gameInfo['sciname']))){
				?>
				<div id="incorrect_sciname">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Sorry, that is not correct</b>
						</div>
						<div style="margin-top:25px;" >
							<b>You did get the scientific name right</b>; it's
							<?php echo $gameInfo['sciname']; ?>, but the family is not <?php echo $_POST['family_answer']; ?>.
						</div>
						<div style="margin-top:40px;" >
							<a href = "index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">Click Here to try again!</a>
							<br /><br />
							OR
							<br /><br />
							<a href = "index.php?submitaction=giveup?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here reveal what the <?php echo ($ootdGameType ?? 'organism'); ?> was-</a>
						</div>
					</div>
				</div>
				<?php
			}

			elseif(($submitAction !== 'giveup') && ($genusAnswer === strtolower($gameInfo['genus'])) && (strtolower($_POST['family_answer']) !== strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) !== strtolower($gameInfo['sciname']))){
				?>
				<div id="incorrect_sciname">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Sorry, that is not correct</b>
						</div>
						<div style="margin-top:25px;" >
							On the bright side, <b>you did get the genus right</b>; it's
							<?php echo $gameInfo['genus']; ?>.
						</div>
						<div style="margin-top:40px;" >
							<a href = "index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">Click Here to try again!</a>
							<br /><br />
							OR
							<br /><br />
							<a href = "index.php?submitaction=giveup?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here reveal what the <?php echo ($ootdGameType ?? 'organism'); ?> was-</a>
						</div>
					</div>
				</div>
				<?php
			}

			elseif(($submitAction !== 'giveup') && ($genusAnswer === strtolower($gameInfo['genus'])) && (strtolower($_POST['family_answer']) === strtolower($gameInfo['family'])) && (strtolower($_POST['sciname_answer']) !== strtolower($gameInfo['sciname']))){
				?>
				<div id="incorrect_sciname">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Sorry, that is not correct</b>
						</div>
						<div style="margin-top:25px;" >
							On the bright side, <b>you did get the family and genus right</b>; The family
							is <?php echo $gameInfo['family']; ?>, and the genus is <?php echo $gameInfo['genus']; ?>.
						</div>
						<div style="margin-top:40px;" >
							<a href = "index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">Click Here to try again!</a>
							<br /><br />
							OR
							<br /><br />
							<a href = "index.php?submitaction=giveup?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here reveal what the <?php echo ($ootdGameType ?? 'organism'); ?> was-</a>
						</div>
					</div>
				</div>
				<?php
			}
			elseif($submitAction === 'giveup'){
				?>
				<div id="giveup">
					<div style="width:670px;margin-top:30px;margin-left:auto;margin-right:auto;clear:both;text-align:center;" >
						<div>
							<b>Too bad!</b>
						</div>
						<div style="margin-top:25px;" >
							It was <br /><br />
							<b><?php echo $gameInfo['family']; ?></b><br />
							<i><?php echo $gameInfo['sciname']; ?></i>
						</div>
						<div style="margin-top:40px;" >
							<a href = "#" onClick="window.open('../../taxa/index.php?taxon=<?php echo $gameInfo['tid']; ?>','plantwindow','width=900,height=650')" >-Click here to learn more about this <?php echo ($ootdGameType ?? 'organism'); ?>-</a>
							<br /><br />
							Thank you for playing!
							<br /><br />
							Check back tomorrow for a new <?php echo ($ootdGameType ?? 'organism'); ?>!
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
