<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view_post.php,v 1.15 2008/04/17 21:25:57 spiderr Exp $

 * @package blogs
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'blogs' );

require_once( BLOGS_PKG_PATH.'BitBlogPost.php' );

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

if( !$gContent->isValid() ) {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( "The blog post you requested could not be found." );
}

$gContent->verifyViewPermission();

$now = $gBitSystem->getUTCTime();
$view = FALSE;

if ( $gContent->hasAdminPermission()  || ( $gContent->hasUserPermission( 'p_blog_posts_read_future' ) && $gContent->hasUserPermission( 'p_blog_posts_read_expired' ) ) ){
	$view = TRUE;
}elseif ( $gContent->mInfo['publish_date'] == $gContent->mInfo['expire_date'] ) {
	$view = TRUE;
}elseif ( $gContent->mInfo['publish_date'] > $now && $gBitUser->hasPermission( 'p_blog_posts_read_future' ) ){
	$view = TRUE;
}elseif ( $gContent->mInfo['expire_date'] < $now && $gBitUser->hasPermission( 'p_blog_posts_read_expired' ) ){
	$view = TRUE;
}elseif ( ( $gContent->mInfo['publish_date'] <= $now ) && ( $gContent->mInfo['expire_date'] > $now || $gContent->mInfo['expire_date'] <= $gContent->mInfo['publish_date'] ) ){
	$view = TRUE;
}

if ($view == TRUE){
	include_once( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );
}else{
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( "The blog post you requested could not be found." );
}

if( $gContent->isValid() ) {
	$gContent->addHit();
}
?>
