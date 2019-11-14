CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "CaseActivity") {

      CRM.Workflow.useActionWindow();

      var lsurl = CRM.url("civicrm/workflows/case/activity", {wid: currentStep.workflow_id, stepName: currentStep.name});
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});
