function SimpleWorkflowStepAddProfile(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#ProfileSelector").val();
    data.entity_name = CRM.$(".SW-Profile .select2-chosen:first").text().trim();
    data.breadcrumb = data.entity_name;
    data.title = data.entity_name;
    data.next = "Next";
    data.options = data.options || {};
    data.options.mode = CRM.$("[name='SW-Profile-Mode']:checked").val();
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateProfile").html()
  );

  template.find(".entity_name").html(data.entity_name);
  template.find(".entity_table").val("Profile");
  template.addClass("Profile");
  return true;
}
