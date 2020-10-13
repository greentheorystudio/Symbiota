<div id="spatialpanel">
    <div id="accordion">
        <h3 class="tabtitle">Search Criteria</h3>
        <div id="criteriatab">
            <ul>
                <li><a class="tabtitle" href="#searchcriteria">Criteria</a></li>
                <li id="spatialCollectionsTab"><a class="tabtitle" href="#searchcollections">Collections</a></li>
            </ul>
            <div id="searchcollections">
                <div class="mapinterface">
                    <form name="spatialcollsearchform" id="spatialcollsearchform" data-ajax="false" action="index.php" method="get">
                        <div>
                            <h1 style="margin:0 0 8px 0;font-size:15px;">Collections to be Searched</h1>
                        </div>
                        <?php
                        if($specArr || $obsArr){
                            ?>
                            <div id="specobsdiv">
                                <div style="margin:0 0 10px 20px;">
                                    <input id="dballcb" data-role="none" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" checked />
                                    Select/Deselect All
                                </div>
                                <?php
                                if($specArr){
                                    $spatialManager->outputFullMapCollArr($specArr);
                                }
                                if($specArr && $obsArr) {
                                    echo '<hr style="clear:both;height:2px;background-color:black;"/>';
                                }
                                if($obsArr){
                                    $spatialManager->outputFullMapCollArr($obsArr);
                                }
                                ?>
                                <div style="clear:both;"></div>
                            </div>
                            <?php
                        }
                        ?>
                    </form>
                </div>
            </div>
            <div id="searchcriteria">
                <div id="spatialcriteriasearchform">
                    <div style="height:25px;">
                        <div style="float:right;">
                            <button data-role="none" type=button id="resetform" name="resetform" onclick='window.open("index.php", "_self");' >Reset</button>
                            <button data-role="none" id="display2" name="display2" onclick='loadPoints();' >Load Records</button>
                        </div>
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div>
                        <span style=""><input data-role="none" type='checkbox' name='thes' id='thes' onchange="buildQueryStrings();" value='1' CHECKED><?php echo $SEARCHTEXT['GENERAL_TEXT_2_MAP']; ?></span>
                    </div>
                    <div id="taxonSearch0">
                        <div id="taxa_autocomplete" >
                            <div style="margin-top:5px;">
                                <select data-role="none" id="taxontype" name="type" onchange="buildQueryStrings();">
                                    <option id='familysciname' value='1'><?php echo $SEARCHTEXT['SELECT_1-1']; ?></option>
                                    <option id='family' value='2'><?php echo $SEARCHTEXT['SELECT_1-2']; ?></option>
                                    <option id='sciname' value='3'><?php echo $SEARCHTEXT['SELECT_1-3']; ?></option>
                                    <option id='highertaxon' value='4'><?php echo $SEARCHTEXT['SELECT_1-4']; ?></option>
                                    <option id='commonname' value='5'><?php echo $SEARCHTEXT['SELECT_1-5']; ?></option>
                                </select>
                            </div>
                            <div style="margin-top:5px;">
                                <?php echo $SEARCHTEXT['TAXON_INPUT']; ?> <input data-role="none" id="taxa" type="text" style="width:275px;" name="taxa" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div>
                        <?php echo $SEARCHTEXT['COUNTRY_INPUT']; ?> <input data-role="none" type="text" id="country" style="width:225px;" name="country" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['STATE_INPUT']; ?> <input data-role="none" type="text" id="state" style="width:150px;" name="state" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['COUNTY_INPUT']; ?> <input data-role="none" type="text" id="county" style="width:225px;"  name="county" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['LOCALITY_INPUT']; ?> <input data-role="none" type="text" id="locality" style="width:225px;" name="local" onchange="buildQueryStrings();" />
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div id="shapecriteriabox">
                        <div id="noshapecriteria">
                            No shapes are selected on the map.
                        </div>
                        <div id="shapecriteria" style="display:none;">
                            Within selected shapes.
                        </div>
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div>
                        <?php echo $SEARCHTEXT['COLLECTOR_LASTNAME']; ?>
                        <input data-role="none" type="text" id="collector" style="width:125px;" name="collector" onchange="buildQueryStrings();" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['COLLECTOR_NUMBER']; ?>
                        <input data-role="none" type="text" id="collnum" style="width:125px;" name="collnum" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_2']; ?>" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['COLLECTOR_DATE']; ?>
                        <input data-role="none" type="text" id="eventdate1" style="width:100px;" name="eventdate1" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_3']; ?>" /> -
                        <input data-role="none" type="text" id="eventdate2" style="width:100px;" name="eventdate2" onchange="buildQueryStrings();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_4']; ?>" />
                    </div>
                    <div style="margin:10px 0 10px 0;"><hr></div>
                    <div>
                        <?php echo $SEARCHTEXT['OCCURRENCE_REMARKS']; ?> <input data-role="none" type="text" id="occurrenceRemarks" style="width:225px;" name="occurrenceRemarks" onchange="buildQueryStrings();" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['CATALOG_NUMBER']; ?>
                        <input data-role="none" type="text" id="catnum" style="width:150px;" name="catnum" onchange="buildQueryStrings();" />
                    </div>
                    <div style="margin-top:5px;">
                        <?php echo $SEARCHTEXT['OTHER_CATNUM']; ?>
                        <input data-role="none" type="text" id="othercatnum" style="width:150px;" name="othercatnum" onchange="buildQueryStrings();" />
                    </div>
                    <div style="margin-top:5px;">
                        <input data-role="none" type='checkbox' name='typestatus' id='typestatus' value='1' onchange="buildQueryStrings();"> <?php echo $SEARCHTEXT['TYPE']; ?>
                    </div>
                    <div style="margin-top:5px;">
                        <input data-role="none" type='checkbox' name='hasimages' id='hasimages' value='1' onchange="buildQueryStrings();"> <?php echo $SEARCHTEXT['HAS_IMAGE']; ?>
                    </div>
                    <div id="searchGeneticCheckbox" style="margin-top:5px;">
                        <input data-role="none" type='checkbox' name='hasgenetic' id='hasgenetic' value='1' onchange="buildQueryStrings();"> <?php echo $SEARCHTEXT['HAS_GENETIC']; ?>
                    </div>
                    <div><hr></div>
                </div>
            </div>
        </div>

        <h3 id="recordsHeader" class="tabtitle" style="display:none;">Records and Taxa</h3>
        <div id="recordstab" style="display:none;width:379px;padding:0;">
            <ul>
                <li><a href='#symbology' onclick='buildCollKey();'>Collections</a></li>
                <li><a href='#queryrecordsdiv' onclick='changeRecordPage(1);'>Records</a></li>
                <li><a href='#maptaxalist' onclick='buildTaxaKey();'>Taxa</a></li>
                <li style="display:none;" id="selectionstab" ><a href='#selectionslist'>Selections</a></li>
            </ul>
            <div id="symbology">
                <div style="margin-bottom:15px;">
                    <div style="float:left;margin-top:20px;">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" style="height:15px;width:15px;margin-bottom:-2px;">">
                                <g>
                                    <circle cx="7.5" cy="7.5" r="7" fill="white" stroke="#000000" stroke-width="1px" ></circle>
                                </g>
                            </svg> = Collection
                        </div>
                        <div style="margin-top:5px;" >
                            <svg style="height:14px;width:14px;margin-bottom:-2px;">" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path stroke="#000000" d="m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z" stroke-width="1px" fill="white"/>
                                </g>
                            </svg> = General Observation
                        </div>
                    </div>
                    <div id="symbolizeResetButt" style='float:right;margin-bottom:5px;' >
                        <div>
                            <button data-role="none" id="symbolizeReset1" onclick='resetSymbology();' >Reset Symbology</button>
                        </div>
                        <div style="margin-top:5px;">
                            <button data-role="none" id="randomColorColl" onclick='autoColorColl();' >Auto Color</button>
                        </div>
                        <div style="margin-top:5px;">
                            <button data-role="none" id="saveCollKeyImage" onclick='saveKeyImage();' >Save Image</button>
                        </div>
                    </div>
                </div>
                <div style="margin:5px 0 5px 0;clear:both;"><hr /></div>
                <div id="collSymbologyKey" style="background-color:white;">
                    <div style="margin-top:8px;">
                        <div style="display:table;">
                            <div id="symbologykeysbox"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="queryrecordsdiv">
                <div style="margin-top:-10px;margin-right:10px;">
                    <fieldset style="border:1px solid black;height:50px;width:360px;margin-left:-10px;padding-top:3px;">
                        <legend><b>Download</b></legend>
                        <div style="height:25px;width:330px;margin-left:auto;margin-right:auto;">
                            <div style="float:left;">
                                <select data-role="none" id="querydownloadselect">
                                    <option>Download Type</option>
                                    <option value="csv">CSV</option>
                                    <option value="kml">KML</option>
                                    <option value="geojson">GeoJSON</option>
                                    <option value="gpx">GPX</option>
                                    <option value="png">Map PNG Image</option>
                                </select>
                            </div>
                            <div style="float:right;">
                                <button data-role="none" type="button" onclick='processDownloadRequest(false);' >Download</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div id="queryrecords"></div>
            </div>
            <div id="maptaxalist" >
                <div style="margin-bottom:15px;">
                    <div style="float:left;margin-top:20px;">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" style="height:15px;width:15px;margin-bottom:-2px;">">
                                <g>
                                    <circle cx="7.5" cy="7.5" r="7" fill="white" stroke="#000000" stroke-width="1px" ></circle>
                                </g>
                            </svg> = Collection
                        </div>
                        <div style="margin-top:5px;" >
                            <svg style="height:14px;width:14px;margin-bottom:-2px;">" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path stroke="#000000" d="m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z" stroke-width="1px" fill="white"/>
                                </g>
                            </svg> = General Observation
                        </div>
                    </div>
                    <div id="symbolizeResetButt" style='float:right;margin-bottom:5px;' >
                        <div>
                            <button data-role="none" id="symbolizeReset2" onclick='resetSymbology();' >Reset Symbology</button>
                        </div>
                        <div style="margin-top:5px;">
                            <button data-role="none" id="randomColorTaxa" onclick='autoColorTaxa();' >Auto Color</button>
                        </div>
                        <div style="margin-top:5px;">
                            <button data-role="none" id="saveTaxaKeyImage" onclick='saveKeyImage();' >Save Image</button>
                        </div>
                    </div>
                </div>
                <div style="margin:5px 0 5px 0;clear:both;"><hr /></div>
                <div style="margin-bottom:30px;">
                    <div style='font-weight:bold;float:left;margin-bottom:5px;'>Taxa Count: <span id="taxaCountNum">0</span></div>
                    <div style="float:right;margin-bottom:5px;">
                        <button data-role="none" id="taxacsvdownload" onclick="exportTaxaCSV();" >Download CSV</button>
                    </div>
                </div>
                <div style="margin:5px 0 5px 0;clear:both;"><hr /></div>
                <div id="taxasymbologykeysbox" style="background-color:white;"></div>
            </div>

            <div id="selectionslist" style="">
                <div>
                    <div style="margin-top:-10px;margin-right:10px;">
                        <fieldset style="border:1px solid black;height:50px;width:360px;margin-left:-10px;padding-top:3px;">
                            <legend><b>Download</b></legend>
                            <div style="height:25px;width:330px;margin-left:auto;margin-right:auto;">
                                <div style="float:left;">
                                    <select data-role="none" id="selectdownloadselect">
                                        <option value="">Download Type</option>
                                        <option value="csv">CSV</option>
                                        <option value="kml">KML</option>
                                        <option value="geojson">GeoJSON</option>
                                        <option value="gpx">GPX</option>
                                    </select>
                                </div>
                                <div style="float:right;">
                                    <button data-role="none" name="submitaction" type="button" onclick='processDownloadRequest(true);' >Download</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div style="margin-top:10px;">
                        <div style="float:left;">
                            <div>
                                <button data-role="none" id="clearselectionsbut" onclick='clearSelections();' >Clear Selections</button>
                            </div>
                        </div>
                        <div id="" style='margin-right:15px;float:right;' >
                            <div>
                                <button data-role="none" id="zoomtoselectionsbut" onclick='zoomToSelections();' >Zoom to Selections</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear:both;height:10px;"></div>
                <table class="styledtable" style="font-family:Arial,serif;font-size:12px;margin-left:-15px;">
                    <thead>
                    <tr>
                        <th style="width:15px;"></th>
                        <th>Catalog #</th>
                        <th>Collector</th>
                        <th style="width:40px;">Date</th>
                        <th>Scientific Name</th>
                    </tr>
                    </thead>
                    <tbody id="selectiontbody"></tbody>
                </table>
            </div>
        </div>

        <h3 class="tabtitle">Vector Tools</h3>
        <div id="vectortoolstab" style="width:379px;padding:0;">
            <ul>
                <li><a class="tabtitle" href="#polycalculatortab">Shapes</a></li>
                <li><a class="tabtitle" href="#pointscalculatortab">Points</a></li>
            </ul>
            <div id="polycalculatortab" style="width:379px;padding:0;">
                <div style="padding:10px;">
                    <div style="height:45px;">
                        <div style="float:right;">
                            Total area of selected shapes (sq/km)
                        </div>
                        <div style="float:right;margin-top:5px;">
                            <input data-role="none" type="text" id="polyarea" style="width:250px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="0" disabled />
                        </div>
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <b>Download Shapes</b> <select data-role="none" id="shapesdownloadselect">
                            <option value="">Download Type</option>
                            <option value="kml">KML</option>
                            <option value="geojson">GeoJSON</option>
                        </select>
                        <button data-role="none" style="margin-left:5px;" type="button" onclick='downloadShapesLayer();' >Download</button>
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <button data-role="none" onclick="createBuffers();" >Buffer</button> Creates buffer polygon of <input data-role="none" type="text" id="bufferSize" style="width:50px;" /> km around selected features.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <button data-role="none" onclick="createPolyDifference();" >Difference</button> Returns a new polygon with the area of the polygon or circle selected first, exluding the area of the polygon or circle selected second.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <button data-role="none" onclick="createPolyIntersect();" >Intersect</button> Returns a new polygon with the area overlapping of both selected polygons or circles.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <button data-role="none" onclick="createPolyUnion();" >Union</button> Returns a new polygon with the combined area of two or more selected polygons or circles. *Note new polygon will replace all selected shapes.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                </div>
            </div>

            <div id="pointscalculatortab" style="width:379px;padding:0;">
                <div id="pointToolsNoneDiv" style="padding:10px;margin-top:10px;display:block;">
                    There are no points loaded on the map.
                </div>
                <div id="pointToolsDiv" style="padding:10px;display:none;">
                    <div>
                        <button data-role="none" onclick="createConcavePoly();" >Concave Hull Polygon</button> Creates a concave hull polygon or multipolygon for
                        <select data-role="none" id="concavepolysource" style="margin-top:3px;" onchange="checkPointToolSource('concavepolysource');">
                            <option value="all">all</option>
                            <option value="selected">selected</option>
                        </select> points with a maximum edge length of <input data-role="none" type="text" id="concaveMaxEdgeSize" style="width:75px;margin-top:3px;" /> kilometers.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                    <div style="margin-top:10px;">
                        <button data-role="none" onclick="createConvexPoly();" >Convex Hull Polygon</button> Creates a convex hull polygon for
                        <select data-role="none" id="convexpolysource" style="margin-top:3px;" onchange="checkPointToolSource('convexpolysource');">
                            <option value="all">all</option>
                            <option value="selected">selected</option>
                        </select> points.
                    </div>
                    <div style="margin:5px 0 5px 0;"><hr /></div>
                </div>
            </div>
        </div>

    </div>
</div>
