CRM.$(function ($) {


  var handleProfileSelectExisting = function(event) {
    var ind = $(event.target).data("step");
    var value = $("#SWProfileSelect_" + ind + "_Value").val();
    var step = CRM.Workflow.steps[ind];

    //Save the value.
    step.SWRelationshipEntityId = value;
    step.SWSelectMode = "existing";

    var request = CRM.api3("workflow", "complete_step", {"workflow": step.workflow_id, "step": step.name, "contact": value});

    request.success(function() {
      CRM.Workflow.CompleteCurrentStep();
      $("#ActionWindow").slideDown();
    });

  };

  function setupSelectType(currentStep) {
    if ($("#SWProfileSelect_" + currentStep.order).length == 0) {

      //Create the Dom Objects
      var cont = $("<div class='SWProfileSelectExistingContainer crm-section' id='SWProfileSelect_" + currentStep.order+ "'></div>");

      var label = $("<div class='label'><label class='SWProfileSelectLabel' for='SWProfileSelect_" + currentStep.order+ "_Value'>"+ts(currentStep.options.existingFieldLabel)+"</label></div>");
      var selectContainer = $("<div class='content'></div>");
      var selector = $("<input class='SWProfileSelectValue' id='SWProfileSelect_" + currentStep.order+ "_Value' />");

      if(currentStep.SWRelationshipEntityId) {
        selector.val(currentStep.SWRelationshipEntityId);
      }

      var saveButton = $("<button data-step='" +currentStep.order+ "'>" + ts(currentStep.options.existingButtonText) + "</button>");
      var createButton = $("<button>" + ts(currentStep.options.existingOrMessage) + "</button>");
      var sepp = $("<hr />").hide();
      //wire up the save button
      saveButton.click(handleProfileSelectExisting);

      createButton.click(function(e) {
        $("#ActionWindow").slideToggle();
        sepp.toggle();
        return e.preventDefault();
      });


      //Add the widgets to the page.
      selectContainer.append(selector);
      cont.append(label).append(selectContainer).append(saveButton).append(createButton).append(sepp);
      $("#PreMessage").after(cont);

      //trigger create of select2 with data source.
      selector.select2({data: CRM._.values(currentStep.options.groupContacts)});
    }
  }


  $("body").on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "Profile") {
      if (CRM.Workflow.method == "inject") {
        $(".crm-contribution-main-form-block").hide();
        $("#ActionWindow").show();
      }

      var actionType = currentStep.options.mode || "current";
      var urlAction = "edit";
      var urlParams = {gid: currentStep.entity_id, reset: 1};
      var showActionWindow = true;
      switch(actionType) {
        case "current":
          if(!currentStep.SWRelationshipEntityId) {
            urlAction = "create";
          }
          break;
        case "related-new":
          if(!currentStep.SWRelationshipEntityId) {
            urlAction = "create";
          } else {
            urlParams.cid = currentStep.SWRelationshipEntityId;
          }
          break;
        case "related-edit":
          if(!currentStep.SWRelationshipEntityId) {
            urlAction = "create";
          }
          break;
        case "create":
          urlAction = "create";
          break;
        case "select-new":
          setupSelectType(currentStep);
          showActionWindow = false;
          urlAction = "create";
          break;
        case "select-edit-new":
          if(currentStep.SWRelationshipEntityId && currentStep.SWSelectMode == "form") {
            urlParams.cid = currentStep.SWRelationshipEntityId;
          } else {
            urlAction = "create";
          }
          setupSelectType(currentStep);
          showActionWindow = false;
          urlAction = "create";
          break;
        case "select-edit-all":
          if(!currentStep.SWRelationshipEntityId) {
            urlAction = "create";
          } else {
            urlParams.cid = currentStep.SWRelationshipEntityId;
          }
          setupSelectType(currentStep);
          showActionWindow = false;
          break;
      }

      if (showActionWindow) {
        $("#ActionWindow").show();
      } else {
        $("#ActionWindow").hide();
      }

      var lsurl = CRM.url("civicrm/profile/" + urlAction, urlParams);
      var aw = CRM.loadForm(lsurl, {target:"#ActionWindow", dialog: false, autoClose:true});
    }
  });

  $("#ActionWindow").on("crmFormSuccess", function(event, data) {
    if (CRM.Workflow.steps[CRM.Workflow.stepIndex].entity_table == "Profile") {
      if(CRM.Workflow.steps[CRM.Workflow.stepIndex].options.hasOwnProperty("groupContacts")) {
        CRM.Workflow.steps[CRM.Workflow.stepIndex].options.groupContacts.push({"id": data.id, "text": data.label});
      }
      CRM.Workflow.steps[CRM.Workflow.stepIndex].SWRelationshipEntityId = data.id;
      CRM.Workflow.steps[CRM.Workflow.stepIndex].SWSelectMode = "form";
    }
  });


  $("body").on("SimpleWorkflow:Step:Teardown", function(event, currentStep) {
    //Remove the select widget if it exists
    if (currentStep.entity_table == "Profile") {
      $("#SWProfileSelect_" + currentStep.order).remove();
    }
  });


});