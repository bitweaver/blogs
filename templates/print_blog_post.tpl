<html>
<head>
<title>
{if $post_info.use_title eq 'y'}{$post_info.title} {tr}posted by{/tr} {displayname hash=$post_info nolink=TRUE} on {$post_info.created|bit_short_datetime}{else}{$post_info.created|bit_short_datetime} {tr}posted by{/tr} {displayname hash=$post_info}{/if}
</title>
</head>
<style type="text/css">
<!--

{literal}
body { margin : 5ex; }
a { color : black; text-decoration : none; }
a:hover { background-color : #deceae;  }

.head { border-bottom: 1px solid black;  }
.title { font-size : larger; margin : 1ex 0; }
.postedby {font-size : smaller; font-style : italic; }

.body { margin : 2ex;}

.foot { border-top : 1px solid black;  }
.permalink {  }
.icon { border: 0px; }
{/literal}
-->
</style>

<body>
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="{$uri}"
    dc:identifer="{$uri}"
    dc:title="{if $post_info.use_title eq 'y'}{$post_info.title} {tr}posted by{/tr} {$post_info.user} on {$post_info.created|bit_short_datetime}{else}{$post_info.created|bit_short_datetime} {tr}posted by{/tr} {$post_info.user}{/if}"
    trackback:ping="{$uri2}" />
</rdf:RDF>
-->

<br />

<div class="head">
{if $post_info.use_title eq 'y'}
<div>{$post_info.title}</div>
<div class="postedby">{tr}posted by{/tr} {displayname hash=$post_info} on {$post_info.created|bit_short_datetime}</div>
{else}
<div class="postedby">{$post_info.created|bit_short_datetime} {tr}posted by{/tr} {displayname hash=$post_info}</div>
{/if}
</div>
<div class="postbody">
{$parsed_data}
<hr />
<table>
<tr><td>
<a href="{$gBitLoc.BLOGS_PKG_URL}view_post.php?blog_id={$post_info.blog_id}&post_id={$post_info.post_id}">{tr}Permalink{/tr}</a>
({tr}referenced by{/tr}: {$post_info.trackbacks_from_count} {tr}posts{/tr} {tr}references{/tr}: {$post_info.trackbacks_to_count} {tr}posts{/tr})
{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'feature_blogposts_comments' )}
{$post_info.num_comments} {tr}comments{/tr}
&nbsp;<a href="{$gBitLoc.BLOGS_PKG_URL}view_post.php?find={$find}&amp;blog_id={$post_info.blog_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;post_id={$post_info.post_id}">{tr}view comments{/tr}</a>
{/if}
</td><td>
<a title="{tr}print{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}print_blog_post.php?post_id={$post_info.post_id}">{biticon ipackage=liberty iname="print" iexplain="Print"}</a>
<a title="{tr}email this post{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}send_post.php?post_id={$post_info.post_id}">{biticon ipackage=liberty iname="mail_send" iexplain="Email"}</a>
</td></tr></table>
</div>

</body>
</html>
