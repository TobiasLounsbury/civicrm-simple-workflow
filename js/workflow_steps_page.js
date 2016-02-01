function SimpleWorkflowStepAddPage(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#PageSelector").val();
    data.entity_name = CRM.$("#PageSelector option:selected").text().trim();
    data.breadcrumb = data.entity_name;
    data.title = data.entity_name;
    data.next = "Next";
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplatePage").html()
  );

  template.find(".entity_name").html(data.entity_name);
  template.find(".entity_table").val("Page");
  template.find(".handle").hide();
  template.addClass("Page");

  if (CRM.$("#Data .Page").length == 0) {
    if (!CRM.$("#Data > hr").length) {
      CRM.$("#Data").append("<hr />");
    }

    SWSetIndexAndData(template, CRM.vars.SimpleWorkflow.nextIndex, data);
    CRM.vars.SimpleWorkflow.nextIndex++;
    CRM.$("#Data").append(template);
  }

  return false;
}

function SimpleWorkflowStepDeletePage(step) {
  CRM.$("#Data > hr").remove();
  return true;
}