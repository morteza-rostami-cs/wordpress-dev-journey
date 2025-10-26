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

# add meta box to Movie post_type
# creates a section on the Movie editor page titled -> Movie Details
add_action(
  'add_meta_boxes', #run when WP builds meta boxes on the post edit screen.
  function() {
    add_meta_box(
      id: 'movie_details_box', # metabox id -> is some kinda join table
      title: 'Movie Details', # ui form title
      callback: 'wpmovie_render_details_box', # function to output html form ui
      screen: 'movie', # attach to movie custom post type
      context: 'normal', # form placement
      priority: 'default', # order priority
    );
  }
);

# render the meta box -> Html
function wpmovie_render_details_box(
  $post, # receives post data
) {

  // verification on save
  wp_nonce_field(
    action: 'wpmovie_save_details',
    name: 'wpmovie_details_nonce',
  );

  $rating = get_post_meta(
    post_id: $post->ID, 
    key: '_movie_rating',
    single: true,
  );

  $year = get_post_meta(
    post_id: $post->ID, # movie.id
    key: '_movie_year', # metadata key in db
    single: true,
  );

  // inject the data into form for update and so on.
  ?>
  <label>🎯 Rating:</label>
  <input 
  type="number" 
  name="movie_rating" 
  value="<?php echo esc_attr($rating); ?>" 
  min="0" 
  max="10" 
  step="0.1" />
  <br><br>
  <label>📅 Release Year:</label>
  <input 
  type="number" 
  name="movie_year" 
  value="<?php echo esc_attr($year); ?>" 
  min="1900" 
  max="<?php echo date('Y'); ?>" />
  <?php
}

# store the meta box inputs on Movie save

add_action(
  hook_name: 'save_post_movie', # Runs when a movie is saved.
  callback: function (int $post_id, WP_POST $post, bool $update) {

    // if: there is a movie_rating -> inside form POST request
    if (isset($_POST['movie_rating'])) {
      // store metadata on post save submit
      update_post_meta(
        post_id: $post_id,
        meta_key: '_movie_rating',
        meta_value: floatval($_POST['movie_rating']), # cast to float
      );
    }

    // if post has -> movie_year
    if (isset($_POST['movie_year'])) {
      update_post_meta(
        post_id: $post_id,
        meta_key: '_movie_year',
        meta_value: intval($_POST['movie_year']),
      );
    }

    # save default genre from plugin settings on first create
    if (!$update) {
      $default_genre = get_option(option: "wpmovie_default_genre");

      if ($default_genre) {
        // when creating movie, set default genre
        wp_set_object_terms(
          object_id: $post_id,
          terms: $default_genre,
          taxonomy: 'genre',
        );
      }
    }
  },
  accepted_args: 3, 
);

# add custom cols to the movie admin table
add_filter(
  hook_name: 'manage_movie_posts_columns',
  callback: function ($columns) {
    $columns['movie_rating'] = 'Rating';
    $columns['movie_year'] = 'Year';

    return $columns;
  }
);

# add cols values
add_action(
  hook_name: "manage_movie_posts_custom_column",
  callback: function($column, $post_id) {
    if ($column === 'movie_rating') {
      echo esc_html(
        get_post_meta(
          post_id: $post_id, 
          key: '_movie_rating', 
          single: true)
      ) ?: '-';
    }

    if ($column === 'movie_year') {
      echo esc_html(
        get_post_meta(
          post_id: $post_id, 
          key: '_movie_year', 
          single: true)
      ) ?: '-';
    }
  },
  priority: 10,
  accepted_args: 2,
);

# make rating & year columns sortable
add_filter(
  hook_name: 'manage_edit-movie_sortable_columns',
  callback: function($columns) {
    $columns['movie_rating'] = 'movie_rating';
    $columns['movie_year'] = 'movie_year';

    return $columns;
  }
);

# add Movie settings menu page

function wpmovie_register_settings_page() {
  // add option settings
  add_options_page(
    page_title: 'Movie Settings',
    menu_title: 'Movie Settings',
    capability: 'manage_options',
    menu_slug: 'wpmovie-settings',
    callback: 'wpmovie_render_settings_page',
  );
}

# runs for admin menu and register -> movie_menu settings
add_action(
  hook_name: 'admin_menu',
  callback: 'wpmovie_register_settings_page',
);

# render settings page (Movie)
function wpmovie_render_settings_page() {
  echo '<div class="wrap">';
  echo '<h1>Movie Settings</h1>';
  echo '<p>welcome to the movie settings page! 🎬</p>';

  echo '<form method="post" action="options.php">';

  settings_fields('wpmovie_settings_group');
  do_settings_sections('wpmovie-settings');
  submit_button();

  echo '</form>';
  echo '</div>';
}

# using options api -> for plugin settings

function wpmovie_register_settings() {
  register_setting(
    option_group: "wpmovie_settings_group",
    option_name: 'wpmovie_default_genre',
  );

  // add a settings section
  add_settings_section(
    id: "wpmovie_general_section",
    title: "General Settings",
    callback: null,
    page: "wpmovie-settings", # section's page
  );

  // register a form field
  // stored in -> wp_options table
  add_settings_field(
    id: "wpmovie_default_genre",
    title: "Default Genre",
    callback: "wpmovie_default_genre_field", # render form field
    page: "wpmovie-settings",
    section: "wpmovie_general_section",
  );
}

add_action(
  hook_name: 'admin_init',
  callback: 'wpmovie_register_settings',
);

# render input field for option
function wpmovie_default_genre_field() {
  # get default value
  $value = get_option(
    option: 'wpmovie_default_genre', # field id
    default_value: '',
  );

  echo '<input 
  type="text"
  name="wpmovie_default_genre"
  value="' . esc_attr($value) . '"
  />';
}

/*

# creating admin menus:
  - add an item under settings tab -> add_options_page()
  - create a top-level menu -> add_menu_page()

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