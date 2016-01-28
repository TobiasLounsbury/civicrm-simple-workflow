CRM.$(function ($) {

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
      "<span class='DeleteProfile' onclick='SWDeleteStep(this)'>delete</span>" +
      "<br />" +
      "<label class='leftmost'>Breadcrumb:</label> <input name='data[" + pid + "][breadcrumb]' value='"+pname+"' />" +
      "<label>Button Text:</label> <input name='data[" + pid + "][next]' value='Next' />" +
      "<label>Title:</label> <input name='data[" + pid + "][title]' size='40' value='" + pname + "' />" +
      "</div>");
      SWRefreshSortable();
      SWReorderProfiles();
    }
  });

});