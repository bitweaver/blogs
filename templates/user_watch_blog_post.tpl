{tr}New blog post: {$mail_title} by {$mail_user} at {$mail_date|bit_short_datetime}{/tr}

{tr}View the blog at:{/tr}
{$mail_machine_raw}/{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$mail_blogid}&post_id={$mail_postid}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/{$smarty.const.USERS_PKG_URL}user_watches.php?hash={$mail_hash}

