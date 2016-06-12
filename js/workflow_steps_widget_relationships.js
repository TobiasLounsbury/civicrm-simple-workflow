
function SWRelationship_RebuildNameList() {

  //Compile the new list
  var list = [CRM.$('<option value="<user>">Active User</option>')];
  CRM.$("#Data .step_name").each(function(index, item) {
    var itemOrder = CRM.$(item).attr("name").replace("data[", "").replace("][name]", "");
    list.push(CRM.$('<option value="' + itemOrder + '">' + CRM.$(item).val() + '</option>'));
  });

  //Setup the Template first so we can clone it.
  CRM.$(".SW-Relationship-Template .SW-Relationship-Contact").empty().append(list);
  SWRelationship_SetContactValue(CRM.$(".SW-Relationship-Template .SW-Relationship-Contact"));
  list = CRM.$(".SW-Relationship-Template .SW-Relationship-Contact option");

  //Set the options for each select preserving the selections.
  CRM.$("#Data .SW-Relationship-Contact").each(function(index, item) {
    var oldValue = CRM.$(item).val();
    CRM.$(item).empty().append(list.clone()).val(oldValue);
    //This updates the hidden fields that also contain the name.
    SWRelationship_SetContactValue(CRM.$(item));
  });
}

function SWRelationship_SetContactValue(obj) {
  var selectValue = obj.val();
  if(CRM.$.isNumeric(selectValue)) {
    obj.parent().find(".SW-Relationship-Contact-Hidden").val(
      CRM.$("#detail_order_" + selectValue).val()
    );
  } else {
    obj.parent().find(".SW-Relationship-Contact-Hidden").val(selectValue);
  }
}

function SWRelationship_AddAll(target, relationships) {
  if (relationships && Object.keys(relationships).length > 0) {
    for (var i in relationships) {
      if (relationships.hasOwnProperty(i)) {
        SWRelationship_Add(target, relationships[i].relType, relationships[i].contact);
      }
    }
  }
}

function SWRelationship_Add(target, type, contact) {
  //Clone the template
  var newRel = CRM.$(".SW-Relationship-Template").clone();
  //Setup the proper classes
  newRel.removeClass("workflow-hidden-section SW-Relationship-Template").addClass("SW-Relationship");

  //Set the Name attribute of the inputs
  var rels = 1;
  var order = target.attr("data-index");
  var last = target.find(".SW-Relationship:last");
  if (last.length > 0) {
    rels = parseInt(last.attr("data-rels")) + 1;
  }
  //Set the Data attribute
  newRel.attr("data-rels", rels);

  //Set the name for the hidden Input
  var inp = newRel.find(".SW-Relationship-Contact-Hidden");
  var inpName = inp.attr("name").replace("#ORDER#", order).replace("#RELS#", rels);
  inp.attr("name", inpName);

  //Set the name of the relationship Type Selector
  var sel = newRel.find(".SW-Relationship-Type");
  var selName = sel.attr("name").replace("#ORDER#", order).replace("#RELS#", rels);
  sel.attr("name", selName);

  //Set the Type if we have it.
  if (type) {
    sel.val(type);
  }

  //Set the Contact if we have it
  if (contact) {
    SWRelationship_SetValueDelayed(newRel.find(".SW-Relationship-Contact"), contact);
  }

  //Add the relationship to the step.
  target.find(".SW-Relationship-List").append(newRel);
}

function SWRelationship_SetValueDelayed(target, value) {
  //todo: Refactor this so it checks that the option exists.
  if(value) {
    var index = CRM.$(".order[value=" + value + "]").closest(".Detail").attr("data-index");
    target.val(index);
    //Stop gap that will wait a while and re-try
    //This could cause problems if it never becomes available.
    if(!target.val()) {
      setTimeout(function() {
        SWRelationship_SetValueDelayed(target, value);
      },100);
    } else {
      SWRelationship_SetContactValue(target);
    }
  }
}

CRM.$(function ($) {


  $("#Data").click(function(e) {
    var obj = $(e.target);

    //Wire up Add buttons
    if (obj.hasClass("SW-Relationship-Add-Button")) {
      SWRelationship_Add(obj.closest(".Detail"));
      return e.preventDefault();
    }

    //wire up the remove buttons
    if (obj.hasClass("SW-Relationship-Remove-Button")) {
      obj.parent().remove();
      return e.preventDefault();
    }

  });

  $("#Data").change(function(e) {
    var obj = $(e.target);


    //Wire up change contact reference
    if (obj.hasClass("SW-Relationship-Contact")) {
      //todo: Some validation so that you can't set a following detail.
      SWRelationship_SetContactValue(obj);
    }

    //Wire up change step name
    if (obj.hasClass("step_name")) {
      SWRelationship_RebuildNameList();
    }

  });

  //Rebuild the list of steps names when a new step is added
  $("#Data").on("step:added", function(e, data, template) {
    SWRelationship_RebuildNameList();
  });

  //Rebuild the list of steps names when a new step is deleted
  $("#Data").on("step:deleted", function(e) {
    SWRelationship_RebuildNameList();
  });

  //Rebuild the list of steps names when the steps are reordered
  $("#Data").on("steps:reordered", function(e) {
    SWRelationship_RebuildNameList();
  });

  //Set initial values of Names and order for step selector
  $("#Data").on("load:complete", function(e) {
    SWRelationship_RebuildNameList();
  });
});