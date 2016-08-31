<h3 class="SWToggleFormTrigger"> + {ts}Page Subsections{/ts}</h3>
<div class="SWToggleForm">
    <div class="help">
        {ts}This step type allows you to cut a page into subsections using a list of jquery selectors. It will only hide the elements in a jQuery subsection{/ts}<br />
        {ts}ex: #billing-payment-block,.custom_post_profile-group,#crm-submit-buttons{/ts}
    </div>
    <label>{ts}jQuery Selector{/ts}:</label>

    <input id="dom-id" size="75" /><br/>

    <label>{ts}Breadcrumb{/ts}:</label>
    <input id="dom-breadcrumb" /><br />

    <button id="AddJQuery" class="AddStepButton" data-step-type="jquery">{ts}Add Subsection to Workflow{/ts}</button>
</div>