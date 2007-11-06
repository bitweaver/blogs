<?php
/**
 * @package blogs
 * @subpackage functions
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

if (!isset($gContent->mPostId)) {
	$parts = parse_url($_SERVER['REQUEST_URI']);

	$paths = explode('/', $parts['path']);
	$blog_id = $paths[count($paths) - 2];
	$post_id = $paths[count($paths) - 1];
	// So this is to process a trackback ping
	if (isset($_REQUEST['__mode'])) {
		// Build RSS listing trackback_from
		$pings = $gContent->getTrackbacksFrom();
	}

	if (isset($_REQUEST['url'])) {
		// Add a trackback ping to the list of trackback_from
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';

		$excerpt = isset($_REQUEST['excerpt']) ? $_REQUEST['excerpt'] : '';
		$blog_name = isset($_REQUEST['blog_name']) ? $_REQUEST['blog_name'] : '';

		if ($gContent->addTrackbackFrom( $_REQUEST['url'], $title, $excerpt, $blog_name ) ) {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>0</error>');
			print ('</response>');
		} else {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>1</error>');
			print ('<message>Error trying to add ping for post</message>');
			print ('</response>');
		}

		die;
	}
}

$gBitSystem->verifyPackage( 'blogs' );

$gBitSystem->verifyPermission( 'p_blogs_view' );

// Check permissions to access this page
if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( tra( 'Post cannot be found' ));
}

$displayHash = array( 'perm_name' => 'p_blogs_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );
$gBitSmarty->assign('post_id', $gContent->mPostId);

//Build absolute URI for this
if ( empty( $_REQUEST['format'] ) || $_REQUEST['format'] == "full" || $_REQUEST['format'] == "data" ){
	$parts = parse_url($_SERVER['REQUEST_URI']);
	$parsed_data = $gContent->parseData();
	if ($gBitSystem->isFeatureActive( 'blog_posts_comments' ) ) {
		$comments_return_url = $_SERVER['PHP_SELF']."?post_id=".$gContent->mPostId;
		$commentsParentId = $gContent->mContentId;
		include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
	}
	$extendedTitle = isset($gContent->mInfo['blogtitle']) ? ' - '.$gContent->mInfo['blogtitle'] : NULL;
	$gBitSystem->setBrowserTitle($gContent->mInfo['title'].$extendedTitle);
}else{
	// if the format requested is not the full post or the readmore data we default to just the first half of the post
	$data = ( $_REQUEST['format'] != "more" )?$gContent->mInfo['raw']:$gContent->mInfo['raw_more'];
	$data = preg_replace( LIBERTY_SPLIT_REGEX, "", $data);
	$parsed_data = $gContent->parseData( $data, ($gContent->getField('format_guid') ? $gContent->getField('format_guid') : 'tikiwiki') );	
}

$gBitSmarty->assign('parsed_data', $parsed_data);
$gBitSmarty->assign('post_info', $gContent->mInfo );

// Display the template
if ( isset( $_REQUEST['output'] ) && $_REQUEST['output']="ajax"){	
	$gBitSystem->display( 'bitpackage:blogs/view_blog_post_xml.tpl', NULL, 'center_only');
}else{
	$gBitSystem->display( 'bitpackage:blogs/view_blog_post.tpl' );
}
?>
