<?php
/*
Plugin Name: Dev Seed - Create Posts & Pages
Description: Small developer plugin to programmatically create sample posts and pages for learning.
Version: 0.1
Author: You
*/

if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Create sample posts and pages on plugin activation.
 * Store created post IDs in an option so we can clean up later.
 */

function dev_seed_activate() {
  // posts
  $items = array(
    array(
      "post_type" => "post",
      "post_title" => "first sample post 1",
      'post_content' => "this is post 1 created from php",
      'post_status' => "publish",
    ),
    array(
      "post_type" => "post",
      "post_title" => "second sample post 2",
      'post_content' => "this is post 2 created from php",
      'post_status' => "publish",
    ),
    );

  
}