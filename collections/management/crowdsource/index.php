<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceCrowdSource.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$catid = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;

if(isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID'] && $catid === 0) {
    $catid = (int)$GLOBALS['DEFAULTCATID'];
}

$csManager = new OccurrenceCrowdSource();

$pArr = array();
if($GLOBALS['SYMB_UID'] && array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS'])) {
    $pArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
}

$statusStr = '';
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Crowdsourcing Score Board</title>
    <link href="../../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <script src="../../../js/external/all.min.js" type="text/javascript"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../../header.php');
    echo "<div class='navpath'>";
    echo "<a href='../../../index.php'>Home</a> &gt;&gt; ";
    echo '<b>Crowdsourcing Score Board</b>';
    echo '</div>';
	?>
    <div id="innertext">
		<h1>Crowdsourcing Score Board</h1>
        <div style="margin-left:20px;float:left;">
			<h2>Top Scores</h2>
			<table class="styledtable" style="font-family:Arial,serif;width:300px;">
				<tr><th><b>User</b></th><th><b>Score</b></th></tr>
			<?php
			$topScoreArr = $csManager->getTopScores($catid);
			if($topScoreArr){
				foreach($topScoreArr as $s => $u){
					echo '<tr><td>'.$u.' </td><td>'.number_format($s).' </td></tr>';
				}
			}
			else{
				echo '<tr><td>Top scores not yet available</td><td>------</td></tr>';
			}
			?>
			</table>
		</div>

		<div style="margin-right:20px;float:right;">
			<h2>Your User Status</h2>
			<?php
			$userStats = $csManager->getUserStats($catid);
			?>
			<fieldset style="background-color:white;margin-bottom:15px;width:250px;padding:15px;">
				<legend><b>Current Standing</b></legend>
				<?php
				if($GLOBALS['SYMB_UID']){
					?>
					<b>Occurrences processed as volunteer:</b> <?php echo number_format($userStats['totalcnt']); ?><br/>
					<?php
					if($userStats['nonvolcnt']) {
                        echo '&nbsp;&nbsp;&nbsp;(Additional as non-volunteer: ' . number_format($userStats['nonvolcnt']) . ')<br/>';
                    }
					?>
					<b>Pending points:</b> <?php echo number_format($userStats['ppoints']); ?>
					<?php
                    if($userStats['ppoints']) {
                        echo '(<a href="review.php?rstatus=5&uid=' . $GLOBALS['SYMB_UID'] . '">view records</a>)';
                    }
                    ?>
                    <br/>
					<b>Approved points:</b> <?php echo number_format($userStats['apoints']); ?>
					<?php
                    if($userStats['apoints']) {
                        echo '(<a href="review.php?rstatus=10&uid=' . $GLOBALS['SYMB_UID'] . '">view records</a>)';
                    }
                    ?>
                    <br/>
					<b>Total Possible Score:</b> <?php echo number_format($userStats['ppoints']+$userStats['apoints']); ?><br/>
					<?php
					if($userStats['nonvolcnt']) {
                        echo '<div style="margin-top:10px">* Only occurrences processed as a volunteer are eligible for points</div>';
                    }
				}
				else{
					?>
					<div>
						<a href="../../../profile/index.php?refurl=../collections/management/crowdsource/index.php">Login</a> to View Current Stats
					</div>
					<?php
				}
				?>
			</fieldset>
		</div>
		<div style="padding:20px;clear:both;">
			<h2>Your Stats by Collections</h2>
			<table class="styledtable" style="font-family:Arial,serif;">
				<tr>
					<th><b>Collection</b></th>
					<th><b>Occurrence<br/>Count</b></th>
					<th><b>Pending<br/>Points</b></th>
					<th><b>Approved<br/>Points</b></th>
					<th><b>Open<br/>Records</b></th>
				</tr>
				<?php
                unset($userStats['totalcnt'], $userStats['nonvolcnt'], $userStats['apoints'], $userStats['ppoints']);
                foreach($userStats as $collId => $sArr){
					$pointArr = $sArr['points'];
					$cntArr = $sArr['cnt'];
					echo '<tr>';
					echo '<td>';
					echo '<b>'.$sArr['name'].'</b>';
					if($GLOBALS['IS_ADMIN'] || in_array($collId, $pArr, true)) {
                        echo ' <a href="../index.php?tabindex=2&collid=' . $collId . '"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>';
                    }
					echo '</td>';
					echo '<td>'.number_format((array_key_exists(5,$cntArr)?$cntArr[5]:0)+(array_key_exists(10,$cntArr)?$cntArr[10]:0)).'</td>';
					echo '<td>'.number_format(array_key_exists(5,$pointArr)?$pointArr[5]:0).'</td>';
					echo '<td>'.number_format(array_key_exists(10,$pointArr)?$pointArr[10]:0).'</td>';
					echo '<td><a href="../../editor/occurrencetabledisplay.php?csmode=1&occindex=0&displayquery=1&reset=1&collid='.$collId.'" target="_blank">'.number_format(array_key_exists(0,$cntArr)?$cntArr[0]:0).'</a></td>';
					echo '</tr>';
				}
				?>
			</table>
		</div>
		<?php
		if(isset($GLOBALS['USER_RIGHTS']['CollAdmin']) || isset($GLOBALS['USER_RIGHTS']['CollEditor'])){
			?>
			<div style="clear:both;margin:30px;">
				<b>Note:</b> You have been identified as an official editor for one or more collections.
				Your points will not be counted in the Top Score table for occurrences
				that belong to collection to which you have edit rights.
				Top scores are posted only for occurrences entered on a volunteer basis.
			</div>
			<?php
		}
		?>
	</div>
	<?php
	include(__DIR__ . '/../../../footer.php');
	?>
</body>
</html>
