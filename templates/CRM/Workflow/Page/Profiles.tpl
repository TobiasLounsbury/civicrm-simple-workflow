{if $workflow}

    <h3>Add a new Profile: </h3>
    <input class="crm-profile-selector crm-form-text" data-group-type="{$profilesDataGroupType}" data-entities='{$profilesDataEntities}' name="ProfileSelector" type="text" id="ProfileSelector">

    <p><button id="AddProfile">Add Profile to Workflow</button></p>

    <h3>Contribution Pages</h3>
    <select id="PageSelector">
        {foreach from=$pages item=p}
            <option value="{$p.id}">{$p.title}</option>
        {/foreach}
    </select>
    <p>If you leave the breadcrumb for a page blank it will not be shown in the list (Use with jQuery Selectors to show billing block as final step).</p>
    <button id="AddPage">Add Page to Workflow</button>
    <p></p>

    <h3>Page Subsections</h3>
    <p>
        This step type allows you to cut a page into subsections using a list of jquery selectors. It will only hide the elements in a jQuery subsection<br />
        ex: #billing-payment-block,.custom_post_profile-group,#crm-submit-buttons
    </p>
    <label style="width: 125px;display:inline-block;font-weight: bold;">jQuery Selector:</label>

    <input id="dom-id" size="75" /><br/>

    <label style="width: 125px;display:inline-block;font-weight: bold;">Breadcrumb:</label>
    <input id="dom-breadcrumb" /><br />

    <button id="AddJQuery">Add Subsection to Workflow</button>
    <p></p>

    <h3>Sort Pages</h3>
    <form id="Data">

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
            <span class='DeleteProfile' onclick='DeleteStep(this)'>delete</span>
            <br />
            <label class="leftmost">Breadcrumb:</label> <input name="data[{$d.order}][breadcrumb]" value="{$d.breadcrumb}" />
            <label>Button Text:</label> <input name="data[{$d.order}][next]" value="{$d.next}" />
            <label>Title:</label> <input name="data[{$d.order}][title]" value="{$d.title}" size='40' />
        </div>

    {/foreach}
    </div>
    </form>
    <p><a class="button" href="#" id="SaveDetails"><span>Save</span></a> <a class="button" href="{crmURL p='civicrm/workflows' q='reset=1'}"><span>Cancel</span></a></p><br />
{else}
    <h3>An error has occurred. Workflow not found.</h3>
{/if}