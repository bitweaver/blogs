<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.php,v 1.1.1.1.2.2 2005/07/26 15:50:02 drewslater Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $module_rows;

$ranking = $gBlog->list_blogs(0, $module_rows, 'last_modified_desc', '', $gQueryUserId );

$gBitSmarty->assign('modLastModifiedBlogs', $ranking["data"]);
?>
