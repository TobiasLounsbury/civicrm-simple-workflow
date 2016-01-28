{if $workflow}

    {foreach from=$uiTemplates item=templatePath}
        <div class="crm-simple-workflow-ui-template">
            {include file=$templatePath location="bottom"}
        </div>
    {/foreach}

    <h3>Sort Pages</h3>
    <form id="Data" class="crm-simple-workflow-steps-form">

    <div id="SortableDetails">
    {foreach from=$details item=d}
        {if $d.entity_table eq "Page"}
            </div>
            <hr/>
        {/if}

        <div id="{$d.entity_table}_{$d.entity_id}" class="{$d.entity_table} Detail">
            {if $d.entity_table ne "Page"}
                <span class='handle'>ↈ</span>
            {/if}
            <input type="hidden" name="data[{$d.order}][order]" value="{$d.order}" class="order"/>
            <input type="hidden" name="data[{$d.order}][entity_table]" value="{$d.entity_table}" />
            {if $d.entity_table eq "jQuery"}
                <label class="path">Elements:</label> <input class='jQuery-Selector' name="data[{$d.order}][entity_id]" value="{$d.entity_id}" />
            {else}
                <input type="hidden" name="data[{$d.order}][entity_id]" value="{$d.entity_id}" />
                <div class='entity_name'>{$d.name}</div>
            {/if}
            <span class='DeleteStep'>delete</span>
            <br />
            <label class="leftmost">Breadcrumb:</label> <input name="data[{$d.order}][breadcrumb]" value="{$d.breadcrumb}" />
            <label>Button Text:</label> <input name="data[{$d.order}][next]" value="{$d.next}" />
            <label>Title:</label> <input name="data[{$d.order}][title]" value="{$d.title}" size='40' />
        </div>

    {/foreach}
    </div>
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