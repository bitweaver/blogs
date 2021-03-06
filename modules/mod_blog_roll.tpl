{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' ) && $modBlogs}
	{bitmodule title="$moduleTitle" name="blog_roll"}
		<ul class="blogs">
			{foreach from=$modBlogs item=blog key=blogId}
				<li><h4><a href="{$blog.blog_url}">{$blog.title|escape|default:"Blog `$blogId`"}</a></h4>
					<div class="post">
					{if $blog.post}
						{$blog.post->getDisplayLink()}
						<div class="date">{$blog.post->getField('last_modified')|bit_short_datetime}</date>
					{else}
						<em>{tr}No Posts{/tr}</em>
					{/if}
					</div>
				</li>
			{/foreach}
		</ul>
	{/bitmodule}
{/if}
{/strip}
