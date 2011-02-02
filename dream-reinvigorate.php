<?php
/*
Plugin Name: Reinvigorate
Plugin URI: http://www.reinvigorate.net/
Description: Reinvigorate tracking plugin for Dreamscape. NOTE. Set your options in Admin Settings.
Author: Ian Tearle
Version: 1.1
Author URI: http://www.iantearle.com/
*/
ozone_action('preferences_menu','re_config_menu');

function re_config_menu()
{
?>
	<!-- /*   Reinvigorate Menu   //===============================*/ -->
    <h3 class="stretchToggle" title="reinvigorate"><a href="#reinvigorate"><span>Reinvigorate // measure. analyze. evolve.</span></a></h3>
    <div class="stretch" id="reinvigorate">
    <label for="re_tracking_id">Tracking ID</label>
    <input type="text" name="re_tracking_id" id="re_tracking_id" value="<?php echo getOption('re_tracking_id'); ?>">
	<?php tooltip('Reinvigorate Tracking ID', 'Sign-in to your Reinvigorate account.<br/><a href=\"http://report.reinvigorate.net/login\">http://report.reinvigorate.net/login</a>. Copy and paste the provided tracking ID into the input box.'); ?>
     <label for="re_name_tagging">Name Tagging</label>
     <input type="hidden" value="0" name="re_name_tagging" />
     <input type="checkbox" name="re_name_tagging" value="1" <?php echo getOption('re_name_tagging') == 1 ? 'checked="checked"' : ''; ?> class="cBox" id="re_name_tagging" />
     <?php tooltip('Reinvigorate Name Tagging', 'If checked, this option will include your name in the tracking code when you are logged in and browsing your website.'); ?>
     </div>
	 <?php
}


function re_config()
{
	if (isset($_POST["re_tracking_id"]))
	{
		if (empty($_POST["re_tracking_id"]))
			$output .= "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("Tracking ID cleared. Stats will not be logged.") . "</strong></p></div>";
		else
			$output .= "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("Saved Changes.") . "</strong></p></div>";
		
		if ($_POST["re_name_tagging"] == "on")
			update_option("re_name_tagging", "");
		else
			update_option("re_name_tagging", "disabled");
			
		update_option("re_tracking_id", trim($_POST["re_tracking_id"]));
	}
}

function re_include_code()
{
	$re_id = getOption("re_tracking_id");
	if (empty($re_id))
		return;
	
	$print = "\n<!-- Reinvigorate Dreamscape Plugin -->\n";
	$print .= "<script type=\"text/javascript\" src=\"http&#58;//include.reinvigorate.net/re_.js\"></script>\n";
	$print .= "<script type=\"text/javascript\">\n";
	$print .= "//<![CDATA[\n";
	
	if (getOption("re_name_tagging") != 0)
	{	
		if (isset($_COOKIE['baked']) && !empty($_COOKIE['baked']))
		{
			$userarray = unserialize(base64_decode($_COOKIE['baked']));
			
			$author = $userarray['username'];
			$context = '';//$userarray['url'];
		
			$print.= "var re_name_tag = \"$author\";\n";
		
			if ($context == "http&#58;//")
				$context = "";
		
			if (empty($context))
				$context = "mailto&#58;" . $userarray['email'];
		
			if ($context == "mailto&#58;")
				$context = "";
		
			if ($context != "mailto&#58;")
				$print.= "var re_context_tag = \"" . str_replace("\"","\\\"",$context) . "\";\n";
		} 
	}
	
	$print .= "re_(\"$re_id\");\n";
	$print .= "//]]>\n";
	$print .= "</script>\n";
	$print .= "<!-- End -->\n\n";
		
	return $print;
}
$reinclude = re_include_code();
if(function_exists('add_variable')){
	add_variable('reinvigorate:'.$reinclude, 'footer');
}
?>