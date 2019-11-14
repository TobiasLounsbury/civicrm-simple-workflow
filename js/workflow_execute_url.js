CRM.$(function ($) {

  CRM.Workflow.handle("Step:Load", function(currentStep) {
    if (currentStep.entity_table === "url") {

      CRM.Workflow.useActionWindow();

      var lsurl = CRM.url(currentStep.entity_id);
      if(currentStep.entity_id.indexOf("http") == 0) {
        lsurl = currentStep.entity_id;
      }
      window.$ = CRM.$;

      //todo: Abstract this out
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});
