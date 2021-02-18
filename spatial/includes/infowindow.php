<div id="infopopup" data-role="popup" class="well" style="width:400px;height:400px;">
    <a class="boxclose infopopup_close" id="boxclose"></a>
    <?php
    if(!in_array('point', $inputWindowModeTools, true)){
        $instructionText = 'Select a feature type in the Draw drop-down menu and then click on the map to draw a new feature. ';
        $instructionText .= 'Click on any drawn feature on the map to select and deselect. ';
        if(strpos($windowType, '-') === false){
            $instructionText .= 'Once all of the features you would like to submit have been selected, click the Submit Coordinates button to submit. ';
        }
        else{
            $instructionText .= 'When the feature you would like to submit is selected, and it is the only selected feature, you can click the Submit Coordinates button to submit. ';
        }
    }
    elseif(in_array('point', $inputWindowModeTools, true)){
        $instructionText = 'Select a Point feature type in the Draw drop-down menu and then click on the map to draw a new point. ';
        if(in_array('uncertainty', $inputWindowModeTools, true)){
            $instructionText .= 'Once a point has been drawn, enter a numeric value for the Coordinate uncertainty in meters to see the uncertainty circle. ';
            $instructionText .= 'Click the Submit Coordinates button to submit both the point and uncertainty. ';
        }
        if(in_array('radius', $inputWindowModeTools, true)){
            $instructionText .= 'Once a point has been drawn, enter a numeric value for the Radius in meters to see the point-radius circle. ';
            $instructionText .= 'Click the Submit Coordinates button to submit both the point and radius. ';
        }
        else{
            $instructionText .= 'Click the Submit Coordinates button to submit the point. ';
        }
    }
    ?>
    <div style="padding:20px;font-size: 14px;">
        <?php echo $instructionText; ?>
    </div>
</div>
