<?php
/** @var OccurrenceCollectionProfile $collManager */
/** @var int $collid */

$statDisplay = array_key_exists('stat',$_REQUEST)?htmlspecialchars($_REQUEST['stat']):'';

if($statDisplay === 'geography'){
	$countryDist = array_key_exists('country',$_REQUEST)?htmlspecialchars($_REQUEST['country']):'';
	$stateDist = array_key_exists('state',$_REQUEST)?htmlspecialchars($_REQUEST['state']):'';
	$distArr = $collManager->getGeographyStats($countryDist,$stateDist);
	if($distArr){
		?>
		<fieldset id="geographystats" style="margin:20px;width:90%;">
			<legend>
				<b>
					<?php
					echo 'Geographic Distribution';
					if($stateDist){
						echo ' - '.$stateDist;
					}
					elseif($countryDist){
						echo ' - '.$countryDist;
					}
					?>
				</b>
			</legend>
			<div style="margin:15px;">Click on the occurrence record counts within the parenthesis to return the records for that term</div>
			<ul>
				<?php
				foreach($distArr as $term => $cnt){
					$countryTerm = ($countryDist?:$term);
					if($countryDist){
                        $stateTerm = ($stateDist?:$term);
                    }
					else{
                        $stateTerm = '';
                    }
					$countyTerm = ($countryDist && $stateDist?$term:'');
					echo '<li>';
					if(!$stateDist) {
                        echo '<a href="collprofiles.php?collid=' . $collid . '&stat=geography&country=' . $countryTerm . '&state=' . $stateTerm . '#geographystats">';
                    }
					echo $term;
					if(!$stateDist) {
                        echo '</a>';
                    }
					echo ' (<a href="../list.php?db='.$collid.'&reset=1&country='.$countryTerm.'&state='.$stateTerm.'&county='.$countyTerm.'" target="_blank">'.$cnt.'</a>)';
					echo '</li>';
				}
				?>
			</ul>
		</fieldset>
		<?php
	}
}
elseif($statDisplay === 'taxonomy'){
	$famArr = $collManager->getTaxonomyStats();
	?>
	<fieldset id="taxonomystats" style="margin:20px;width:90%;">
		<legend><b>Family Distribution</b></legend>
		<div style="margin:15px;float:left;">
            Click on the occurrence record counts within the parenthesis to return the records for that family
		</div>
		<div style="clear:both;">
			<ul>
				<?php
				foreach($famArr as $name => $cnt){
					echo '<li>';
					echo $name;
					echo ' (<a href="../list.php?db='.$collid.'&type=1&reset=1&taxa='.$name.'" target="_blank">'.$cnt.'</a>)';
					echo '</li>';
				}
				?>
			</ul>
		</div>
	</fieldset>
	<?php
}
