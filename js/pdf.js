/*!
 * webtrees: online genealogy
 * Copyright (C) 2018 JustCarmen (http://justcarmen.nl)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

var moduleName = qstring('mod');

// Get querystring
function qstring(key, url) {
  'use strict';
  var KeysValues, KeyValue, i;
  if (url === null || url === undefined) {
    url = window.location.href;
  }
  KeysValues = url.split(/[\?&]+/);
  for (i = 0; i < KeysValues.length; i++) {
    KeyValue = KeysValues[i].split("=");
    if (KeyValue[0] === key) {
      return KeyValue[1];
    }
  }
}

subModuleName = "fancy_treeview_pdf";
rootId = qstring('rootid');
if (!rootId) {
  rootId = qstring('pid');
}

// convert page to pdf
$("#pdf").click(function(e) {
  e.preventDefault();
  $(".pdf-waiting-message").css('visibility', 'visible').hide().fadeIn("slow");
  createPDF();
});

function createPDF() {
  // initialize the content (stays in memory)
  var content = $('<div id="pdf-content">')

  content.append($("#fancy-treeview-page, #fancy-treeview-tab").clone());

  if ($("#btn-next").length > 0) {
    $(".fancy-treeview-content", content).load("module.php?mod=" + subModuleName + "&mod_action=full_pdf&rootid=" + rootId + "&ged=" + WT_GEDCOM, function() {
      getPDF(content);
    });
  } else {
    getPDF(content);
  }
}

function getPDF(content) {
  $.when(modifyContent(content)).then(function() {
    // Simplify the output
    var output = new Array;
    $("h2, .card-header, .parents, .children", content).each(function() {
      var string = $(this).wrap('<p>').parent().html();
      if ($(this).hasClass('parents')) {
        // change image path in text output in stead of in the dom to prevent a 404 error (only seen in firebug).
        // We need to replace the image src path with the server file path for mPDF to catch the image.
        var img_src = $(this).find("img").attr("src");
        var img_path = FTV_CACHE_DIR + $(this).find("img").data("cachefilename");
        if (typeof img_src !== 'undefined') {
          img_src = img_src.replace(/&/g, "&amp;");
          string = string.replace(img_src, img_path);
        }
      }
      output.push(string);
    });

    var html = '';
    for (var i = 0; i < output.length; i++) {
      html += output[i];
    }

    $.ajax({
      type: "POST",
      url: "module.php?mod=" + subModuleName + "&mod_action=write_pdf&rootid=" + rootId + "&title=" + FTV_PDF_PAGE_TITLE,
      data: {
        "pdfContent": html
      },
      success: function() {
        $(".pdf-waiting-message").fadeTo("slow", 0);
        window.location.href = "module.php?mod=" + subModuleName + "&mod_action=output_pdf&rootid=" + rootId + "&title=" + FTV_PDF_PAGE_TITLE + "&ged=" + WT_GEDCOM;
      }
    });
  });
}

function modifyContent(content) {
  // add a class for styling
  $(".parents", content).each(function() {
    $(".NAME:first", this).addClass("name-first");
  });

  // remove or unwrap all elements we do not need in pdf display
  // default webtrees images can not be converted to pdf
  $("img", content).each(function() {
    if (parseInt($(this).data("pdf")) === 0) {
      $(this).parent().remove();
    }
  });
  $(".generation.private", content).parents(".generation-block").remove();
  $(".generation-block", content).removeAttr("data-gen data-pids");
  $(".back-to-top", content).remove();
  $("a, span.SURN, span.date", content).contents().unwrap();
  $("a", content).remove(); //left-overs
//
  // mPDF doesn't support dir="auto", so set the textdirection to rtl if needed.
  if (textDirection === "rtl") {
    $("span[dir=auto]", content).each(function() {
      $(this).attr("dir", "rtl");
    });
  }

  // Turn blocks into a table for better display in pdf
  $(".family", content).each(function() {
    var obj = $(this);
    obj.find(".parents-data").replaceWith("<td class=\"parents-data\">" + obj.find(".parents-data").html());
    obj.find("img").wrap("<td class=\"image\" style=\"width:" + obj.find("img").width() + "px\">");
    obj.find(".parents").replaceWith("<table class=\"parents\"><tr>" + obj.find(".parents").html());
    obj.find(".child").each(function() {
      $(this).replaceWith("<tr><td>" + $(this).html());
    });
    obj.find(".children ol").each(function() {
      $(this).replaceWith('<table class="children-list">' + $(this).html());
    });
  });

  $(".private", content).each(function() {
    $(this).append("<table class=\"parents\"><tr><td>" + $(this).text());
  });

  //mPDF does not support multilevel ordered list, so we make our own
  $(".generation-block", content).each(function(index) {
    var main = (index + 1);
    $(this).find(".generation").each(function() {
      $(this).find(".family").each(function(index) {
        var i = (index + 1);
        if (textDirection === "rtl") {
          var dot = "";
        } else {
          var dot = ".";
        }
        $(this).find(".parents tr").prepend("<td class=\"index\">" + main + "." + i + dot + " </td>");
        $(this).find(".children tr").each(function(index) {
          $(this).prepend("<td class=\"index\">" + main + "." + i + "." + (index + 1) + dot + "  </td>");
        });
      });
    });
  });

}