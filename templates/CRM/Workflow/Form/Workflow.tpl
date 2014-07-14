{* HEADER *}
<div id="help">
    {ts}Use this form to edit the name and settings for this Workflow{/ts}
</div>
<div class="crm-block crm-form-block">
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    </div>

    {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

    {*
    id
    name
    description
    require_login
    is_active
    *}


    {foreach from=$elementNames item=elementName}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
    {/foreach}

    {* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

      <div>
        <span>{$form.favorite_color.label}</span>
        <span>{$form.favorite_color.html}</span>
      </div>

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    <a class="button" href="{crmURL p='civicrm/workflows' q='reset=1'}"><span>Cancel</span></a>
    </div>
</div>