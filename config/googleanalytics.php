<?php 
if(isset($GOOGLE_ANALYTICS_KEY) && $GOOGLE_ANALYTICS_KEY) {
	?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $GOOGLE_ANALYTICS_KEY; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $GOOGLE_ANALYTICS_KEY; ?>');
    </script>
	<?php 
} 
?>
