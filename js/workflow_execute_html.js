CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "html") {

      CRM.Workflow.useActionWindow();

      CRM.Workflow.$actionWindow.html(currentStep.entity_id);

      //Set the Button Text and show it if applicable
      CRM.Workflow.setButtonText();
    }
  });
});
