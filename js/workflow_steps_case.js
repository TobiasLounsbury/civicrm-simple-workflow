function SimpleWorkflowStepAddCase(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    data.entity_id = CRM.$("#SWCaseSelector").val();
    data.breadcrumb = CRM.$("#SWCaseBreadcrumb").val();
    data.name = data.breadcrumb.toLowerCase().replace(" ", "_");
    data.entity_name = CRM.$("#SWCaseSelector option:selected").text().trim();
    data.title = data.entity_name;
    data.next = "Next";
    data.options = {
      "include_profile": "",
      "core_fields": "",
      mode: "create",
      defaults: {}
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
   {id: "case_type_id", text: ts("Case Type")},
   {id: "medium_id", text: ts("Activity Medium")},
   {id: "activity_location", text: ts("Location")},
   {id: "activity_details", text: ts("Details")},
   {id: "activity_subject", text: ts("Subject")},
   {id: "status_id", text: ts("Case Status")},
   {id: "start_date", text: ts("Case Start Date")},
   {id: "duration", text: ts("Activity Duration")},
   {id: "attachments", text: ts("Attachments")}
  ];


  cFields.select2({data: fieldsData, multiple: true});
  template.find("div.case_option_core_fields").css("width", "75%");

  //Set values for defaults.
  if(data.hasOwnProperty("options") && data.options.hasOwnProperty("defaults")) {
    for (var x in fieldsData) {
      if (data.options.defaults.hasOwnProperty(fieldsData[x].id)) {
        if(fieldsData[x].id === 'client_id') {
          template.find(".case_option_defaults_client_id").prop("checked", data.options.defaults.client_id);
        } else {
          template.find(".case_option_defaults_" + fieldsData[x].id).val(data.options.defaults[fieldsData[x].id]);
        }
      }
    }
  }

  if(data.hasOwnProperty("options") && data.options.hasOwnProperty("include_profile")) {
    template.find(".case_option_include_profile").val(data.options.include_profile);
  }
  template.find(".case_option_include_profile").crmProfileSelector({"groupTypeFilter": "Case"});

  template.find(".entity_table").val("Case");
  template.addClass("Case");
  return true;
}
