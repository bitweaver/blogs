<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/index.php,v 1.10 2007/06/15 16:15:06 wjames5 Exp $

 * @package blogss
 * @subpackage functions
 */
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'blogs' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_blogs_view' );

require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

// {{{ start hack
// this is a very stupid hack until we can sort out the following problem: when 
// calling center_list_blog_posts.tpl, the php file that goes with it is only 
// called when the tpl file is called, which is in the middle of the rendering 
// process i.e. after header_inc.tpl has been rendered.  services such as stars 
// need to be called before the rendering process that data can be passed on 
// into their header_inc.tpl file.
$blogPost = new BitBlogPost();
$dummy = array();
$blogPosts = $blogPost->getList( $dummy );
// }}} end hack

$gBitSmarty->assign( 'showEmpty', TRUE );

// Display the template
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );
?>
