<?php
/**
 * @package blogs
 * @subpackage functions
 */

	global $gContent, $gBitSmarty;

	if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
		$postId = @BitBase::verifyId( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : NULL;
		$conId = @BitBase::verifyId( $_REQUEST['content_id'] ) ? $_REQUEST['content_id'] : NULL;
		$gContent = new BitBlogPost( $postId, $conId );
		$gContent->load();
		$comments_return_url = $_SERVER['PHP_SELF']."?post_id=$postId";
		$gBitSmarty->assign_by_ref( 'gContent', $gContent );
	}
?>
