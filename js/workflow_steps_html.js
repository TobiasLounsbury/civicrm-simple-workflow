function SFHTMLEnableWYSIWYG(selector) {
  //Setup the wysiwyg
  if(window.CKEDITOR) {
    var ck = CKEDITOR.replace(selector);
    if (ck) {
      CRM._.extend(ck.config, {width: '99%'});
    }
  }
}

function SimpleWorkflowStepAddHtml(template, index, data) {
  //Set defaults if we have no data.
  if(CRM.$.isEmptyObject(data) ) {
    if(window.CKEDITOR) {
      data.entity_id = CKEDITOR.instances.SWHTMLHtml.getData();
      CKEDITOR.instances.SWHTMLHtml.setData("");
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

  //Enable the WYSIWYG
  if(window.CKEDITOR) {
    SFHTMLEnableWYSIWYG(eid[0]);
  } else {
    setTimeout(function() {SFHTMLEnableWYSIWYG(eid[0]);}, 2500);
  }

  template.find(".entity_table").val("html");
  template.addClass("html");
  template.find(".entity_name").html("Free HTML");

  return true;
}

(function($, _) {

  SFHTMLEnableWYSIWYG("#SWHTMLHtml");

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