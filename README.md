Fancy Treeview PDF
==================

PDF extension for the [Fancy Treeview module](https://github.com/JustCarmen/fancy_treeview)

This module requires [webtrees 1.7.0](https://github.com/fisharebest/webtrees) or later AND [Fancy Treeview 1.7.5](https://github.com/JustCarmen/fancy_treeview) or later. Download the latest stable release of this module [here](https://github.com/JustCarmen/fancy_treeview/releases/latest).
The development version (master branch) only works with webtrees 1.8.0-dev.

Description
-----------
This module is an extension for the [Fancy Treeview module](https://github.com/JustCarmen/fancy_treeview). If you install this module it offers your users the possibility to download a Fancy Treeview page as PDF. Like the Fancy Treeview webpage, the PDF contains an overview of all descendants of a selected root person in a tree. It contains the same information as the Fancy Treeview page (images are included). Besides the PDF comes with an index with the names of all individuals in alphabetical order. It is designed to use with all kind of languages, including rtl-languages.

Required disk space
-------------------
The [mPDF-library](https://github.com/mpdf/mpdf) is a big library. Since webtrees offers many languages we need the complete font-library that comes with mPDF to serve your visitors from all parts of the world.
If you don’t need the PDF-option or you don’t have enough disk space on your server (minimum of 100MB free space is required) don’t install this extra module.

If you have less space on your server and still want to use this module it could be possible if you don't serve all webtrees languages to your visitors. If you only provide a few you probably can remove most of the fonts in the folder mpdf/ttfonts, which will reduce package size. You should NOT remove any fonts from the DejaVu family because these are the basic fonts needed for the PDF creation. If you have removed any of the other fonts, you should test the PDF creation in any language you use on your website to see if everything is working properly.

Translations
------------
This module doesn't contain any translation files. The few texts inside this module are added to the translation files inside the main module [Fancy Treeview](https://github.com/JustCarmen/fancy_treeview).

Stylesheets
------------
This module only contains stylesheets to style the PDF-document. The few webstyles used inside this module are added to the themefiles inside the main module [Fancy Treeview](https://github.com/JustCarmen/fancy_treeview).

Installation and updating
-------------------------
After you have installed this module goto the webtrees control panel to activate this module. If you don't see the Fancy Treeview PDF module in the list of installed modules, you probably haven't installed the Fancy Treeview module yet.
_Please note: This module won't work without the main module Fancy Treeview installed._

For more information about installation and updating modules go to the JustCarmen help pages: http://www.justcarmen.nl/help

Bugs and feature requests
-------------------------
If you experience any bugs or have a feature request for this module you can [create a new issue](https://github.com/JustCarmen/fancy_treeview_pdf/issues?state=open) or [use the webtrees subforum 'customising'](http://www.webtrees.net/index.php/en/forum/4-customising) to contact me.


