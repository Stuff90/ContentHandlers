<?php



	/**
	*
	* The error handler is a Trait used to display errors meaningfully
	*
	**/
	require_once('src/ErrorHandler.php');



	/**
	*
	* The Content class allow to display data more easily
	*
	**/
	require_once('src/Content.php');



	/**
	*
	* A wrapper to Wordpress WP_Query object freindly
	*
	**/
	require_once('src/ContentManager.php');



	/**
	*
	* Abstract class providing basic method for templating
	*
	**/
	require_once('src/ContentRender.php');



	/**
	*
	* Simple class with one static method to quickly include template without path wondering
	*
	**/
	require_once('src/Template.php');



	/**
	*
	* Class to loop inside data array and include template automatically
	*
	**/
	require_once('src/ListItem.php');



	/**
	*
	* Class dedicated to parse content in order to display it properly
	* This behavior is fully customizable
	*
	**/
	require_once('src/FieldRender.php');



