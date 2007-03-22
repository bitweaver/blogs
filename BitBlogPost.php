<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_blogs/BitBlogPost.php,v 1.49 2007/03/22 01:58:36 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitBlogPost.php,v 1.49 2007/03/22 01:58:36 spiderr Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.49 $ $Date: 2007/03/22 01:58:36 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyComment.php');
require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php');
require_once( BLOGS_PKG_PATH.'BitBlog.php');

define( 'BITBLOGPOST_CONTENT_TYPE_GUID', 'bitblogpost' );

define( 'BITBLOGPOST_SPLIT_REGEX', "/\.{3}split\.{3}[\r\n]?/i" );

/**
 * @package blogs
 */
class BitBlogPost extends LibertyAttachable {
	var $mPostId;
	function BitBlogPost( $pPostId=NULL, $pContentId=NULL ) {
		LibertyAttachable::LibertyAttachable();
		$this->registerContentType( BITBLOGPOST_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITBLOGPOST_CONTENT_TYPE_GUID,
			'content_description' => 'Blog Post',
			'handler_class' => 'BitBlogPost',
			'handler_package' => 'blogs',
			'handler_file' => 'BitBlogPost.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mPostId = (int)$pPostId;
		$this->mContentId = (int)$pContentId;
		$this->mContentTypeGuid = BITBLOGPOST_CONTENT_TYPE_GUID;
	}

	/**
	 * Load a Blog Post section
	 */
	function load( $pLoadComments = TRUE ) {
		if( $this->verifyId( $this->mPostId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem, $gBitUser;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
			$lookupColumn = $this->verifyId( $this->mPostId )? 'post_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mPostId )? $this->mPostId : $this->mContentId;
			array_push( $bindVars, $lookupId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

/* DEPRICATED - Slated for removal
			$query = "SELECT bp.*, lc.*, blc.`title` as `blogtitle`, b.`allow_comments`,b.`allow_comments`, b.`use_title`, buu.`user_id` AS `blog_user_id`, uu.`login`, uu.`real_name`, lf.`storage_path` as avatar $selectSql
				FROM `".BIT_DB_PREFIX."blog_posts` bp
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = bp.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( uu.`user_id` = lc.`user_id` ) 
					INNER JOIN `".BIT_DB_PREFIX."blogs` b ON (b.`blog_id` = bp.`blog_id`)
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` blc ON (blc.`content_id` = b.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` buu ON( buu.`user_id` = blc.`user_id` ) $joinSql
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uu.`user_id` = a.`user_id` AND uu.`avatar_attachment_id`=a.`attachment_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
				WHERE bp.`$lookupColumn`=? $whereSql ";
				// this was the last line in the query - tiki_user_preferences is DEAD DEAD DEAD!!!
//				LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_user_preferences` tup ON ( uu.`user_id`=tup.`user_id` AND tup.`pref_name`='theme' )
*/

			$query = "
				SELECT bp.*, lc.*, uu.`login`, uu.`real_name`, lf.`storage_path` as avatar 
					$selectSql
				FROM `".BIT_DB_PREFIX."blog_posts` bp
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = bp.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( uu.`user_id` = lc.`user_id` ) 
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uu.`user_id` = a.`user_id` AND uu.`avatar_attachment_id`=a.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
					$joinSql
				WHERE bp.`$lookupColumn`=? $whereSql ";


			if( $this->mInfo = $this->mDb->getRow( $query, $bindVars ) ) {
				$this->mPostId = $this->mInfo['post_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mInfo['blogs'] = $this->getBlogMemberships( $this->mContentId );
				
				/* this needs to be part of loop that gets all blogs post is in
				$this->mInfo['blog_url'] = BitBlog::getDisplayUrl( $this->mInfo['blog_id'] );
				*/
				
				$this->mInfo['title'] = $this->getTitle();
				$this->mInfo['raw'] = $this->mInfo['data'];
				$this->mInfo['data'] = preg_replace( BITBLOGPOST_SPLIT_REGEX, "", $this->mInfo['data'] );
				$this->mInfo['use_title'] = $gBitUser->getPreference( 'user_blog_posts_use_title', 'y', $this->mInfo['user_id'] ) ;
					
				if( $pLoadComments ) {
					$comment = new LibertyComment();
					$this->mInfo['num_comments'] = $comment->getNumComments($this->mInfo['content_id']);
					// Get the comments associated with this post
					$this->mInfo['comments'] = $comment->getComments($this->mInfo['content_id'], $gBitSystem->getConfig( 'comments_per_page', 10 ) );
				}

				if (!$this->mInfo['trackbacks_from'] || $this->mInfo['trackbacks_from']===null)
					$this->mInfo['trackbacks_from'] = serialize(array());

				if (!$this->mInfo['trackbacks_to'] || $this->mInfo['trackbacks_to']===null)
					$this->mInfo['trackbacks_to'] = serialize(array());

				$this->mInfo['trackbacks_from_count'] = count(array_keys(unserialize($this->mInfo['trackbacks_from'])));
				$this->mInfo['trackbacks_from'] = unserialize($this->mInfo['trackbacks_from']);
				$this->mInfo['trackbacks_to'] = unserialize($this->mInfo['trackbacks_to']);
				$this->mInfo['trackbacks_to_count'] = count($this->mInfo['trackbacks_to']);

				LibertyAttachable::load();
				if( $this->mStorage ) {
					foreach( array_keys( $this->mStorage ) as $key ) {
						$this->mStorage[$key]['wiki_plugin_link'] = '{attachment id='.$key.'}';
					}
				}
			}
		}
		return( count( $this->mInfo ) );
	}

	function getTitle( $pHash = NULL ) {
		global $gBitSystem;
		$ret = NULL;
		if( empty( $pHash ) && !empty( $this->mInfo ) ) {
			$pHash = &$this->mInfo;
		}

		if( !empty( $pHash['title'] ) ) {
			$ret = $pHash['title'];
		} elseif( !is_null( $pHash ) ) {
			$date_format = $gBitSystem->get_long_date_format();
			if( $gBitSystem->mServerTimestamp->get_display_offset() ) {
				$date_format = preg_replace( "/ ?%Z/", "", $date_format );
			} else {
				$date_format = preg_replace( "/%Z/", "UTC", $date_format );
			}
			$date_string = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC( $pHash['created'] );
			$ret = $gBitSystem->mServerTimestamp->strftime( $date_format, $date_string, true );
		}

		return $ret;
	}

	function getBlogMemberships( $pPostContentId ){
		global $gBitSystem;
		$ret = NULL;
		if( @$this->verifyId( $pPostContentId ) ) {
			$bindVars = array( (int)$pPostContentId );
			$query = "SELECT b.`content_id` AS hash_key, bpm.*, b.*, lc.* 
				FROM `".BIT_DB_PREFIX."blogs_posts_map` bpm 
				INNER JOIN		`".BIT_DB_PREFIX."blogs`				 b ON b.`content_id` = bpm.`blog_content_id`
				INNER JOIN		`".BIT_DB_PREFIX."liberty_content`		lc ON lc.`content_id` = b.`content_id` 
				WHERE bpm.post_content_id = ?";

			if( $ret = $this->mDb->getAssoc( $query, $bindVars ) ) {
				foreach( array_keys( $ret ) as $blogContentId ) {
					$ret[$blogContentId]['blog_url'] = BitBlog::getDisplayUrl( $ret[$blogContentId]['blog_id'] );
				}
			}
		} else {
			$this->mErrors['post_id'] = "Invalid post id.";
		}
		return $ret;
	}

	
	/**
	* Make sure the data is safe to store
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	* This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	* NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	*
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	*
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	*
	* @access private
	**/
	function verify( &$pParamHash ) {
		global $gBitUser, $gBitSystem;

		// make sure we're all loaded up of we have a mPostId
		if( $this->verifyId( $this->mPostId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( !empty( $pParamHash['data'] ) ) {
			$pParamHash['edit'] = $pParamHash['data'];
		}

		// check for name issues, first truncate length if too long
		if( !empty( $pParamHash['title'] ) ) {
			if( empty( $this->mPostId ) ) {
				if( empty( $pParamHash['title'] ) ) {
					$this->mErrors['title'] = 'You must enter a title for this blog post.';
				} else {
					$pParamHash['content_store']['title'] = substr( $pParamHash['title'], 0, 160 );
				}
			} else {
				$pParamHash['content_store']['title'] =( isset( $pParamHash['title'] ) )? substr( $pParamHash['title'], 0, 160 ): '';
			}
		} else if( empty( $pParamHash['title'] ) ) {
			// no name specified
			$this->mErrors['title'] = 'You must enter a title for this blog post';
		}

		if( !empty( $pParamHash['publish_Month'] ) ) {
			$dateString = $pParamHash['publish_Year'].'-'.$pParamHash['publish_Month'].'-'.$pParamHash['publish_Day'].' '.$pParamHash['publish_Hour'].':'.$pParamHash['publish_Minute'];
			//$timestamp = strtotime( $dateString );
			$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			if( $timestamp !== -1 ) {
				$pParamHash['publish_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['publish_date'] ) ) {
			$pParamHash['post_store']['publish_date'] = $pParamHash['publish_date'];
		}

		if( !empty( $pParamHash['expire_Month'] ) ) {
			$dateString = $pParamHash['expire_Year'].'-'.$pParamHash['expire_Month'].'-'.$pParamHash['expire_Day'].' '.$pParamHash['expire_Hour'].':'.$pParamHash['expire_Minute'];
			//$timestamp = strtotime( $dateString );
			$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			if( $timestamp !== -1 ) {
				$pParamHash['expire_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['expire_date'] ) ) {
			$pParamHash['post_store']['expire_date'] = $pParamHash['expire_date'];
		}

		return( count( $this->mErrors )== 0 );
	}
	
	/**
	 * Check that the class has a valid blog loaded
	 */
	function isValid() {
		return( $this->verifyId( $this->mPostId ) && is_numeric( $this->mPostId ) && $this->mPostId > 0 );
	}

	/**
	 * Check if the current user is the blog owner
	 */
 	function isBlogOwner( $pUserId=NULL ) {
		$ret = FALSE;
		global $gBitUser;
		if( empty( $pUserId ) && $gBitUser->isValid() ) {
			$pUserId = $gBitUser->mUserId;
		}
		if( $this->isValid() && ($pUserId == $this->mInfo["blog_user_id"]) ) {
			$ret = 'y';
		}
		return $ret;
	}
	
	/**
	 * Store a Blog Post
	 */
	function store( &$pParamHash ) {
		global $gBitSystem;
		if( $this->verify( $pParamHash )&& LibertyAttachable::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."blog_posts";
			$this->mDb->StartTrans();

			// Send trackbacks recovering only successful trackbacks
			$trackbacks = serialize( $this->sendTrackbacks( $pParamHash['trackback'] ) );
			
			if( $this->isValid() ) {
				$locId = array( "content_id" => $this->mContentId );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['post_store'], $locId );
			} else {
				$pParamHash['post_store']['content_id'] = $pParamHash['content_id'];
				if( @$this->verifyId( $pParamHash['post_id'] ) ) {
					// if pParamHash['post_id'] is set, someone is requesting a particular post_id. Use with caution!
					$pParamHash['post_store']['post_id'] = $pParamHash['post_id'];
				} else {
					$pParamHash['post_store']['post_id'] = $this->mDb->GenID( 'blog_posts_post_id_seq' );
				}
				$this->mPostId = $pParamHash['post_store']['post_id'];
				//store the new post
				$result = $this->mDb->associateInsert( $table, $pParamHash['post_store'] );
			}
			
			// let's reload to get a full mInfo hash which is needed below
			$this->load();
			
			// if blog_content_id, then map the post to the relative blogs
			if ( !empty($pParamHash['blog_content_id']) ){
				$this->storePostMap( $this->mContentId, $pParamHash['blog_content_id'] );
			}

			// Update post with trackbacks successfully sent
			// Can this be moved below into similar function below? -wjames5
			// this throws an error on site population because post_id is not defined in pParamHash - wjames5
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from`=?, `trackbacks_to` = ? where `post_id`=?";
			$this->mDb->query($query,array(serialize(array()), $trackbacks, (int) $pParamHash['post_id']));

			if( $gBitSystem->isFeatureActive( 'users_watches' ) ) {
				global $gBitUser, $gBitSmarty;
				if( $nots = $gBitUser->getEventWatches( 'blog_post', $this->mInfo['blog_id'] ) ) {
					foreach ($nots as $not) {
						$gBitSmarty->assign('mail_site', $_SERVER["SERVER_NAME"]);	
						$gBitSmarty->assign('mail_title', $this->mInfo['title']);
						$gBitSmarty->assign('mail_blogid', $this->mInfo['blog_id']);
						$gBitSmarty->assign('mail_postid', $this->mPostId);
						$gBitSmarty->assign('mail_date', $gBitSystem->getUTCTime());
						$gBitSmarty->assign('mail_user', $this->mInfo['login']);
						$gBitSmarty->assign('mail_data', $this->mInfo['data']);
						$gBitSmarty->assign('mail_hash', $not['hash']);
						$foo = parse_url($_SERVER["REQUEST_URI"]);
						$machine = httpPrefix(). $foo["path"];
						$gBitSmarty->assign('mail_machine', $machine);
						$parts = explode('/', $foo['path']);

						if (count($parts) > 1)
							unset ($parts[count($parts) - 1]);

						$gBitSmarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
						$mail_data = $gBitSmarty->fetch('bitpackage:blogs/user_watch_blog_post.tpl');
						@mail($not['email'], tra('Blog post'). ' ' . $title, $mail_data, "From: ".$gBitSystem->getPrefence( 'site_sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n");
					}
				}
			}
			
			//is this nearly identical to the above and can they be consolodated? -wjames5
			if( !empty( $pParamHash['trackbacks'] ) ) {
				$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_to`=? where `post_id`=?";
				$result = $this->mDb->query($query,array($trackbacks, $user_id, $post_id));
			}

			$this->mDb->CompleteTrans();
			$this->load();
		}
		return ( count( $this->mErrors ) == 0 );
	}


	/* make sure data is safe to store in
	 * blogs_posts_map 
	 */
	function verifyPostMap( &$pParamHash ) {
		global $gBitUser, $gBitSystem;

		$pParamHash['map_store'] = array();
		
		if( @$this->verifyId( $pParamHash['post_content_id'] ) ) {
			$postContentId = $pParamHash['post_content_id'];

			//this is to set the time we add a post to a blog.
			$timeStamp = $gBitSystem->getUTCTime();
			
			$blogMixed = $pParamHash['blog_content_id_mixed'];		
			if( !empty( $blogMixed )){
				if (!is_array( $blogMixed ) && !is_numeric( $blogMixed ) ){
					$blogIds = explode( ",", $blogMixed );
				}else if ( is_array( $blogMixed ) ) {
					$blogIds = $blogMixed;
				}else if ( is_numeric( $blogMixed ) ) {
					$blogIds = array( $blogMixed );
				}
			}
		
			foreach( $blogIds as $value ) {
				if( @$this->verifyId( $value ) ) {
					array_push( $pParamHash['map_store'], array( 
						'post_content_id' => $postContentId, 
						'blog_content_id' => (int)$value, 
						'date_added' => $timeStamp,
					));			
				} else {
					$this->mErrors[$value] = "Invalid blog id.";
				}
			}			
		} else {
			$this->mErrors['post_content_id'] = "Invalid post id.";
		}

		return ( count( $this->mErrors ) == 0 );
	}
	
	/**
	 * Map a Post to a Blog or multiple Blogs
	 * @param pPostContentId the content_id of the post we are associating with blogs.
	 * @param pBlogMixed the content_id or and array of ids of the blogs we want the post to show up in.
	 */
	function storePostMap( $pPostContentId, $pBlogMixed ) {
		global $gBitSystem;
		$pParamHash['post_content_id'] = $pPostContentId;
		$pParamHash['blog_content_id_mixed'] = $pBlogMixed;
		if( $this->verifyPostMap( $pParamHash ) ) {
			$this->mDb->StartTrans();
			//should we check for validity on the post and should we check for validity on the blog ids? -wjames5
			if( $this->isValid() ) {
				// wipe out all existing mappings for this post, and re-insert
				$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `post_content_id`=?", array( $this->mContentId ) );
				foreach ( $pParamHash['map_store'] as $value) {
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."blogs_posts_map", $value );					
					/* DEPRECATED - this looks stupid -wjames5
					//prolly should check that the insert above worked before upping the post count
					$query = "UPDATE `".BIT_DB_PREFIX."blogs` set `posts`=`posts`+1 where `content_id`=?";
					$result = $this->mDb->query($query, $value['blog_content_id'] );
					*/
				}
			}
			$this->mDb->CompleteTrans();
		}
		return ( count( $this->mErrors ) == 0 );
	}
	

	/**
	 * Remove complete blog post set and any comments
	 */
	function expunge() {
		$ret = FALSE;
		// let's force a full load to make sure everything is loaded.
		// lets not -wjames5
		//$this->load();
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			// First kill any comments which belong to this post
			 foreach($this->mInfo['comments'] as $comment) {
				$tmpComment = new LibertyComment($comment['comment_id']);
				$tmpComment->deleteComment();
			}

			$query = "DELETE FROM `".BIT_DB_PREFIX."blog_posts` WHERE `content_id` = ?";			
			$result = $this->mDb->query( $query, array( $this->mContentId ) );

			// remove all references in blogs_posts_map where post_content_id = content_id
			$query_map = "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `post_content_id` = ?";			
			$result = $this->mDb->query( $query_map, array( $this->mContentId ) );

			// Do this last so foreign keys won't complain (not the we have them... yet ;-)
			if( LibertyAttachable::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	/**
	 * Generate a valid url for the Blog
	 *
	 * @param	object	PostId of the item to use
	 * @return	object	Url String
	 */
	function getDisplayUrl( $pContentId=NULL ) {
		$ret = NULL;
		if( empty( $pContentId ) ) {
			$pContentId = $this->mContentId;
		}
		global $gBitSystem;
		if( @BitBase::verifyId( $pContentId ) ) {
			$rewrite_tag = $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ? 'view/' : '';
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				$ret = BLOGS_PKG_URL.$rewrite_tag.'content/'.$pContentId;
			} else {
				$ret = BLOGS_PKG_URL.'view_post.php?content_id='.$pContentId;
			}
		}
		return $ret;
	}

	/**
	 * Generate a valid display link for the Blog
	 *
	 * @param	object	PostId of the item to use
	 * @param	array	Not used
	 * @return	object	Fully formatted html link for use by Liberty
	 */
	function getDisplayLink( $pTitle=NULL, $pMixed=NULL ) {
		global $gBitSystem;
		if( empty( $pTitle ) && !empty( $this ) ) {
			$pTitle = $this->getField( 'title', $this->getContentTypeDescription() );
		}

		if( empty( $pMixed ) && !empty( $this ) ) {
			$pMixed = $this->mInfo;
		}

		$ret = $pTitle;
		if( $gBitSystem->isPackageActive( 'blogs' ) ) {
			$ret = '<a title="'.htmlspecialchars( BitBlogPost::getTitle( $pMixed ) ).'" href="'.BitBlogPost::getDisplayUrl( $pMixed['content_id'] ).'">'.htmlspecialchars( BitBlogPost::getTitle( $pMixed  ) ).'</a>';
		}

		return $ret;
	}

    /**
    * Returns include file that will
    * @return the fully specified path to file to be included
    */
	function getRenderFile() {
		return( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );
	}

	function sendTrackbacks( $pTrackbacks ) {
		$ret = array();
		if( $this->isValid() && !empty( $pTrackbacks ) ) {
		// Split to get each URI
		$tracks = explode(',', $pTrackbacks);

		//Build uri for post
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$uri = httpPrefix(). str_replace('post',
			'view_post', $parts['path']). '?post_id=' . $this->mPostId . '&amp;blog_id=' . $this->mInfo['blog_id'];
		include_once ( UTIL_PKG_PATH.'Snoopy.class.inc' );
		$snoopy = new Snoopy;

		foreach ($tracks as $track) {
			@$fp = fopen($track, 'r');

			if ($fp) {
				$data = '';

				while (!feof($fp)) {
					$data .= fread($fp, 32767);
				}

				fclose ($fp);
				preg_match("/trackback:ping=(\"|\'|\s*)(.+)(\"|\'\s)/", $data, $reqs);

				if (!isset($reqs[2]))
				return $ret;

				@$fp = fopen($reqs[2], 'r');

					if ($fp) {
						fclose ($fp);

						$submit_url = $reqs[2];
						$submit_vars["url"] = $uri;
						$submit_vars["blog_name"] = $this->mInfo['blogtitle'];
						$submit_vars["title"] = $this->mInfo['title'] ? $this->mInfo['title'] : date("d/m/Y [h:i]", $this->mInfo['created']);
						$submit_vars["title"] .= ' ' . tra('by'). ' ' . BitUser::getDisplayName( FALSE, $this->mInfo );
						$submit_vars["excerpt"] = substr($post_info['data'], 0, 200);
						$snoopy->submit($submit_url, $submit_vars);
						$back = $snoopy->results;

						if (!strstr('<error>1</error>', $back)) {
							$ret[] = $track;
						}
					}
				}
			}
		}
		return $ret;
	}

	function getList( &$pListHash ) {
		global $gBitUser, $gBitSystem;

		if( empty( $pListHash['sort_mode'] ) ) {
			$pListHash['sort_mode'] = 'created_desc';
		}

		$this->prepGetList( $pListHash );

		$selectSql = ''; $joinSql = ''; $whereSql = '';	
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
			
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		if( @$this->verifyId( $pListHash['blog_id'] ) ) {
			array_push( $bindVars, (int)$pListHash['blog_id'] );
			$joinSql .= " LEFT OUTER JOIN `".BIT_DB_PREFIX."blogs_posts_map` bpm ON ( bpm.`post_content_id` = bp.`content_id` ) ";
			$joinSql .= " LEFT OUTER JOIN `".BIT_DB_PREFIX."blogs` b ON ( bpm.`blog_content_id`=b.`content_id` ) ";
								//	" ON ( b.`content_id` = bpm.`blog_content_id` AND bp.`content_id` = bpm.`post_content_id` )";			
			$whereSql .= ' AND b.`blog_id` = ? ';
		}

		if( @$this->verifyId( $pListHash['post_id_gt'] ) ) {
			array_push( $bindVars, (int)$pListHash['post_id_gt'] );
			$whereSql .= ' AND bp.`post_id` > ? ';
		}

		if( @$this->verifyId( $pListHash['post_id_lt'] ) ) {
			array_push( $bindVars, (int)$pListHash['post_id_lt'] );
			$whereSql .= ' AND bp.`post_id` < ? ';
		}

		if( @$this->verifyId( $pListHash['user_id'] ) ) {
			array_push( $bindVars, (int)$pListHash['user_id'] );
			$whereSql .= ' AND lc.`user_id` = ? ';
		}
				
		if( !empty( $pListHash['login'] ) ) {
			array_push( $bindVars, $pListHash['login'] );
			$whereSql .= ' AND uu.`login` = ? ';
		}
		
		if( $pListHash['find'] ) {
			$findesc = '%' . strtoupper( $pListHash['find'] ) . '%';
 			$whereSql .= "AND (UPPER(lc.`data`) like ?) ";
			$bindVars[] =$findesc;
		}

		if( !empty( $pListHash['date'] ) && is_numeric( $pListHash['date'] ) ) {
			$whereSql .= " AND  lc.`created`<=? ";
			$bindVars[]= $pListHash['date'];
		}

		if( !empty( $pListHash['date_start'] ) && is_numeric( $pListHash['date_start'] ) ) {
			$whereSql .= " AND  tc.`created`>=? ";
			$bindVars[]= $pListHash['date_start'];
		}
		if( !empty( $pListHash['date_end'] ) && is_numeric( $pListHash['date_end'] ) ) {
			$whereSql .= " AND  tc.`created`<=? ";
			$bindVars[]= $pListHash['date_end'];
		}

		// determine if future and expired posts are viewable
		$timestamp = $gBitSystem->getUTCTime();
		if( !$gBitUser->hasPermission( 'p_blog_posts_read_future' ) ) {
			$whereSql .= " AND ( bp.`publish_date` <= ? OR bp.`publish_date` IS NULL)";
			$bindVars[] = ( int )$timestamp;
		}
		if( !$gBitUser->hasPermission( 'p_blog_posts_read_expired' ) ) {
			$whereSql .= " AND ( bp.`expire_date` >= ? OR bp.`expire_date` <= bp.`publish_date` OR bp.`expire_date` IS NULL )";
			$bindVars[] = ( int )$timestamp;
		}

		$sort_mode_prefix = 'lc';
		if ($pListHash['sort_mode'] == 'post_id_desc') {
			$sort_mode_prefix = 'bp';
			}
		if ($pListHash['sort_mode'] == 'post_id_asc') {
			$sort_mode_prefix = 'bp';
			}
			
		$sort_mode = $sort_mode_prefix . '.' . $this->mDb->convertSortmode( $pListHash['sort_mode'] ); 
	
		$query = "
			SELECT
				bp.*, lc.*,
				uu.`email`, uu.`login`, uu.`real_name`, lf.`storage_path` as avatar
				$selectSql
			FROM `".BIT_DB_PREFIX."blog_posts` bp
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON lc.`content_id`         = bp.`content_id`
				INNER JOIN		`".BIT_DB_PREFIX."users_users`			 uu ON uu.`user_id`			   = lc.`user_id` 
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON lc.`content_id`         = lch.`content_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`	  a ON (uu.`user_id` = a.`user_id` AND a.`attachment_id` = uu.`avatar_attachment_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`		 lf ON lf.`file_id`			   = a.`foreign_id`
				$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY $sort_mode";
	
		$query_cant = "
			SELECT COUNT( * ) 
			FROM `".BIT_DB_PREFIX."blog_posts` bp
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON lc.`content_id` = bp.`content_id`
				INNER JOIN		`".BIT_DB_PREFIX."users_users`			 uu ON uu.`user_id`			   = lc.`user_id` 
				$joinSql 
			WHERE lc.`content_type_guid` = ? $whereSql ";
		
		$result = $this->mDb->query($query,$bindVars,$pListHash['max_records'],$pListHash['offset']);
		$cant = $this->mDb->getOne($query_cant,$bindVars);
		$ret = array();

		$comment = &new LibertyComment();
		while ($res = $result->fetchRow()) {
			$res['no_fatal'] = TRUE;
			$accessError = $this->invokeServices( 'content_verify_access', $res, FALSE );
			if( empty( $accessError ) ) {
				
				//"DEPRECATED - Slated for removal
				/*
				if ( $gBitSystem->isPackageActive( 'categories' ) ) {
					global $categlib;
					require_once( CATEGORIES_PKG_PATH.'categ_lib.php' );
					$res['categs'] = $categlib->get_object_categories( BITBLOGPOST_CONTENT_TYPE_GUID, $res["content_id"] );
				}
				*/
				
				// DEPRECATED - more complex comments retrieval - Slated for removal
				/*
				if ( $pListHash['load_num_comments'] || $pListHash['load_comments'] ) {
					$comment = new LibertyComment();
					$res['num_comments'] = $comment->getNumComments($res['content_id']);
					if( $pListHash['load_comments'] ) {
						// Get the comments associated with this post
						$res['comments'] = $comment->getComments($res['content_id'], $gBitSystem->getConfig( 'comments_per_page', 10 ) );
					}
				} else {
					$res['comments'] = NULL;
				}
				*/
				
				$res['avatar'] = (!empty($res['avatar']) ? BIT_ROOT_URL.dirname($res['avatar']).'/small.jpg' : NULL);
				$res['num_comments'] = $comment->getNumComments( $res['content_id'] );
				$res['post_url'] = BitBlogPost::getDisplayUrl( $res['content_id'] );
				$res['display_url'] = $res['post_url'];
				$res['blogs'] = $this->getBlogMemberships( $res['content_id'] );

				// trackbacks
				if($res['trackbacks_from']!=null)
					$res['trackbacks_from'] = unserialize($res['trackbacks_from']);

				if (!is_array($res['trackbacks_from']))
					$res['trackbacks_from'] = array();

				$res['trackbacks_from_count'] = count(array_keys($res['trackbacks_from']));
				if($res['trackbacks_to']!=null)
					$res['trackbacks_to'] = unserialize($res['trackbacks_to']);
				if ($res['user_id'] == $gBitUser->mUserId) {
					$res['ownsblog'] = 'y';
				} else {
					$res['ownsblog'] = 'n';
				}
				$res['trackbacks_to_count'] = count($res['trackbacks_to']);
				
				$res['pages'] = $this->getNumberOfPages( $res['data'] );
				
				// deal with the parsing
				$parseHash['format_guid']     = $res['format_guid'];
				$parseHash['content_id']      = $res['content_id'];
				$parseHash['cache_extension'] = 'desc';
				// support for ...split... and auto split
				if( !empty( $pListHash['full_data'] ) ) {
					$parseHash['data'] = $res['data'];
					$res['parsed'] = $this->parseData( $parseHash );
				} else {
					if( preg_match( BITBLOGPOST_SPLIT_REGEX, $res['data'] ) ) {
						$res['man_split'] = TRUE;
						$parts = preg_split( BITBLOGPOST_SPLIT_REGEX, $res['data'] );
						if( empty( $parts[1] ) ) {
							$res['has_more'] = FALSE;
						}
						$parseHash['data'] = $parts[0];
					} else {
						$parseHash['data'] = substr( $res['data'], 0, $gBitSystem->getConfig( 'blog_posts_description_length', 500 ) );
					}

					// description shouldn't contain {maketoc}
					$parseash['data'] = preg_replace( "/\{maketoc[^\}]*\}/i", "", $parseHash['data'] );
					$res['parsed'] = $this->parseData( $parseHash );
					$res['parsed_description'] = $res['parsed'];
		
					// this is needed to remove trailing stuff from the parser and insert ...			
					$trailing_junk_pattern = "/(<br[^>]*>)*$/i";
					$res['parsed_description'] = preg_replace( $trailing_junk_pattern, "", $res['parsed_description'] );
					if( preg_replace( "/\{maketoc[^\}]*\}/i", "", $res['data'] ) != $parseHash['data'] && empty( $res['man_split'] )) {
						// we append ... when the split was generated automagically
						$res['parsed_description'] .= '&hellip;';
						$res['has_more'] = TRUE;
					} elseif( preg_replace( "/\{maketoc[^\}]*\}/i", "", $res['data'] ) != $parseHash['data'] ) {
						$res['has_more'] = TRUE;
					}
				}
				
				$ret[] = $res;
				
			} elseif( !empty( $accessError ) ) {
				if( !empty( $accessError['access_control'] ) ) {
					$res['post_url'] = BitBlogPost::getDisplayUrl( $res['content_id'] );
					$res['display_url'] = $res['post_url'];
					/* this needs to be part of loop that gets all blogs post is in
					$res['blog_url'] = BitBlog::getDisplayUrl( $res['blog_content_id'] );
					*/
					$res["parsed_data"] = $accessError['access_control'];
					$ret[] = $res;
				}
			} else {
			}
		}

		$pListHash["data"] = $ret;
		$pListHash["cant"] = $cant;

		LibertyContent::postGetList( $pListHash );

		return $pListHash;
	}

	/**
	 *
	 */
	function addTrackbackFrom( $url, $title = '', $excerpt = '', $blog_name = '') {
		if( $this->isValid() ) {
			$tbs = $this->getTrackbacksFrom( $this->mPostId );
			$aux = array(
				'title' => $title,
				'excerpt' => $excerpt,
				'blog_name' => $blog_name
			);

			$tbs[$url] = $aux;
			$st = serialize($tbs);
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from`=? where `post_id`=?";
			$this->mDb->query( $query, array( $st, $this->mPostId ) );
			return true;
		}
	}

	/**
	 *
	 */
	function getTrackbacksFrom() {
		if( $this->isValid() ) {
			$st = $this->mDb->getOne("select `trackbacks_from` from `".BIT_DB_PREFIX."blog_posts` where `post_id`=?",array( $this->mPostId ) );
			return unserialize($st);
		}
	}

	/**
	 *
	 */
	function getTrackbacksTo() {
		if( $this->isValid() ) {
			$st = $this->mDb->getOne("select `trackbacks_to` from `".BIT_DB_PREFIX."blog_posts` where `post_id`=?", array( $this->mPostId ) );
			return unserialize($st);
		}
	}

	/**
	 *
	 */
	function clearTrackbacksFrom() {
		if( $this->isValid() ) {
			$empty = serialize(array());
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from` = ? where `post_id`=?";
			$this->mDb->query( $query, array( $empty, $this->mPostId ) );
		}
	}

	/**
	 *
	 */
	function clearTrackbacksTo() {
 		if( $this->isValid() ) {
 			$empty = serialize(array());
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_to` = ? where `post_id`=?";
			$this->mDb->query( $query, array( $empty, $this->mPostId ) );
		}
	}

}
