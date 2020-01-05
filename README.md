Fancy Treeview PDF
==================

[![Latest Release](https://img.shields.io/github/release/JustCarmen/fancy_treeview_pdf.svg)][1]
[![webtrees major version](https://img.shields.io/badge/webtrees-v1.x-green)][2]

PDF extension for the [Fancy Treeview module][7]. This is a webtrees 1 module. It cannot be used with webtrees 2.

Description
-----------
This module is an extension for the [Fancy Treeview module][7]. If you install this module it offers your users the possibility to download a Fancy Treeview page as PDF. Like the Fancy Treeview webpage, the PDF contains an overview of all descendants of a selected root person in a tree. It contains the same information as the Fancy Treeview page (images are included). Besides the PDF comes with an index with the names of all individuals in alphabetical order. It is designed to use with all kind of languages, including rtl-languages.

Required disk space
-------------------
The [mPDF-library][8] is a huge library. Since webtrees offers many languages we need the complete font-library that comes with mPDF to serve your visitors from all parts of the world.
If you don’t need the PDF-option or you don’t have enough disk space on your server (minimum of 100MB free space is required) don’t install this extra module.

If you have less space on your server and still want to use this module it could be possible if you don't serve all webtrees languages to your visitors. If you only provide a few you probably can remove most of the fonts in the folder mpdf/ttfonts, which will reduce package size. You should NOT remove any fonts from the DejaVu family because these are the basic fonts needed for the PDF creation. If you have removed any of the other fonts, you should test the PDF creation in any language you use on your website to see if everything is working properly.

Translations
------------
This module doesn't contain any translation files. The few texts inside this module are added to the translation files inside the main module [Fancy Treeview][7].

Stylesheets
------------
This module only contains stylesheets to style the PDF-document. The few webstyles used inside this module are added to the themefiles inside the main module [Fancy Treeview][7].

Installation & upgrading
---------------------------------------
For more information about these subjects go to the [JustCarmen help pages][4].
_Please note: This module won't work without the main module Fancy Treeview[7] installed._

Bugs and feature requests
-------------------------
If you experience any bugs or have a feature request for this module you can [create a new issue on GitHub][5] or [use the webtrees subforum 'customising'][6] to contact me.

[1]: https://github.com/JustCarmen/fancy_treeview_pdf/releases/latest
 [2]: https://webtrees.github.io/download/
 [3]: https://poeditor.com/join/project/uzdUt7S0Bd
 [4]: http://www.justcarmen.nl/help-category/modules-help
 [5]: https://github.com/JustCarmen/fancy_treeview_pdf/issues?state=open
 [6]: http://www.webtrees.net/index.php/en/forum/4-customising
 [7]: https://github.com/JustCarmen/fancy_treeview/tree/wt-1.7
 [8]: https://github.com/mpdf/mpdf
