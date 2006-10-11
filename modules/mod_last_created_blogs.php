<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_created_blogs.php,v 1.4 2006/10/11 06:05:13 spiderr Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $module_rows;
$listHash['max_records'] = $module_rows;
$listHash['sort_mode'] = 'created_desc';
$listHash['user_id'] = $gQueryUserId;
$ranking = $gBlog->getList( $listHash );

$gBitSmarty->assign('modLastCreatedBlogs', $ranking["data"]);
?>
