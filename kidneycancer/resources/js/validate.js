// http://stackoverflow.com/questions/979975/how-to-get-the-value-from-the-get-parameters
var QueryString = function () {
  // This function is anonymous, is executed immediately and
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  }
  return query_string;
}();
function get_next_url() {
  next_patient = QueryString.patient;
  next_feature = parseInt(QueryString.feature) + 1;
  next_image = parseInt(QueryString.image);
  if (parseInt(QueryString.feature) == 4) {
    next_image = next_image + 1;
    next_feature = 0;
  }
  if (next_image >= imgdata.num_images) {
    return "";
  }
  var next_query = "/p/validate.php?patient=" + next_patient + "&image=" + next_image + "&feature=" + next_feature;
  return next_query;
}
$(document).ready(function() {
  patient_id = imgdata.patient.id;
  $(document).on("click","a#val_button", function() {
    console.log("Validate button pressed.");
    $.post("/annotate/validate_api.php",{image:imgdata.image,patient:patient_id,feature:imgdata.feature,validate:"1",notes:""})
      .done(function(data) {
        if (data == "0") {
          window.location.href = get_next_url();
        }
        else {
          alert("An error occurred. Please report this bug.");
        }
      });
  });
  $(document).on("click","a#flag_button", function() {
    flg_notes = $("input#flag_notes").val();
    console.log("Flag button pressed with flag notes: " + flg_notes);
    $.post("/annotate/validate_api.php",{image:imgdata.image,patient:patient_id,feature:imgdata.feature,validate:"0",notes:flg_notes})
      .done(function(data) {
        if (data == "0") {
          window.location.href = get_next_url();
        }
        else {
          alert("An error occurred. Please report this bug.");
        }
      });
  });
});
