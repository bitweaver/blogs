<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_blogs/BitBlogPost.php,v 1.34 2006/10/21 19:25:55 lsces Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitBlogPost.php,v 1.34 2006/10/21 19:25:55 lsces Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.34 $ $Date: 2006/10/21 19:25:55 $ $Author: lsces $
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyComment.php');
require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php');
require_once( BLOGS_PKG_PATH.'BitBlog.php');

define( 'BITBLOGPOST_CONTENT_TYPE_GUID', 'bitblogpost' );

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
	}

	/**
	 * Load a Blog Post section
	 */
	function load( $pLoadComments = TRUE ) {
		if( $this->verifyId( $this->mPostId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$lookupColumn = $this->verifyId( $this->mPostId )? 'post_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mPostId )? $this->mPostId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$query = "SELECT bp.*, lc.*, blc.`title` as `blogtitle`, b.`allow_comments`,b.`allow_comments`, b.`use_title`, buu.`user_id` AS `blog_user_id`, uu.`login`, uu.`real_name`, lf.`storage_path` as avatar $selectSql
				FROM `".BIT_DB_PREFIX."blog_posts` bp
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = bp.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( uu.`user_id` = lc.`user_id` ) 
					INNER JOIN `".BIT_DB_PREFIX."blogs` b ON (b.`blog_id` = bp.`blog_id`)
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` blc ON (blc.`content_id` = b.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` buu ON( uu.`user_id` = blc.`user_id` ) $joinSql
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uu.`user_id` = a.`user_id` AND uu.`avatar_attachment_id`=a.`attachment_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
				WHERE bp.`$lookupColumn`=? $whereSql ";
				// this was the last line in the query - tiki_user_preferences is DEAD DEAD DEAD!!!
//				LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_user_preferences` tup ON ( uu.`user_id`=tup.`user_id` AND tup.`pref_name`='theme' )

			if( $this->mInfo = $this->mDb->getRow( $query, $bindVars ) ) {
				$this->mPostId = $this->mInfo['post_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mInfo['blog_url'] = BitBlog::getBlogUrl( $this->mInfo['blog_id'] );
				$this->mInfo['title'] = $this->getTitle();

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
				if ( $gBitSystem->isPackageActive( 'categories' ) ) {
					global $categlib;
					$this->mInfo['categs'] = $categlib->get_object_categories( BITBLOGPOST_CONTENT_TYPE_GUID, $this->mContentId );
				}
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

		if( empty( $pHash['title'] ) ) {
			$date_format = $gBitSystem->get_long_date_format();
			if( $gBitSystem->mServerTimestamp->get_display_offset() ) {
				$date_format = preg_replace( "/ ?%Z/", "", $date_format );
			} else {
				$date_format = preg_replace( "/%Z/", "UTC", $date_format );
			}
			$date_string = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC( $pHash['created'] );
			$ret = $gBitSystem->mServerTimestamp->strftime( $date_format, $date_string, true );
		} else {
			$ret = $pHash['title'];
		}

		return $ret;
	}

	/**
	 * Verify that the store hash is valid and complete missing values
	 */
	function verify( &$pParamHash ) {
		global $gBitUser;
		if( @$this->verifyId( $pParamHash['post_id'] ) && empty( $this->mContentId ) && empty( $pParamHash['content_id'] ) ) {
			$this->mPostId = $pParamHash['post_id'];
			$this->load();
		}
		if (empty($pParamHash['user_id'])) {
			$pParamHash['user_id'] = $gBitUser->mUserId;
		}
		$pParamHash['content_type_guid'] = BITBLOGPOST_CONTENT_TYPE_GUID;
		return( count( $this->mErrors ) == 0 );
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
		global $gBlog, $gBitSystem;
		$pParamHash['upload']['thumbnail'] = TRUE;

		$this->mDb->StartTrans();
		if( $this->verify( $pParamHash ) && LibertyAttachable::store( $pParamHash ) ) {
 			if( @$this->verifyId( $pParamHash['attachment_id'] ) ) {
				// we just shoved an attachment onto the blog so we will concat the new link for usability.
				// THis is a bit hackish to do here. I imagine we will allow for this down in Liberty eventually,
				// but right now there is a chicken-n-egg situation storing 'data' before the attachments. - spiderr
				$pParamHash['edit'] .= '{ATTACHMENT(id=>'.$pParamHash['attachment_id'].')}{ATTACHMENT}';
				LibertyContent::store( $pParamHash );
			}
			if (empty($pParamHash['post_id'])) {
				global $gBitSmarty, $gBitSystem;
				$tracks = serialize(explode(',', $pParamHash['trackback']));
				$pParamHash['edit'] = strip_tags($pParamHash['edit'], '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre>');
				$now = $gBitSystem->getUTCTime();
				if (empty($pParamHash['post_date']))
					$pParamHash['post_date'] = (int)$now;
				$pParamHash['post_id'] = $this->mDb->GenID('blog_posts_post_id_seq');

				$query = "insert into `".BIT_DB_PREFIX."blog_posts`(`blog_id`, `post_id`, `content_id`, `trackbacks_from`,`trackbacks_to`) values(?,?,?,?,?)";
				$result = $this->mDb->query($query,array($pParamHash['blog_id'],$pParamHash['post_id'],$pParamHash['content_store']['content_id'],serialize(array()),serialize(array())));
				$this->mPostId = $pParamHash['post_id'];

				// Send trackbacks recovering only successful trackbacks
				$trackbacks = serialize( $this->sendTrackbacks( $pParamHash['trackback'] ) );
				// Update post with trackbacks successfully sent
				$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from`=?, `trackbacks_to` = ? where `post_id`=?";
				$this->mDb->query($query,array(serialize(array()),$trackbacks,(int) $pParamHash['post_id']));
				$query = "update `".BIT_DB_PREFIX."blogs` set `posts`=`posts`+1 where `blog_id`=?";
				$result = $this->mDb->query($query,array((int)$pParamHash['blog_id']));

				// let's reload to get a full mInfo hash which is needed below
				$this->load();

				if( $gBitSystem->isFeatureActive( 'users_watches' ) ) {
					global $gBitUser;
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
			}

			if( !empty( $pParamHash['trackbacks'] ) ) {
				$trackbacks = serialize($gBlog->send_trackbacks($post_id, $trackbacks));
				$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_to`=? where `post_id`=?";
				$result = $this->mDb->query($query,array($trackbacks,$user_id,$post_id));
			}
		}
		$this->mDb->CompleteTrans();
		return( count( $this->mErrors ) == 0 );
	}


	/**
	 * Remove complete blog post set and any comments
	 */
	function expunge() {
		$this->mDb->StartTrans();
		// let's force a full load to make sure everything is loaded.
		$this->load();
		if( $this->isValid() ) {
			// First kill any comments which belong to this post
			 foreach($this->mInfo['comments'] as $comment) {
				$tmpComment = new LibertyComment($comment['comment_id']);
				$tmpComment->deleteComment();
			}

			$query = "delete from `".BIT_DB_PREFIX."blog_posts` where `post_id`=?";
			$result = $this->mDb->query( $query, array( (int) $this->mPostId ) );

			$query = "update `".BIT_DB_PREFIX."blogs` set `posts`=`posts`-1 where `blog_id`=?";
			$result = $this->mDb->query( $query, array( (int)$this->mInfo['blog_id'] ) );
			// Do this last so foreign keys won't complain (not the we have them... yet ;-)
			LibertyAttachable::expunge();
		}
		$this->mDb->CompleteTrans();

		return true;
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

		$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		if( @$this->verifyId( $pListHash['blog_id'] ) ) {
			array_push( $bindVars, (int)$pListHash['blog_id'] );
			$whereSql .= ' AND bp.`blog_id` = ? ';
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
		if( $pListHash['find'] ) {
			$findesc = '%' . strtoupper( $pListHash['find'] ) . '%';
 			$whereSql .= "AND (UPPER(`data`) like ?) ";
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

		$sort_mode_prefix = 'lc';
		if ($pListHash['sort_mode'] == 'post_id_desc') {
			$sort_mode_prefix = 'bp';
			}
		if ($pListHash['sort_mode'] == 'post_id_asc') {
			$sort_mode_prefix = 'bp';
			}
			
		$sort_mode = $sort_mode_prefix . '.' . $this->mDb->convert_sortmode( $pListHash['sort_mode'] ); 
		
		$query = "SELECT bp.*, lc.*, blc.`title` AS `blogtitle`, blc.`data` AS `blogdescription`, b.`allow_comments`, uu.`email`, uu.`login`, uu.`real_name`, lf.`storage_path` as avatar $selectSql
				FROM `".BIT_DB_PREFIX."blog_posts` bp
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = bp.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( uu.`user_id` = lc.`user_id` ) 
					INNER JOIN `".BIT_DB_PREFIX."blogs` b ON (b.`blog_id` = bp.`blog_id`)
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` blc ON (blc.`content_id` = b.`content_id`)
					$joinSql
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uu.`user_id` = a.`user_id` AND a.`attachment_id` = uu.`avatar_attachment_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
				WHERE b.`blog_id` = bp.`blog_id` $whereSql order by $sort_mode";
		$query_cant = "SELECT COUNT(bp.`post_id`) FROM `".BIT_DB_PREFIX."blog_posts` bp, `".BIT_DB_PREFIX."liberty_content` lc $joinSql WHERE lc.`content_id` = bp.`content_id` $whereSql ";
		$result = $this->mDb->query($query,$bindVars,$pListHash['max_records'],$pListHash['offset']);
		$cant = $this->mDb->getOne($query_cant,$bindVars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['no_fatal'] = TRUE;
			$accessError = $this->invokeServices( 'content_verify_access', $res, FALSE );
			if( empty( $accessError ) ) {
				$res['avatar'] = (!empty($res['avatar']) ? BIT_ROOT_URL.dirname($res['avatar']).'/small.jpg' : NULL);
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

				$res['post_url'] = BitBlogPost::getDisplayUrl( $res['content_id'] );
				$res['display_url'] = $res['post_url'];
				$res['blog_url'] = BitBlog::getBlogUrl( $res['blog_id'] );

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

				if ( $gBitSystem->isPackageActive( 'categories' ) ) {
					global $categlib;
					require_once( CATEGORIES_PKG_PATH.'categ_lib.php' );
					$res['categs'] = $categlib->get_object_categories( BITBLOGPOST_CONTENT_TYPE_GUID, $res["content_id"] );
				}

				if( $pListHash['parse_data'] ) {
					$res["parsed_data"] = $this->parseData( $res );
				}

				$ret[] = $res;
			} elseif( !empty( $accessError ) ) {
				if( !empty( $accessError['access_control'] ) ) {
					$res['post_url'] = BitBlogPost::getDisplayUrl( $res['content_id'] );
					$res['display_url'] = $res['post_url'];
					$res['blog_url'] = BitBlog::getBlogUrl( $res['blog_id'] );
					$res["parsed_data"] = $accessError['access_control'];
					$ret[] = $res;
				}
			} else {
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
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
