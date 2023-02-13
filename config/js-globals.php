<script type="text/javascript">
    const DEFAULT_LANG = '<?php echo $GLOBALS['DEFAULT_LANG']; ?>';
    const CLIENT_ROOT = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>';
    const SOLR_MODE = '<?php echo $GLOBALS['SOLR_MODE']; ?>';
    const USER_DISPLAY_NAME = '<?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>';

    const TAXONOMIC_RANKS = JSON.parse('<?php echo $GLOBALS['TAXONOMIC_RANKS']; ?>');

    const SPATIAL_INITIAL_ZOOM = <?php echo $GLOBALS['SPATIAL_INITIAL_ZOOM']; ?>;
    const SPATIAL_INITIAL_CENTER = <?php echo $GLOBALS['SPATIAL_INITIAL_CENTER']; ?>;
    const SPATIAL_POINT_FILL_COLOR = '<?php echo $GLOBALS['SPATIAL_POINT_FILL_COLOR']; ?>';
    const SPATIAL_POINT_BORDER_COLOR = '<?php echo $GLOBALS['SPATIAL_POINT_BORDER_COLOR']; ?>';
    const SPATIAL_POINT_BORDER_WIDTH = <?php echo $GLOBALS['SPATIAL_POINT_BORDER_WIDTH']; ?>;
    const SPATIAL_POINT_POINT_RADIUS = <?php echo $GLOBALS['SPATIAL_POINT_POINT_RADIUS']; ?>;
    const SPATIAL_POINT_SELECTIONS_BORDER_COLOR = '<?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR']; ?>';
    const SPATIAL_POINT_SELECTIONS_BORDER_WIDTH = <?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH']; ?>;
    const SPATIAL_SHAPES_FILL_COLOR = '<?php echo $GLOBALS['SPATIAL_SHAPES_FILL_COLOR']; ?>';
    const SPATIAL_SHAPES_BORDER_COLOR = '<?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR']; ?>';
    const SPATIAL_SHAPES_BORDER_WIDTH = <?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH']; ?>;
    const SPATIAL_SHAPES_POINT_RADIUS = <?php echo $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS']; ?>;
    const SPATIAL_SHAPES_OPACITY = '<?php echo $GLOBALS['SPATIAL_SHAPES_OPACITY']; ?>';
    const SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR']; ?>';
    const SPATIAL_SHAPES_SELECTIONS_FILL_COLOR = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR']; ?>';
    const SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH = <?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH']; ?>;
    const SPATIAL_SHAPES_SELECTIONS_OPACITY = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY']; ?>';
    const SPATIAL_DRAGDROP_FILL_COLOR = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR']; ?>';
    const SPATIAL_DRAGDROP_BORDER_COLOR = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR']; ?>';
    const SPATIAL_DRAGDROP_BORDER_WIDTH = <?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH']; ?>;
    const SPATIAL_DRAGDROP_POINT_RADIUS = <?php echo $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS']; ?>;
    const SPATIAL_DRAGDROP_OPACITY = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_OPACITY']; ?>';
    const SPATIAL_DRAGDROP_RASTER_COLOR_SCALE = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE']; ?>';

    const http = new XMLHttpRequest();
    let abortController;
    const proxyApiUrl = CLIENT_ROOT + '/api/proxy.php';
    const occurrenceTaxonomyApiUrl = CLIENT_ROOT + '/api/collections/occTaxonomyController.php';
    const languageApiUrl = CLIENT_ROOT + '/api/misc/languageController.php';
    const taxonomyApiUrl = CLIENT_ROOT + '/api/taxa/taxaController.php';
    const imageApiUrl = CLIENT_ROOT + '/api/images/imageController.php';
    const mediaApiUrl = CLIENT_ROOT + '/api/media/mediaController.php';
</script>
