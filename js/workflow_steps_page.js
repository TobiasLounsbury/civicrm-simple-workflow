CRM.$(function ($) {
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
      "<span class='DeleteStep'>delete</span>" +
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