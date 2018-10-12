CRM.$(function ($) {

  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "url") {
      if (CRM.Workflow.method == "inject") {
        $("#Main").hide();
        $("#ActionWindow").show();
      }

      var lsurl = CRM.url(currentStep.entity_id);
      if(currentStep.entity_id.indexOf("http") == 0) {
        lsurl = currentStep.entity_id;
      }
      window.$ = CRM.$;
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:false});
    }
  });
});
