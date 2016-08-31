<h3 class="SWToggleFormTrigger">{ts}Add a new Profile{/ts}:</h3>
<div class="SWToggleForm SW-Profile">
    <input class="crm-profile-selector crm-form-text" data-group-type="{$profilesDataGroupType}" data-entities='{$profilesDataEntities}' name="ProfileSelector" type="text" id="ProfileSelector">
    <p>
        <input type="radio" name="SW-Profile-Mode" value="current" id="SW-Profile-Mode-Current" checked="checked" /> <label for="SW-Profile-Mode-Current">{ts}Use to update current Contact{/ts}</label>
        <br />
        <input type="radio" name="SW-Profile-Mode" value="related-new" id="SW-Profile-Mode-Related-New" /> <label for="SW-Profile-Mode-Related-New">{ts}Create a New Contact (allow edit){/ts}</label>
        <br />
        <input type="radio" name="SW-Profile-Mode" value="create" id="SW-Profile-Mode-Create" /> <label for="SW-Profile-Mode-Create">{ts}Use to create new Contact on every visit{/ts}</label>
        <br />
        <input type="radio" name="SW-Profile-Mode" value="select-new" id="SW-Profile-Mode-Existing-New" /> <label for="SW-Profile-Mode-Existing-New">{ts}Select existing or create new Contact{/ts}</label>
        <br />
        <input type="radio" name="SW-Profile-Mode" value="select-edit" id="SW-Profile-Mode-Existing-Edit" /> <label for="SW-Profile-Mode-Existing-Edit">{ts}Select existing or create new Contact (allow edit){/ts}</label>
    </p>
    <p><button id="AddProfile" class="AddStepButton" data-step-type="profile">{ts}Add Profile to Workflow{/ts}</button></p>
</div>