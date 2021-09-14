<?php
/** @var boolean $inputWindowMode */
/** @var array $inputWindowModeTools */
/** @var string $inputWindowSubmitText */
?>
<div id="maptoolcontainer">
    <div id="maptoolbox">
        <div class="topToolboxRow">
            <div id="drawcontrol">
                <span class="maptext">Draw</span>
                <select id="drawselect">
                    <option value="None">None</option>
                    <?php
                    if(!$inputWindowModeTools || in_array('polygon', $inputWindowModeTools, true)){
                        echo '<option value="Polygon">Polygon</option>';
                    }
                    if(!$inputWindowModeTools || in_array('polygon', $inputWindowModeTools, true) || in_array('box', $inputWindowModeTools, true)){
                        echo '<option value="Box">Box</option>';
                    }
                    if(!$inputWindowModeTools || in_array('circle', $inputWindowModeTools, true)){
                        echo '<option value="Circle">Circle</option>';
                    }
                    if(!$inputWindowModeTools || in_array('linestring', $inputWindowModeTools, true)){
                        echo '<option value="LineString">Line</option>';
                    }
                    if(!$inputWindowModeTools || in_array('point', $inputWindowModeTools, true)){
                        echo '<option value="Point">Point</option>';
                    }
                    ?>
                </select>
            </div>
            <div id="basecontrol">
                <span class="maptext">Base Layer</span>
                <select data-role="none" id="base-map" onchange="changeBaseMap();">
                    <option value="googleterrain">Google Terrain</option>
                    <option value="googleroadmap">Google Terrain-Roadmap</option>
                    <option value="googlealteredroadmap">Google Roadmap</option>
                    <option value="googlehybrid">Google Satellite-Roadmap</option>
                    <option value="googlesatellite">Google Satellite</option>
                    <option value="worldtopo">ESRI World Topo</option>
                    <option value="worldimagery">ESRI World Imagery</option>
                    <option value="esristreet">ESRI StreetMap</option>
                    <option value="ocean">ESRI Ocean</option>
                    <option value="ngstopo">National Geographic Topo</option>
                    <option value="natgeoworld">National Geographic World</option>
                    <option value="openstreet">OpenStreetMap</option>
                    <option value="blackwhite">Stamen Design Black &amp; White</option>
                </select>
            </div>
        </div>
        <div class="middleToolboxRow">
            <div id="selectcontrol">
                <span class="maptext">Active Layer</span>
                <select id="selectlayerselect" onchange="setActiveLayer();">
                    <option id="lsel-none" value="none">None</option>
                </select>
            </div>
        </div>
        <div class="bottomToolboxRow">
            <?php
            if(!$inputWindowMode){
                ?>
                <div id="settingsLink" style="margin-left:22px;float:left;">
                    <span class="maptext"><a class="mapsettings_open" href="#mapsettings"><b>Settings</b></a></span>
                </div>
                <?php
            }
            ?>
            <div id="layerControllerLink" style="margin-left:22px;float:left;">
                <span class="maptext"><a class="addLayers_open" href="#addLayers"><b>Layers</b></a></span>
            </div>
            <?php
            if($inputWindowMode){
                ?>
                <div id="infopopupLink" style="margin-left:22px;float:left;">
                    <span class="maptext"><a class="infopopup_open" href="#infopopup"><b>Info</b></a></span>
                </div>
                <div style="margin-left:22px;float:left;">
                    <button data-role="none" id="inputSubmitButton" type="button" onclick='processInputSubmit();' disabled>Submit <?php echo $inputWindowSubmitText; ?></button>
                </div>
                <?php
            }
            ?>
            <div id="deleteSelections" style="margin-left:22px;float:left;">
                <button data-role="none" type="button" onclick='deleteSelections();' >Delete Shapes</button>
            </div>
        </div>
        <div style="clear:both;"></div>
        <?php
        if(in_array('uncertainty', $inputWindowModeTools, true) || in_array('radius', $inputWindowModeTools, true)){
            $labelText = in_array('uncertainty', $inputWindowModeTools, true) ? 'Coordinate uncertainty' : 'Radius';
            ?>
            <div style="margin-top:8px;clear:both;color:white;">
                <span class="maptext"><?php echo $labelText; ?> in meters: </span>
                <input data-role="none" id="inputpointuncertainty" type="text" style="width:100px;" name="inputpointuncertainty" onchange="processInputPointUncertaintyChange();" title="Coordinate uncertainty in meters" />
            </div>
            <?php
        }
        if(!$inputWindowMode){
            ?>
            <div id="dateslidercontrol" style="margin-top:5px;display:none;">
                <div style="margin:5px 0 5px 0;color:white;"><hr /></div>
                <div id="setdatediv" style="">
                    <span class="maptext">Earliest</span>
                    <input data-role="none" type="text" id="datesliderearlydate" style="width:100px;margin-right:5px;" onchange="checkDSLowDate();" />
                    <span class="maptext">Latest</span>
                    <input data-role="none" type="text" id="datesliderlatedate" style="width:100px;margin-right:25px;" onchange="checkDSHighDate();" />
                    <button data-role="none" type="button" onclick="setDSValues();" >Set</button>
                </div>
                <div style="margin:5px 0 5px 0;color:white;"><hr /></div>
                <div id="animatediv">
                    <div>
                        <span class="maptext">Interval Duration (years)</span>
                        <input data-role="none" type="text" id="datesliderinterduration" style="width:40px;margin-right:5px;" onchange="checkDSAnimDuration();" />
                        <span class="maptext">Interval Time (seconds)</span>
                        <input data-role="none" type="text" id="datesliderintertime" style="width:40px;margin-right:10px;" onchange="checkDSAnimTime();" />
                    </div>
                    <div style="clear:both;"></div>
                    <div style="margin-top:3px;">
                        <div style="float:left;">
                        <span style="margin-right:5px;">
                            <span class="maptext">Save Images</span>
                            <input data-role="none" type='checkbox' id='dateslideranimimagesave' onchange="checkDSSaveImage();" value='1'>
                        </span>
                            <span style="margin-right:5px;">
                            <span class="maptext">Reverse</span>
                            <input data-role="none" type='checkbox' id='dateslideranimreverse' value='1'>
                        </span>
                            <span>
                            <span class="maptext">Dual</span>
                            <input data-role="none" type='checkbox' id='dateslideranimdual' value='1'>
                        </span>
                        </div>
                        <div style="float:right;">
                            <button data-role="none" type="button" onclick="setDSAnimation();" >Start</button>
                            <button data-role="none" type="button" onclick="stopDSAnimation();" >Stop</button>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
