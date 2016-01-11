<?php
    /**
    * Plugin Name: Content Hanlders
    * Plugin URI: http://ileotech.com/
    * Description: Content Hanlders tool
    * Version: 1.0
    * Author: Simon BERNARD for Ileotech
    * Author URI: http://ileotech.com/
    * License: All rights reserved to Ileotech
    */

define("CONTENT_HANDLERS_PATH", ABSPATH . 'wp-content/plugins/content-handlers/');

/**
*
* The error handler is a Trait used to display errors meaningfully
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/ErrorHandler.php');



/**
*
* The Content class allow to display data more easily
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/Content.php');



/**
*
* A wrapper to Wordpress WP_Query object freindly
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/ContentManager.php');



/**
*
* Abstract class providing basic method for templating
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/ContentRender.php');



/**
*
* Simple class with one static method to quickly include template without path wondering
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/Template.php');



/**
*
* Class to loop inside data array and include template automatically
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/ListItem.php');



/**
*
* Class dedicated to parse content in order to display it properly
* This behavior is fully customizable
*
**/
require_once( CONTENT_HANDLERS_PATH . 'src/FieldRender.php');
