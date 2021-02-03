<?php
/**
 * @version $Header$
 */
 
/**
 * required setup
 */
require_once( '../kernel/includes/setup_inc.php' );

if( !@BitBase::verifyId( $_REQUEST['blog_id'] ) && $gBitSystem->isFeatureActive( 'blog_home' )) {
	$_REQUEST['blog_id'] = $gBitSystem->getConfig( 'blog_home' );
}

// if we have a blog_id, we display the correct blog - otherwise we simply display recent posts
if( @BitBase::verifyId( $_REQUEST['blog_id'] )) {
	include_once( BLOGS_PKG_INCLUDE_PATH.'display_bitblog_inc.php' );
} else {
	include_once( BLOGS_PKG_PATH.'recent_posts.php' );
}
?>
