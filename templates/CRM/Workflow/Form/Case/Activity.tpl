{foreach from=$elementNames item=elementName}
    <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
    </div>
{/foreach}

{if $customProfiles}
    <div class="crm-sf-profiles">
        {foreach from=$customProfiles key=ufID item=ufFields }
            {include file="CRM/UF/Form/Block.tpl" fields=$ufFields}
        {/foreach}
    </div>
{/if}


<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
