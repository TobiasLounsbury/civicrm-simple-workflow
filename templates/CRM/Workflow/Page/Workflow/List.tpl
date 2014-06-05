


<!--
<div id="help">
    {ts}{/ts}
</div>
//-->


{if $rows}
    <div id="workflow-list">
        {strip}
            {* handle enable/disable actions *}
            {include file="CRM/common/enableDisableApi.tpl"}
            {include file="CRM/common/jsortable.tpl"}
            <table id="options" class="display">
                <thead>
                <tr>
                    <th id="sortable">{ts}Name{/ts}</th>
                    <th>{ts}Description{/ts}</th>
                    <th>{ts}Login Required{/ts}</th>
                    <th>{ts}Active?{/ts}</th>
                    <th></th>
                </tr>
                </thead>
                {foreach from=$rows item=row}
                    <tr id="Workflow_Item-{$row.id}" class="crm-entity crm-wokflow_{$row.id} {cycle values="even-row,odd-row"} {$row.class} {if NOT $row.is_active}disabled{/if}">
                        <td class="crm-workflow">{$row.name}</td>
                        <td class="right">{$row.description}</td>
                        <td>{if NOT $row.require_login}No{else}Yes{/if}</td>
                        <td>{if NOT $row.is_active}No{else}Yes{/if}</td>
                        <td>{$row.action|replace:'xx':$row.id}</td>
                    </tr>
                {/foreach}
            </table>
        {/strip}

    </div>

{else}
    <div class="messages status no-popup">
        <div class="icon inform-icon"></div>
        {ts}There are no workflows yet.{/ts}
    </div>
{/if}


<p></p>
<a href="{crmURL p='civicrm/workflows/add' q="reset=1"}" class="button"><span><div class="icon add-icon"></div>{ts}Add Workflow{/ts}</span></a>
