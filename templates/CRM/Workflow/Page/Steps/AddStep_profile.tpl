<h3 class="SWToggleFormTrigger">{ts}Add a new Profile{/ts}:</h3>
<div class="SWToggleForm SW-Profile">
    <input class="crm-profile-selector crm-form-text" data-group-type="{$profilesDataGroupType}" data-entities='{$profilesDataEntities}' name="ProfileSelector" type="text" id="ProfileSelector">
    <p>
        <input type="radio" name="SW-Profile-Mode" value="update" id="SW-Profile-Current" checked="checked" /> <label for="SW-Profile-Current">{ts}Use to update current Contact{/ts}</label>
        <input type="radio" name="SW-Profile-Mode" value="create" id="SW-Profile-Anonymous" /> <label for="SW-Profile-Anonymous">{ts}Use to create new Contact{/ts}</label>
    </p>
    <!--//TODO: Add relationships that will be auto-created if this is a new contact//-->
    <p><button id="AddProfile" class="AddStepButton" data-step-type="profile">{ts}Add Profile to Workflow{/ts}</button></p>
</div>