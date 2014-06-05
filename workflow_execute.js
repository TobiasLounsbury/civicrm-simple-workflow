CRM.$(function ($) {
    //Utility function when we don't know if steps will be zero indexed.
    function first(p){for(var i in p)return p[i];}

    function get_next_step() {
        return $("ol.WorkflowSteps .stepTodo:first").data("order");
    }

    function load_step(order) {

        //Set the currentStep
        currentStep = CRM.Workflow.steps[order];

        if (currentStep.entity_table == "Profile") {
            if (CRM.Workflow.method == "inject") {
                $(".crm-contribution-main-form-block").hide();
                $("#ActionWindow").show();
            }
            var aw = CRM.loadForm(CRM.url("civicrm/profile/edit", {gid: currentStep.entity_id, reset: 1}), {target:"#ActionWindow", dialog: false, autoClose:false});
        }

        if (currentStep.entity_table == "Page") {

            //Hide the workflow pane
            $("#ActionWindow").hide();
            $("jQueryNext").hide()
            //Show the contribution form we hid earlier
            $(".crm-contribution-main-form-block").show();
        }

        if (currentStep.entity_table == "jQuery") {

            //Hide the workflow pane
            $("#ActionWindow").hide();

            //Hide all of the extra elements we don't want to see in this step.
            $(CRM.Workflow.allSelector).hide();

            //Hide or show the Next button depending on if we are on the last page or not
            if (parseInt(currentStep.order) == parseInt(CRM.Workflow.lastStep)) {
                $("#jQueryNext").hide()
            } else {
                $("#jQueryNext span").text(" " + currentStep.next + " ");
                $("#jQueryNext").show()
            }
            //Show the Elements that make up this step
            $(currentStep.entity_id).show();

            //Show the contribution form we hid earlier
            $(".crm-contribution-main-form-block").show();
        }

        if (currentStep.options && currentStep.options.title) {
            $("#WorkflowTitle legend").text(currentStep.options.title);
            $("#WorkflowTitle").show();
        } else {
            $("#WorkflowTitle").hide();
        }

        //Set the new form step to active
        $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepDone");
        $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepTodo");
        $('ol.WorkflowSteps li[data-order='+order+']').removeClass("stepAvailable");
        $('ol.WorkflowSteps li[data-order='+order+']').addClass("stepActive");

    }

    function inject_workflow_elements() {
        $("#Main").before('<div id="ActionWindow"></div>');
        $("#ActionWindow").before('<ol class="WorkflowSteps" id="WorkflowSteps"></ol>');
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
        $(".crm-contribution-main-form-block").append("<a href='#' id='jQueryNext' class='button'><span> Next </span></a>");
        $("#jQueryNext").hide();

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

            $('ol.WorkflowSteps li[data-order='+currentStep.order+']').removeClass("stepActive");
            if ($('ol.WorkflowSteps li[data-order='+currentStep.order+']').hasClass("completed")) {
                $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepDone");
            } else {
                $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepTodo");
                $('ol.WorkflowSteps li[data-order='+currentStep.order+']').addClass("stepAvailable");
            }

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
    swin.on("crmLoad", function(data) {
        console.log($("#ActionWindow .form-submit").val(" " + currentStep.next + " "));
        $.getScript(CRM.config.resourceBase + "workflow/workflow_" + currentStep.entity_table.toLowerCase() + "_" + currentStep.entity_id + ".js");
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

    //Load the first step when the page loads
    //Or the last step if we are returning.
    if (CRM.Workflow.returning) {
        load_step(CRM.Workflow.lastStep);
    }  else {
        load_step(currentStep.order);
    }

});
