CRM.$(function ($) {

  $("body").on("SimpleWorkflow-loadStep", function(event, currentStep) {
    if (currentStep.entity_table == "Page") {
      //Hide the workflow pane
      $("#ActionWindow").hide();
      $("jQueryNext").hide()
      //Show the contribution form we hid earlier
      $(".crm-contribution-main-form-block").fadeIn("fast");
    }
  });
});