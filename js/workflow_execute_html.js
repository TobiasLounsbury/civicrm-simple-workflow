CRM.$(function ($) {

  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "html") {

      if (CRM.Workflow.method == "inject") {
        $(".crm-contribution-main-form-block").hide();
        $("#ActionWindow").show();
      }

      $("#ActionWindow").html(currentStep.entity_id);

      //Set the Button Text and show it if applicable
      CRM.Workflow.SetButtonText();
    }
  });
  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {

  });
});
