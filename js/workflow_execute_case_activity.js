CRM.$(function ($) {

  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "CaseActivity") {
      if (CRM.Workflow.method == "inject") {
        $(".crm-contribution-main-form-block").hide();
        $("#ActionWindow").show();
      }

      var lsurl = CRM.url("civicrm/workflows/case/activity", {wid: currentStep.workflow_id, stepName: currentStep.name});
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});
