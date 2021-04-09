<?php 
if(isset($GLOBALS['GOOGLE_ANALYTICS_KEY']) && $GLOBALS['GOOGLE_ANALYTICS_KEY']) {
	?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $GLOBALS['GOOGLE_ANALYTICS_KEY']; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $GLOBALS['GOOGLE_ANALYTICS_KEY']; ?>');
    </script>
	<?php 
} 
?>
