<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * Copyright (C) 2016 JustCarmen
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
namespace JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template;

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\User;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\FancyTreeviewPdfClass;
use mPDF;

class PdfTemplate extends FancyTreeviewPdfClass {

	public function pageBody() {
		$tmp_dir = WT_DATA_DIR . 'ftv_pdf_tmp/';

		define('_JPGRAPH_PATH', $tmp_dir);
		define('_MPDF_TEMP_PATH', $tmp_dir);
		define('_MPDF_TTFONTDATAPATH', $tmp_dir);

		require_once(WT_MODULES_DIR . $this->getName() . '/mpdf/mpdf.php');

		$stylesheet = file_get_contents($this->directory . '/css/style.css');
		$stylesheet_rtl = file_get_contents($this->directory . '/css/style-rtl.css');
		
		$html = Filter::post('pdfContent');

		$header = '<header>=== ' . $this->tree->getTitleHtml() . ' ===</header>';
		$footer = '<footer>' .
			'<table><tr>' .
			'<td class="left">' . WT_BASE_URL . '</td>' .
			'<td class="center">{DATE d-m-Y}</td>' .
			'<td class="right">{PAGENO}</td>' .
			'</tr></table>' .
			'</footer>';

			$mpdf = new mPDF();

			$mpdf->simpleTables = true;
			$mpdf->shrink_tables_to_fit = 1;

			$mpdf->autoScriptToLang = true;
			$mpdf->baseScript = 1;
			$mpdf->autoVietnamese = true;
			$mpdf->autoArabic = true;

			$mpdf->autoLangToFont = true;
			if (I18N::direction() === 'rtl') {
				$mpdf->SetDirectionality('rtl');
			}

			if (I18N::direction() === 'rtl') {
				$mpdf->WriteHTML($stylesheet_rtl, 1);
			} else {
				$mpdf->WriteHTML($stylesheet, 1);
			}

			$mpdf->setAutoTopMargin = 'stretch';
			$mpdf->setAutoBottomMargin = 'stretch';
			$mpdf->autoMarginPadding = 5;

			$admin = User::find($this->tree->getPreference('WEBMASTER_USER_ID'))->getRealName();

			$mpdf->setCreator($this->getTitle() . ' - a webtrees module by justcarmen.nl');
			$mpdf->SetTitle(Filter::get('title'));
			$mpdf->setAuthor($admin);

			$mpdf->SetHTMLHeader($header);
			$mpdf->setHTMLFooter($footer);

			$html_chunks = explode("\n", $html);
			$chunks = count($html_chunks);
			$i = 1;
			foreach ($html_chunks as $html_chunk) {
				// write html body parts only (option 2);
				if ($i === 1) {
					// first chunk (initialize all buffers - init=true)
					$mpdf->WriteHTML($html_chunk, 2, true, false);
				} elseif ($i === $chunks) {
					// last chunck (close all buffers - close=true)
					$mpdf->WriteHTML($html_chunk, 2, false, true);
				} else {
					// all other parts (keep the buffer open)
					$mpdf->WriteHTML($html_chunk, 2, false, false);
				}
				$i++;
			}
			
			$index = '
				<pagebreak type="next-odd" />
				<h2>' . I18N::translate('Index') . '</h2>
				<columns column-count="2" column-gap="5" />
				<indexinsert usedivletters="on" links="on" collation="' . WT_LOCALE . '.utf8" collationgroup="' . I18N::collation() . '" />';
			$mpdf->writeHTML($index);
			$mpdf->Output($tmp_dir . Filter::get('title') . '.pdf', 'F');
	}	
}
