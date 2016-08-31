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

  //Handle initial visibility of relationships
  if (data.options.mode == "current") {
    template.find(".SW-Profile-Relationships-Wrapper").hide();
  } else {
    //Add all the relationships we have saved if any.
    SWRelationship_AddAll(template, data.options.relationships);
  }

  //Handle the Existing Filters
  var selectModes = ["select-new", "select-edit-new", "select-edit-existing"];
  if (selectModes.indexOf(data.options.mode) == -1) {
    template.find(".SW-Profile-Select-Existing-Wrapper").hide();
  }


  template.find(".entity_name").html(data.entity_name);
  template.find(".entity_table").val("Profile");
  template.addClass("Profile");
  return true;
}

CRM.$(function ($) {

  $("#Data").change(function(e) {
    var obj = $(e.target);

    //Wire up change profile mode
    if (obj.hasClass("SW-Profile-Mode")) {

      //This handles the Relationship Dialog
      if (obj.val() == "current") {
        obj.closest(".Detail").find(".SW-Profile-Relationships-Wrapper").slideUp();
      } else {
        obj.closest(".Detail").find(".SW-Profile-Relationships-Wrapper").slideDown();
      }

      //Show hide the select-wrapper
      if (obj.val() == "select-new" || obj.val() == "select-edit-new" || obj.val() == "select-edit-existing") {
        obj.closest(".Detail").find(".SW-Profile-Select-Existing-Wrapper").slideDown();
      } else {
        obj.closest(".Detail").find(".SW-Profile-Select-Existing-Wrapper").slideUp();
      }

    }
  });
});