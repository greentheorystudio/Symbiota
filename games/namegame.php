<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GamesManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$clName = (array_key_exists('listname',$_REQUEST)?$_REQUEST['listname']: '');
$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$dynClid = array_key_exists('dynclid',$_REQUEST)?(int)$_REQUEST['dynclid']:0;

if(!$clName){
	$gameManager = new GamesManager();
	if($clid){
		$gameManager->setClid($clid);
	}
	elseif($dynClid){
		$gameManager->setDynClid($dynClid);
	}
	$clName = $gameManager->getClName();
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Name Game</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	<style type="text/css">
		.lettertable{
            border:1px solid #000000;
            border-spacing:3px;
        }

        #charactertable td{
            margin-left: auto;
            margin-right: auto;
            vertical-align: middle;
            border:1px solid #000000;
            width:50px;
            cursor:pointer;
            font-family: times new roman, serif;
            font-size:25px;
            font-weight:bold;
            color:#000000
        }

        .buttonout{
            border:5px outset #CCCC99;
            font-weight:normal
        }

        .question{
            font-size:30px;
        }
		#rw{
            margin-left:auto;
            margin-right:auto
        }
	</style>
	<script>
        let mainList = [['','']];
        const hangpics = [
            ["../images/games/namegame/man1_0.gif","../images/games/namegame/man1_1.gif","../images/games/namegame/man1_2.gif","../images/games/namegame/man1_3.gif","../images/games/namegame/man1_4.gif","../images/games/namegame/man1_5.gif","../images/games/namegame/gallow.gif","../images/games/namegame/gallow5.gif","../images/games/namegame/gallow4.gif","../images/games/namegame/gallow3.gif","../images/games/namegame/gallow2.gif","../images/games/namegame/gallow1.gif","../images/games/namegame/spacer.gif","../images/games/namegame/man1win.gif"],
            ["../images/games/namegame/woman1_0.gif","../images/games/namegame/woman1_1.gif","../images/games/namegame/woman1_2.gif","../images/games/namegame/woman1_3.gif","../images/games/namegame/woman1_4.gif","../images/games/namegame/woman1_5.gif","../images/games/namegame/gallow.gif","../images/games/namegame/gallow5.gif","../images/games/namegame/gallow4.gif","../images/games/namegame/gallow3.gif","../images/games/namegame/gallow2.gif","../images/games/namegame/gallow1.gif","../images/games/namegame/spacer.gif","../images/games/namegame/woman1win.gif"],
            ["../images/games/namegame/man2_0.gif","../images/games/namegame/man2_1.gif","../images/games/namegame/man2_2.gif","../images/games/namegame/man2_3.gif","../images/games/namegame/man2_4.gif","../images/games/namegame/man2_5.gif","../images/games/namegame/gallow.gif","../images/games/namegame/gallow5.gif","../images/games/namegame/gallow4.gif","../images/games/namegame/gallow3.gif","../images/games/namegame/gallow2.gif","../images/games/namegame/gallow1.gif","../images/games/namegame/spacer.gif","../images/games/namegame/man2win.gif"],
            ["../images/games/namegame/woman2_0.gif","../images/games/namegame/woman2_1.gif","../images/games/namegame/woman2_2.gif","../images/games/namegame/woman2_3.gif","../images/games/namegame/woman2_4.gif","../images/games/namegame/woman2_5.gif","../images/games/namegame/gallow.gif","../images/games/namegame/gallow5.gif","../images/games/namegame/gallow4.gif","../images/games/namegame/gallow3.gif","../images/games/namegame/gallow2.gif","../images/games/namegame/gallow1.gif","../images/games/namegame/spacer.gif","../images/games/namegame/woman2win.gif"],
            ["../images/games/namegame/wwoman0.gif","../images/games/namegame/wwoman1.gif","../images/games/namegame/wwoman2.gif","../images/games/namegame/wwoman3.gif","../images/games/namegame/wwoman4.gif","../images/games/namegame/wwoman5.gif","../images/games/namegame/gallow.gif","../images/games/namegame/gallow5.gif","../images/games/namegame/gallow4.gif","../images/games/namegame/gallow3.gif","../images/games/namegame/gallow2.gif","../images/games/namegame/gallow1.gif","../images/games/namegame/spacer.gif","../images/games/namegame/wwomanwin.gif"],
            ["../images/games/namegame/flower0.gif","../images/games/namegame/flower1.gif","../images/games/namegame/flower2.gif","../images/games/namegame/flower3.gif","../images/games/namegame/flower4.gif","../images/games/namegame/flower5.gif","../images/games/namegame/flower6.gif","../images/games/namegame/flower7.gif","../images/games/namegame/flower8.gif","../images/games/namegame/flower9.gif","../images/games/namegame/flower10.gif","../images/games/namegame/flower11.gif","../images/games/namegame/flower12.gif","../images/games/namegame/flowerwin.gif"],
            ["../images/games/namegame/plant0.gif","../images/games/namegame/plant1.gif","../images/games/namegame/plant2.gif","../images/games/namegame/plant3.gif","../images/games/namegame/plant4.gif","../images/games/namegame/plant5.gif","../images/games/namegame/plant6.gif","../images/games/namegame/plant7.gif","../images/games/namegame/plant8.gif","../images/games/namegame/plant9.gif","../images/games/namegame/plant10.gif","../images/games/namegame/plant11.gif","../images/games/namegame/plant12.gif","../images/games/namegame/plantwin.gif"],
            ["../images/games/namegame/tempcover0.jpg","../images/games/namegame/tempcover1.jpg","../images/games/namegame/tempcover2.jpg","../images/games/namegame/tempcover3.jpg","../images/games/namegame/tempcover4.jpg","../images/games/namegame/tempcover5.jpg","../images/games/namegame/tempcover6.jpg","../images/games/namegame/plant7.gif","../images/games/namegame/plant8.gif","../images/games/namegame/plant9.gif","../images/games/namegame/plant10.gif","../images/games/namegame/plant11.gif","../images/games/namegame/plant12.gif","../images/games/namegame/tempcover0.jpg"],
            ["../images/games/namegame/apple_0.gif","../images/games/namegame/apple_1.gif","../images/games/namegame/apple_2.gif","../images/games/namegame/apple_3.gif","../images/games/namegame/apple_4.gif","../images/games/namegame/apple_5.gif","../images/games/namegame/apple_6.gif","../images/games/namegame/apple_7.gif","../images/games/namegame/apple_8.gif","../images/games/namegame/apple_9.gif","../images/games/namegame/apple_10.gif","../images/games/namegame/apple_11.gif","../images/games/namegame/apple_12.gif","../images/games/namegame/apple_win.gif"]
        ];

        const PreImage0 = new Image();
        const PreImage1 = new Image();
        const PreImage2 = new Image();
        const PreImage3 = new Image();
        const PreImage4 = new Image();
        const PreImage5 = new Image();
        const PreImage6 = new Image();

        const defaultImage = "../images/games/namegame/plant7.gif";
        const maxWildCards = 1;

        const imgSetId = "imageset";
        let lastImgId = "img7";
        const imgSetVal = "6";
        let lastImg = "";

        let firstload = "1";
        const levelSet = "levelset";
        let lastLevelId = "level2";
        let levelSetVal = "6";
        let lastLevelImg = "";
        let won = 0;
        let gameover = 0;
        let played = 0;
        let running = 0;
        let lastChar = "";
        let hintShown = 0;
        let wordChosen = "";
        let avatar = 0;
        let wordCount = 0;
        let selectedNums = [];
        let secondWord = '';
        let RealName = '';
        let wildCount = 0;
        let last = '';
        let guessCount = 0;
        let chosenWord = '';
        let currentNum;
        let done = 0;
        let sFont = 0;
        let lFont = 0;
        let wildCardArr = [];

        const step = 5;
        let repeat = "";
        
        function toggle(divID) {
            const ele = document.getElementById(divID);
            if(ele.style.display === "block") {
		    	ele.style.display = "none";
			}
			else {
				ele.style.display = "block";
			}
		} 

		function getWordList(){
			$.ajax({
				method: "POST",
				url: "rpc/getwordlist.php",
				dataType: "json",
				data: {
					clid : <?php echo $clid; ?>,
					dynclid: <?php echo $dynClid; ?> 
				},
				success: function(data) {
                    mainList = data;
                    generate();
				}
			});
		}
		
		function openPopup(urlStr,windowName){
            let wWidth = 900;
            try{
				if(document.getElementById('innertext').offsetWidth){
					wWidth = document.getElementById('innertext').offsetWidth*1.05;
				}
				else if(document.body.offsetWidth){
					wWidth = document.body.offsetWidth*0.9;
				}
			}
			catch(e){
			}
            const newWindow = window.open(urlStr, windowName, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
		}
		
		function initNameGame(){
			mClick(imgSetId,lastImgId,imgSetVal,'../images/games/namegame/plant_on.gif','../images/games/namegame/plant_off.gif');
			mClick(levelSet,lastLevelId,levelSetVal,'../images/games/namegame/radio_on4.gif','../images/games/namegame/radio_off4.gif');

            const tds = document.getElementById("charactertable").getElementsByTagName("TD");

            for(let i=0 ; i<tds.length ; i++){
				tds[i].getElementsByTagName("span")[0].onmouseover = function(){
				    this.offsetParent.bgColor='#CCCC99';
				};
				tds[i].getElementsByTagName("span")[0].onmousedown = function(){
				    this.offsetParent.style.color='#FFFFFF';
				};
				tds[i].getElementsByTagName("span")[0].onmouseout = function(){
				    this.offsetParent.bgColor='';
				    this.offsetParent.style.color='#000000';
				};
			
				if(i<tds.length-1){
					tds[i].getElementsByTagName("span")[0].onclick = function(){
					    getKey(this.id);
					};
				}
			
				if(i == tds.length-1){
					tds[i].getElementsByTagName("span")[0].onclick = function(){
					    wildCard();
					};
				}
			}
			getWordList();
			generate();
		}

		function mOver(setId,imgId,imgOn){
			if(setId == imgSetId && running == 0){
				(lastImgId !== imgId?document.getElementById(imgId).src = imgOn:"");
			}
		}

		function mOut(setId,imgId,imgOff){
			if(setId == imgSetId && running == 0){
				(lastImgId !== imgId?document.getElementById(imgId).src = imgOff:"");
			}
		}

		function mClick(setId,imgId,imgVal,imgOn,imgOff){
			if(running == 1) {
			    return;
			}

            if (setId == imgSetId) {
                document.getElementById(imgId).src = imgOn;

                if (lastImgId !== "") {
                    (lastImgId !== imgId ? document.getElementById(lastImgId).src = lastImg : "");
                }

                lastImgId = imgId;
                lastImg = imgOff;
                avatar = imgVal;
            }
		
			if(setId == levelSet){
				document.getElementById(imgId).src = imgOn;

				if (lastLevelId !== ""){
					(lastLevelId !== imgId?document.getElementById(lastLevelId).src = lastLevelImg:"");
				}

				lastLevelId = imgId;
				lastLevelImg = imgOff;
				levelSetVal = imgVal;
			}
			level();
		}

		function generate(){
            const numbersRange = mainList.length;
            let firstRun = true;
            selectedNums = [];

            let rndnum;
            for (let i = 0; i < numbersRange; i++) {
                wordChosen = false;
                rndnum = Math.floor(Math.random() * numbersRange);

                if (!firstRun) {
                    for (let j = 0; j < selectedNums.length; j++) {
                        if (rndnum == selectedNums[j]) {
                            wordChosen = true;
                            i--;
                        }
                    }
                }

                if (!wordChosen) {
                    selectedNums[i] = rndnum;
                    firstRun = false;
                }
            }
            wordCount = 0;
			newWord();
		}

		function newWord(){
			if(wordCount == selectedNums.length){
				getWordList();
				return;
			}
			
			lastChar = "";
			running = 0;
			clearTimeout(Number(repeat));
            let temp;
            wildCount = 0;
            hintShown = 0;
			document.getElementById("hintdisplay").innerHTML = "";
            const charDisplay = document.getElementById("charactertable").getElementsByTagName('span');

            for(let k=0; k<charDisplay.length; k++){
				charDisplay[k].style.visibility = "visible";
			}

            currentNum = selectedNums[wordCount];
            chosenWord = mainList[currentNum][0].toLowerCase();
            RealName = chosenWord;

            const tempWord = chosenWord;
            const varpos = tempWord.indexOf(" var.");
            const ssppos = tempWord.indexOf(" subsp.");
            secondWord = '';
            let subWord;
            if (varpos !== -1) {
                subWord = tempWord.substring(0, varpos);
                secondWord = tempWord.substring(varpos);
                secondWord = secondWord.toUpperCase();
                chosenWord = subWord;
            } else if (ssppos !== -1) {
                subWord = tempWord.substring(0, ssppos);
                secondWord = tempWord.substring(ssppos);
                secondWord = secondWord.toUpperCase();
                chosenWord = subWord;
            }
            const lengthofword = chosenWord;
            const tempchosen = chosenWord;
            let templength = 0;
            let tempbuilder = "";
            let wordLength = tempchosen.length;
            for(let m=0; m<wordLength; m++){
				if(tempchosen.charAt(m) !== " "){
					tempbuilder += tempchosen.charAt(m);
				}
				else{
					tempbuilder += "\u00A0\u00A0\u00A0\u00A0";
					templength += 3;
				}
			}
			chosenWord = tempbuilder;
			
			initWildCard(chosenWord);
			wordLength=lengthofword.length;
			wordLength+=templength;
			temp="";

            for(let n=0; n<wordLength; n++){
				if((chosenWord.charAt(n) !== " ") && (chosenWord.charAt(n) !== "\u00A0")){
					if(chosenWord.charAt(n) === ".")
						temp += ".";
					else
						temp += "_";
				}
				else{
					temp += "\u00A0";
				}
			}
			document.getElementById("attempt").innerHTML=temp;
			if (firstload == "1"){
				firstload = "0";
			}
            last = temp;
            wordChosen = 1;
			wordCount++;
			document.getElementById("showhint").disabled = false;

            const now = new Date();

            PreImage0.src = '../images/games/namegame/tempcover0.jpg?' + now.getTime();
			PreImage1.src = '../images/games/namegame/tempcover1.jpg?' + now.getTime();
			PreImage2.src = '../images/games/namegame/tempcover2.jpg?' + now.getTime();
			PreImage3.src = '../images/games/namegame/tempcover3.jpg?' + now.getTime();
			PreImage4.src = '../images/games/namegame/tempcover4.jpg?' + now.getTime();
			PreImage5.src = '../images/games/namegame/tempcover5.jpg?' + now.getTime();
			PreImage6.src = '../images/games/namegame/tempcover6.jpg?' + now.getTime();
			level();
		}

		function newGame (){
			if (gameover == 0) {
			    played++;
			}
			document.getElementById("plays").innerHTML = played.toString();
			document.getElementById("rate").innerHTML = ((won/played)*100).toFixed(2)+"%";
			gameover = 0;
			newWord();
		}

		function level(){
			if(running == 1){
			    return;
			}
            guessCount = levelSetVal;

            if(avatar == 5){
				if(guessCount == 12)
					document.getElementById("hpic").src = "../images/games/namegame/flower12.gif";
				else if(guessCount == 6)
					document.getElementById("hpic").src = "../images/games/namegame/flower6.gif";
				else if(guessCount == 3)
					document.getElementById("hpic").src = "../images/games/namegame/flower3.gif";
			}
			else if(avatar == 6){
				if(guessCount == 12)
					document.getElementById("hpic").src = "../images/games/namegame/plant12.gif";
				else if(guessCount == 6)
					document.getElementById("hpic").src = "../images/games/namegame/plant7.gif";
				else if(guessCount == 3)
					document.getElementById("hpic").src = "../images/games/namegame/plant4.gif";
			}
			else if(avatar == 7){
				if(guessCount == 12)
					document.getElementById("hpic").src = "../images/games/namegame/plant12.gif";
				else if(guessCount == 6)
					document.getElementById("hpic").src = "../images/games/namegame/tempcover6.jpg";
				else if(guessCount == 3)
					document.getElementById("hpic").src = "../images/games/namegame/tempcover3.jpg";
			}
			else if(avatar == 8){
				if(guessCount == 12)
					document.getElementById("hpic").src = "../images/games/namegame/apple_12.gif";
				else if(guessCount == 6)
					document.getElementById("hpic").src = "../images/games/namegame/apple_6.gif";
				else if(guessCount == 3)
					document.getElementById("hpic").src = "../images/games/namegame/apple_6.gif";
			}
			else if(guessCount == 12){
				document.getElementById("hpic").src = "../images/games/namegame/spacer.gif";
			}
			else if((avatar >= 0)&&(avatar <= 4)){
				document.getElementById("hpic").src = "../images/games/namegame/gallows.gif";
			}
			
			document.getElementById("counter").innerHTML = "Chances left = "+guessCount;
			document.getElementById("splash").style.display = "none";
		}

		function getKey(e){
			let myNewString;
            running = 1;
            let keyCode = e.keyCode;
            const chkChar = e;
            let temp = "";

            let currentChar;
            if (keyCode) {
                if (keyCode >= 65 && keyCode <= 90) {
                    keyCode += 32;
                }
                currentChar = keyCode;
            } else {
                currentChar = chkChar.charCodeAt(0);
            }

			if(currentChar == 13){
				newWord();
				return;
			}

			if(wordChosen == 0){
			    return;
			}

			for(let k=0; k<wildCardArr.length; k++){
				if(wildCardArr[k] == String.fromCharCode(currentChar)){
					wildCardArr.splice(k, 1);
				}
			}

			if(lastChar == currentChar || currentChar < 44|| currentChar > 46 && currentChar < 97 || currentChar > 122){
				return;
			}

			if(document.getElementById(String.fromCharCode(currentChar)).style.visibility === "hidden"){
			    return;
			}

            let correct = false;
            document.getElementById(String.fromCharCode(currentChar)).style.visibility = "hidden";

			for(let n=0; n<last.length; n++){
				if(String.fromCharCode(currentChar) == chosenWord.charAt(n)){
					temp += chosenWord.charAt(n);
					correct = true;
				}
				else{
					temp += last.charAt(n);
				}
			}

			if(correct == false && guessCount > 0){
				guessCount--;
				document.getElementById("hpic").src = hangpics[avatar][guessCount];
			}
			
			document.getElementById("attempt").innerHTML = temp.toUpperCase();
			document.getElementById("counter").innerHTML = "Chances left = "+guessCount;
			
			last = temp;
			lastChar = currentChar;
			
			if(guessCount == 0){
				wordChosen = 0;
				gameover = 1;
				document.getElementById("counter").innerHTML = "<div id='rw' style='width:190px;text-align:center;' onmouseover=\"this.className='buttonover'\" onmouseout=\"this.className='buttonout'\" onmousedown=\"this.className='buttondown'\" onmouseup=\"this.className='buttonup'\" class='buttonout' onclick='showWord()'><b>Reveal the Species</b></div>";
				played++;
				document.getElementById("plays").innerHTML = played.toString();
                myNewString = RealName.replaceAll(/\u00A0\u00A0\u00A0\u00A0/g, "%20");
                document.getElementById("splash").innerHTML = "<div style='font-size:20px;color:red;text-align:center;'>Too Bad</div><div style='font-size:16px;color:#0000FF;text-align:center;'><a href='#' onClick=\"openPopup('../taxa/index.php?taxon="+myNewString+"','tpwin');\"><b>Click here for more about this species</b></a></div>";
				document.getElementById("splash").style.display = "";
				document.getElementById("rate").innerHTML = ((won/played)*100).toFixed(0)+"%";
				gameEnd();
			}

			if(temp == chosenWord){
				wordChosen = 0;
				gameover = 1;
				played++;
				document.getElementById("plays").innerHTML = played.toString();
				won++;
				document.getElementById("wins").innerHTML = won.toString();
                myNewString = RealName.replaceAll(/\u00A0\u00A0\u00A0\u00A0/g, "%20");
                if (secondWord !== '')
					document.getElementById("attempt").innerHTML = chosenWord.toUpperCase()+"<br><span style=\"font-size:12px\">"+secondWord+"</span>";
				else
					document.getElementById("attempt").innerHTML = chosenWord.toUpperCase();
				document.getElementById("splash").innerHTML = "<span style='color:#336699'>You Win!</span><br><a href = '#' onClick=\"openPopup('../taxa/index.php?taxon="+myNewString+"','tpwin')\"> <span style='font-size:4px;color:#0000FF;text-align:center;'><u><b><br>Click here for more about this species</b></u></span></a><br>";
                document.getElementById("hintdisplay").innerHTML = mainList[currentNum][1];
				document.getElementById("splash").style.display = "";
				document.getElementById("rate").innerHTML = ((won/played)*100).toFixed(0)+"%";
				document.getElementById("hpic").src = hangpics[avatar][13];
				gameEnd();
			}
			
			if(guessCount == 1){
				document.getElementById("showhint").disabled=true;
				document.getElementById("?").style.visibility="hidden";
			}
		}

		function showHint(){
			running=1;
			if(guessCount <= 1){
			    return;
			}
			if(hintShown == 0){
				guessCount--;
				hintShown = 1;
				document.getElementById("hintdisplay").innerHTML = mainList[currentNum][1];
				document.getElementById("counter").innerHTML = "Chances left = "+guessCount;
				document.getElementById("hpic").src = hangpics[avatar][guessCount];
			}
			document.getElementById("showhint").disabled = true;
		}

		function showWord(){
			if(wordChosen == 0 && guessCount != 0){
			    return;
			}
			if (secondWord !== '') {
                document.getElementById("attempt").innerHTML = chosenWord.toUpperCase() + "<br><span style=\"font-size:12px\">" + secondWord + "</span>";
            }
			else {
                document.getElementById("attempt").innerHTML = chosenWord.toUpperCase();
            }
			hintShown = 1;
			document.getElementById("hintdisplay").innerHTML = mainList[currentNum][1];
			clearTimeout(repeat);
			document.getElementById("rw").style.display="none";
		}

		function gameEnd(){
			done = 1;
			sFont = 1;
			lFont = 50;
			goSplash();
		}

		function goSplash(){
			document.getElementById("splash").style.visibility = "visible";
			document.getElementById("splash").style.fontSize = '30px';
			sFont += step;
			repeat = setTimeout("goSplash()",10);
			if(sFont > lFont){
				clearTimeout(repeat);
			}
		}

		function initWildCard(str){
			wildCardArr = [];
			wildCardArr[0] = str.charAt(0);
            let isIn;
            for (let i = 0; i < str.length; i++) {
                isIn = 0;
                for (let j = 0; j < wildCardArr.length; j++) {
                    if (str.charAt(i) == wildCardArr[j]) {
                        isIn = 1;
                    }
                }
                if (isIn == 0 && str.charAt(i) !== " ") {
                    wildCardArr[wildCardArr.length] = str.charAt(i);
                }
            }
		}

		function wildCard(){
			if(wildCardArr.length == 0||guessCount <= 1){
			    return;
			}
			wildCount++;
            const rdm = Math.floor(Math.random() * wildCardArr.length);
            const wildCardChar = wildCardArr[rdm]; //.splice(rdm, 1).toString()
			if(wildCount == maxWildCards){
				document.getElementById("?").style.visibility="hidden";
			}
			guessCount--;
			document.getElementById("hpic").src = hangpics[avatar][guessCount];
			getKey(wildCardChar);
		}

	</script>
</head>

<body onload="initNameGame()">

	<?php
	include(__DIR__ . '/../header.php');
	echo '<div class="navpath">';
	echo '<a href="../index.php">Home</a> &gt;&gt; ';
    echo '<a href="../checklists/checklist.php?cl='.$clid.'">';
    echo $clName;
    echo '</a> &gt;&gt; ';
	echo ' <b>Name Game</b>';
	echo '</div>';
	?>
	
	<div id="innertext">
		<div style="width:100%;text-align:center;">
			<h1><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Name Game</h1>
		</div>
		<div style="width:100%;text-align:center;margin:10px;">
			I am thinking of a species found within the following checklist: <b><?php echo $clName;?></b><br/> 
			What am I thinking of? 
		</div>
		<div style="width:140px;margin-left:auto;margin-right:auto;margin-top:20px;">
			<div id="imageset" style="cursor:pointer;">
				<img onclick="mClick(this.parentNode.id,this.id,'6','../images/games/namegame/plant_on.gif','../images/games/namegame/plant_off.gif')" onmouseover="mOver(this.parentNode.id,this.id,'../images/games/namegame/plant_on.gif')" onmouseout="mOut(this.parentNode.id,this.id,'../images/games/namegame/plant_off.gif')" src="../images/games/namegame/plant_off.gif" id="img7">
				<img onclick="mClick(this.parentNode.id,this.id,'5','../images/games/namegame/flower_on.gif','../images/games/namegame/flower_off.gif')" onmouseover="mOver(this.parentNode.id,this.id,'../images/games/namegame/flower_on.gif')" onmouseout="mOut(this.parentNode.id,this.id,'../images/games/namegame/flower_off.gif')" src="../images/games/namegame/flower_off.gif" id="img6">
				<img onclick="mClick(this.parentNode.id,this.id,'8','../images/games/namegame/apple_on.gif','../images/games/namegame/apple_off.gif')" onmouseover="mOver(this.parentNode.id,this.id,'../images/games/namegame/apple_on.gif')" onmouseout="mOut(this.parentNode.id,this.id,'../images/games/namegame/apple_off.gif')" src="../images/games/namegame/apple_off.gif" id="img8">
			</div> 
		</div>
		<div style="width:600px;margin-left:auto;margin-right:auto;margin-top:20px;">
			<div style="float:left;width:250px;">
				<div style="width:150px;margin-left:auto;margin-right:auto;">
					<img id="hpic" style="width:150px;" src="../images/games/namegame/plant7.gif">
				</div>
				<div id="counter" style="text-align:center;width:190px;margin-left:auto;margin-right:auto;">Chances left = 6</div>
			</div> 
			<div style="float:right;width:250px;">
				<div style="clear:both;width:150px;margin-left:auto;margin-right:auto;margin-top:20px;">
					<div style="margin-top:30px;">
						<b>Difficulty</b>
					</div>
					<div id="levelset" style="cursor:pointer">
						Hard <img onclick="mClick(this.parentNode.id,this.id,'3','../images/games/namegame/radio_on4.gif','../images/games/namegame/radio_off4.gif')" src="../images/games/namegame/radio_off4.gif" id="level1">
						<img onclick="mClick(this.parentNode.id,this.id,'6','../images/games/namegame/radio_on4.gif','../images/games/namegame/radio_off4.gif')" src="../images/games/namegame/radio_off4.gif" id="level2">
						<img onclick="mClick(this.parentNode.id,this.id,'12','../images/games/namegame/radio_on4.gif','../images/games/namegame/radio_off4.gif')" src="../images/games/namegame/radio_off4.gif" id="level3"> Easy
					</div>
					<div style="margin-top:10px;">
						Games Played <span id="plays">0</span><br>
						Games Won <span id="wins">0</span><br>
						Success Rate <span id="rate">0</span>
					</div>
				</div>
				<div style="width:250px;margin-left:auto;margin-right:auto;margin-top:15px;">
					<div id="showhint" style="text-align:center;width:110px;float:left;" onclick="showHint();" onmouseover="this.className='buttonover'" onmouseout="this.className='buttonout'" onmousedown="this.className='buttondown'" onmouseup="this.className='buttonup'" class="buttonout">Show Family</div>
					<div id="newgame" style="text-align:center;width:90px;float:right;" onclick="newGame();" onmouseover="this.className='buttonover'" onmouseout="this.className='buttonout'" onmousedown="this.className='buttondown'" onmouseup="this.className='buttonup'" class="buttonout">New Game</div>
				</div>
			</div>
		</div>
		<div style="clear:both;width:100%;text-align:center;padding-top:20px;">
			<div id="hintdisplay" style="font-size:20px;"></div>
		</div>
		<div style="clear:both;width:100%;text-align:center;padding-top:20px;">
			<div id="attempt" style="letter-spacing:5px;font-weight:bold;font-size:20px"></div>
		</div>
		<div style="clear:both;width:100%;text-align:center;padding-top:20px;">
			<div id="splash" style="color:#336699"></div>
		</div>
		<div style="clear:both;width:450px;margin-left:auto;margin-right:auto;margin-top:10px;">
			<table id="charactertable" class="lettertable" style="border:0;width:450px;">
				<tr style="text-align:center;height:40px;vertical-align:middle;">
					<td><span id="a" style="display:block">A</span></td>
					<td><span id="b" style="display:block">B</span></td>
					<td><span id="c" style="display:block">C</span></td>
					<td><span id="d" style="display:block">D</span></td>
					<td><span id="e" style="display:block">E</span></td>
					<td><span id="f" style="display:block">F</span></td>
					<td><span id="g" style="display:block">G</span></td>
					<td><span id="h" style="display:block">H</span></td>
					<td><span id="i" style="display:block">I</span></td>
				</tr>
				<tr style="text-align:center;height:40px;">
					<td><span id="j" style="display:block">J</span></td>
					<td><span id="k" style="display:block">K</span></td>
					<td><span id="l" style="display:block">L</span></td>
					<td><span id="m" style="display:block">M</span></td>
					<td><span id="n" style="display:block">N</span></td>
					<td><span id="o" style="display:block">O</span></td>
					<td><span id="p" style="display:block">P</span></td>
					<td><span id="q" style="display:block">Q</span></td>
					<td><span id="r" style="display:block">R</span></td>
				</tr>
				<tr style="text-align:center;height:40px;">
					<td><span id="s" style="display:block">S</span></td>
					<td><span id="t" style="display:block">T</span></td>
					<td><span id="u" style="display:block">U</span></td>
					<td><span id="v" style="display:block">V</span></td>
					<td><span id="w" style="display:block">W</span></td>
					<td><span id="x" style="display:block">X</span></td>
					<td><span id="y" style="display:block">Y</span></td>
					<td><span id="z" style="display:block">Z</span></td>
					<td style="vertical-align: center;">
						<span id="qmark" style="display:block;" class="question"  title="Wild Card">?</span>
					</td>
				</tr>
			</table>
		</div>
		<div style="width:450px;margin-left:auto;margin-right:auto;margin-top:20px;">
			<div>
				How to play:
				<ul>
					<li>Type or click on a letter to guess
					<li>Difficulty level affects how many chances you get: 3, 6, or 12
					<li>Showing the family uses one of your chances
					<li>Using the wild card [?] uses one of your chances
					<li>Spaces are already displayed for you
					<li>You cannot change settings settings while in the middle of a game
					<li>The "Show Family"/wild card cannot be used if you are down to your last guess
				</ul>
			</div>
		</div>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
