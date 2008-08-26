<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.php,v 1.9 2008/08/26 19:09:00 laetzer Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gQueryUserId, $gBitThemes;

extract( $moduleParams );
//$params = $gBitThemes->getModuleParameters('bitpackage:blogs/mod_top_visited_blogs.tpl', $gQueryUserId);

$listHash['max_records'] = $params['module_rows'];
$listHash['sort_mode'] = 'hits_desc';
$listHash['user_id'] = $gQueryUserId;

//produces White Screen Of Death:
//$listHash['is_hit'] = TRUE;

if( @BitBase::verifyId( $module_params['group_id'] ) ) {
	$listHash['group_id'] = $module_params['group_id'];
}
$blog = new BitBlog();
$ranking = $blog->getList( $listHash );

$gBitSmarty->assign('modTopVisitedBlogs', $ranking["data"]);
$gBitSmarty->assign('bulletSrc', isset($params["bullet"]) ? $params['bullet'] : NULL);
?>
