<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/index.php,v 1.9 2007/06/13 20:19:32 squareing Exp $

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
require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyPermission( 'p_blogs_view' );


// {{{ start hack
// this is a very stupid hack until we can sort out the following problem: when 
// calling center_list_blog_posts.tpl, the php file that goes with it is only 
// called when the tpl file is called, which is in the middle of the rendering 
// process i.e. after header_inc.tpl has been rendered.  services such as stars 
// need to be called before the rendering process that data can be passed on 
// into their header_inc.tpl file.
$dummy = array();
$gContent->getList( $dummy );
// }}} end hack


$gBitSmarty->assign( 'showEmpty', TRUE );
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );

?>
