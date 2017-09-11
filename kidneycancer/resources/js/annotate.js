// adopted from http://www.williammalone.com/articles/create-html5-canvas-javascript-drawing-app/

$(document).on("click", "a[href='#']", function () {
  return false;
});

$(document).ready(function() {

  // Handle scrolling
  var scrollval = imgdata.scrollval;
	var scrollvalx = imgdata.scrollvalx;
	if (!scrollvalx) {
		scrollvalx = 0;
	}
	var scrollvaly = imgdata.scrollvaly;
	if (!scrollvaly) {
		scrollvaly = 0;
	}
  // Set image bar to correct scroll location
  $("div#images_slider").scrollLeft(scrollval);
	$("div#annotator_cont").scrollLeft(scrollvalx)
	$("div#annotator_cont").scrollTop(scrollvaly)

  var imagewidth = imgdata.scale*imgdata.width;
  var imageheight = imgdata.scale*imgdata.height;
  var brushwidth = 10;
  var drawn = 0;
  var relevantData = new Array();

  var style = imgdata.style;
  var style_hex = "";
  var style_letter = "";
  if (style == 0) {
    style_letter = "r";
    style_hex = "#f00000"; // red
  }
  else if (style == 1) {
    style_letter = "g";
    style_hex = "#08a404"; // green
  }
  else if (style == 2) {
    style_letter = "b";
    style_hex = "#1544f3"; // blue
  }
  else if (style == 3) {
    style_letter = "y";
    style_hex = "#f3bc15"; // yellow
  }
  //Canvas
  var clickX = new Array();
  var clickY = new Array();
  var clickDrag = new Array();
  var clickWeight = new Array();

  var unClickX = new Array();
  var unClickY = new Array();
  var unClickDrag = new Array();
  var unClickWeight = new Array();


  var paint;
  //get context
  var context = document.getElementById('annotator').getContext("2d");
  context.imageSmoothingEnabled=false;

  var single_id = context.createImageData(1,1);


  function change_style(new_style) {
    style = new_style;
    style_hex = "";
    if (style == 0) {
      style_hex = "#f00000"; // red
    }
    else if (style == 1) {
      style_hex = "#08a404"; // green
    }
    else if (style == 2) {
      style_hex = "#1544f3"; // blue
    }
    else if (style == 3) {
      style_hex = "#f3bc15"; // yellow
    }
  }

  function addClick(x, y, dragging, width) {
    //console.log("click added: " + x + " " + y + " " + dragging + " " + width);
    clickX.push(x);
    clickY.push(y);
    clickDrag.push(dragging);
    clickWeight.push(width);
    unClickX = new Array();
    unClickY = new Array();
    unClickDrag = new Array();
    unClickWeight = new Array();
  }

  function undo() {
    //console.log("undo called");
    if (clickX.length > 0) {
      dragging = clickDrag[clickDrag.length - 1];
      //console.log(dragging);
      if (dragging) {
        while(dragging) {
          dragging = false;
            unClickX.push(clickX.pop());
            unClickY.push(clickY.pop());
            unClickDrag.push(clickDrag.pop());
            unClickWeight.push(clickWeight.pop());
            dragging = unClickDrag[unClickDrag.length -1];
        }
      }
      else {
        unClickX.push(clickX.pop());
        unClickY.push(clickY.pop());
        unClickDrag.push(clickDrag.pop());
        unClickWeight.push(clickWeight.pop());
      }
      drawn = 0;
      redraw();
    }
    else {
      //console.log("nothing to undo");
    }
  }

  function redo() {
    if (unClickX.length > 0) {

      clickX.push(unClickX.pop());
      clickY.push(unClickY.pop());
      clickDrag.push(unClickDrag.pop());
      clickWeight.push(unClickWeight.pop());
      dragging = unClickDrag[unClickDrag.length -1];
      while (dragging && (unClickX.length > 0)) {
        clickX.push(unClickX.pop());
        clickY.push(unClickY.pop());
        clickDrag.push(unClickDrag.pop());
        clickWeight.push(unClickWeight.pop());
        dragging = unClickDrag[unClickDrag.length -1];
      }
      redraw();
    }
  }

  function redraw() {
    //console.log(clickWeight);
    //clear the canvas
    if (drawn == 0) {
      context.clearRect(0, 0, context.canvas.width, context.canvas.height);
    }
    context.strokeStyle = style_hex;

    context.lineJoin = "round";
    context.lineWidth = brushwidth;

    for (var i = drawn; i < clickX.length; i++) {
      if (clickWeight[i] == 0) {
        //console.log("redrawing bucket");
        myImageData = context.getImageData(0,0,imagewidth,imageheight);
        relevantData = getRelevantData(myImageData.data);
        bucketfill(clickX[i], clickY[i], relevantData);
      }
      else {
        //console.log("redrawing line");
        context.beginPath();
        if (clickDrag[i] && i) {
          context.moveTo(clickX[i-1], clickY[i-1]);
        }
        else {
          context.moveTo(clickX[i]-1, clickY[i]);
        }
        context.lineTo(clickX[i], clickY[i]);
        context.closePath();
        context.stroke();
      }
      drawn  ++;
    }

  }

  function drawpixel(x,y) {
    if (style == 0) {
      single_id.data[0] = 240;
      single_id.data[1] = 0;
      single_id.data[2] = 0;
      single_id.data[3] = 255;
      context.putImageData(single_id, x, y, 0, 0, 1, 1);
      relevantData[x][y] = 240;
    }
    else if (style == 1) {
      single_id.data[0] = 8;
      single_id.data[1] = 164;
      single_id.data[2] = 4;
      single_id.data[3] = 255;
      context.putImageData(single_id, x, y, 0, 0, 1, 1);
      relevantData[x][y] = 8;
    }
    else if (style == 2) {
      single_id.data[0] = 20;
      single_id.data[1] = 68;
      single_id.data[2] = 243;
      single_id.data[3] = 255;
      context.putImageData(single_id, x, y, 0, 0, 1, 1);
      relevantData[x][y] = 20;
    }
    else if (style == 3) {
      single_id.data[0] = 243;
      single_id.data[1] = 188;
      single_id.data[2] = 21;
      single_id.data[3] = 255;
      context.putImageData(single_id, x, y, 0, 0, 1, 1);
      relevantData[x][y] = 243;
    }
  }

  // http://stackoverflow.com/questions/3115982/how-to-check-if-two-arrays-are-equal-with-javascript
  function arraysEqual(a, b) {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (a.length != b.length) return false;
    for (var i = 0; i < a.length; ++i) {
      if (a[i] !== b[i]) return false;
    }
    return true;
  }

  function open_and_valid(x,y,data,pixelstack) {
    /*
    for (i = 0; i < pixelstack.length; i++) {
      if (arraysEqual([x, y],pixelstack[i])) {
        return false;
      }
    }
    */
    if (style == 0) {
      threshold = 240;
    }
    else if (style == 1) {
      threshold = 8;
    }
    else if (style == 2) {
      threshold = 20;
    }
    else if (style == 3) {
      threshold = 243;
    }
    /*console.log(threshold);*/
    return (((x>=0)&&(y>=0)) && ((x<imagewidth)&&(y<imageheight)) && (relevantData[x][y] < threshold) && (data[x][y] < threshold));
  }

  function bucketfill(x,y,data) {
    pixelstack = new Array();
    pixelstack.push([x, y]);
    while (pixelstack.length > 0) {
      pt = pixelstack.pop();
      if (open_and_valid(pt[0],pt[1],data,pixelstack)) {
        drawpixel(pt[0],pt[1]);
        pixelstack.push([pt[0]+1,pt[1]]);
        pixelstack.push([pt[0]-1,pt[1]]);
        pixelstack.push([pt[0],pt[1]+1]);
        pixelstack.push([pt[0],pt[1]-1]);
      }
    }
  }

  function getRelevantData(data) {
    retval = [];
    for (i = 0; i < imagewidth; i++) {
      retval.push([]);
      for (j = 0; j < imageheight; j++) {
        retval[i].push(data[((j*(imagewidth*4)) + (i*4))])
      }
    }
    return retval;
  }
  // handle touch events
  // DO NOT CHANGE THIS FUNCTION WITHOUT ALSO CHANGING THE ONE BELOW
  $(document).on('touchstart', function (e) {
    var self = document.getElementById("annotator");
    if (self == e.target) {
      var mouseX = e.originalEvent.touches[0].pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft();
      var mouseY = e.originalEvent.touches[0].pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop();

      if (brushwidth > 0) {
        paint = true;
        addClick(e.originalEvent.touches[0].pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.originalEvent.touches[0].pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), false, brushwidth);
        redraw();
      }
      else {
        //console.log("bucket fill at " + mouseX + ', ' + mouseY);
        addClick(e.originalEvent.touches[0].pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.originalEvent.touches[0].pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), false, brushwidth);
        myImageData = context.getImageData(0,0,imagewidth,imageheight);
        relevantData = getRelevantData(myImageData.data);
        bucketfill(mouseX, mouseY, relevantData);
        //console.log(myImageData.data.length);
      }
    }
  });

  //detect mousemove in canvas element
  //draw at new position
  $(document).on('touchmove', function (e) {
    var self = document.getElementById("annotator");
    if (self == e.target) {
      //console.log('handle move');
      if (paint && (brushwidth > 0)) {
        addClick(e.originalEvent.touches[0].pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.originalEvent.touches[0].pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), true, brushwidth);
        redraw();
      }
    }
  });

  //detect mouseup in canvas element
  //stop painting
  $(document).on('touchend', function (e) {
    if (self == e.target) {
      //console.log('handle end');
      paint = false;
    }
  });

  //bind touch events to canvas
  //var touchel = document.getElementById("annotator");
  //touchel.addEventListener("touchstart", handleStart, false);
  //touchel.addEventListener("touchend", handleEnd, false);
  //touchel.addEventListener("touchmove", handleMove, false);

  //detect mousedown in canvas element
  //start painting
  //DO NOT CHANGE THIS FUNCTION WITHOUT ALSO CHANGING THE ONE ABOVE
  $('canvas#annotator').mousedown(function (e) {
    //console.log('mousedown');
    var mouseX = e.pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft();
    var mouseY = e.pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop();

    if ((e.which == 1) && (brushwidth > 0)) {
      paint = true;
      addClick(e.pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), false, brushwidth);
      redraw();

      //detect mousemove in canvas element
      //draw at new position
      $('canvas#annotator').mousemove(function (e) {
        if (paint && (brushwidth > 0)) {
          addClick(e.pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), true, brushwidth);
          redraw();
        }
      });

      //detect mouseup in canvas element
      //stop painting
      $('canvas#annotator').mouseup(function (e) {
        paint = false;
      });

      //detect mouse leaving canvase element
      //stop painting
      $('canvas#annotator').mouseleave(function (e) {
        paint = false;
      });
    }
    else {
      //console.log("bucket fill at " + mouseX + ', ' + mouseY);
      addClick(e.pageX - $("div#annotator_cont").offset().left + $("div#annotator_cont").scrollLeft(), e.pageY - $("div#annotator_cont").offset().top + $("div#annotator_cont").scrollTop(), false, 0);
      // get status of all current pixels
      myImageData = context.getImageData(0,0,imagewidth,imageheight);
      relevantData = getRelevantData(myImageData.data);
      bucketfill(mouseX, mouseY, relevantData);
      //console.log(myImageData.data.length);
      return false;
    }
  });

  //Handle island events
  function get_brush_width(id) {
    return parseInt(id.substring(4,(len - 2)));
  }

  function redraw_island() {
    cursel = $("a.tool.sel").attr('id');
    if (typeof(cursel) != "undefined") {
      if (brushwidth != get_brush_width(cursel)) {
        $('a#' + cursel).removeClass('sel');
        $('a#icon' + brushwidth + '_a').addClass('sel');
        //console.log('brushwidth' + brushwidth);
        $('div#annotator_cont').removeClass();
        $('div#annotator_cont').addClass('brushwidth' + brushwidth);
      }
    }
    else {
      $('a#icon' + brushwidth + '_a').addClass('sel');
      $('div#annotator_cont').removeClass();
      $('div#annotator_cont').addClass('brushwidth' + brushwidth);
    }
  }
  redraw_island();

  //click to change tools
  $(document).on("click", "a.tool", function (e) {
    var id = $(this).attr('id');
    len = id.length;
    if (len > 9) {
      brushwidth = 0;
      redraw_island();
      //console.log("bucket");
    }
    else {
      brushwidth = get_brush_width(id);
      redraw_island();
    }
  });

	function add_scrolls(newloc) {
		newloc = newloc + "&scrollval=" + scrollval + "&scrollvalx=" + scrollvalx + "&scrollvaly=" + scrollvaly;
		return newloc;
	}

  function sethiddenlink(newloc) {
		newloc = add_scrolls(newloc);
		console.log(newloc);
    window.location.href=newloc;
  }

  function sethiddenrefresh(scale = -1, feature = -1, patient = "", image = -1) {
    newloc = window.location.protocol + "//" + window.location.hostname + window.location.pathname + "?";
    if (scale == -1) {
      scale = imgdata.scale;
    }
    newloc = newloc + "scale=" + scale;
    if (feature == -1) {
      feature = imgdata.feature;
    }
    newloc = newloc + "&feature=" + feature;
    if (patient == "") {
      patient = imgdata.id;
    }
    newloc = newloc + "&patient=" + patient;
    if (image == -1) {
      image = imgdata.image_ind;
    }
    newloc = newloc + "&image=" + image;
    sethiddenlink(newloc);
  }

  function zoom(direction) {
    //console.log("zoom " + direction);
    if (direction == "in") {
      if (imgdata.scale < 8) {
        newlocation = window.location.protocol + "//" + window.location.hostname + window.location.pathname + "?scale=" + (imgdata.scale+1);
        //console.log("in to " + newlocation);
        //sethiddenlink(newlocation);
        sethiddenrefresh(imgdata.scale+1);
      }
      else {
        //console.log("Max zoom reached");
        return 1;
      }
    }
    else {
      if (imgdata.scale > 1) {
        newlocation = window.location.protocol + "//" + window.location.hostname + window.location.pathname + "?scale=" + (imgdata.scale-1);
        //console.log("out to " + newlocation);
        //sethiddenlink(newlocation);
        sethiddenrefresh(imgdata.scale-1);
      }
      else {
        //console.log("Min zoom reached");
        return 1;
      }
    }
    return 0;
  }


  // Zoom buttons
  $(document).on("click", "a.zoom_icon", function() {
    id = $(this).attr("id");
    if (id == "zoom_in") {
      zoom("in");
    }
    else if (id == "zoom_out") {
      zoom("out");
    }
  });

  //keyboard shortcuts
  var map = {}
  onkeydown = onkeyup = function (e) {
    e = e || event;
    map[e.keyCode] = e.type == 'keydown';
    map[17] = e.ctrlKey;
    map[16] = e.shiftKey;
    if (map[17] || map[91] || map[93]) { /* ctrl */
      if (map[187] || map[107]) { /* plus */
        //console.log("zoom in");
        zoom("in");
        return false;
      }
      else if (map[189] || map[109]) { /* minus */
        //console.log("zoom out");
        zoom("out");
        return false;
      }
      else if (map[90]) {
        if (map[16]) { /* redo */
          redo();
        }
        else { /* undo */
          undo();
        }
      }
    }
  }

  function is_finished(feature) {
    return (($("button#submit_left_kidney").hasClass("comp") || feature == "left_kidney") &&
            ($("button#submit_right_kidney").hasClass("comp") || feature == "right_kidney") &&
            ($("button#submit_left_cancer").hasClass("comp") || feature == "left_cancer") &&
            ($("button#submit_right_cancer").hasClass("comp") || feature == "right_cancer") &&
            ($("button#submit_aorta").hasClass("comp") || feature == "aorta"));
  }

	$(document).on("click", "a#nextbutton", function () {
		sethiddenlink($(this).attr("href"));
		return false;
	});
	$(document).on("click", "a#prevbutton", function () {
		sethiddenlink($(this).attr("href"));
		return false;
	});

  $(document).on("click","button.submit_feature", function () {
    feature = $(this).attr("id").substring(7);
    var tmpcanvas = document.getElementById("annotator");
    var dataURL = tmpcanvas.toDataURL("image/png");
    data = {
      imgdat:dataURL,
      patient:imgdata.id,
      image:imgdata.image_name,
      image_ind:imgdata.image_ind,
      feature:feature,
      user:imgdata.user,
      height:imgdata.height,
      width:imgdata.width
    };
    $.post('/annotate/submit.php', data).done(function(response){
      resp = $.parseJSON(response);
      if (resp.error == "") {
        console.log("successfully submitted");
        console.log(response);
        console.log("redirecting to next feature");
        if (imgdata.feature == 3) {
          next_feature = 0;
        }
        else {
          next_feature = imgdata.feature + 1;
        }
        if (is_finished(feature)) {
          image = imgdata.image_ind + 1;
        }
        else {
          image = imgdata.image_ind;
        }
        sethiddenlink("/p/annotate.php?feature=" + next_feature +
                                   "&patient=" + imgdata.id +
                                   "&image=" + image +
                                   "&scale=" + imgdata.scale);
      }
      else {
        console.log("failed to submit");
        console.log(response);
        console.log("sent: ");
        console.log(data);
      }
      return false;
    });
  });
  $('div#page-wrapper').removeClass("r g b y");
  $('div#page-wrapper').addClass(style_letter);

  console.log("document ready");
  console.log(imagewidth);

  new_height = 700+700;
  new_height_str = new_height.toString() + "px";
  $("#page-wrapper").css("height",new_height_str);
  $("#page-wrapper").css("min-width",1050 + "px");
  console.log(imageheight);
  $("div.finished_annotation").css("top",(new_height-400) + "px");
	$(document).scrollTop(80);


  // Handle scroll moving
  $("div#images_slider").on("scroll", function() {
    scrollval = $("div#images_slider").scrollLeft();
  });
	$("div#annotator_cont").on("scroll", function() {
		scrollvalx = $("div#annotator_cont").scrollLeft();
		scrollvaly = $("div#annotator_cont").scrollTop();
	});

  $("a.patient_image").on("click", function() {
		sethiddenlink(this.href);
    return false;
  });

});
