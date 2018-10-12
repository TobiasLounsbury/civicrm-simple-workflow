CRM.$(function ($) {

  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "Page") {
      //Hide the workflow pane
      $("#ActionWindow").hide();
      $("jQueryNext").hide()
      //Show the contribution form we hid earlier
      $("#Main").fadeIn("fast");
    }
  });
});