<?php
/**
 * Plugin Name: WP Movie Library
 * Plugin URI:  https://github.com/YOUR_USERNAME/wp-movie-library
 * Description: A custom plugin to manage and display movies with custom fields, genres, and frontend views.
 * Version:     1.0.0
 * Author:      Morteza Rostami
 * Author URI:  https://rostami-dev.top
 * License:     GPL2
 * Text Domain: wp-movie-library
 */

// prevents the direct execution of this script, by calling a url
if (!defined('ABSPATH')) {
  exit; //prevent direct access
}

function wpmovie_activate_notice() {

  // store plugin version inside wp options table.
  # think of options table as some global site wide storage
  add_option('wpmovie_plugin_version', '1.0.0');

  // log to /wp-content/debug.php
  error_log('✅ movie plugin is working!');

  // register custom post first -> then flush -> for permalink to work
  wpmovie_register_post_type();

  // flush rewrite rules once, for permalink s to work
  // without: new post type page -> is undefined
  // http://localhost:10004/movie/first-movie-1/ => get 404
  flush_rewrite_rules();
}

// register a function , that runs only on plugin activation
// __FILE__ => path the the current file
register_activation_hook(__FILE__, 'wpmovie_activate_notice');

// runs on deactivation
function wpmovie_deactivate() {
  // clean up temp data
  error_log('🛑 wp movie library deactivated');
}

// register deactivate function
register_deactivation_hook(__FILE__, 'wpmovie_deactivate');

// custom post_type for movies
function wpmovie_register_post_type() {

  // wp admin setting -> stuff that shows inside wp admin
  $labels = [
    'name' => 'Movies',
    'singular_name' => 'Movie',
    'add_new' => 'Add New', // can add movie
    'add_new_item' => 'Add New Movie', // add new item text 
    'edit_item' => 'Edit Movie',
    'new_item' => 'New Movie',
    'view_item' => 'View Movie',
    'search_items' => 'Search Movies',
  ];

  // basically you get something similar to posts
  $args = [
    "labels" => $labels, 
    "public" => true, # visible on frontend and admin
    "has_archive" => true, # enables archive page /movie
    "menu_position" => 5, # position in admin menu, maybe after posts

    'menu_icon' => 'dashicons-video-alt2', // video camera icon

    // editor options
    "supports" => [
      'title', // title input
      'editor',  // description -> title editor
      'thumbnail', // feature image box
      'excerpt',
      'comments',
    ], 

    # control url slug (custom) for SEO
    'rewrite' => [
      'slug' => "movies", # /movies/first-move
      'with_front' => true,
    ],
    'show_in_rest' => true, # enable gutenberg editor + rest api
  ];

  // register a post type with slug -> /movie
  register_post_type("movie", $args);
}

// hook into wordpress initialization -> so this function runs on init and register a post type
add_action("init", "wpmovie_register_post_type");

// for adding new movie cols/data -> i can use custom fields or meta boxes
// custom post -> is not a separate table data -> in lives inside the same table: wp_posts -> with post_type = 'movie'

# custom taxonomy

function wpmovie_register_taxonomy(){ 
  // admin options for this taxonomy in admin panel
  $labels = [
    'name' => 'Genres',# label for taxonomy
    'singular_name' => 'Genre', 
    'search_items' => 'Search Genres',
    'all_items' => 'All Genres',
    'edit_item' => 'Edit Genre',
    'update_item' => 'Update Genre',
    'add_new_item' => 'Add New genre',
    'new_item_name' => 'New Genre Name',
    'menu_name' => 'Genres',
  ];

  $args = [ 
    'labels' => $labels,
    'public' => true,
    # if: true
      # behaves like categories -> categories are relational -> parent -> child
      # books => romance
      # also you get checkboxes in ui
    'hierarchical' => false,
    # adds a col in the movies admin table -> with genres
    'show_admin_column' => true, # show in movie admin table
    'show_in_rest' => true, # gutenberg + rest api 
  ];

  # register taxonomy
  register_taxonomy(
    taxonomy: 'genre', // name
    object_type: ['movie'], # attach it to Movie CPT
    args: $args # arguments
  );
}

# runs this on plugin init -> setup taxonomy
add_action(
  "init", 
  'wpmovie_register_taxonomy',
);

// add taxonomy genre admin filter dropdown ui

# runs when WP renders the filter area above list tables (posts/pages).
add_action(
  "restrict_manage_posts", # an action
  function () { # callback

  global $typenow;

  // run this only for movie CPT
  if ($typenow !== 'movie') return;

  #error_log($typenow);

  $taxonomy = 'genre';
  // so query_string has -> ?genre=6
  $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

  // error_log($selected); 6 or 7 or whatever number

  // this is the actual <select> element
  wp_dropdown_categories([
    'show_option_all' => 'All Genres', // select all
    'taxonomy' => $taxonomy, # taxonomy = genre
    'name' => $taxonomy, # form name field
    'orderby' => 'name', 
    'selected' => $selected, # current option.value -> comes from url_str
    'hierarchical' => false, # indent children
    'show_count' => false,
    'hide_empty' => true, # don't show a empty category
  ]);
});

# filtering code -> when the dropdown changes

add_filter(
  'parse_query', # runs when WP builds the main WP_Query from request vars . 
  function($query) {

  global $pagenow; # current admin page eg: edit.php
  $taxonomy = 'genre';  # taxonomy name
  # & -> assignment by reference
  $q_vars = &$query->query_vars; # query strings array

  // error_log(implode(', ', $query->query_vars));
  #error_log(json_encode($query->query_vars)); # associative array of all url_str s 

  if(
    $pagenow === "edit.php" && # on edit page
    isset($q_vars['post_type']) && # there is a post_type
    $q_vars['post_type'] === 'movie' && # post_type/taxonomy = 'movie'
    isset($q_vars[$taxonomy]) && # there is a ?genre=movie in url string
    is_numeric($q_vars[$taxonomy]) && # value is numeric
    $q_vars[$taxonomy] != 0 # and it is not 0
    ) {
      // when you select a taxonomy item in <select> it sets an id: ?post_type=movie&genre=12)

      // then: we look up the slug based on id
      $term = get_term_by(
        field: 'id', # select id
        value: $q_vars[$taxonomy], # value in url for movie 
        taxonomy: $taxonomy
      );

      // replace id with slug -> for query to work
      $q_vars[$taxonomy] = $term->slug;
  }

});

/*

# taxonomy is essentially -> a tagging or categories system.

# two types of taxonomies:

  # categories:
    - hierarchical -> like folders
    - news -> sport
    - posts can have one or multiple categories

  # tags:

    - non-hierarchical -> keywords -> flat
    - eg: wordpress, javascript
    - posts can have many tags

  # custom taxonomies
    - Movie CPT -> Genre or Director

4️⃣ Why Use Taxonomies?

Organization: Group related content logically

Navigation: Build menus, filters, or archive pages by taxonomy

Custom Queries: Easily fetch posts based on taxonomy

Extensibility: Can attach custom taxonomies to any CPT

5️⃣ How Taxonomies Work Internally

Database: Stored in tables wp_terms, wp_term_taxonomy, wp_term_relationships

Each post (or CPT) can have many terms from many taxonomies

WordPress provides functions like:

get_the_terms($post_id, 'genre') → get all genres for a movie

wp_set_post_terms($post_id, ['Action'], 'genre') → assign terms

6️⃣ In Admin

When you register a taxonomy, WordPress automatically adds a UI panel to assign it to posts/CPTs

You can control whether it shows in the admin table, meta boxes, or REST API

*/ 