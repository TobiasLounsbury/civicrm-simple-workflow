function SimpleWorkflowStepAddHtml(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    if(window.CKEDITOR) {
      data.entity_id = CKEDITOR.instances.SWHTMLHtml.getData();
    } else {
      data.entity_id = CRM.$("#SWHTMLHtml").val();
    }
    data.breadcrumb = CRM.$("#SWHTMLBreadcrumb").val();
    data.name = data.breadcrumb.toLowerCase().replace(" ", "_");
    data.title = data.breadcrumb;
    data.next = "Next";
  }

  template.find(".crm-simple-workflow-step-details").prepend(
    CRM.$("#SimpleWorkflowTypeTemplateHtml").html()
  );

  var eid = template.find(".entity_id");
  eid.attr("name", eid.attr("name").replace("#ORDER#", index));

  //Enable the WYSIWYG if we can.
  if(window.CKEDITOR) {
    var ck = CKEDITOR.replace(eid[0]);
    if (ck) {
      CRM._.extend(ck.config, {width: '100%'});
    }
  }


  template.find(".entity_table").val("html");
  template.addClass("html");
  template.find(".entity_name").html("Free HTML");

  //Clear the Old Editor
  if(window.CKEDITOR) {
    CKEDITOR.instances.SWHTMLHtml.setData("");
  }

  return true;
}

(function($, _) {

  //Setup the Add Step wysiwyg
  if(window.CKEDITOR) {
    var ck = CKEDITOR.replace($("#SWHTMLHtml")[0]);
    if (ck) {
      _.extend(ck.config, {width: '99%'});
    }
  }

  //Make sure the textareas are updated before save
  $("#Data").on("before:save", function(e) {
    if(window.CKEDITOR) {
      for (var i in CKEDITOR.instances) {
        if (CKEDITOR.instances.hasOwnProperty(i)) {
          CKEDITOR.instances[i].updateElement();
        }
      }
    }
  });


})(CRM.$, CRM._);