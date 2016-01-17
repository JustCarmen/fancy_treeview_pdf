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

use Fisharebest\Webtrees\I18N;

/**
 * Class FancyTreeview PDF
 */
class FancyTreeviewPdfClass extends FancyTreeviewPdfModule {

	public function getPdfIcon() {
		if ($this->access) {
			return
				'<div id="dialog-confirm" title="' . I18N::translate('Generate PDF') . '" style="display:none">
					<p>' . I18N::translate('The pdf contains only visible generation blocks.') . '</p>
				</div>
				<a id="pdf" href="#"><i class="icon-mime-application-pdf"></i></a>';
		}
	}

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
	public function printName($person, $name) {
		return '<indexentry content="' . $this->getSortName($person) . '">' . $name . '</indexentry>';
	}

	/**
	 * We need the index entry tag for generation the index page in pdf
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
	public function includeJs($controller) {
		if ($this->access) {
			$controller->addExternalJavascript($this->directory . '/js/pdf.js');
		}
	}

}
