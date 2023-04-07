<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            ArtBen777
 * @copyright         2023 ArtBen777
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Search Slug Plugin
 * Plugin URI:        https://github.com/art-ben/search-slug-plugin
 * Description:       Plugin which allows you to search post/pages/post_types by slug inside /wp-admin area
 * Version:           0.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Artem Benevskiy
 * Author URI:        https://github.com/art-ben/
 * Text Domain:       search-slug-plugin
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/art-ben/search-slug-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

function slug_search_init() {
    load_plugin_textdomain('search-slug-plugin', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'slug_search_init');

function slug_search_admin_pages($query) {
    global $pagenow;

    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['s']) && strpos($_GET['s'], 'slug:') !== false) {
        $slug = str_replace('slug:', '', $_GET['s']);
        $query->query_vars['name'] = $slug; // set the slug to the query
        unset($query->query_vars['s']); // remove the search term, because without this, the search will return nothing && but this clear basic search title "Search results for"
    }
}
add_action('pre_get_posts', 'slug_search_admin_pages');

function slug_search_acf($args, $field, $post_id) {
    if (is_admin() && strpos($args['s'], 'slug:') !== false) {
        $slug = str_replace('slug:', '', $args['s']);
        $args['name'] = $slug;
        unset($args['s']); // remove the search term, because without this, the search will return nothing
    }

    return $args;
}
add_filter('acf/fields/post_object/query', 'slug_search_acf', 10, 3);
add_filter('acf/fields/page_link/query', 'slug_search_acf', 10, 3);
add_filter('acf/fields/relationship/query', 'slug_search_acf', 10, 3);

function search_slug_nav_menu( $q ) {
	if(isset($_POST['action']) && $_POST['action']=="menu-quick-search" && isset($_POST['menu-settings-column-nonce'])){ // check if we are in the menu admin page and in action for quick search
		$search_term = str_replace('slug:', '', $_POST['q']);
		if( is_a($q->query_vars['walker'], 'Walker_Nav_Menu_Checklist') ){
			$q->query_vars['name'] =  $search_term;
			unset($q->query_vars['s']); // remove the search term, because without this, the search will return nothing
		}
	}
	return $q;
}

add_action( 'pre_get_posts', 'search_slug_nav_menu', 10, 2 );

