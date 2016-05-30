<h3 class="SWToggleFormTrigger">{ts}Contribution Pages{/ts}</h3>
<div class="SWToggleForm">
    <div class="help">{ts}If you leave the breadcrumb for a page blank it will not be shown in the list (Use with jQuery Selectors to show billing block as final step){/ts}.</div>
    <select id="PageSelector">
        {foreach from=$pages item=p}
            <option value="{$p.id}">{$p.title}</option>
        {/foreach}
    </select><br /><br />
    <button id="AddPage" class="AddStepButton" data-step-type="page">{ts}Add Page to Workflow{/ts}</button>
</div>