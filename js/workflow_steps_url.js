function SimpleWorkflowStepAddUrl(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#SWURLUrl").val();
    data.breadcrumb = CRM.$("#SWURLBreadcrumb").val();
    data.name = data.breadcrumb.toLowerCase().replace(" ", "_");
    data.title = data.breadcrumb;
    data.next = "Next";
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateUrl").html()
  );

  template.find(".entity_table").val("url");
  template.addClass("url");
  return true;
}
