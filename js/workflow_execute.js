CRM.$(function ($) {
  //Utility function when we don't know if steps will be zero indexed.
  function first(p){for(var i in p)return p[i];}

  function get_next_step() {
    return $("ol.WorkflowSteps .stepTodo:first").data("order");
  }

  function load_step(order) {

    //unLoad the old Step
    $("body").trigger("SimpleWorkflow-unloadStep", CRM.Workflow.steps[CRM.Workflow.stepIndex]);

    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').removeClass("stepActive");
    if ($('ol.WorkflowSteps li[data-order='+currentStep.order+']').hasClass("completed")) {
      $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepDone");
    } else {
      $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepTodo");
      $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepAvailable");
    }

    //Set the currentStep
    currentStep = CRM.Workflow.steps[order];
    $("body").trigger("SimpleWorkflow-loadStep", currentStep);


    if (currentStep.title && currentStep.title.length) {
      $("#WorkflowTitle legend").text(currentStep.title);
      $("#WorkflowTitle").show();
    } else {
      $("#WorkflowTitle").hide();
    }

    $("#PreMessage").html(currentStep.pre_message);
    $("#PostMessage").html(currentStep.post_message);

    //Set the new form step to active
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepDone");
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepTodo");
    $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepAvailable");
    $('ol.WorkflowSteps li[data-order='+order+']').addClass("stepActive");

    //Set the window Hash so it can be recalled on backbutton press
    location.hash = order;
    CRM.Workflow.stepIndex = order;
  }

  function inject_workflow_elements() {
    $("#Main").before('<div id="ActionWindow"></div>');
    $("#ActionWindow").before('<ol class="WorkflowSteps" id="WorkflowSteps"></ol>');
    $("#ActionWindow").before('<div id="PreMessage"></div>');
    $("#crm-submit-buttons").before('<div id="PostMessage"></div>');


    if (CRM.Workflow.returning) {
      var liclass = "stepDone";
      $("#ActionWindow").hide();
    } else {
      var liclass = "stepTodo";
      $(".crm-contribution-main-form-block").hide();
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
    $(".crm-contribution-main-form-block").append("<a href='#' id='jQueryNext' class='button'><span> Next </span></a><div class='clear'></div>");
    $("#jQueryNext").hide();

    //add wrapper to billing payment block so that it can be controlled.
    $("#billing-payment-block").wrap('<div class="WorkflowBillingBlock" id="WorkflowBillingBlock"></div>');

    //add wrapper to submit button so that it can be controlled.
    $("#crm-submit-buttons").wrap('<div class="WorkflowSubmit" id="WorkflowSubmit"></div>');

  }



  //run
  //Check the method we are using and inject elements when needed
  if (CRM.Workflow.method == "inject") {
    inject_workflow_elements();
  }

  //Set the breadcrumb width
  $("ol.WorkflowSteps li").css("width", CRM.Workflow.breadcrumWidth + "%");

  //Allow each step in the breadcrumb to be clickable
  $("ol.WorkflowSteps li").click(function(e) {
    var obj = $(e.target).parent();

    if(obj.hasClass("stepDone") || obj.hasClass("stepAvailable")) {

      //If we are injecting into a page, but moving to the page content
      if (obj.hasClass("Page") && CRM.Workflow.method == "inject") {
        //Destroy the snippet so we don't get any overlapping behavior
        $("#ActionWindow").crmSnippet("destroy");
      }

      load_step(obj.data("order"));
    }
  });

  //Enable the jQuery Next button to move the flow along
  $("#jQueryNext").click(function(e) {
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').removeClass("stepActive");
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepDone");
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("completed");

    //Load the next step in the workflow
    load_step(get_next_step());

    return e.preventDefault();
  });

  var currentStep = first(CRM.Workflow.steps);

  //Initiate the object that will load the pages
  //This must be below where we inject the ActionWindow
  var swin = $("#ActionWindow").crmSnippet();

  //Bind to the load event to make small changes to the various Forms
  swin.on("crmLoad", function(event) {
    if($(event.target).attr("id") == "ActionWindow") {
      $("#ActionWindow .crm-form-submit").val(" " + currentStep.next + " ");
      $("#ActionWindow a.cancel").hide();
      var stepfname = window['SimpleWorkflow_Step_' + currentStep.name + "_Load"];
      if (typeof stepfname == 'function') {
        stepfname();
      }
    }
  });

  swin.on("crmBeforeLoad", function(e, data) {

  });

  //When each form is submitted.
  swin.on("crmFormSuccess", function(e, data) {
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').removeClass("stepActive");
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepDone");
    $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("completed");

    //This keeps the page from "refreshing" and loading the workflow a second time into the div
    $("#ActionWindow").crmSnippet("destroy");

    //Load the next step in the workflow
    load_step(get_next_step());
  });

  //Setup history functions So clicking back, takes you to previous tab
  window.onhashchange = function() {
    if (location.hash.length > 0) {
      var hashOrder = parseInt(location.hash.replace('#',''),10);
      load_step(hashOrder);
    }
  };

  //Load the first step when the page loads
  //Or the last step if we are returning.
  if (CRM.Workflow.returning) {

    //Mark all the steps as visited so it doesn't look like we are on the last step
    //with an incomplete workflow
    $('ol.WorkflowSteps li').addClass("completed");
    $('ol.WorkflowSteps li:last').removeClass("completed");


    //Loop through the tabs, and check to see if there are errors, and if so
    //add an error class to them.
    $.each(CRM.Workflow.steps, function(tab, data) {
      if ($(data.entity_id).find(".error").length) {
        $('ol.WorkflowSteps li[data-order='+tab+']').addClass("stepHasErrors");
      }
    });

    load_step(CRM.Workflow.lastStep);
  }  else {
    load_step(currentStep.order);
  }

});

