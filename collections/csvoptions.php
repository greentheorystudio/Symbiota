<div id="csvoptions" data-role="popup" class="well" style="width:600px;height:80%;">
    <a class="boxclose csvoptions_close" id="boxclose"></a>
    <h2>Download CSV Data</h2>
    <div style="margin:15px;width:550px;">
        By downloading data, the user confirms that he/she has read and agrees with the general
        <a href="../misc/usagepolicy.php" target="_blank">data usage terms</a>.
        Note that additional terms of use specific to the individual collections
        may be distributed with the data download. When present, the terms
        supplied by the owning institution should take precedence over the
        general terms posted on the website.
    </div>
    <div style='margin:15px;width:100%;'>
        <b>Download Occurrence Records</b>
        <table>
            <tr>
                <td style="vertical-align:top">
                    <div style="margin:10px;">
                        <b>Structure:</b>
                    </div>
                </td>
                <td>
                    <div style="margin:10px 0;">
                        <input data-role="none" type="radio" name="schema" id="csvschemasymb" value="native" checked /> Native<br />
                        <input data-role="none" type="radio" name="schema" id="csvschemadwc" value="dwc" /> Darwin Core<br />
                        *<a href='http://rs.tdwg.org/dwc/index.htm' class='bodylink' target='_blank'>What is Darwin Core?</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top">
                    <div style="margin:10px;">
                        <b>Data Extensions:</b>
                    </div>
                </td>
                <td>
                    <div style="margin:10px 0;">
                        <input data-role="none" type="checkbox" name="identifications" id="csvidentifications" value="1" onchange="extensionSelected(this);" checked /> include Determination History<br />
                        <input data-role="none" type="checkbox" name="images" id="csvimages" value="1" onchange="extensionSelected(this);" checked /> include Image Records<br />
                        *Output must be a compressed archive
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top">
                    <div style="margin:10px;">
                        <b>File Format:</b>
                    </div>
                </td>
                <td>
                    <div style="margin:10px 0;">
                        <input data-role="none" type="radio" name="format" id="csvformatcsv" value="csv" checked /> Comma Delimited (CSV)<br />
                        <input data-role="none" type="radio" name="format" id="csvformattab" value="tab" /> Tab Delimited<br />
                    </div>
                </td>
            </tr>
            <tr id="zipSelectionRow">
                <td style="vertical-align:top">
                    <div style="margin:10px;">
                        <b>Compression:</b>
                    </div>
                </td>
                <td>
                    <div style="margin:10px 0;">
                        <input data-role="none" type="checkbox" name="zip" id="csvzip" value="1" onchange="zipSelected(this);" checked /> Compressed ZIP file<br />
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width:550px;">
                    <input type="hidden" name="cset" value='utf-8' />
                    <div style="margin:10px;float:right;">
                        <button data-role="none" onclick='prepCsvControlForm();' >Download Data</button>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
