function RefreshSortable() {
  CRM.$( "#SortableDetails" ).sortable({
    placeholder: 'PlaceHolder',
    forcePlaceholderSize: true,
    handle: ".handle",
    update: function( event, ui ) {
      ReorderProfiles();
    }
  });
  CRM.$(".handle").disableSelection();
}

function ReorderProfiles() {
  CRM.$("#Data .Detail").each(function (i) {
    CRM.$(this).find(".order").val(i + 1);
  });
}
function DeleteStep(obj) {
  CRM.$(obj).parent().remove();
  ReorderProfiles();
}

CRM.$(function ($) {
  $("#SaveDetails").click(function(e) {


    CRM.api3('Workflow', 'save', {
      "data": $("#Data").serialize(),
      "wid": CRM.vars.SimpleWorkflow.wid
    }).done(function(result) {
      if (!result.is_error) {
        CRM.alert("All changes have been saved", "Saved", "success");
        window.location = CRM.url("civicrm/workflows");
      } else {
        CRM.alert("There was an error saving your changes.<br />" + result.error_message, "Error", "error");
      }
    });
  });

  $("#AddProfile").click(function(e) {
    var porder = $("#SortableDetails .Detail").length + 1;
    var pid = "0:Profile:" + porder;
    var pname = $(".select2-chosen").text().trim();
    if ($("#Profile_"+$("#ProfileSelector").val()).length == 0) {
      $("#SortableDetails").append("<div id='Profile_" + $("#ProfileSelector").val() + "' class='Profile Detail'>"+
      "<span class='handle'>ↈ</span>" +
      "<input type='hidden' name='data[" + pid + "][order]' value='" + porder + "' class='order' />" +
      "<input type='hidden' name='data[" + pid + "][entity_table]' value='Profile' />" +
      "<input type='hidden' name='data[" + pid + "][entity_id]' value='" + $("#ProfileSelector").val() + "' />" +
      "<label class='entity_name'>" + pname + "</label>" +
      "<span class='DeleteProfile' onclick='DeleteStep(this)'>delete</span>" +
      "<br />" +
      "<label class='leftmost'>Breadcrumb:</label> <input name='data[" + pid + "][breadcrumb]' value='"+pname+"' />" +
      "<label>Button Text:</label> <input name='data[" + pid + "][next]' value='Next' />" +
      "<label>Title:</label> <input name='data[" + pid + "][title]' size='40' value='" + pname + "' />" +
      "</div>");
      RefreshSortable();
      ReorderProfiles();
    }
  });
  $("#AddPage").click(function(e) {
    var porder = $("#SortableDetails .Detail").length + 1;
    var pid = "0:Page:" + porder;
    var pname = $("#PageSelector option:selected").text().trim();

    if ($("#Data .Page").length == 0) {
      if(!$("#Data hr").length) {
        $("#Data").append("<hr />");
      }
      $("#Data").append("<div id='Page_" + $("#PageSelector option:selected").val() + "' class='Page Detail'>"+
      "<input type='hidden' name='data[" + pid + "][order]' value='" + porder + "' class='order' />" +
      "<input type='hidden' name='data[" + pid + "][entity_table]' value='Page' />" +
      "<input type='hidden' name='data[" + pid + "][entity_id]' value='" + $("#PageSelector option:selected").val() + "' />" +
      "<label class='entity_name'>" + pname + "</label>" +
      "<span class='DeleteProfile' onclick='DeleteStep(this)'>delete</span>" +
      "<br />" +
      "<label class='leftmost'>Breadcrumb:</label> <input name='data[" + pid + "][breadcrumb]' value='"+pname+"' />" +
      "<label>Button Text:</label> <input name='data[" + pid + "][next]' value='Next' />" +
      "<label>Title:</label> <input name='data[" + pid + "][title]' size='40' value='" + pname + "' />" +
      "</div>");

      RefreshSortable();
      ReorderProfiles();
    }
  });
  $("#AddJQuery").click(function(e) {
    var porder = $("#SortableDetails .Detail").length + 1;
    var jid = $("#SortableDetails .jQuery").length + 1;
    var pid = "0:jQuery:" + porder;
    var newpath = $("#dom-id").val().trim();
    $("#SortableDetails").append("<div id='jQuery_" + jid + "' class='jQuery Detail'>"+
    "<span class='handle'>ↈ</span>" +
    "<input type='hidden' name='data[" + pid + "][order]' value='" + porder + "' class='order' />" +
    "<input type='hidden' name='data[" + pid + "][entity_table]' value='jQuery' />" +

    "<label class='path'>Elements:</label> <input class='jQuery-Selector' name='data[" + pid + "][entity_id]' value='" + newpath + "' />" +


    "<span class='DeleteProfile' onclick='DeleteStep(this)'>delete</span>" +
    "<br />" +
    "<label class='leftmost'>Breadcrumb:</label> <input name='data[" + pid + "][breadcrumb]' value='"+$("#dom-breadcrumb").val().trim()+"' />" +
    "<label>Button Text:</label> <input name='data[" + pid + "][next]' value='Next' />" +
    "<label>Title:</label> <input name='data[" + pid + "][title]' size='40' value='" + $("#dom-breadcrumb").val().trim()+ "' />" +
    "</div>");

    $("#dom-id").val("");
    $("#dom-breadcrumb").val("");
    RefreshSortable();
    ReorderProfiles();

  });
  RefreshSortable();
});

