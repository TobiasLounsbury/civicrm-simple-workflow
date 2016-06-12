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
  CRM.$("#Data").trigger("steps:reordered");
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
  CRM.$("#Data").trigger("step:deleted", obj);
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
  obj.find("input,textarea,select,label").each(function() {
    var inp = CRM.$(this);

    //Do attribute string replacement.
    var fieldAttrs = ["name", "id", "for"];
    for (var i in fieldAttrs) {
      if (inp.attr(fieldAttrs[i])) {
        inp.attr(fieldAttrs[i], inp.attr(fieldAttrs[i]).replace("#ORDER#", index));
      }
    }

    if(inp.attr("name")) {
      var data_name = inp.attr("name").replace(/.*\[([^\[]*)\]$/g, "$1");

      if(inp.is(":checkbox")) {
        if (data.hasOwnProperty(data_name)) {
          inp.prop("checked", data[data_name]);
        }

        if (data.hasOwnProperty("options") && data.options !== null && data.options.hasOwnProperty(data_name)) {
          inp.prop("checked", data.options[data_name]);
        }
      } else if(inp.is(":radio")) {
        if (data.hasOwnProperty(data_name)) {
          inp.prop("checked", (inp.val() == data[data_name]));
        } else if (data.hasOwnProperty("options") && data.options !== null && data.options.hasOwnProperty(data_name)) {
          inp.prop("checked", (inp.val() == data.options[data_name]));
        }
      } else {
        if (data.hasOwnProperty(data_name)) {
          inp.val(data[data_name]);
        }

        if (data.hasOwnProperty("options") && data.options !== null && data.options.hasOwnProperty(data_name)) {
          inp.val(data.options[data_name]);
        }
      }
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
    template.attr("data-index", CRM.vars.SimpleWorkflow.nextIndex);
    if(func(template, CRM.vars.SimpleWorkflow.nextIndex, data)) {
      SWSetIndexAndData(template, CRM.vars.SimpleWorkflow.nextIndex, data);
      CRM.$("#SortableDetails").append(template);
    }
    CRM.vars.SimpleWorkflow.nextIndex++;
  }

  SWRefreshSortable();
  SWReorderSteps();
  CRM.$("#Data").trigger("step:added", data, template);
}



function SWValidateSteps(formData) {
  var valid = true;
  var names = [];
  var stepTypes = [];
  for(var i in formData.data) {
    if(!formData.data[i].name) {
      CRM.alert(ts("Name is a required field for all steps"), ts("Error"), "warning");
      valid = false;
    }
    names.push(formData.data[i].name);
    stepTypes.push(formData.data[i].entity_table);
  }

  if(names.length !== CRM._.unique(names).length) {
    CRM.alert(ts("Names must be unique for each step"), ts("Error"), "warning");
    valid = false;
  }

  //Allow other scripts to do validation
  for(var t in stepTypes) {
    var func = SWGetStepFunction("SimpleWorkflowValidateSteps", stepTypes[t]);
    if (func) {
      valid = (valid && func(formData));
    }
  }

  return valid;
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
    //Trigger a before save so other scripts can do stuff before we
    //Serialize the data.
    $("#Data").trigger("before:save");

    var formData = $("#Data").serializeObject();
    if(SWValidateSteps(formData)) {
      CRM.api3('Workflow', 'save', {
        "data": $("#Data").serialize(),
        "wid": CRM.vars.SimpleWorkflow.wid
      }).done(function (result) {
        if (!result.is_error) {
          CRM.alert(ts("All changes have been saved"), ts("Saved"), "success");
          window.location = CRM.url("civicrm/workflows");
        } else {
          CRM.alert(ts("There was an error saving your changes") + ".<br />" + result.error_message, ts("Error"), "error");
        }
      });
    }
    e.preventDefault();
  });

  //Load the Data
  for(var i in CRM.vars.SimpleWorkflow.details) {
    SWAddStep(CRM.vars.SimpleWorkflow.details[i].entity_table, CRM.vars.SimpleWorkflow.details[i]);
  }

  //Setup the sortable.
  SWRefreshSortable();

  //show the first form
  $("#AddSteps .SWToggleForm:first").show();
  
  //Let anyting that wants to run some init code do so now.
  $("#Data").trigger("load:complete");
});

