CRM.$(function ($) {

  $("body").on("SimpleWorkflow-loadStep", function(event, currentStep) {

    if (currentStep.entity_table == "jQuery") {

      //Hide the workflow pane
      $("#ActionWindow").hide();

      $(".crm-contribution-main-form-block").slideUp("fast", function(e) {

        //Hide all of the extra elements we don't want to see in this step.
        $(CRM.Workflow.allSelector).hide();

        //Show the Elements that make up this step
        $(currentStep.entity_id).show();

        //Hide or show the Next button depending on if we are on the last page or not
        if (parseInt(currentStep.order) == parseInt(CRM.Workflow.lastStep)) {
          $("#jQueryNext").hide()
        } else {
          $("#jQueryNext span").text(" " + currentStep.next + " ");
          $("#jQueryNext").show()
        }

        //Show the contribution form we hid earlier
        $(".crm-contribution-main-form-block").slideDown();

        var stepfname = window['CRM_Workflow_' + currentStep.breadcrumb.replace(/ /g, "_")];
        if (typeof stepfname == 'function') {
          stepfname();
        }
      });
    }
  });
});