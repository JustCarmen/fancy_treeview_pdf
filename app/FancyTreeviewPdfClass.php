<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * Copyright (C) 2017 JustCarmen
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
namespace JustCarmen\WebtreesAddOns\FancyTreeviewPdf;

use Fisharebest\Webtrees\Controller\BaseController;
use Fisharebest\Webtrees\I18N;

/**
 * Class FancyTreeview PDF
 */
class FancyTreeviewPdfClass extends FancyTreeviewPdfModule {

  /**
   * Get the pdf icon.
   * This functions is called from the Fancy Treeview Page Template
   *
   * @return string
   */
	public function getPdfIcon() {
		if ($this->access()) {
			return '<div class="pdf-icon col-sm-auto align-self-end text-right"><a id="pdf" href="#"><i class="icon-mime-application-pdf"></i></a></div>';
		}
	}

	/**
	 * Show a waiting message while generating the PDF file
	 * This function is called from the Fancy Treeview Page Template
	 *
	 * @return string
	 */
	public function getPdfWaitingMessage() {
		return
		'<div class="pdf-waiting-message text-primary small text-center my-2">' . I18N::translate('Creating PDF file. This process may take a while. Please wait...') . '</div>';
	}

	/**
	 * The sortname is used in the pdf index
	 *
	 * @param type $person
	 * @return type
	 */
	private function getSortName($person) {
		$sortname = $person->getSortName();
		$text1    = I18N::translateContext('Unknown given name', '…');
		$text2    = I18N::translateContext('Unknown surname', '…');
		$search   = [',', '@P.N.', '@N.N.'];
		$replace  = [', ', $text1, $text2];
		return str_replace($search, $replace, $sortname);
	}

	/**
	 * Print the full name of a person
	 *
	 * @param type $person
	 * @return string
	 */
	public function printName($person, $name) {
		return '<indexentry content="' . $this->getSortName($person) . '">' . $name . '</indexentry>';
	}

	/**
	 * We need the index entry tag to generate the index page in pdf
	 *
	 * @param type $person
	 * @param type $xref
	 * @return string
	 */
	public function printNameUrl($person, $url) {
		return '<indexentry content="' . $this->getSortName($person) . '">' . $url . '</indexentry>';
	}

	/**
	 * Determine which javascript file we need
	 *
	 * @param type $controller
	 * @param type $page
	 *
	 * @return inline and/or external Javascript
	 */
	public function includeJs($controller, $tab = false) {
		if ($this->access()) {
			$scriptvars = 'var FTV_CACHE_DIR        = ' . json_encode($this->module()->cacheDir()) . '; ' .
		  'var FTV_PDF_PAGE_TITLE		= "' . urlencode(strip_tags($controller->getPageTitle())) . '";';
			if ($tab && $this->tab()) {
				return
			'<script>' . $scriptvars . '</script>' .
			'<script src="' . $this->directory . '/js/pdf.js" defer="defer"></script>';
			} else {
				$controller
			->addInlineJavascript($scriptvars, BaseController::JS_PRIORITY_HIGH)
			->addExternalJavascript($this->directory . '/js/pdf.js');
			}
		}
	}
}
