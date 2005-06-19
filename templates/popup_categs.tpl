{section name=i loop=$categs}<div><a href="{$gBitLoc.BLOGS_PKG_URL}{$page}?blog_id=:::blogid:::&amp;post_id=:::postid:::&amp;addcateg={$categs[i].category_id}">{biticon ipackage=categories
iname=$categs[i].name|cat:'.png'}</a></div>{/section}<div><a href="{$gBitLoc.BLOGS_PKG_URL}{$page}?blog_id=:::blogid:::&amp;post_id=:::postid:::&amp;delcategs=1">{tr}Clear{/tr}</a></div>
