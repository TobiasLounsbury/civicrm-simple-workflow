<h3>Contribution Pages</h3>
<select id="PageSelector">
    {foreach from=$pages item=p}
        <option value="{$p.id}">{$p.title}</option>
    {/foreach}
</select>
<p>If you leave the breadcrumb for a page blank it will not be shown in the list (Use with jQuery Selectors to show billing block as final step).</p>
<button id="AddPage">Add Page to Workflow</button>