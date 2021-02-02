<?php
/**
 * @version $Header$
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_CLASS_PATH.'BitBlog.php' );

global $gBitThemes;

extract( $moduleParams );
//$params = $gBitThemes->getModuleParameters('bitpackage:blogs/mod_top_visited_blogs.tpl', $gQueryUserId);

$listHash['max_records'] = $params['module_rows'];
$listHash['sort_mode'] = 'hits_desc';

//produces White Screen Of Death:
//$listHash['is_hit'] = TRUE;

BitUser::userCollection( $_REQUEST, $listHash );

$blog = new BitBlog();
$ranking = $blog->getList( $listHash );

$_template->tpl_vars['modTopVisitedBlogs'] = new Smarty_variable( $ranking);
$_template->tpl_vars['bulletSrc'] = new Smarty_variable( isset($params["bullet"]) ? $params['bullet'] : NULL);
?>
