CRM.$(function ($) {

  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "Profile") {
      if (CRM.Workflow.method == "inject") {
        $(".crm-contribution-main-form-block").hide();
        $("#ActionWindow").show();
      }

      var actionType = currentStep.options.mode || "edit";
      var lsurl = CRM.url("civicrm/profile/" + actionType, {gid: currentStep.entity_id, reset: 1});
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:true});
    }
  });
});