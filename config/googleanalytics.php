<?php 
if(isset($GOOGLE_ANALYTICS_KEY) && $GOOGLE_ANALYTICS_KEY) {
	?>
	window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo $GOOGLE_ANALYTICS_KEY; ?>');
	<?php 
} 
?>
