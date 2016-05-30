function SimpleWorkflowStepAddJquery(template, index, data) {

  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#dom-id").val().trim();
    data.breadcrumb = CRM.$("#dom-breadcrumb").val().trim();
    data.title = data.breadcrumb;
    data.next = "Next";
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateJquery").html()
  );

  template.find(".entity_table").val("jquery");
  template.addClass("jQuery");
  return true;
}