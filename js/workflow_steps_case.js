function SimpleWorkflowStepAddCase(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#SWCaseSelector").val();
    data.breadcrumb = CRM.$("#SWCaseBreadcrumb").val();
    data.name = data.breadcrumb.toLowerCase();
    data.entity_name = CRM.$("#SWCaseSelector option:selected").text().trim();
    data.title = data.entity_name;
    data.next = "Next";
    data.options = {
      "include_profile": CRM.$("#SWCaseIncludeProfile").is(":checked")
    };
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateCase").html()
  );

  template.find(".entity_name").html(data.entity_name);

  template.find(".option_include_profile").prop("checked", data.options.include_profile);

  template.find(".entity_table").val("Case");
  template.addClass("Case");
  return true;
}
