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
      "include_profile": CRM.$("#SWCaseIncludeProfile").is(":checked"),
      "core_fields": "",
      mode: "create"
    };
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateCase").html()
  );

  template.find(".entity_name").html(data.entity_name);


  var mode = template.find(".case_option_mode");
  mode.attr("name", mode.attr("name").replace("#ORDER#", index));
  mode.val(data.options.mode);

  var cFields = template.find(".case_option_core_fields");
  cFields.attr("name", cFields.attr("name").replace("#ORDER#", index));
  cFields.val(data.options.core_fields);

  var fieldsData = [
   {id: "client_id", text: ts("Client ID")},
   {id: "medium_id", text: ts("Activity Medium")},
   {id: "activity_details", text: ts("Details")},
   {id: "activity_subject", text: ts("Subject")},
   {id: "status_id", text: ts("Case Status")},
   {id: "start_date", text: ts("Case Start Date")},
   {id: "duration", text: ts("Activity Duration")},
   {id: "attachments", text: ts("Attachments")},
  ];


  cFields.select2({data: fieldsData, multiple: true});
  template.find("div.case_option_core_fields").css("width", "75%");

  template.find(".case_option_include_profile").prop("checked", data.options.include_profile);

  template.find(".entity_table").val("Case");
  template.addClass("Case");
  return true;
}
