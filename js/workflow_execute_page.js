CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "Page") {
      //Hide the workflow pane
      CRM.Workflow.hideActionWindow();
      //todo: Is this doing anything?
      $("jQueryNext").hide();
      //Show the contribution form we hid earlier
      $("#Main").fadeIn("fast");
    }
  });
});
