<script>
	$(function () {ldelim}
		$('#aamSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
		{rdelim});
</script>

<form
		class="pkp_form"
		id="aamSettings"
		method="POST"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
	<!-- Always add the csrf token to secure your form -->
	{csrf}

	{fbvFormArea}
	{fbvFormSection title="plugins.generic.aam.settings.ignored"}
	{fbvElement type="textArea" id="ignoredArticle" value=$ignoredArticle label="plugins.generic.aam.settings.ignored.desc"}
	{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
