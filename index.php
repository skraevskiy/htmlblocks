<?php
/*
 * Plugin Name: WPPL HTML Blocks
 * Description: WordPress plugin to add HTML shortcodes with CSS and JS inserts into the content of any pages
 * Author:      Sergey Kraevskiy
 * Version:     1.0
*/

require_once 'functions.php';

add_action('init', 'wppl_htmlblocks_register_post_type');
add_action('add_meta_boxes', 'wppl_htmlblocks_add_meta_boxes');
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts');

add_action('trashed_post', 'trashed_post_htmlblocks');
add_action('save_post_htmlblocks', 'save_post_htmlblocks');

add_action('restrict_manage_posts', 'wppl_htmlblocks_restrict_manage_posts');
add_filter('parse_query', 'wppl_htmlblocks_parse_query');

add_shortcode('htmlblocks', 'wppl_htmlblocks_sc');

add_filter('manage_htmlblocks_posts_columns', 'wppl_manage_htmlblocks_posts_columns');
add_action('manage_htmlblocks_posts_custom_column', 'wppl_manage_htmlblocks_posts_custom_column', 10, 2);

add_filter('admin_footer', 'wppl_htmlblocks_admin_footer');