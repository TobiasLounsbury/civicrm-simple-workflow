/**
 * Updates the Sortable Area after new elements have been added
 */
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

/**
 * Updates the Order input item Elements for each
 * Step when they are reordered, to maintain data
 * integrity
 */
function SWReorderSteps() {
  CRM.$("#Data .Detail").each(function (i) {
    CRM.$(this).find(".order").val(i + 1);
  });
}

/**
 * This function composes a function name from a
 * prefix and step type then searches a number of
 * name spaces to see if that function exists. If
 * it does, a reference is returned, otherwise false.
 *
 * @param prefix
 *  The type of function being looked for
 * @param type
 *  The step type you want to take this action for.
 * @returns
 *  Function or (bool)False
 */
function SWGetStepFunction(prefix, type) {
  var func = prefix + type.charAt(0).toUpperCase() + type.substr(1);

  if(CRM.hasOwnProperty("SimpleWorkflow") && CRM.$.isFunction(CRM.SimpleWorkflow[func])) {
    return CRM.SimpleWorkflow[func];
  }

  if(CRM.$.isFunction(CRM[func])) {
    return CRM[func];
  }

  if(CRM.$.isFunction(window[func])) {
    return window[func];
  }

  return false;
}


/**
 * This function handles click events to toggle the visibility
 * of the various Add Step Forms
 */
function SWToggleAddForm(event) {
  var obj = CRM.$(event.target).next();
  if(obj.hasClass("SWToggleForm")) {
    obj.slideToggle();
  }
}


/**
 * This function handles click events to toggle the visibility
 * of the step detail pane.
 */
function SWToggleStepDetails(event) {
  var obj = CRM.$(event.target).parent().find(".crm-simple-workflow-step-details");
  if(obj.hasClass("SWDetailVisible")) {
    CRM.$(event.target).html(ts("Show Details"));
    obj.removeClass("SWDetailVisible");
    obj.slideUp();
  } else {
    CRM.$(event.target).html(ts("Hide Details"));
    obj.addClass("SWDetailVisible");
    obj.slideDown();
  }
}

/**
 * Click Handler for the delete "button" on the individual
 * Step detail elements.
 */
function SWDeleteStep(event) {
  var obj = CRM.$(event.target).parent();
  var stepType = obj.find(".entity_table").val();
  var func = SWGetStepFunction("SimpleWorkflowStepDelete", stepType);
  if(func) {
    if(func(obj)) {
      obj.remove();
    }
  } else {
    obj.remove();
  }
  SWReorderSteps();
}

/**
 *
 *
 * @param obj
 *  A reference to the new Step/Detail DOM element
 *  that is being created
 * @param index INT
 *  The data index (unique int) used to keep any data from overlapping
 * @param data
 *  The data to set within each input/textarea
 */
function SWSetIndexAndData(obj, index, data) {
  obj.find("input,textarea").each(function() {
    var inp = CRM.$(this);

    inp.attr("name", inp.attr("name").replace("#ORDER#", index));

    var data_name = inp.attr("name").replace(/.*\[([^\[]*)\]$/g, "$1");

    if (data.hasOwnProperty(data_name)) {
      inp.val(data[data_name]);
    }

    if (data.hasOwnProperty("options") && data.options !== null && data.options.hasOwnProperty(data_name)) {
      inp.val(data.options[data_name]);
    }
  });
}

/**
 * This function takes a type of step, and calls it's
 * constructor function to compose the element, update
 * data and insert it into the sortable pane.
 *
 * @param stepType
 *  The type of step this is.
 * @param data
 * The data to insert into this step element
 */
function SWAddStep(stepType, data) {
  if (data === null) {
    data = {};
  }
  var func = SWGetStepFunction("SimpleWorkflowStepAdd", stepType.charAt(0).toUpperCase() + stepType.substr(1));
  if(func) {
    var template = CRM.$("#SimpleWorkflowTypeTemplateDefault").clone();
    template.removeAttr("id");
    if(func(template, CRM.vars.SimpleWorkflow.nextIndex, data)) {
      SWSetIndexAndData(template, CRM.vars.SimpleWorkflow.nextIndex, data);
      CRM.vars.SimpleWorkflow.nextIndex++;
      CRM.$("#SortableDetails").append(template);
    }
  }

  SWRefreshSortable();
  SWReorderSteps();
}


/**
 * Initial Page setup
 */
CRM.$(function ($) {

  //Set the initial Index to 0.
  //This is used to make sure we don't get any overlap of data
  //structures
  CRM.vars.SimpleWorkflow.nextIndex = 0;

  //Wire up all the delete buttons
  $(".crm-simple-workflow-steps-form").on("click", ".DeleteStep", SWDeleteStep);
  //Wire up all the toggle detail buttons
  $(".crm-simple-workflow-steps-form").on("click", ".SWToggleDetails", SWToggleStepDetails);

  //Wire up all the toggles for showing the ADD panes
  $("#AddSteps").on("click", ".SWToggleFormTrigger", SWToggleAddForm);

  //Wire up all the AddStep buttons
  $("#AddSteps").on("click", ".AddStepButton", function(event) {
    var stepType = CRM.$(event.target).data("step-type");
    SWAddStep(stepType, {});
  });

  //Wire up the Save Workflow button
  $("#SaveDetails").click(function(e) {
    CRM.api3('Workflow', 'save', {
      "data": $("#Data").serialize(),
      "wid": CRM.vars.SimpleWorkflow.wid
    }).done(function(result) {
      if (!result.is_error) {
        CRM.alert(ts("All changes have been saved"), ts("Saved"), "success");
        window.location = CRM.url("civicrm/workflows");
      } else {
        CRM.alert(ts("There was an error saving your changes") + ".<br />" + result.error_message, ts("Error"), "error");
      }
    });
  });

  //Load the Data
  for(var i in CRM.vars.SimpleWorkflow.details) {
    SWAddStep(CRM.vars.SimpleWorkflow.details[i].entity_table, CRM.vars.SimpleWorkflow.details[i]);
  }

  //show the first form
  $("#AddSteps .SWToggleForm:first").show();

  //Setup the sortable.
  SWRefreshSortable();
});

