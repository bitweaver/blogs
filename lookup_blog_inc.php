<?php
global $gContent, $gBitSmarty;

require_once( BLOGS_PKG_PATH.'BitBlog.php');

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gBlog ) || !is_object( $gBlog ) || !$gBlog->isValid() ) {
	// if blog_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['blog_id'] ) ) {
		$gBlog = new BitBlog( $_REQUEST['blog_id'] );
		$gBlog->load();
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gBlog = new BitBlog( NULL, $_REQUEST['content_id'] );
		$gBlog->load();
	} else {
		$gBlog = new BitBlog();
	}

	$gBitSmarty->assign_by_ref( "gBlog", $gBlog );
 }
?>
