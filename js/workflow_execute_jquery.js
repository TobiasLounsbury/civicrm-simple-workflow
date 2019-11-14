CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "jquery") {

      //Hide the workflow pane
      CRM.Workflow.hideActionWindow();

      $("#Main").slideUp("fast", function(e) {

        //Hide all of the extra elements we don't want to see in this step.
        $(CRM.Workflow.allSelector).hide();

        //Show the Elements that make up this step
        $(currentStep.entity_id).show();

        //Set the Button Text and show if applicable
        CRM.Workflow.setButtonText();

        //Show the contribution form we hid earlier
        $("#Main").slideDown();

        //todo: This should be deprecated in favor of more
        //deterministic methods rather than maric naming
        var stepfname = window['CRM_Workflow_' + currentStep.breadcrumb.replace(/ /g, "_")];
        if (typeof stepfname == 'function') {
          stepfname();
        }
      });
    }
  });

  CRM.Workflow.handle("Step:Teardown", function(currentStep) {
      //Remove the select widget if it exists
      if (currentStep.entity_table == "jquery") {
        $(currentStep.entity_id).hide();
      }
    });
});
