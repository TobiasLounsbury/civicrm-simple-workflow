CRM.$(function ($) {

  //Make sure our object exists
  CRM.Workflow = CRM.Workflow || {};


  //Utility function when we don't know if steps will be zero indexed.
  function first(p){for(var i in p)return p[i];}


  /**
   *
   *
   * @returns {*|jQuery|undefined}
   */
  CRM.Workflow.getNextStep = function() {
    return $("ol.WorkflowSteps .stepTodo:first").data("order");
  };


  /**
   *
   *
   * @param order
   */
  CRM.Workflow.loadStep = function(order) {
    if(CRM.Workflow.currentStep) {
      $('ol.WorkflowSteps li[data-order=' + CRM.Workflow.currentStep.order + ']').removeClass("stepActive");
      if ($('ol.WorkflowSteps li[data-order=' + CRM.Workflow.currentStep.order + ']').hasClass("completed")) {
        $('ol.WorkflowSteps li[data-order=' + CRM.Workflow.currentStep.order + ']').addClass("stepDone");
      } else {
        $('ol.WorkflowSteps li[data-order=' + CRM.Workflow.currentStep.order + ']').addClass("stepTodo");
        $('ol.WorkflowSteps li[data-order=' + CRM.Workflow.currentStep.order + ']').addClass("stepAvailable");
      }
    }

    //Scroll to the top of the page when a new step is loaded.
    $("html, body").animate({ scrollTop: 0 }, 300);

    //Set the CRM.Workflow.currentStep
    CRM.Workflow.currentStep = CRM.Workflow.steps[order];
    //Trigger an Step Load event
    CRM.Workflow.trigger("Step:Load", CRM.Workflow.currentStep);


    if (CRM.Workflow.currentStep.title && CRM.Workflow.currentStep.title.length) {
      $("#WorkflowTitle legend").text(CRM.Workflow.currentStep.title);
      $("#WorkflowTitle").show();
    } else {
      $("#WorkflowTitle").hide();
    }

    $("#PreMessage").html(CRM.Workflow.currentStep.pre_message);
    $("#PostMessage").html(CRM.Workflow.currentStep.post_message);

    //Set the new form step to active
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepDone");
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepTodo");
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepAvailable");
    $('ol.WorkflowSteps li[data-order='+order+']').addClass("stepActive");

    //Set the window Hash so it can be recalled on backbutton press
    CRM.Workflow.loadingStep = true;
    location.hash = order;
    CRM.Workflow.stepIndex = order;
  };


  /**
   *
   * @param index
   */
  CRM.Workflow.skipToStep = function(index) {

    //Trigger the teardown event
    CRM.Workflow.trigger("Step:Teardown", CRM.Workflow.steps[CRM.Workflow.stepIndex]);

    //todo: This should be refactored so that Step types take care of themselves
    //If we are injecting into a page, but moving to the page content
    if (CRM.Workflow.method === "inject" && CRM.Workflow.steps[index].entity_table === "Page") {
      //Destroy the snippet so we don't get any overlapping behavior
      CRM.Workflow.$actionWindow.crmSnippet("destroy");
    }

    //Hide the SW Button
    $("#SWNextButton").hide();

    CRM.Workflow.loadStep(index);
  };


  /**
   * Creates some of the needed DOM Elements that make this whole thing tick over.
   *
   * todo: Refactor all of this so that progress is its own class and there are no more
   * magic classes or element ids
   */
  CRM.Workflow.injectWorkflowElements = function() {
    CRM.Workflow.$actionWindow = $('<div id="ActionWindow" class="simpleworkflow-action-window"></div>');
    $("#Main").before(CRM.Workflow.$actionWindow);
    CRM.Workflow.$actionWindow.before('<ol class="WorkflowSteps" id="WorkflowSteps"></ol>');
    CRM.Workflow.$actionWindow.before('<div id="PreMessage"></div>');
    $("#crm-submit-buttons").before('<div id="PostMessage"></div>');
    $("#WorkflowSteps").before('<div id="SW_PreFormMessage"></div>');
    $("#SW_PreFormMessage").html(CRM.Workflow.workflow.pre_message);
    $("#crm-submit-buttons").after('<div id="SW_PostFormMessage"></div>');
    $("#SW_PostFormMessage").html(CRM.Workflow.workflow.post_message);

    if (CRM.Workflow.returning) {
      var liclass = "stepDone";
      CRM.Workflow.$actionWindow.hide();
    } else {
      var liclass = "stepTodo";
      $("#Main").hide();
    }
    for (var i in CRM.Workflow.steps){
      if (CRM.Workflow.steps[i]['breadcrumb']) {
        $("#WorkflowSteps").append('<li class="'+liclass+' '+ CRM.Workflow.steps[i]['entity_table'] + '" data-order="' + CRM.Workflow.steps[i]['order'] + '"><span>' + CRM.Workflow.steps[i]['breadcrumb'] + '</span></li>');
      }
    }
    //Move the Intro Text
    $("#WorkflowSteps").before( $("#intro_text") );

    //Add a title Object
    $("#WorkflowSteps").after("<fieldset id='WorkflowTitle'><legend></legend></fieldset>");
    $("#WorkflowTitle").hide();

    //add a jquery next button if we are on a contirubtion page
    $("#Main").append("<a href='#' id='SWNextButton' class='button'><span> Next </span></a><div class='clear'></div>");

    //add wrapper to billing payment block so that it can be controlled.
    $("#billing-payment-block").wrap('<div class="WorkflowBillingBlock" id="WorkflowBillingBlock"></div>');

    //add wrapper to submit button so that it can be controlled.
    $("#crm-submit-buttons").wrap('<div class="WorkflowSubmit" id="WorkflowSubmit"></div>');

  };


  /**
   * Complete the current step
   *
   * @constructor
   */
  CRM.Workflow.CompleteCurrentStep = function() {

    //todo: Abstract this out to a progress rendering class
    //Add Classes to the progress-bar
    $('ol.WorkflowSteps li[data-order='+CRM.Workflow.currentStep.order+']').removeClass("stepActive");
    $('ol.WorkflowSteps li[data-order='+CRM.Workflow.currentStep.order+']').addClass("stepDone");
    $('ol.WorkflowSteps li[data-order='+CRM.Workflow.currentStep.order+']').addClass("completed");

    //Trigger the teardown event
    CRM.Workflow.trigger("Step:Teardown", CRM.Workflow.steps[CRM.Workflow.stepIndex]);

    //Hide the SW Button
    $("#SWNextButton").hide();

    //Load the next step in the workflow
    CRM.Workflow.loadStep(CRM.Workflow.getNextStep());
  };

  /**
   * Returns the last step in the workflow
   *
   * @returns {step}
   */
  CRM.Workflow.lastStep = function() {
    var last = Object.keys(CRM.Workflow.steps).slice(-1)[0];
    return CRM.Workflow.steps[last];
  };

  /**
   * Returns true/false if we are on the last step of the workflow
   *
   * @returns {boolean}
   */
  CRM.Workflow.onLastStep = function() {
    var last = CRM.Workflow.lastStep();
    return (CRM.Workflow.currentStep.order === last.order);
  };


  /**
   *
   */
  CRM.Workflow.setButtonText = function() {
    if (CRM.Workflow.onLastStep()) {
      $("#SWNextButton").hide()
    } else {
      $("#SWNextButton span").text(" " + CRM.Workflow.currentStep.next + " ");
      $("#SWNextButton").show()
    }
  };


  /**
   * Do some handling if we are in an injected state and want to use the
   * action window
   */
  CRM.Workflow.useActionWindow = function() {
    if (CRM.Workflow.method === "inject") {
      //todo: Abstract this out maybe?
      $("#Main").hide();
      CRM.Workflow.showActionWindow();
    }
  };

  /**
   * Helper function so steps don't need to know about the action window
   * where it is stored, what the ID is, etc.
   *
   * @param animate
   */
  CRM.Workflow.hideActionWindow = function(animate) {
    if(animate) {
      CRM.Workflow.$actionWindow.slideUp(animate);
    } else {
      CRM.Workflow.$actionWindow.hide();
    }
  };


  /**
   * Helper function so steps don't need to know about the action window
   * where it is stored, what the ID is, etc.
   *
   * @param animate
   */
  CRM.Workflow.showActionWindow = function(animate) {
    if(animate) {
      CRM.Workflow.$actionWindow.slideDown(animate);
    } else {
      CRM.Workflow.$actionWindow.show();
    }
  };

  /** Object to Store event handler callbacks **/
  CRM.Workflow.callbacks = {};

  /**
   * Function to register Workflow event handlers
   *
   * @param actionName
   * @param callback
   * @param weight
   */
  CRM.Workflow.handle = function(actionName, callback, weight) {
    weight = weight || 50;
    if (!CRM.Workflow.callbacks.hasOwnProperty(actionName)) {
      CRM.Workflow.callbacks[actionName] = [];
    }
    CRM.Workflow.callbacks[actionName].push({"weight": weight, "callback": callback});
  };

  /**
   * Trigger a Workflow event for all handlers that have been
   * registered for that action
   *
   * @param actionName
   */
  CRM.Workflow.trigger = function(actionName) {
    if (CRM.Workflow.callbacks.hasOwnProperty(actionName)) {
      //todo: Take weight into account
      for(var x in CRM.Workflow.callbacks[actionName]) {
        if(CRM.Workflow.callbacks[actionName].hasOwnProperty(x) && (typeof CRM.Workflow.callbacks[actionName][x].callback === "function")) {
          CRM.Workflow.callbacks[actionName][x].callback.apply(null, Array.prototype.slice.call(arguments, 1));
        }
      }
    }
  };



  /***********[ Run The Page ]*****************/
  //Check the method we are using and inject elements when needed
  if (CRM.Workflow.method === "inject") {
    CRM.Workflow.injectWorkflowElements();
  }

  //Set the breadcrumb width
  $("ol.WorkflowSteps li").css("width", CRM.Workflow.breadcrumWidth + "%");

  //todo: Abstract this out to a progress class
  //Allow each step in the breadcrumb to be clickable
  $("ol.WorkflowSteps li").click(function(e) {
    var obj = $(this);
    //todo: This whole function should be abstracted out
    if(obj.hasClass("stepDone") || obj.hasClass("stepAvailable")) {
      CRM.Workflow.skipToStep(obj.data("order"));
    }
  });

  //todo: This should probably be moved up to where the button is created
  //Enable the custom Next button to move the flow along
  $("#SWNextButton").click(function(e) {
    var data = {"valid": true, "step": CRM.Workflow.currentStep};
    CRM.Workflow.trigger("Step:Validate", data);
    if (data.valid) {
      CRM.Workflow.CompleteCurrentStep();
    }
    return e.preventDefault();
  });
  $("#SWNextButton").hide();

  //Start at the beginning, the beginning is a very good place to start
  CRM.Workflow.currentStep = first(CRM.Workflow.steps);

  //Initiate the object that will load the pages
  //This must be below where we inject the ActionWindow
  var $swin = CRM.Workflow.$actionWindow.crmSnippet();

  //Bind to the load event to make small changes to the various Forms
  $swin.on("crmLoad", function(event) {
    if($(event.target).attr("id") === "ActionWindow") {

      //todo: Should this be here or handled in a callable, or via
      //custom step handlers
      CRM.Workflow.$actionWindow.find(".crm-form-submit").val(" " + CRM.Workflow.currentStep.next + " ");
      CRM.Workflow.$actionWindow.find("a.cancel").hide();

      //Trigger so that step handlers can do what they need.
      CRM.Workflow.trigger("Action:crmLoad", data);

      //Add a hidden field to trigger the backend that this is a workflow "form"
      CRM.Workflow.$actionWindow.find("form").append("<input type='hidden' name='SimpleWorkflowFormStep' value='" + CRM.Workflow.workflow.id + "_" + CRM.Workflow.currentStep.name + "' />");
    }
  })
  .on("crmBeforeLoad", function(e, data) {
    CRM.Workflow.trigger("Action:crmBeforeLoad", data);
  })
  .on("crmFormLoad", function(e, data) {
    CRM.Workflow.trigger("Action:crmFormLoad", data);
  })

  //When each form is submitted.
  .on("crmFormSuccess", function(e, data) {

    CRM.Workflow.trigger("Action:crmFormSuccess", data);
    //This keeps the page from "refreshing" and loading the workflow a second time into the div
    CRM.Workflow.$actionWindow.crmSnippet("destroy");
    CRM.Workflow.CompleteCurrentStep();
  });

  //Setup history functions So clicking back, takes you to previous tab
  window.onhashchange = function() {
    if (CRM.Workflow.loadingStep) {
      CRM.Workflow.loadingStep = false;
    } else {
      if (location.hash.length > 0) {
        var hashOrder = parseInt(location.hash.replace('#', ''), 10);
        CRM.Workflow.skipToStep(hashOrder);
      }
    }
  };

  //Load the first step when the page loads
  //Or the last step if we are returning.
  if (CRM.Workflow.returning) {

    //todo: Abstract this out into a render/display class or state machine
    //Mark all the steps as visited so it doesn't look like we are on the last step
    //with an incomplete workflow
    $('ol.WorkflowSteps li').addClass("completed");
    $('ol.WorkflowSteps li:last').removeClass("completed");


    //todo: Abstract this out into a render/display class or state machine
    //Loop through the tabs, and check to see if there are errors, and if so
    //add an error class to them.
    $.each(CRM.Workflow.steps, function(tab, data) {
      if ($(data.entity_id).find(".error").length) {
        $('ol.WorkflowSteps li[data-order='+tab+']').addClass("stepHasErrors");
      }
    });

    if(CRM.Workflow.allSelector) {
      $(CRM.Workflow.allSelector).hide();
    }

    CRM.Workflow.loadStep(CRM.Workflow.lastStep().order);
  }  else {
    if(CRM.Workflow.allSelector) {
      $(CRM.Workflow.allSelector).hide();
    }
    CRM.Workflow.loadStep(CRM.Workflow.currentStep.order);
  }

});

