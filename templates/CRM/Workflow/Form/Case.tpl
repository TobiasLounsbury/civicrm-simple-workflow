{foreach from=$elementNames item=elementName}
    {if $elementName neq 'start_date_time'}
        <div class="crm-section">
            <div class="label">{$form.$elementName.label}</div>
            {if $elementName eq 'start_date'}
                <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=start_date}</div>
            {else}
                <div class="content">{$form.$elementName.html}</div>
            {/if}
            <div class="clear"></div>
        </div>
    {/if}
{/foreach}

{if $includeAttachments}
    <table>
        <tr class="crm-activity-form-block-attachment">
            <td colspan="2">
                {include file="CRM/Form/attachment.tpl"}
            </td>
        </tr>
    </table>
{/if}


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
