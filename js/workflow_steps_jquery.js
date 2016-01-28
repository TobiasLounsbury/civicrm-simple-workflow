CRM.$(function ($) {
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


    "<span class='DeleteStep'>delete</span>" +
    "<br />" +
    "<label class='leftmost'>Breadcrumb:</label> <input name='data[" + pid + "][breadcrumb]' value='"+$("#dom-breadcrumb").val().trim()+"' />" +
    "<label>Button Text:</label> <input name='data[" + pid + "][next]' value='Next' />" +
    "<label>Title:</label> <input name='data[" + pid + "][title]' size='40' value='" + $("#dom-breadcrumb").val().trim()+ "' />" +
    "</div>");

    $("#dom-id").val("");
    $("#dom-breadcrumb").val("");
    SWRefreshSortable();
    SWReorderProfiles();

  });
});