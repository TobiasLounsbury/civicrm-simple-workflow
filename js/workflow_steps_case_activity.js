function SimpleWorkflowStepAddCaseActivity(template, index, data) {
  //Set defaults if we have no data.
  console.log("Adding Case Activity");
  if (CRM.$.isEmptyObject(data)) {
    data.entity_id = CRM.$("#SWCaseActivitySelector").val();
    data.breadcrumb = CRM.$("#SWCaseActivityBreadcrumb").val();
    data.name = data.breadcrumb.toLowerCase().replace(" ", "_");
    data.entity_name = CRM.$("#SWCaseActivitySelector option:selected").text().trim();
    data.title = data.entity_name;
    data.next = "Next";
    data.options = {
      "include_profile": CRM.$("#SWCaseActivityIncludeProfile").val(),
    };
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateCaseActivity").html()
  );

  data.entity_name = data.entity_name || CRM.$("#SWCaseActivitySelector option[value='" +data.entity_id+ "']").text();
  template.find(".entity_name").html(data.entity_name);


  if (data.options.case_order) {
    list = CRM.$(".SW-Relationship-Template .SW-Relationship-Contact option");
    template.find(".SW-Relationship-Contact").empty().append(list.clone()).val(data.options.case_order - 1);
  }


  if(data.hasOwnProperty("options") && data.options.hasOwnProperty("include_profile")) {
    template.find(".case_activity_option_include_profile").val(data.options.include_profile);
  }
  template.find(".case_activity_option_include_profile").crmProfileSelector({"groupTypeFilter": "Activity"});

  template.find(".entity_table").val("CaseActivity");
  template.addClass("CaseActivity");
  return true;
}