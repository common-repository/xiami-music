<?php
/*
 Plugin Name: Xiami Music
 Plugin URI: http://www.xiami.com/
 Description: This is plugin let you insert auto-search music words in the post.
 Author: sospartan
 Version: 0.24
 Author URI: http://www.xiami.com/blog
 */
add_filter('the_content','convertXiamiUrl',1,2);
add_filter('mce_external_plugins', 'tinymce_xiami_plugin');
add_filter('mce_buttons','tinymce_xiami_buttons');
//把指定标签转化虾米的链接
function convertXiamiUrl($content){

	$text = $content;
	// BBCode [code]
	if (!function_exists('escape')) {
		function escape($s) {
			global $text;
			//			$text = strip_tags($text);
			$code = $s[1];
			$code = htmlspecialchars($code);
			$code = str_replace("[", "&#91;", $code);
			$code = str_replace("]", "&#93;", $code);
			return '<pre><code>'.$code.'</code></pre>';
		}
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);

	$in = array(
	'/\[song\](.*?)\[\/song\]/ms',
	'/\[album\](.*?)\[\/album\]/ms',
	'/\[artist\](.*?)\[\/artist\]/ms',
	'/\[music\](.*?)\[\/music\]/ms'
	);
	$out = array(
	'<a class="xiami_link" href="http://www.xiami.com/search/find?song=\1" rel="song:\1" target="_blank">\1</a>',
	'<a class="xiami_link" href="http://www.xiami.com/search/find?album=\1" rel="album:\1" target="_blank">\1</a>',
	'<a class="xiami_link" href="http://www.xiami.com/search/find?artist=\1" rel="artist:\1" target="_blank">\1</a>',
	'<a class="xiami_link" href="http://www.xiami.com/search/find?music=\1" rel="music:\1" target="_blank">\1</a>'
	);

	$text = preg_replace($in, $out, $text);
	return $text;
}
function tinymce_xiami_plugin($plugin_array) {
	$plugin_array['xiami_tags'] = WP_PLUGIN_URL.'/xiami_music/xiami_plugin.js';
	return $plugin_array;

}
function tinymce_xiami_buttons($buttons) {

	array_push($buttons, 'separator');

	array_push($buttons, 'xiami_music');
	array_push($buttons, 'xiami_song');
	array_push($buttons, 'xiami_album');
	array_push($buttons, 'xiami_artist');

	return $buttons;

}
function add_xiami_options(){
	add_option('xiami_options', array(
	'bobo_title'=>'我正在听',
	'bobo_width'=>235,
	'bobo_heigth'=>346,
	'bobo_f_color'=>'FF8719',
	'bobo_b_color'=>'494949',
	'albums_title'=>'我的唱片架',
	'albums_direction'=>'V',
	'albums_size'=>'M',
	'collects_title'=>'我的精选集',
	'collects_direction'=>'V',
	'collects_size'=>'M',
	'artists_title'=>'我收藏的艺人',
	'artists_direction'=>'H',
	'artists_size'=>'M'
	)
	);
	add_option('xiami_user_id',86);
	add_option('xiami_cache_limit',20);
}

function setup_xiami_options(){
	add_options_page('Xiami Settings', 'XiaMi', 1, __FILE__, 'xiami_options');
}
add_action('admin_menu', 'setup_xiami_options');

function xiami_options(){
	if ((isset($_POST['xiami_user_id']) && is_numeric($_POST['xiami_user_id']))||(isset($_POST['xiami_cache_limit']) && is_numeric($_POST['xiami_cache_limit']))) {
		isset($_POST['xiami_user_id']) && update_option('xiami_user_id', $_POST['xiami_user_id']);
		isset($_POST['xiami_cache_limit']) && update_option('xiami_cache_limit', $_POST['xiami_cache_limit']);
		?>
<div class="updated">
<p><strong><?php
_e('Options saved', 'uga')
?></strong></p>
</div>
<?php
	}
	?>
<div id="xiami_body">
<div class="wrap"><?php
if (true)
{
	//todo check php function ablitilty
}
else
{
	?>
<div class="error" style="padding: 5px; font-weight: bold;">fmTuner
requires PHP version 5 or greater. Please contact your web host for more
information.</div>
	<?php
}
?>
<h2>XiaMi Settings</h2>
<form method="post"><?php wp_nonce_field('update-options'); // Protect against XSS ?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="xiami_user_id">Xiami.com ID:</label></th>
			<td><input type="text" size="25"
				value="<?php echo get_option('xiami_user_id'); ?>"
				id="xiami_user_id" name="xiami_user_id" /> <br />
			Enter your <a href="http://www.xiami.com" target="_blank">Xiami.com</a>
			ID (digit)</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="xiami_cache_limit">Cache limit:</label></th>
			<td><input type="text" size="25"
				value="<?php echo get_option('xiami_cache_limit'); ?>"
				id="xiami_cache_limit" name="xiami_cache_limit" /> <br />
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="hidden" name="action" value="update" /> <input
	type="hidden" name="page_options"
	value="xiami_user_id,xiami_cache_limit" /> <input type="submit"
	name="Submit" value="<?php _e('Save Changes') ?>" /></p>
</form>
</div>
</div>

<?php
}
function delete_xiami_options(){
	delete_option('xiami_options');
	delete_option('xiami_user_id');
}
register_activation_hook(__FILE__, 'add_xiami_options');
register_deactivation_hook(__FILE__, 'delete_xiami_options');

function widget_xiami_init() {

	# does this wordpress environment support widgets?
	if (!function_exists('register_sidebar_widget'))
	return;

	//fetch spec widget size
	function fetchSize($dir,$siz){
		if($dir=='V'){
			switch($siz){
				case 'L':
					$width=227;$height=627;
					break;
				case 'M':
					$width=227;$height=403;
					break;
				case 'S':
					$width=171;$height=291;
					break;
				default:
					$width=227;$height=403;
			}
		}else{
			switch(size){
				case 'L':
					$width=675;$height=235;
					break;
				case 'M':
					$width=507;$height=235;
					break;
				case 'S':
					$width=451;$height=179;
					break;
				default:
					$width=507;$height=235;
			}
		}
		return array('width'=>$width,'heigth'=>$height);
	}

	# xiami bobo
	function widget_xiamibobo($args) {
		extract($args);

		$options = get_option('xiami_options');

		echo "\n\n" . $before_widget . $before_title . $options['bobo_title'] . $after_title . "\n";

			

		echo ' <embed src="http://www.xiami.com/widget/'.get_option('xiami_user_id').'_2_'.$options['bobo_width'].'_'.$options['bobo_heigth'].'_'.$options['bobo_f_color'].'_'.$options['bobo_b_color'].'/shufflePlayer.swf" type="application/x-shockwave-flash" width="'.$options['bobo_width'].'" height="'.$options['bobo_heigth'].'" wmode="opaque"></embed>';
		//    $lfm = new LastFmRecords();
		//    $lfm->display();
		echo $after_widget . "\n\n";
	}

	function widget_xiamibobo_control() {
		$options = get_option('xiami_options');

		if (($_POST['xiamibobo-submit'])) {
			if("" != $_POST['xiamibobo-title']):
			$options['bobo_title'] = strip_tags(stripslashes($_POST['xiamibobo-title']));
			endif;

			if(is_numeric($_POST['xiamibobo-width'])):
			$options['bobo_width'] = $_POST['xiamibobo-width'];
			endif;

			if(is_numeric($_POST['xiamibobo-heigth'])):
			$options['bobo_heigth'] = $_POST['xiamibobo-heigth'];
			endif;

			if("" != $_POST['xiamibobo-f-color']):
			$options['bobo_f_color'] = dechex(hexdec(substr($_POST['xiamibobo-f-color'],0,2)))
			.dechex(hexdec(substr($_POST['xiamibobo-f-color'],2,2)))
			.dechex(hexdec(substr($_POST['xiamibobo-f-color'],4,2)));
			endif;

			if("" != $_POST['xiamibobo-b-color']):
			$options['bobo_b_color'] = dechex(hexdec(substr($_POST['xiamibobo-b-color'],0,2)))
			.dechex(hexdec(substr($_POST['xiamibobo-b-color'],2,2)))
			.dechex(hexdec(substr($_POST['xiamibobo-b-color'],4,2)));
			endif;

			update_option('xiami_options', $options);
		}

		$title = htmlspecialchars($options['bobo_title'], ENT_QUOTES);
		$width = $options['bobo_width'];
		$heigth = $options['bobo_heigth'];
		$fcolor = $options['bobo_f_color'];
		$bcolor = $options['bobo_b_color'];

		?>
<p><label for="xiamibobo-title">Title: <input style="width: 200px;"
	class="widefat" id="xiamibobo-title" name="xiamibobo-title" type="text"
	value="<?php echo $title ?>" /> </label></p>
<p><label for="xiamibobo-width">Width: <input style="width: 30px;"
	id="xiamibobo-width" name="xiamibobo-width" type="text"
	value="<?php echo $width ?>" /> </label></p>

<p><label for="xiamibobo-heigth">Heigth: <input style="width: 30px;"
	id="xiamibobo-heigth" name="xiamibobo-heigth" type="text"
	value="<?php echo $heigth ?>" /> </label></p>
<p><label for="xiamibobo-f-color">Foreground colour(hexadecimal): <b>#</b><input
	style="width: 50px;" id="xiamibobo-f-color" name="xiamibobo-f-color"
	type="text" value="<?php echo $fcolor ?>" /> </label></p>

<p><label for="xiamibobo-b-color">Background colour(hexadecimal): <b>#</b><input
	style="width: 50px;" id="xiamibobo-b-color" name="xiamibobo-b-color"
	type="text" value="<?php echo $bcolor ?>" /> </label></p>

<input
	type="hidden" id="xiamibobo-submit" name="xiamibobo-submit" value="1" />
		<?php
	}

	# xiami albums collects
	function widget_xiamialbums($args) {
		extract($args);

		$options = get_option('xiami_options');
		$size = fetchSize($options['albums_direction'],$options['albums_size']);
		echo "\n\n" . $before_widget . $before_title . $options['albums_title'] . $after_title . "\n";
		echo '<embed src="http://www.xiami.com/widget/'.get_option('xiami_user_id').'_'.$options['albums_direction'];
		echo '_'.$options['albums_size'].'_album/wallPlayer.swf" type="application/x-shockwave-flash"  width="'.$size['width'].'" height="';
		echo $size['heigth'].'" wmode="opaque"></embed>';

		echo $after_widget . "\n\n";
	}

	function widget_xiamialbums_control(){
		$options = get_option('xiami_options');


		if($_POST['xiamialbums-submit']){
			if("" != $_POST['xiamialbums-title']){
				$options['albums_title'] = strip_tags(stripslashes($_POST['xiamialbums-title']));
			}

			if(in_array($_POST['xiamialbums-direction'],array('H','V'))){
				$options['albums_direction'] = $_POST['xiamialbums-direction'];
			}

			if(in_array($_POST['xiamialbums-size'],array('L','M','S'))){
				$options['albums_size'] = $_POST['xiamialbums-size'];
			}

			update_option('xiami_options', $options);
		}
		$title = $options['albums_title'];
		$direction = $options['albums_direction'];
		$size = $options['albums_size'];

		?>
<p><label for="xiamialbums-title">Title: <input style="width: 200px;"
	class="widefat" id="xiamialbums-title" name="xiamialbums-title"
	type="text" value="<?php echo $title ?>" /> </label></p>
<p><strong>Direction: </strong> <select id="xiamialbums-direction"
	name="xiamialbums-direction">
	<option value="V" <?php if($direction=='V'){?> selected="selected"
	<?php }?>>vertical</option>
	<option value="H" <?php if($direction=="H"){?> selected="selected"
	<?php }?>>horizontal</option>
</select></p>

<p><strong>Size: </strong> <select id="xiamialbums-size"
	name="xiamialbums-size">
	<option value="L" <?php if($size=='L'){?> selected="selected" <?php }?>>Large</option>
	<option value="M" <?php if($size=="M"){?> selected="selected" <?php }?>>Middle</option>
	<option value="S" <?php if($size=="S"){?> selected="selected" <?php }?>>Small</option>
</select></p>

<input type="hidden"
	id="xiamialbums-submit" name="xiamialbums-submit" value="1" />
	<?php
	}

	# xiami artists collects
	function widget_xiamiartists($args) {
		extract($args);

		$options = get_option('xiami_options');
		$size = fetchSize($options['artists_direction'],$options['artists_size']);
		echo "\n\n" . $before_widget . $before_title . $options['artists_title'] . $after_title . "\n";
		echo '    <embed src="http://www.xiami.com/widget/';
		echo get_option('xiami_user_id').'_'.$options['artists_direction'].'_'.$options['artists_size'];
		echo '_artist/wallPlayer.swf" type="application/x-shockwave-flash"';
		echo 'width="'.$size['width'].'" height="'.$size['heigth'].'" wmode="opaque"></embed>';

		echo $after_widget . "\n\n";
	}

	function widget_xiamiartists_control(){
		$options = get_option('xiami_options');

		if (($_POST['xiamiartists-submit'])){
			if("" != $_POST['xiamiartists-title']){
				$options['artists_title'] = strip_tags(stripslashes($_POST['xiamiartists-title']));
			}
			if(in_array($_POST['xiamiartists-direction'],array('H','V'))){
				$options['artists_direction'] = $_POST['xiamiartists-direction'];
			}
			if(in_array($_POST['xiamiartists-size'],array('L','M','S'))){
				$options['artists_size'] = $_POST['xiamiartists-size'];
			}

			update_option('xiami_options', $options);
		}
		$title = $options['artists_title'];
		$direction = $options['artists_direction'];
		$size = $options['artists_size'];

		?>
<p><label for="xiamiartists-title">Title: <input style="width: 200px;"
	class="widefat" id="xiamiartists-title" name="xiamiartists-title"
	type="text" value="<?php echo $title ?>" /> </label></p>
<p><strong>Direction: </strong> <select id="xiamiartists-direction"
	name="xiamiartists-direction">
	<option value="V" <?php if($direction=='V'){?> selected="selected"
	<?php }?>>vertical</option>
	<option value="H" <?php if($direction=="H"){?> selected="selected"
	<?php }?>>horizontal</option>
</select></p>
<p><strong>Size: </strong> <select id="xiamiartists-size"
	name="xiamiartists-size">
	<option value="L" <?php if($size=='L'){?> selected="selected" <?php }?>>Large</option>
	<option value="M" <?php if($size=="M"){?> selected="selected" <?php }?>>Middle</option>
	<option value="S" <?php if($size=="S"){?> selected="selected" <?php }?>>Small</option>
</select></p>

<input
	type="hidden" id="xiamiartists-submit" name="xiamiartists-submit"
	value="1" />
	<?php
	}

	# xiami collects
	function widget_xiamicollects($args) {
		extract($args);

		$options = get_option('xiami_options');
		$size = fetchSize($options['collects_direction'],$options['collects_size']);
		echo "\n\n" . $before_widget . $before_title . $options['collects_title'] . $after_title . "\n";
		echo '    <embed src="http://www.xiami.com/widget/';
		echo get_option('xiami_user_id').'_'.$options['collects_direction'].'_'.$options['collects_size'];
		echo '_collect/wallPlayer.swf" type="application/x-shockwave-flash"';
		echo 'width="'.$size['width'].'" height="'.$size['heigth'].'" wmode="opaque"></embed>';
		echo $after_widget . "\n\n";
	}

	function widget_xiamicollects_control(){
		$options = get_option('xiami_options');

		if (($_POST['xiamicollects-submit'])){
			if("" != $_POST['xiamicollects-title']){
				$options['collects_title'] = strip_tags(stripslashes($_POST['xiamicollects-title']));
			}
			if(in_array($_POST['xiamicollects-direction'],array('H','V'))){
				$options['collects_direction'] = $_POST['xiamicollects-direction'];
			}
			if(in_array($_POST['xiamicollects-size'],array('L','M','S'))){
				$options['collects_size'] = $_POST['xiamicollects-size'];
			}
			update_option('xiami_options', $options);
		}
		$title = $options['collects_title'];
		$direction = $options['collects_direction'];
		$size = $options['collects_size'];

		?>
<p><label for="xiamicollects-title">title: <input style="width: 200px;"
	class="widefat" id="xiamicollects-title" name="xiamicollects-title"
	type="text" value="<?php echo $title ?>" /> </label></p>
<p><strong>Direction: </strong> <select id="xiamicollects-direction"
	name="xiamicollects-direction">
	<option value="V" <?php if($direction=='V'){?> selected="selected"
	<?php }?>>vertical</option>
	<option value="H" <?php if($direction=="H"){?> selected="selected"
	<?php }?>>horizontal</option>
</select></p>
<p><strong>Size: </strong> <select id="xiamicollects-size"
	name="xiamicollects-size">
	<option value="L" <?php if($size=='L'){?> selected="selected" <?php }?>>Large</option>
	<option value="M" <?php if($size=="M"){?> selected="selected" <?php }?>>Middle</option>
	<option value="S" <?php if($size=="S"){?> selected="selected" <?php }?>>Small</option>
</select></p>

<input type="hidden"
	id="xiamicollects-submit" name="xiamicollects-submit" value="1" />
	<?php
	}



	register_sidebar_widget('xiami bobo', 'widget_xiamibobo');
	register_widget_control('xiami bobo', 'widget_xiamibobo_control', 375, 95);
	register_sidebar_widget('xiami albums', 'widget_xiamialbums');
	register_widget_control('xiami albums', 'widget_xiamialbums_control', 375, 95);
	register_sidebar_widget('xiami artists', 'widget_xiamiartists');
	register_widget_control('xiami artists', 'widget_xiamiartists_control', 375, 95);
	register_sidebar_widget('xiami collects', 'widget_xiamicollects');
	register_widget_control('xiami collects', 'widget_xiamicollects_control', 375, 95);
}

add_action('plugins_loaded', 'widget_xiami_init');


//xiami url add a popup tip
function xiami_display_hook($content='') {
	$content .=xiami_wp_head();
	return $content;
}
function xiami_wp_head() {
	$path = WP_PLUGIN_URL.'/xiami_music/';
	$load_query =<<<sc
	<script type="text/javascript" >
	function dhtmlLoadScript(url)
{
   var e = document.createElement("script");
   e.src = url;
   e.type="text/javascript";
   document.getElementsByTagName("head")[0].appendChild(e);
}

if(typeof jQuery == 'undefined'){
   dhtmlLoadScript("/wp-includes/js/jquery/jquery.js");
}
</script>
sc;
echo $load_query;
	//echo '<script type="text/javascript" src="'.get_option('siteurl').'/wp-includes/js/jquery/jquery.js?ver=1.2.6"></script>'."\n";
	
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $path . 'xiami_popup.css" />'."\n";
	add_action('wp_footer', 'addFooter');
}
function addFooter() {
	$path = WP_PLUGIN_URL.'/xiami_music/';
	echo '<script type="text/javascript" src="'.$path.'xiami_popup.js"></script>'."\n";
}

add_action('wp_head', 'xiami_display_hook');
?>
