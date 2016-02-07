<?php
/**
 * @package blogs
 * @subpackage functions
 */

/**
 * Initial Setup
 */
global $gContent; 
require_once( BLOGS_PKG_PATH.'BitBlogPost.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if blog_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['post_id'] ) ) {
		$gContent = new BitBlogPost( $_REQUEST['post_id'] );
		$gContent->load();
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitBlogPost( NULL, $_REQUEST['content_id'] );
		$gContent->load();
	} else {
		$gContent = new BitBlogPost();
	}

	$gBitSmarty->assignByRef( 'gContent', $gContent );
}
?>
