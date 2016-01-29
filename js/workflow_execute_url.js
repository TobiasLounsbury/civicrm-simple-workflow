CRM.$(function ($) {

  $("body").on("SimpleWorkflow-loadStep", function(event, currentStep) {
    if (currentStep.entity_table == "url") {
      if (CRM.Workflow.method == "inject") {
        $(".crm-contribution-main-form-block").hide();
        $("#ActionWindow").show();
      }
      var lsurl = CRM.url(currentStep.entity_id);
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});