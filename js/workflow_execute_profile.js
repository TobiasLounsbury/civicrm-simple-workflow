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


    //Watch for successful form completion and react accordingly
    $("body").on("SimpleWorkflow:action:crmFormSuccess", function(event, data) {
      let currentStep = CRM.Workflow.steps[CRM.Workflow.stepIndex];
      if (currentStep.entity_table == "Profile") {
        if(currentStep.options.hasOwnProperty("groupContacts")) {
          currentStep.options.groupContacts.push({"id": data.id, "text": data.label});
        }

        var actionType = currentStep.options.mode || "current";
        if(actionType === "current" && !(currentStep.SWRelationshipEntityId)) {
          CRM.Workflow.newCurrentCID = data.id;
        }

        currentStep.SWRelationshipEntityId = data.id;
        currentStep.SWSelectMode = "form";
      }
    })


    // Watch for Form Load, and check that it is the current step
    // And if so, trigger a custom Load:Profile:Complete event
    // This is so that other watchers don't have to repeat this
    // logic, and can simply wait for this custom event to run any custom logic
    // for a custom profile step.

    .on("SimpleWorkflow:action:crmFormLoad", function(event, data) {
        //Make sure we are expecting to load a profile
        if (CRM.Workflow.steps[CRM.Workflow.stepIndex].entity_table == "Profile") {
          //Make sure this is the profile we are expecting.
          var term = "gid=" + CRM.Workflow.steps[CRM.Workflow.stepIndex].entity_id + "&";
          if (data.url.search(term) > -1) {
            $("body").trigger("SimpleWorkflow:Step:Load:Profile:Complete", CRM.Workflow.steps[CRM.Workflow.stepIndex]);
          }
        }
      })


    .on("SimpleWorkflow:Step:Load", function(event, currentStep) {
    if (currentStep.entity_table == "Profile") {
      if (CRM.Workflow.method == "inject") {
        $("#Main").hide();
        $("#ActionWindow").show();
      }

      var actionType = currentStep.options.mode || "current";
      var urlAction = "edit";
      var urlParams = {gid: currentStep.entity_id, reset: 1};
      var showActionWindow = true;
      switch(actionType) {
        case "current":
          //if(!currentStep.SWRelationshipEntityId) {
          if(!currentStep.SWRelationshipEntityId && !CRM.Workflow.newCurrentCID) {
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
  })


  // Teardown of custom things done in the setup step.
  //$("body")
    .on("SimpleWorkflow:Step:Teardown", function(event, currentStep) {
      //Remove the select widget if it exists
      if (currentStep.entity_table == "Profile") {
        $("#SWProfileSelect_" + currentStep.order).remove();
      }
    });

});
