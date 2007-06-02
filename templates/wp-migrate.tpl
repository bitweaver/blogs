{strip}
<h1>WordPress {tr}Migration{/tr}</h1>
{formfeedback hash=$errors}
{if !empty($errorMap)}
	<p>{tr}There were the following errors with the migration:{/tr}</p>
	{foreach from=$errorMap item=errorSet}
		{formfeedback hash=$errorSet}
	{/foreach}
{/if}
{if empty($errors.success)}
{form}
	<p>{tr}In order to migrate from WordPress we need to know how your installation is configured.{/tr}</p>
	{legend legend="WordPress Settings"}
		{formlabel label="WordPress Install Direcotry" for="wp_config"}
		{forminput}
			<input type=text name="wp_config" id="wp_config" value="{$wp_config|default:"/path/to/wp/"}" width=60/>
			{formhelp note="The full path to your wordpress installation directory."}
		{/forminput}
	{/legend}
	<input type="submit" name="migrate" value="Migrate!" />
{/form}
{/if}
{/strip}
