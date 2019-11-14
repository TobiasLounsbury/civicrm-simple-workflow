CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "Case") {

      CRM.Workflow.useActionWindow();

      var lsurl = CRM.url("civicrm/workflows/case", {wid: currentStep.workflow_id, stepName: currentStep.name});
      //todo: This should likely be abstracted so that the actionwindow Id is not needed
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});
