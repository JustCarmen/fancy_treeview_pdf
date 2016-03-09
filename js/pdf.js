/*
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * Copyright (C) 2016 JustCarmen
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* global WT_CSRF_TOKEN, FTV_PDF_ModuleName, FTV_CACHE_DIR, RootID, PageTitle, textDirection */

function qstring(key, url) {
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

// convert page to pdf
jQuery("#pdf").click(function() {
	jQuery(".pdf-waiting-message").fadeIn("slow");
	createPDF();
});

function createPDF() {
	// initialize the content (stays in memory)
	var content = jQuery('<div id="pdf-content">')
	
	content.append(jQuery("#fancy_treeview-page").clone());

	if (jQuery("#btn_next").length > 0) {
		jQuery("#fancy_treeview", content).load("module.php?mod=" + FTV_PDF_ModuleName + "&mod_action=full_pdf&rootid=" + qstring('rootid'), function() {
			getPDF(content);
		});
	} else {
		getPDF(content);
	}
}

function getPDF(content) {
	jQuery.when(modifyContent(content)).then(function() {
		// Simplify the output
		var output = new Array;
		jQuery("h2, .blockheader, .parents, .children-text, .children-list", content).each(function() {
			// change image path in text output in stead of in the dom to prevent a 404 error (only seen in firebug). 
			// We need to replace the image src path with the server file path for mPDF to catch the image.
			var img_src = jQuery(this).find("img").attr("src");
			var img_path = FTV_CACHE_DIR + jQuery(this).find("img").data("cachefilename");
			var string = jQuery(this).wrap('<p>').parent().html();
			if(typeof img_src !== 'undefined') {
				img_src = img_src.replace(/&/g , "&amp;");
				string = string.replace(img_src, img_path);
			}
			output.push(string);	
		});

		var html = '';
		for (var i = 0; i < output.length; i++) {
			html += output[i];
		}
		
		jQuery.ajax({
			type: "POST",
			url: "module.php?mod=" + FTV_PDF_ModuleName + "&mod_action=write_pdf&rootid=" + RootID + "&title=" + PageTitle,
			data: {
				"pdfContent": html
			},
			csrf: WT_CSRF_TOKEN,
			success: function() {
				jQuery(".pdf-waiting-message").fadeOut("slow");
				window.location.href = "module.php?mod=" + FTV_PDF_ModuleName + "&mod_action=output_pdf&title=" + PageTitle;
			}
		})
	});
}

function modifyContent(content) {
	// first reset the special blockheader in the colors and clouds theme back to default
	jQuery("table.blockheader", content).each(function() {
		jQuery(this).replaceWith('<div class="blockheader">' + jQuery(this).html() + '</div>');
	});

	// remove or unwrap all elements we do not need in pdf display
	jQuery(".hidden, .header-link, .tooltip-text", content).remove();
	jQuery(".generation.private", content).parents(".generation-block").remove();
	jQuery(".generation-block", content).removeAttr("data-gen data-pids");
	jQuery(".blockheader", content).removeClass("ui-state-default");
	jQuery("a, span.SURN, span.date", content).contents().unwrap();
	jQuery("a", content).remove(); //left-overs

	// mPDF doesn't support dir="auto", so set the textdirection to rtl if needed.
	if (textDirection === "rtl") {
		jQuery("span[dir=auto]", content).each(function() {
			jQuery(this).attr("dir", "rtl");
		});
	}

	// Set some extra classes
	jQuery(".parents", content).each(function() {
		jQuery(".NAME:first", this).addClass("parents-name");
	});
	jQuery(".children p", content).addClass("children-text");

	// Turn blocks into a table for better display in pdf
	jQuery(".family", content).each(function() {
		var obj = jQuery(this);
		obj.find(".desc").replaceWith("<td class=\"desc\">" + obj.find(".desc").html());
		obj.find("img").wrap("<td class=\"image\" style=\"width:" + obj.find("img").width() + "px\">");
		obj.find(".parents").replaceWith("<table class=\"parents\"><tr>" + obj.find(".parents").html());
		obj.find(".child").each(function() {
			jQuery(this).replaceWith("<tr><td>" + jQuery(this).html());
		});
		obj.find(".children ol").each(function() {
			jQuery(this).replaceWith('<table class="children-list">' + jQuery(this).html());
		});
	});

	jQuery(".private", content).each(function() {
		jQuery(this).append("<table class=\"parents\"><tr><td>" + jQuery(this).text());
	});

	//mPDF does not support multilevel ordered list, so we make our own
	jQuery(".generation-block", content).each(function(index) {
		var main = (index + 1);
		jQuery(this).find(".generation").each(function() {
			jQuery(this).find(".family").each(function(index) {
				var i = (index + 1);
				if (textDirection === "rtl") {
					var dot = "";
				} else {
					var dot = ".";
				}
				jQuery(this).find(".parents tr").prepend("<td class=\"index\">" + main + "." + i + dot + " </td>");
				jQuery(this).find(".children tr").each(function(index) {
					jQuery(this).prepend("<td class=\"index\">" + main + "." + i + "." + (index + 1) + dot + "  </td>");
				});
			});
		});
	});

}