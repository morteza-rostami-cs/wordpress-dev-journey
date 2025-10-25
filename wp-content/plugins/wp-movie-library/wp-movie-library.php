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