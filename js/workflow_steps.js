function SWRefreshSortable() {
  CRM.$( "#SortableDetails" ).sortable({
    placeholder: 'PlaceHolder',
    forcePlaceholderSize: true,
    handle: ".handle",
    update: function( event, ui ) {
      SWReorderSteps();
    }
  });
  CRM.$(".handle").disableSelection();
}

function SWReorderSteps() {
  CRM.$("#Data .Detail").each(function (i) {
    CRM.$(this).find(".order").val(i + 1);
  });
}

function SWDeleteStep(event) {
  CRM.$(event.target).parent().remove();
  SWReorderSteps();
}

CRM.$(function ($) {

  $(".crm-simple-workflow-steps-form").on("click", ".DeleteStep", SWDeleteStep);

  $("#SaveDetails").click(function(e) {
    CRM.api3('Workflow', 'save', {
      "data": $("#Data").serialize(),
      "wid": CRM.vars.SimpleWorkflow.wid
    }).done(function(result) {
      if (!result.is_error) {
        CRM.alert("All changes have been saved", "Saved", "success");
        window.location = CRM.url("civicrm/workflows");
      } else {
        CRM.alert("There was an error saving your changes.<br />" + result.error_message, "Error", "error");
      }
    });
  });

  SWRefreshSortable();
});

