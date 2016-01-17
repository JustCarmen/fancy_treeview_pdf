<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\BaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;
use JustCarmen\WebtreesAddOns\FancyTreeview\FancyTreeviewClass;

/**
 * Class FancyTreeview
 */
class FancyTreeviewPdfClass extends FancyTreeviewClass {

	/**
	 * The sortname is used in the pdf index
	 * 
	 * @param type $person
	 * @return type
	 */
	private function getSortName($person) {
		$sortname = $person->getSortName();
		$text1 = I18N::translateContext('Unknown given name', '…');
		$text2 = I18N::translateContext('Unknown surname', '…');
		$search = array(',', '@P.N.', '@N.N.');
		$replace = array(', ', $text1, $text2);
		return str_replace($search, $replace, $sortname);
	}

	/**
	 * Print the full name of a person
	 *
	 * @param type $person
	 * @return string
	 */
	private function printName($person) {
		return
			'<indexentry content="' . $this->getSortName($person) . '">' .
			$person->getFullName() .
			'</indexentry>';
	}

	/**
	 * Print the name of a person with the link to the individual page
	 *
	 * @param type $person
	 * @param type $xref
	 * @return string
	 */
	private function printNameUrl($person, $xref = '') {
		if ($xref) {
			$name = ' name="' . $xref . '"';
		} else {
			$name = '';
		}

		// we need the index entry tag for generation the index page in pdf
		return
			'<indexentry content="' . $this->getSortName($person) . '">' .
			'<a' . $name . ' href="' . $person->getHtmlUrl() . '">' .
			$person->getFullName() .
			'</a>' .
			'</indexentry>';
	}
	
	/**
	 * Determine which javascript file we need
	 *
	 * @param type $controller
	 * @param type $page
	 *
	 * @return inline and/or external Javascript
	 */
	protected function includeJs($controller, $page) {
		parent::includeJs($controller, $page);
		
		if ($this->options('show_pdf_icon') >= Auth::accessLevel($this->tree)) {
			$controller->addExternalJavascript($this->directory . '/js/pdf.js');
		}
	}

}
