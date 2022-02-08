<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Data Usage Guidelines</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h1>Guidelines for Acceptable Use of Data</h1>

    <h2>Recommended Citation Formats</h2>
    <div class="pmargin">
        Use one of the following formats to cite data retrieved from the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> network:
        <h3>General Citation:</h3>
        <div class="pmargin">
            <?php
            echo $GLOBALS['DEFAULT_TITLE'].'. '.date('Y').'. ';
            echo 'http//:'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/').'index.php. ';
            echo 'Accessed on '.date('F d').'. ';
            ?>
        </div>

        <h3>Usage of occurrence data from specific institutions:</h3>
        <div class="pmargin">
            Biodiversity occurrence data published by: &lt;List of Collections&gt;
            (Accessed through <?php echo $GLOBALS['DEFAULT_TITLE']; ?> Data Portal,
            <?php echo 'http//:'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/').'index.php'; ?>, YYYY-MM-DD)<br/><br/>
            <b>For example:</b><br/>
            Biodiversity occurrence data published by:
            Field Museum of Natural History, Museum of Vertebrate Zoology, and New York Botanical Garden
            (Accessed through <?php echo $GLOBALS['DEFAULT_TITLE']; ?> Data Portal,
            <?php echo 'http//:'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/').'index.php, '.date('Y-m-d').')'; ?>
        </div>
    </div>

    <h2>Occurrence Record Use Policy</h2>
    <div class="pmargin">
        <ul>
            <li>
                While <?php echo $GLOBALS['DEFAULT_TITLE']; ?> will make every effort possible to control and document the quality
                of the data it publishes, the data are made available "as is". Any report of errors in the data should be
                directed to the appropriate curators and/or collections managers.
            </li>
            <li>
                <?php echo $GLOBALS['DEFAULT_TITLE']; ?> cannot assume responsibility for damages resulting from mis-use or
                mis-interpretation of datasets or from errors or omissions that may exist in the data.
            </li>
            <li>
                It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
                has resulted in data used in subsequent research. We encourages users to
                contact the original investigator responsible for the data that they are accessing.
            </li>
            <li>
                <?php echo $GLOBALS['DEFAULT_TITLE']; ?> asks that users not redistribute data obtained from this site without permission for data owners.
                However, links or references to this site may be freely posted.
            </li>
        </ul>
    </div>

    <h2>Images</h2>
    <div class="pmargin">
        Images within this website have been generously contributed by their owners to
        promote education and research. These contributors retain the full copyright for
        their images. Unless stated otherwise, images are made available under the Creative Commons
        Attribution-ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA</a>)
        Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution
        regarding the source of the content is made. If the content is altered, transformed, or enhanced,
        it may be re-distributed only under the same or similar license by which it was acquired.
    </div>

    <h2>Notes on Specimen Records and Images</h2>
    <div class="pmargin">
        Specimens are used for scientific research and because of skilled preparation and
        careful use they may last for hundreds of years. Some collections have specimens
        that were collected over 100 years ago that are no longer occur within the area.
        By making these specimens available on the web as images, their availability and
        value improves without an increase in inadvertent damage caused by use. Note that
        if you are considering making specimens, remember collecting normally requires
        permission of the landowner and, in the case of rare and endangered plants,
        additional permits may be required. It is best to coordinate such efforts with a
        regional institution that manages a publically accessible collection.
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
