jQuery(document).ready(function()
{
	console.log("Init: ACF Widget 1.0");

	jQuery("body").on("change", "select.acf-widget-observer", function()
	{
		var acf_group_id = jQuery(this).val();
		console.log(acf_group_id);
	});
});