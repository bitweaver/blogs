<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.php,v 1.2 2005/06/28 07:45:40 spiderr Exp $
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

$smarty->assign('modLastModifiedBlogs', $ranking["data"]);
?>
