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

        //Trigger a custom event in case we need to do some custom work/cleanup elsewhere
        CRM.Workflow.trigger("Step:Load:Complete:jquery", currentStep);
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
