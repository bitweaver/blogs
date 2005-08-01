<?php
/**
 * @package blogs
 */
 
/**
 * required setup
 */
require_once( BLOGS_PKG_PATH.'BitBlogPost.php');
require_once( LIBERTY_PKG_PATH.'LibertyComment.php');

define( 'BITBLOG_CONTENT_TYPE_GUID', 'bitblog' );

/**
 * @package blogs
 */
class BitBlog extends BitBase {
	function BitBlog() {
		BitBase::BitBase();
	}

	// BLOG METHODS ////
	/*shared*/
	function list_blogs($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $user_id = NULL, $add_sql = NULL) {
		global $gBitSystem;

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$mid = " WHERE (UPPER(tb.`title`) like ? or UPPER(tb.`description`) like ?) ";
			$bindvars=array($findesc,$findesc);
			if ($user_id) {
				$mid .= " AND tb.`user_id` = ?";
				$bindvars[] = $user_id;
			}
		} elseif( $user_id ) { // or a string
			$mid = " WHERE tb.`user_id` = ? ";
			$bindvars=array( $user_id );
		} else {
			$mid = '';
			$bindvars=array();
		}

		if ($add_sql) {
			if (strlen($mid) > 1) {
				$mid .= ' AND '.$add_sql.' ';
			} else {
				$mid = "WHERE $add_sql ";
			}
		}

		$query = "SELECT tb.*, uu.`login` as `user`, uu.`real_name`
			  FROM `".BIT_DB_PREFIX."tiki_blogs` tb INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = tb.`user_id`)
			  $mid order by tb.".$this->convert_sortmode($sort_mode);

		$result = $this->query($query,$bindvars,$maxRecords,$offset);

		$ret = array();

		while ($res = $result->fetchRow()) {
			if ( $gBitSystem->isPackageActive( 'categories' ) ) {
				global $categlib;
				$res['categs'] = $categlib->get_object_categories( BITBLOG_CONTENT_TYPE_GUID, $res['blog_id'] );
			}
			$res['blog_url'] = $this->getBlogUrl( $res['blog_id'] );
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$query_cant = "SELECT COUNT(tb.`blog_id`) FROM `".BIT_DB_PREFIX."tiki_blogs` tb $mid";

		$cant = $this->getOne($query_cant, $bindvars);
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_user_blogs($user_id, $maxRecords = NULL) {
		$ret = NULL;

		if ($user_id) {

			$sql = "SELECT tb.*, uu.`user_id`, uu.`login` as `user`, uu.`email`, uu.`real_name`
					FROM `".BIT_DB_PREFIX."tiki_blogs` tb INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = tb.`user_id`)
					WHERE tb.`user_id` = ? $mid";

			if ($maxRecords && is_numeric($maxRecords) && $maxRecords >= 0) {
				$blogsRes = $this->query($sql, array($user_id), $maxRecord);
			} else {
				$blogsRes = $this->query($sql, array($user_id));
			}
			$ret = $blogsRes->getRows();
		}
		return $ret;
	}

	function get_num_user_blogs($user_id) {
		$ret = NULL;
		if ($user_id) {
			$sql = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."tiki_blogs` WHERE `user_id` = ?";
			$ret = $this->getOne($sql, array( $user_id ));
		}
		return $ret;
	}

	function getContentType() {
		return 'bitblog';
	}

	function getBlogUrl( $pBlogId ) {
		global $gBitSystem;
		if( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
			$ret = BLOGS_PKG_URL.$pBlogId;
		} else {
			$ret = BLOGS_PKG_URL.'view.php?blog_id='.$pBlogId;
		}
		return $ret;
	}


	/*shared*/
	function get_blog($blog_id) {
		global $gBitSystem;
		$ret = NULL;
		if ($blog_id && is_numeric($blog_id)) {

			$query = "SELECT tb.*, uu.`login` as `user`, uu.`user_id`, uu.`real_name`, tup.`value` AS `blog_style`, tf.`storage_path` as avatar
				  	  FROM `".BIT_DB_PREFIX."tiki_blogs` tb INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = tb.`user_id`)
			  			LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_attachments` ta ON (uu.`user_id` = ta.`user_id` AND uu.`avatar_attachment_id`=ta.`attachment_id`)
						LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_files` tf ON (tf.`file_id` = ta.`foreign_id`)
						LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_user_preferences` tup ON ( uu.`user_id`=tup.`user_id` AND tup.`pref_name`='theme' )
			  		  WHERE tb.`blog_id`= ?";

			$result = $this->query($query,array((int)$blog_id));
			$ret = $result->fetchRow();
			if ($ret) {
				$ret['avatar'] = (!empty($res['avatar']) ? BIT_ROOT_URL.$res['avatar'] : NULL);
				if ( $gBitSystem->isPackageActive( 'categories' ) ) {
					global $categlib;
					$ret['categs'] = $categlib->get_object_categories( BITBLOG_CONTENT_TYPE_GUID, $blog_id );
				}

				if( empty( $ret['max_posts'] ) || !is_numeric( $ret['max_posts'] ) ) {
					$ret['max_posts'] = 10; // spiderr hack to hardcode fail safe
				}
			}
		}
		return $ret;
	}

	/*shared*/
	function list_user_blogs($user_id = NULL, $include_public = false) {
		$ret = NULL;
		if ($user_id && is_numeric($user_id)) {
			$bindvars=array();
			$bindvars[] = (int)$user_id;
			$mid = '';
			if ($include_public) {
				$mid .= "OR `public`=?";
				$bindvars[]='y';
			}
			$query = "SELECT tb.*, uu.`login` as `user`, uu.`real_name`
				  FROM `".BIT_DB_PREFIX."tiki_blogs` tb INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = tb.`user_id`)
			  	  WHERE tb.`user_id` = ? $mid ";
			$result = $this->query($query,$bindvars);
			$ret = array();

			while ($res = $result->fetchRow()) {
				$ret[] = $res;
			}
		}
		return $ret;
	}


	function add_blog_hit($blog_id) {
		global $count_admin_pvs;
		global $gBitUser;

		if ($count_admin_pvs == 'y' || !$gBitUser->isAdmin()) {
			$bindvars = array( $blog_id );
			$ownerSql = '';
			if( $gBitUser->isValid() ) {
				// this is a super cheap way to keep us from 'hitting' our own blog
				$ownerSql = ' AND `user_id` != ?';
				array_push( $bindvars, $gBitUser->mUserId );
			}
			$query = "update `".BIT_DB_PREFIX."tiki_blogs` set `hits` = `hits`+1 where `blog_id`=? $ownerSql";
			$result = $this->query($query,$bindvars);
		}

		return true;
	}

	function replace_blog($title, $description, $user_id, $public, $max_posts, $blog_id, $heading, $use_title, $use_find,
		$allow_comments, $creation_date = NULL) {
		$now = date("U");
		if ($creation_date == NULL)
			$creation_date = (int)$now;

		if ( !empty( $blog_id ) ) {
			$query = "update `".BIT_DB_PREFIX."tiki_blogs` set `title`=? ,`description`=?,`user_id`=?,`public`=?,`last_modified`=?,`max_posts`=?,`heading`=?,`use_title`=?,`use_find`=?,`allow_comments`=? where `blog_id`=?";

			$result = $this->query($query,array($title,$description,$user_id,$public,$now,$max_posts,$heading,$use_title,$use_find,$allow_comments,$blog_id));
		} else {
			$query = "insert into `".BIT_DB_PREFIX."tiki_blogs`(`created`,`last_modified`,`title`,`description`,`user_id`,`public`,`posts`,`max_posts`,`hits`,`heading`,`use_title`,`use_find`,`allow_comments`)
                       values(?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array($creation_date,(int) $now,$title,$description,$user_id,$public,0,(int) $max_posts,0,$heading,$use_title,$use_find,$allow_comments));
			$query2 = "select max(`blog_id`) from `".BIT_DB_PREFIX."tiki_blogs` where `last_modified`=?";
			$blog_id = $this->getOne($query2,array((int) $now));
		}

		return $blog_id;
	}

	function expunge($blog_id) {
		$this->mDb->StartTrans();
		$query = "delete from `".BIT_DB_PREFIX."tiki_blogs` where `blog_id`=?";

		$result = $this->query($query,array((int) $blog_id));
		$query = "delete from `".BIT_DB_PREFIX."tiki_blog_posts` where `blog_id`=?";
		$result = $this->query($query,array((int) $blog_id));
		$this->remove_object( BITBLOG_CONTENT_TYPE_GUID, $blog_id );
		$this->mDb->CompleteTrans();
		return true;
	}

	// ported from gBitSystem - one day BitBlog will inherit from LibertyContent, until then, dupe this func...
	function remove_object($type, $id) {
		global $categlib;
		if( is_object( $categlib ) ) {
			$categlib->uncategorize_object($type, $id);
		}
		// Now remove comments
		$object = $type . $id;
//		$query = "delete from `".BIT_DB_PREFIX."tiki_comments` where `object`=?  and `object_type`=?";
//		$result = $this->query($query, array( $id, $type ));
		// Remove individual permissions for this object if they exist
		$query = "delete from `".BIT_DB_PREFIX."users_objectpermissions` where `object_id`=? and `object_type`=?";
		$result = $this->query($query,array((int)$object,$type));
		return true;
	}

	function get_random_blog_post($blog_id = NULL, $load_comments = FALSE) {
		$ret = NULL;
		$bindvars = array();

		if ($blog_id && is_numeric($blog_id)) {
			$sql = "SELECT `post_id` FROM `".BIT_DB_PREFIX."tiki_blog_posts` WHERE `blog_id` = ?";
			$rs = $this->query($sql, array($blog_id));
			$rows = $rs->getRows();
			$numPosts = count($rows);
			if ($numPosts > 0) {
				$post_id = $rows[rand(0, $numPosts-1)]['post_id'];	// Get a random post_id array index
				$blogPost = new BitBlogPost( $post_id );
				$blogPost->load($load_comments);
				$ret = $blogPost;
			}
		}
		return $ret;
	}


	function add_blog_activity($blog_id) {

		//Caclulate activity, update tiki_blogs and purge activity table
		$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

		$day0 = $today - (24 * 60 * 60);
		$day1 = $today - (2 * 24 * 60 * 60);
		$day2 = $today - (3 * 24 * 60 * 60);
		// Purge old activity
		$query = "delete from `".BIT_DB_PREFIX."tiki_blog_activity` where `day`<?";
		$result = $this->query($query,array((int) $day2));
		// Register new activity
		$query = "select count(*) from `".BIT_DB_PREFIX."tiki_blog_activity` where `blog_id`=? and `day`=?";
		$result = $this->getOne($query,array((int) $blog_id,(int)$today));

		if ($result) {
			$query = "update `".BIT_DB_PREFIX."tiki_blog_activity` set `posts`=`posts`+1 where `blog_id`=? and `day`=?";
		} else {
			$query = "insert into `".BIT_DB_PREFIX."tiki_blog_activity`(`blog_id`,`day`,`posts`) values(?,?,1)";
		}

		$result = $this->query($query,array((int) $blog_id,(int) $today));
		// Calculate activity
		$query = "select `posts` from `".BIT_DB_PREFIX."tiki_blog_activity` where `blog_id`=? and `day`=?";
		$vtoday = $this->getOne($query,array((int) $blog_id,(int) $today));
		$day0 = $this->getOne($query,array((int) $blog_id,(int) $day0));
		$day1 = $this->getOne($query,array((int) $blog_id,(int) $day1));
		$day2 = $this->getOne($query,array((int) $blog_id,(int) $day2));
		$activity = (2 * $vtoday) + ($day0)+(0.5 * $day1) + (0.25 * $day2);
		// Update tiki_blogs with activity information
		$query = "update `".BIT_DB_PREFIX."tiki_blogs` set `activity`=? where `blog_id`=?";
		$result = $this->query($query,array($activity,(int) $blog_id));
	}

	function get_blog_owner($blog_id) {
		$user_id = NULL;
		if ($blog_id && is_numeric($blog_id)) {
			$sql = "SELECT `user_id` FROM `".BIT_DB_PREFIX."tiki_blogs` WHERE `blog_id` = ?";
			$user_id = $this->getOne($sql, array($blog_id));
		}
		return $user_id;
	}

	function get_post_owner($post_id) {
		$user_id = NULL;
		if ($post_id && is_numeric($post_id)){
			$sql = "SELECT tc.`user_id` FROM `".BIT_DB_PREFIX."tiki_blog_posts` tbp, `".BIT_DB_PREFIX."tiki_content` tc
					WHERE tbp.`post_id`= ? AND tbp.`content_id` = tc.`content_id`";
			$user_id = $this->getOne($sql, array($post_id));
		}
		return $user_id;
	}

	function viewerCanPostIntoBlog() {
		global $gBitUser;
		return ($this->mInfo['user_id'] == $gBitUser->mUserId || $gBitUser->isAdmin() || $this->mInfo['public'] == 'y' );
	}

	function viewerHasPermission($pPermName = NULL) {
		global $gBitUser;
		$ret = FALSE;
		if ($gBitUser->mUserId && $pPermName) {
			$ret = $gBitUser->object_has_permission( $gBitUser->mUserId, $this->mInfo['blog_id'], $this->getContentType(), $pPermName );
		}
		return $ret;
	}
}

global $gBlog;
$gBlog = new BitBlog();

?>
