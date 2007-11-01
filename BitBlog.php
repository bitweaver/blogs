<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/BitBlog.php,v 1.59 2007/11/01 10:57:52 squareing Exp $
 * @version  $Revision: 1.59 $
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
class BitBlog extends LibertyContent {
	var $mBlogId;

	function BitBlog( $pBlogId=NULL, $pContentId=NULL ) {
		$this->mBlogId = @$this->verifyId( $pBlogId ) ? $pBlogId : NULL;
		parent::LibertyContent( $pContentId );
		$this->registerContentType( BITBLOG_CONTENT_TYPE_GUID, array(
			'content_description' => 'Blog',
			'handler_class' => 'BitBlog',
			'handler_package' => 'blogs',
			'handler_file' => 'BitBlog.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITBLOG_CONTENT_TYPE_GUID;

		// Permission setup
		$this->mViewContentPerm  = 'p_blogs_view';
		$this->mEditContentPerm  = 'p_blogs_create';
		$this->mAdminContentPerm = 'p_blogs_admin';
	}

	function get_num_user_blogs($user_id) {
		$ret = NULL;
		if ($user_id) {
			$sql = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."blogs` WHERE `user_id` = ?";
			$ret = $this->mDb->getOne($sql, array( $user_id ));
		}
		return $ret;
	}

	function getDisplayUrl( $pBlogId = NULL, $pParamHash = NULL ) {
		global $gBitSystem;
		$ret = NULL;

		if( empty( $pBlogId ) && !empty( $this ) ) {
			$pBlogId = $this->mBlogId;
		}

		if ( BitBase::verifyId( $pBlogId ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				$ret = BLOGS_PKG_URL.'view/'.$pBlogId;
			} elseif( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = BLOGS_PKG_URL.$pBlogId;
			} else {
				$ret = BLOGS_PKG_URL.'view.php?blog_id='.$pBlogId;
			}
		} else {
			$ret = LibertyContent::getDisplayUrl( NULL, $pParamHash );
		}
		return $ret;
	}


	/**
	* Check if there is an article loaded
	* @return bool TRUE on success, FALSE on failure
	* @access public
	**/
	function isValid() {
		return( $this->verifyId( $this->mBlogId ) && $this->verifyId( $this->mContentId ) );
	}

	function load() {
		$this->mInfo = $this->getBlog( $this->mBlogId, $this->mContentId );
		$this->mContentId = $this->getField( 'content_id' );
		$this->mBlogId = $this->getField('blog_id');
	}


	/*shared*/
	function getBlog( $pBlogId, $pContentId = NULL ) {
		global $gBitSystem;
		$ret = NULL;

		$lookupId = (!empty( $pBlogId ) ? $pBlogId : $pContentId);
		$lookupColumn = (!empty( $pBlogId ) ? 'blog_id' : 'content_id');

		$bindVars = array( (int)$lookupId );
		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		if ( BitBase::verifyId( $lookupId ) ) {
			$query = "
				SELECT b.*, lc.*, lch.`hits`, uu.`login`, uu.`login`, uu.`user_id`, uu.`real_name`, lf.`storage_path` as avatar $selectSql
				FROM `".BIT_DB_PREFIX."blogs` b
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
					$joinSql
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON (lc.`content_id` = lch.`content_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uu.`user_id` = a.`user_id` AND uu.`avatar_attachment_id`=a.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
				WHERE b.`$lookupColumn`= ? $whereSql";
			// this was the last line in the query - tiki_user_preferences is DEAD DEAD DEAD!!!
//						LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_user_preferences` tup ON ( uu.`user_id`=tup.`user_id` AND tup.`pref_name`='theme' )

			$result = $this->mDb->query($query,$bindVars);
			$ret = $result->fetchRow();
			$ret['postscant'] = $this->getPostsCount( $ret['content_id'] );
			if ($ret) {
				$ret['avatar'] = (!empty($res['avatar']) ? BIT_ROOT_URL.$res['avatar'] : NULL);
				if( empty( $ret['max_posts'] ) || !is_numeric( $ret['max_posts'] ) ) {
					$ret['max_posts'] = 10; // spiderr hack to hardcode fail safe
				}
				if( !empty( $ret['data'] ) ) {
					$ret['parsed'] = $this->parseData( $ret['data'], $ret['format_guid'] );
				}
			}
		}
		return $ret;
	}

	function verify( &$pParamHash ) {
		global $gBitUser;

		$pParamHash['blog_store']['max_posts'] = !empty( $pParamHash['max_posts'] ) && is_numeric( $pParamHash['max_posts'] ) ? $pParamHash['max_posts'] : NULL;
		$pParamHash['blog_store']['use_title'] = isset( $pParamHash['use_title'] ) ? 'y' : 'n';
		$pParamHash['blog_store']['allow_comments'] = isset( $pParamHash['allow_comments'] ) ? 'y' : 'n';
		$pParamHash['blog_store']['use_find'] = isset( $pParamHash['use_find'] ) ? 'y' : 'n';

		return( count( $this->mErrors ) == 0 );
	}

	function store( &$pParamHash ) {
		global $gBitSystem;
		if( $this->verify( $pParamHash ) && parent::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."blogs";
			$this->mDb->StartTrans();
			if( $this->isValid() ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['blog_store'], array( "blog_id" => $pParamHash['blog_id'] ) );
			} else {
				// DEPRECATED - this looks stupid -wjames5
				//$pParamHash['blog_store']['posts'] = 0;
				$pParamHash['blog_store']['content_id'] = $this->mContentId;
				if( isset( $pParamHash['blog_id'] )&& is_numeric( $pParamHash['blog_id'] ) ) {
					// if pParamHash['blog_id'] is set, someone is requesting a particular blog_id. Use with caution!
					$pParamHash['blog_store']['blog_id'] = $pParamHash['blog_id'];
				} else {
					$pParamHash['blog_store']['blog_id'] = $this->mDb->GenID( 'blogs_blog_id_seq' );
				}
				$this->mBlogId = $pParamHash['blog_store']['blog_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['blog_store'] );
			}
			$this->mDb->CompleteTrans();
		}
		return( count( $this->mErrors ) == 0 );
	}

	function expunge() {
		$ret = FALSE;
		if ( $this->isValid() ) {
			$this->mDb->StartTrans();

			// remove all references in blogs_posts_map where post_content_id = content_id
			$query_map = "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `blog_content_id` = ?";
			$result = $this->mDb->query( $query_map, array( $this->mContentId ) );

			$query = "DELETE from `".BIT_DB_PREFIX."blogs` where `content_id`=?";
			$result = $this->mDb->query( $query, array( (int)$this->mContentId ) );

			if( parent::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
			$this->mDb->CompleteTrans();
		}
		return $ret;
	}

	function get_random_blog_post($blog_id = NULL, $load_comments = FALSE) {
		$ret = NULL;
		$bindVars = array();

		if ( $this->verifyId( $blog_id ) ) {
			$sql = "SELECT `post_id` FROM `".BIT_DB_PREFIX."blog_posts` WHERE `blog_id` = ?";
			$rs = $this->mDb->query($sql, array($blog_id));
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

	// BLOG METHODS ////
	function getList( &$pParamHash ) {
		global $gBitSystem;

		LibertyContent::prepGetList( $pParamHash );

		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$bindVars = array();
//		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		// You can use a title or an array of blog_id
		if (!empty($pParamHash['find'])) {
			if (is_array($pParamHash['find'])) {
				$whereSql .= " AND b.`blog_id` IN ( ".implode( ',',array_fill( 0,count( $pParamHash['find'] ),'?' ) ).") ";
				$bindVars = array_merge($bindVars, $pParamHash['find']);
			}
			else {
				$findesc = '%' . strtoupper( $pParamHash['find'] ) . '%';
				$whereSql = " AND (UPPER(lc.`title`) like ? or UPPER(lc.`data`) like ?) ";
				$bindVars=array($findesc,$findesc);
			}
		}
		if( @$this->verifyId( $pParamHash['user_id'] ) ) {
			$whereSql .= " AND uu.`user_id` = ? ";
			$bindVars[] = $pParamHash['user_id'];
		}

		if( @$this->verifyId( $pParamHash['group_id'] ) ) {
			array_push( $bindVars, (int)$pParamHash['group_id'] );
			$joinSql .= " INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON (ugm.`user_id`=uu.`user_id`)";
			$whereSql .= ' AND ugm.`group_id` = ? ';
		}

		if( !empty( $pParamHash['is_active'] ) ) {
			$whereSql .= " AND b.`activity` IS NOT NULL";
		}

		if( !empty( $pParamHash['is_hit'] ) ) {
			$whereSql .= " AND lch.`hits` IS NOT NULL";
		}

		if( !empty( $pParamHash['content_perm_name'] ) ) {
			$this->getContentPermissionsSql( $pParamHash['content_perm_name'], $selectSql, $joinSql, $whereSql, $bindVars );
		}

		if( !empty( $whereSql ) ) {
			$whereSql = preg_replace( '/^[\s]*AND/', ' WHERE ', $whereSql );
		}

		$query = "
			SELECT b.`content_id` AS `hash_key`, b.*, uu.`login`, uu.`real_name`, lc.*, lch.hits $selectSql
			FROM `".BIT_DB_PREFIX."blogs` b
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
				$joinSql
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON (lc.`content_id` = lch.`content_id`)
			$whereSql order by ".$this->mDb->convertSortmode($pParamHash['sort_mode']);

		$ret = array();

		// Return a data array, even if empty
		$pParamHash["data"] = array();

		if( $ret = $this->mDb->getAssoc( $query, $bindVars, $pParamHash['max_records'], $pParamHash['offset'] ) ) {
			foreach( array_keys( $ret ) as $blogContentId ) {
				$ret[$blogContentId]['blog_url'] = $this->getDisplayUrl( $ret[$blogContentId]['blog_id'] );
				//get count of post in each blog
				$ret[$blogContentId]['postscant'] = $this->getPostsCount( $ret[$blogContentId]['content_id'] );
				// deal with the parsing
				$parseHash['format_guid']   = $ret[$blogContentId]['format_guid'];
				$parseHash['content_id']    = $ret[$blogContentId]['content_id'];
				$parseHash['data'] 			= $ret[$blogContentId]['data'];
				$ret[$blogContentId]['parsed'] = $this->parseData( $parseHash );
			}
		}

		$query_cant = "
			SELECT COUNT(b.`blog_id`)
				FROM `".BIT_DB_PREFIX."blogs` b
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
				$joinSql
			$whereSql";
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		LibertyContent::postGetList( $pParamHash );

		return $ret;
	}

	function getPostsCount($pBlogContentId){
		global $gBitSystem;
		$ret = NULL;
		if( @$this->verifyId( $pBlogContentId ) ) {
			$whereSql = 'bpm.`blog_content_id` = ?';
			$bindVars = array((int)$pBlogContentId);
			BitBlogPost::getDateRestrictions(array(), $whereSql, $bindVars);
			$query = "SELECT COUNT(*)
				FROM `".BIT_DB_PREFIX."blogs_posts_map` bpm
				INNER JOIN `".BIT_DB_PREFIX."blog_posts` bp ON (bpm.`post_content_id`=bp.`content_id`)
				WHERE $whereSql";

			$ret = $this->mDb->getOne( $query, $bindVars );
		} else {
			$this->mErrors['content_id'] = "Invalid blog content id.";
		}
		return $ret;
	}

	//This doesnt even appear to be used in blogs before this refactoring -wjames5
	function viewerCanPostIntoBlog() {
		global $gBitUser;
		return ($this->getField('user_id') == $gBitUser->mUserId || $gBitUser->isAdmin() || $this->getField('is_public') == 'y' );
	}

	function hasPostPermission() {
		$ret = FALSE;
		if( $this->isValid() ) {
			// for now just check edit permission, however eventually we'll want to separate this notion so blog editors and posters can be distinguished
			$ret = $this->hasEditPermission();
		}
		return $ret;
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
?>
