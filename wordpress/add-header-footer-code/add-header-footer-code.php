<?php
// ADD THIS CODE ON functions.php OR USE CODE SNIPPETS PLUGIN
/**
 * Add Code on Header
 */
add_action('wp_head', 'headCode');

function headCode()
{
?>
	<!-- Header Code -->
<?php
};

/**
 * Add Code on Footer
 */
// Footer Code
add_action('wp_footer', 'footerCode');
function footerCode()
{
?>
	<!-- Footer Code -->
<?php
};
