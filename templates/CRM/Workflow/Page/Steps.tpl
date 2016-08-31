{if $workflow}

    <div id="AddSteps">
    {foreach from=$uiTemplates item=templatePath}
        <div class="crm-simple-workflow-ui-template">
            {include file=$templatePath location="bottom"}
        </div>
    {/foreach}
    </div>

    <h3 id="sortPagesHeader">Sort Pages</h3>
    <form id="Data" class="crm-simple-workflow-steps-form">
        <div id="SortableDetails"></div>
    </form>

    <div id="WorkflowStepTemplates">
        {foreach from=$typeTemplates item=templatePath}
            {include file=$templatePath location="bottom"}
        {/foreach}
    </div>

    <p><a class="button" href="#" id="SaveDetails"><span>Save</span></a> <a class="button" href="{crmURL p='civicrm/workflows' q='reset=1'}"><span>Cancel</span></a></p><br />
{else}
    <h3>An error has occurred. Workflow not found.</h3>
{/if}