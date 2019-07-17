<?php
function wppl_htmlblocks_register_post_type(){
	$labels = array(
		'name' => 'HTML Блоки',
		'singular_name' => 'Блок',
		'add_new' => 'Добавить',
		'add_new_item' => 'Добавить новый',
		'edit_item' => 'Редактировать',
		'new_item' => 'Новый',
		'view_item' => 'Посмотреть',
		'search_items' => 'Найти',
		'not_found' =>  'Блоков не найдено',
		'not_found_in_trash' => 'В корзине блоков не найдено',
		'parent_item_colon' => '',
		'menu_name' => 'HTML Блоки'
	);

	$supports = array('title');

	register_post_type('htmlblocks',
		array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 50,
			'supports' => $supports,
		)
	);

	$labels_for_htmlblocks_category = array(
		'name' => 'Рубрики HTMl Блоков',
		'singular_name' => 'Рубрика',
		'search_items' => 'Найти рубрику',
		'all_items' => 'Все рубрики',
		'view_item ' => 'Посмотреть рубрику',
		'parent_item' => 'Родительская рубрика',
		'parent_item_colon' => 'Родительская рубрика:',
		'edit_item' => 'Ребактировать рубрику',
		'update_item' => 'Обновить рубрику',
		'add_new_item' => 'Добавить новую рубрику',
		'new_item_name' => 'Имя новой рубрики',
		'menu_name' => 'Рубрики'
	);

	register_taxonomy('htmlblocks_category', array('htmlblocks'), array(
		'labels' => $labels_for_htmlblocks_category,
		'hierarchical' => true,
		'show_ui' => true,
		'query_var' => true
	));
}

function wppl_htmlblocks_restrict_manage_posts() {
	global $typenow;
	$post_type = 'htmlblocks';
	$taxonomy  = 'htmlblocks_category';
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __($info_taxonomy->label),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}

function wppl_htmlblocks_parse_query($query) {
	global $pagenow;
	$post_type = 'htmlblocks';
	$taxonomy  = 'htmlblocks_category';
	$q_vars    = &$query->query_vars;
	if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

function wppl_htmlblocks_mb_options($post){
	$wppl_htmlblocks_mb_option_html = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_html', true);
	$wppl_htmlblocks_mb_option_html_close = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_html_close', true);
	$wppl_htmlblocks_mb_option_css = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_css', true);
	$wppl_htmlblocks_mb_option_js = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_js', true);
	$wppl_htmlblocks_mb_option_js_deps = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_js_deps', true);
	$wppl_htmlblocks_mb_option_sc = get_post_meta($post->ID, 'wppl_htmlblocks_mb_option_sc', true);

	echo '<p>Shortcode для вставки (появится/обновится после сохранения)</p>
		<input tabIndex="-1" style="width: 100%;" readonly id="wppl_htmlblocks_mb_option_sc" name="wppl_htmlblocks_mb_option_sc" value="' . (!empty($wppl_htmlblocks_mb_option_sc) ? $wppl_htmlblocks_mb_option_sc . '[/htmlblocks]' : '') . '" />
		<input tabIndex="-1" style="width: 100%;" readonly value="' . (!empty($wppl_htmlblocks_mb_option_sc) ? $wppl_htmlblocks_mb_option_sc . 'Содержимое[/htmlblocks]' : '') . '" /><hr />';

	$settings = array(
		'wpautop' => 0,
		'media_buttons' => 0,
		'textarea_rows' => 10,
		'tabindex' => -1,
		'tinymce' => 0,
		'quicktags' => 0
	);

	echo '<p>HTML код (без CSS и JS)</p>';
	$settings['textarea_name'] = 'wppl_htmlblocks_mb_option_html';
	wp_editor((!empty($wppl_htmlblocks_mb_option_html) ? $wppl_htmlblocks_mb_option_html : ''), 'wppl_htmlblocks_mb_option_html', $settings);

	echo '<p>Закрытие пред. HTML кода (без CSS и JS), только если используется парный Shortcode</p>';
	$settings['textarea_name'] = 'wppl_htmlblocks_mb_option_html_close';
	wp_editor((!empty($wppl_htmlblocks_mb_option_html_close) ? $wppl_htmlblocks_mb_option_html_close : ''), 'wppl_htmlblocks_mb_option_html_close', $settings);

	echo '<p>CSS код (без ' . htmlspecialchars('<style></style>') . ')</p>';
	$settings['textarea_name'] = 'wppl_htmlblocks_mb_option_css';
	wp_editor((!empty($wppl_htmlblocks_mb_option_css) ? $wppl_htmlblocks_mb_option_css : ''), 'wppl_htmlblocks_mb_option_css', $settings);

	echo '<p>JS код (без ' . htmlspecialchars('<script></script>') . ')</p>';
	$settings['textarea_name'] = 'wppl_htmlblocks_mb_option_js';
	wp_editor((!empty($wppl_htmlblocks_mb_option_js) ? $wppl_htmlblocks_mb_option_js : ''), 'wppl_htmlblocks_mb_option_js', $settings);

	echo '<p>JS зависимости от др. скриптов (через запятую)<br/>
	<input style="width: 100%;" id="wppl_htmlblocks_mb_option_js_deps" name="wppl_htmlblocks_mb_option_js_deps" value="' . (!empty($wppl_htmlblocks_mb_option_js_deps) ? $wppl_htmlblocks_mb_option_js_deps : '') . '" /></p>';
}

function wppl_htmlblocks_add_meta_boxes(){
	add_meta_box('wppl_htmlblocks_mb_options', 'HTML/CSS/JS блоки', 'wppl_htmlblocks_mb_options', array('htmlblocks'));
}

function save_post_htmlblocks($post_id) {
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_html', $_POST['wppl_htmlblocks_mb_option_html']);
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_html_close', $_POST['wppl_htmlblocks_mb_option_html_close']);
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_css', $_POST['wppl_htmlblocks_mb_option_css']);
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_js', $_POST['wppl_htmlblocks_mb_option_js']);
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_js_deps', $_POST['wppl_htmlblocks_mb_option_js_deps']);
	update_post_meta($post_id, 'wppl_htmlblocks_mb_option_sc', htmlspecialchars('[htmlblocks id="' . $post_id . '"]'));

	wppl_htmlblocks_create_files($post_id, $_POST['wppl_htmlblocks_mb_option_css'], $_POST['wppl_htmlblocks_mb_option_js']);
}

function minimizeCSS($css){
	$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css);
	$css = preg_replace('/\s{2,}/', ' ', $css);
	$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
	$css = preg_replace('/;}/', '}', $css);

	return $css;
}

function minifyJavascriptCode($js){
	$js = preg_replace('/([-\+])\s+\+([^\s;]*)/', '$1 (+$2)', $js);
	$js = preg_replace('/\s+\|\|\s+/', ' || ', $js);
	$js = preg_replace('/\s+\&\&\s+/', ' && ', $js);
	$js = preg_replace('/\s*([=+-\/\*:?])\s*/', '$1 ', $js);

	$blocks = array('for', 'while', 'if', 'else');
	foreach ($blocks as $block) $js = preg_replace('/(\s*\b' . $block . '\b[^{\n]*)\n([^{\n]+)\n/i', '$1{$2}', $js);

	$js = preg_replace(array("/\s*\n\s*/", "/\h+/"), array("\n", " "), $js); // \h+ horizontal white space
	$js = preg_replace(array('/([^a-z0-9\_])\h+/i', '/\h+([^a-z0-9\$\_])/i'), '$1', $js);
	$js = preg_replace('/\n?([[;{(\.+-\/\*:?&|])\n?/', '$1', $js);
	$js = preg_replace('/\n?([})\]])/', '$1', $js);
	$js = str_replace("\nelse", "else", $js);
	$js = preg_replace("/([^}])\n/", "$1;", $js);
	$js = preg_replace("/;?\n/", ";", $js);

	return $js;
}

function wppl_htmlblocks_create_files($post_id, $post_css, $post_js) {
	$filesDir = WP_PLUGIN_DIR . '/' . plugin_basename(__DIR__) . '/files/';

	if (!file_exists($filesDir) && !is_dir($filesDir)) mkdir($filesDir, 0755);
	if (!is_dir($filesDir)) return false;

	$stylePath = $filesDir . 'style-' . $post_id . '.css';
	$scriptPath = $filesDir . 'script-' . $post_id . '.js';

	$styleHandle = fopen($stylePath, 'w');
	$scriptHandle = fopen($scriptPath, 'w');

	$post_css = minimizeCSS(stripslashes($post_css));
	$post_js = /*minifyJavascriptCode(*/stripslashes($post_js)/*)*/;

	fwrite($styleHandle, $post_css);
	fwrite($scriptHandle, $post_js);

	fclose($styleHandle);
	fclose($scriptHandle);

	if (is_file($stylePath) && is_file($scriptPath)) return true;
}

function wppl_htmlblocks_delete_files($post_id) {
	$filesDir = WP_PLUGIN_DIR . '/' . plugin_basename(__DIR__) . '/files/';

	$stylePath = $filesDir . 'style-' . $post_id . '.css';
	$scriptPath = $filesDir . 'script-' . $post_id . '.js';

	if (file_exists($stylePath)) unlink($stylePath);
	if (file_exists($scriptPath)) unlink($scriptPath);
}

function trashed_post_htmlblocks($post_id) {
	wppl_htmlblocks_delete_files($post_id);
}

function admin_enqueue_scripts() {
	$code_editor_settings = array(
		'type' => 'text/html',
		'codemirror' => array(
				'tabSize' => 2
		),
	);

	wp_add_inline_script('code-editor', sprintf('jQuery(function($) { var wppl_htmlblocks_mb_option_html = $("#wppl_htmlblocks_mb_option_html"); if (wppl_htmlblocks_mb_option_html.length > 0) wp.codeEditor.initialize(wppl_htmlblocks_mb_option_html, %s); });', wp_json_encode(wp_enqueue_code_editor($code_editor_settings))));
	wp_add_inline_script('code-editor', sprintf('jQuery(function($) { var wppl_htmlblocks_mb_option_html_close = $("#wppl_htmlblocks_mb_option_html_close"); if (wppl_htmlblocks_mb_option_html_close.length > 0) wp.codeEditor.initialize(wppl_htmlblocks_mb_option_html_close, %s); });', wp_json_encode(wp_enqueue_code_editor($code_editor_settings))));
	wp_add_inline_script('code-editor', sprintf('jQuery(function($) { var wppl_htmlblocks_mb_option_css = $("#wppl_htmlblocks_mb_option_css"); if (wppl_htmlblocks_mb_option_css.length > 0) wp.codeEditor.initialize(wppl_htmlblocks_mb_option_css, %s); });', wp_json_encode(wp_enqueue_code_editor(array('type' => 'text/css')))));
	wp_add_inline_script('code-editor', sprintf('jQuery(function($) { var wppl_htmlblocks_mb_option_js = $("#wppl_htmlblocks_mb_option_js"); if (wppl_htmlblocks_mb_option_js.length > 0) wp.codeEditor.initialize(wppl_htmlblocks_mb_option_js, %s); });', wp_json_encode(wp_enqueue_code_editor(array('type' => 'application/javascript')))));
}

function wppl_htmlblocks_sc_links($id, $deps = '') {
	$stylePath = WP_PLUGIN_DIR . '/' . plugin_basename(__DIR__) . '/files/style-' . $id . '.css';
	$scriptPath = WP_PLUGIN_DIR . '/' . plugin_basename(__DIR__) . '/files/script-' . $id . '.js';

	if (file_exists($stylePath)) wp_enqueue_style('style-' . $id, plugin_dir_url(__FILE__) . 'files/style-' . $id . '.css');
	if (file_exists($stylePath)) wp_enqueue_script('script-' . $id, plugin_dir_url(__FILE__) . 'files/script-' . $id . '.js', $deps, '', true);
}

function wppl_htmlblocks_sc($atts, $content = '') {
	if (empty($atts['id'])) return false;

	$wppl_htmlblocks_mb_option_html = get_post_meta($atts['id'], 'wppl_htmlblocks_mb_option_html', true);
	
	if (empty($wppl_htmlblocks_mb_option_html)) return false;
	
	$wppl_htmlblocks_mb_option_html_close = get_post_meta($atts['id'], 'wppl_htmlblocks_mb_option_html_close', true);
	$wppl_htmlblocks_mb_option_css = get_post_meta($atts['id'], 'wppl_htmlblocks_mb_option_css', true);
	$wppl_htmlblocks_mb_option_js = get_post_meta($atts['id'], 'wppl_htmlblocks_mb_option_js', true);
	$wppl_htmlblocks_mb_option_js_deps = get_post_meta($atts['id'], 'wppl_htmlblocks_mb_option_js_deps', true);

	if (!empty($wppl_htmlblocks_mb_option_js_deps)) $deps = explode(',', $wppl_htmlblocks_mb_option_js_deps);

	if (empty($wppl_htmlblocks_mb_option_html) && empty($wppl_htmlblocks_mb_option_css) && empty($wppl_htmlblocks_mb_option_js)) return false;

	add_action('wp_footer', function() use ($atts, $deps) { wppl_htmlblocks_sc_links($atts['id'], $deps); });

	return $wppl_htmlblocks_mb_option_html . (!empty($content) && !empty($wppl_htmlblocks_mb_option_html_close) ? $content . $wppl_htmlblocks_mb_option_html_close : '');
}

function wppl_manage_htmlblocks_posts_columns($defaults) {
	$defaults['htmlblocks_column_sc'] = 'Shortcode';
    return $defaults;
}

function wppl_manage_htmlblocks_posts_custom_column($column_name, $post_ID) {
	if ($column_name == 'htmlblocks_column_sc') {
		$wppl_htmlblocks_mb_option_sc = get_post_meta($post_ID, 'wppl_htmlblocks_mb_option_sc', true);
        echo $wppl_htmlblocks_mb_option_sc;
    }
}

function wppl_htmlblocks_admin_footer() {
	if(get_current_screen()->parent_base != 'edit') return;
	echo '<script type="text/javascript"> jQuery(document).ready(function($) { $(window).keydown(function(e) { if(e.ctrlKey && e.keyCode == 83) { e.preventDefault(); $submit = $("[name=\"save\"]").click(); } }); });</script>';
}
?>