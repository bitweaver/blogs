{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/edit_blog.tpl,v 1.21 2007/03/26 16:32:09 wjames5 Exp $ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="edit blogs">
	<div class="header">
		<h1>{if $gContent->isValid()}{tr}Edit Blog{/tr}{else}{tr}Create Blog{/tr}{/if}</h1>
	</div>

	<div class="body">
		{if $smarty.request.preview}
			<div class="preview">
				<div class="header">
					<h1>{$title}</h1>
					{if $parsed}<h2>{$parsed}</h2>{/if}
					<div class="date">
						{tr}By{/tr}: {displayname user=$user_name}, {if $created}{$created|bit_short_datetime}{else}{$gContent->getField('created')|bit_short_datetime}{/if}<br />
					</div>
				</div>
			</div>
		{/if}

		{form ipackage="blogs" ifile="edit.php"}
			{jstabs}
				{jstab title="Blog Settings"}
					{legend legend="Blog Settings"}
						<input type="hidden" name="blog_id" value="{$gContent->getField('blog_id')}" />
						<div class="row">
							{formfeedback warning=$warning}
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{if $gContent->getTitle()}{$gContent->getTitle()|escape}{else}{displayname hash=$gBitUser->mInfo nolink=FALSE}'s Blog{/if}" />
							{/forminput}
						</div>

						{if $gBitSystem->isPackageActive( 'smileys' )}
							{include file="bitpackage:smileys/smileys_full.tpl"}
						{/if}

						{if $gBitSystem->isPackageActive( 'quicktags' )}
							{include file="bitpackage:quicktags/quicktags_full.tpl"}
						{/if}

						{include file="bitpackage:liberty/edit_format.tpl"}

						<div class="row">
							{forminput}
								<textarea {spellchecker} id="{$textarea_id}" name="edit" rows="{$smarty.cookies.rows|default:5}" cols="50">{if $edit}{$edit|escape}{else}{$gContent->getField('data')|escape}{/if}</textarea>
								{formhelp note=''}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Number of posts to show" for="max_posts"}
							{forminput}
								<input type="text" name="max_posts" id="max_posts" value="{$gContent->getField('max_posts')|escape|default:10}" />
								{formhelp note='Enter the number of blog posts you wish to display when viewing this blog.'}
							{/forminput}
						</div>

{* DEPRECATED - Slated for removal  -wjames5
						{if $gBitUser->hasPermission('p_blogs_create_is_public')}
						<div class="row">
							{formlabel label="Public" for="is_public"}
							{forminput}
								<input type="checkbox" name="is_public" id="is_public" {if $gContent->getField('is_public') eq 'y'}checked="checked"{/if} />
								{formhelp note='Allow other user to post in this blog'}
							{/forminput}
						</div>
						{/if}
*}

						<div class="row">
							{formlabel label="Use titles in blog posts" for="use_title"}
							{forminput}
								<input type="checkbox" name="use_title" id="use_title" {if !$gContent->isValid() || $gContent->getField('use_title') eq 'y'}checked="checked"{/if} />
								{formhelp note='If this is not seelcted, the time and date of when the post was created will be displayed instead of the post title.'}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Allow search" for="use_find"}
							{forminput}
								<input type="checkbox" name="use_find" id="use_find" {if !$gContent->isValid() || $gContent->getField('use_find') eq 'y'}checked="checked"{/if} />
								{formhelp note='Allow users to search this blog for occurances of words.'}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Allow comments" for="allow_comments"}
							{forminput}
								<input type="checkbox" name="allow_comments" id="allow_comments" {if !$gContent->isValid() || $gContent->getField('allow_comments') eq 'y'}checked="checked"{/if} />
								{formhelp note='Are other users allowed to add comments to posts made in this blog?'}
							{/forminput}
						</div>

						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}preview{/tr}" />&nbsp;
							<input type="submit" name="save_blog" value="{tr}save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}

			{/jstabs}
		{/form}

	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
