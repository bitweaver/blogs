<?php
/**
 * @package blogs
 * @subpackage functions
 */

/**
 * required setup
 */
global $gContent;
require_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if blog_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['blog_id'] ) ) {
		$gContent = new BitBlog( $_REQUEST['blog_id'] );
		$gContent->load();
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitBlog( NULL, $_REQUEST['content_id'] );
		$gContent->load();
	} else {
		$gContent = new BitBlog();
	}

	$gBitSmarty->assignByRef( 'gContent', $gContent );
} 
?>
