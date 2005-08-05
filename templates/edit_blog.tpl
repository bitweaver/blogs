{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/edit_blog.tpl,v 1.1.1.1.2.2 2005/08/05 22:59:51 squareing Exp $ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="edit blogs">
	<div class="header">
		<h1>{tr}Create or Edit Blog{/tr}</h1>
	</div>

	<div class="body">
		{if strlen($heading) > 0}
		<div class="preview">
			<div class="heading">
				<h2>{$description}</h2>
			</div>
			<div class="introduction">
			  {eval var=$heading}
			</div>
		</div>
		{/if}

		{if $individual eq 'y'}
			{formfeedback warning="<a href='`$smarty.const.KERNEL_PKG_URL`object_permissions.php?objectName=blog%20`$title`&amp;object_type=blog&amp;permType=blogs&amp;object_id=`$blog_id`'>There are individual permissions set for this blog</a>" position="top"}
		{/if}

		{form ipackage="blogs" ifile="edit.php"}
			{jstabs}
				{jstab title="Edit/Create Blog"}
					{legend legend="Edit Blog"}
						<input type="hidden" name="blog_id" value="{$blog_id|escape}" />
						<div class="row">
							{formfeedback warning=$warning}
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{if $title}{$title|escape}{else}{displayname hash=$gBitUser->mInfo nolink=FALSE}'s Blog{/if}" />
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Description" for="description"}
							{forminput}
								<textarea name="description" id="description" rows="2" cols="40">{$description|escape}</textarea>
								{formhelp note=''}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Number of posts to show" for="max_posts"}
							{forminput}
								<input type="text" name="max_posts" id="max_posts" value="{$max_posts|escape|default:10}" />
								{formhelp note='Enter the number of blog posts you wish to display when viewing this blog.'}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Allow other user to post in this blog" for="public"}
							{forminput}
								<input type="checkbox" name="public" id="public" {if $public eq 'y'}checked="checked"{/if} />
								{formhelp note=''}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Use titles in blog posts" for="use_title"}
							{forminput}
								<input type="checkbox" name="use_title" id="use_title" {if !$use_title || $use_title eq 'y'}checked="checked"{/if} />
								{formhelp note='If this is not seelcted, the time and date of when the post was created will be displayed instead of the post title.'}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Allow search" for="use_find"}
							{forminput}
								<input type="checkbox" name="use_find" id="use_find" {if !$use_find || $use_find eq 'y'}checked="checked"{/if} />
								{formhelp note='Allow userers to search this blog for occurances of words.'}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Allow comments" for="allow_comments"}
							{forminput}
								<input type="checkbox" name="allow_comments" id="allow_comments" {if !$allow_comments || $allow_comments eq 'y'}checked="checked"{/if} />
								{formhelp note='Are other users allowed to add comments to posts made in this blog?'}
							{/forminput}
						</div>

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}preview{/tr}" />&nbsp;
							<input type="submit" name="save_blog" value="{tr}save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{if $gBitSystem->isPackageActive( 'categories' )}
					{jstab title="Categorize"}
						{legend legend="Categorize"}
							{include file="bitpackage:categories/categorize.tpl"}
						{/legend}
					{/jstab}
				{/if}
			{/jstabs}
		{/form}

	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
