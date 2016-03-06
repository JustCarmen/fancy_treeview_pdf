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
namespace JustCarmen\WebtreesAddOns\FancyTreeviewPdf;

use Composer\Autoload\ClassLoader;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use JustCarmen\WebtreesAddOns\FancyTreeview\FancyTreeviewModule;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\AdminTemplate;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\PdfTemplate;

class FancyTreeviewPdfModule extends FancyTreeviewModule {
	
	/** @var boolean. */
	var $access;
	
	/** {@inheritdoc} */
	public function __construct() {
		parent::__construct();
		
		$this->directory = WT_MODULES_DIR . $this->getName();
		
		if (!$this->getSetting('FTV_PDF_ACCESS_LEVEL') || $this->getSetting('FTV_PDF_ACCESS_LEVEL') >= Auth::accessLevel($this->tree)) {
			$this->access = true;
		}
		
		// register the namespaces
		$loader = new ClassLoader();
		$loader->addPsr4('JustCarmen\\WebtreesAddOns\\FancyTreeviewPdf\\', $this->directory . '/src');
		$loader->register();
	}

	public function getName() {
		return 'fancy_treeview_pdf';
	}

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of the Fancy Treeview PDF-module */ I18N::translate('Fancy Treeview PDF');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the module */ I18N::translate('Fancy Treeview module extension: offer your users to download a Fancy Treeview page as PDF.');
	}
	
	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}
	
	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
			case 'admin_config':
				if (Filter::postBool('save') && Filter::checkCsrf()) {
					$this->setSetting('FTV_PDF_ACCESS_LEVEL', Filter::postInteger('NEW_FTV_PDF_ACCESS_LEVEL'));
					Log::addConfigurationLog($this->getTitle() . ' config updated');
				}
				$template = new AdminTemplate;
				return $template->pageContent();
				
			case 'full_pdf':
				echo $this->module()->printPage(0);
				break;

			case 'write_pdf':
				$template = new PdfTemplate();
				return $template->pageBody();
				
			case 'output_pdf':				
				$file = WT_DATA_DIR . 'ftv_pdf_cache/' . Filter::get('title') . '.pdf';
				
				header('Content-Description: File Transfer');				
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: public, must-revalidate, max-age=0');
				header('Pragma: public');
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
				header('Content-Type: application/force-download');
				header('Content-Type: application/octet-stream', false);
				header('Content-Type: application/download', false);
				header('Content-Type: application/pdf', false);
				header('Content-disposition: attachment; filename="'.Filter::get('title').'"');
				
				$fd = fopen($file,'rb');
				fpassthru($fd);
				fclose($fd);
				unlink($file);
				break;

			case 'pdf_data':
				$template = new PdfTemplate;
				return $template->pageData();

			case 'pdf_thumb_data':
				$xref			= Filter::get('mid');
				$mediaobject	= Media::getInstance($xref, $this->tree);
				$thumb			= Filter::get('thumb');
				if ($thumb === '2') { // Fancy thumb
					echo $this->module()->cacheFileName($mediaobject);
				} else {
					echo $mediaobject->getServerFilename('thumb');
				}
				break;

			default:
				http_response_code(404);
				break;
		}
	}
	
	/** {@inheritdoc} */
	public function hasTabContent() {
		return false;
	}
	
	/** {@inheritdoc} */
	public function getMenu() {
		return null;
	}
	
}

$row = Database::prepare(
	"SELECT SQL_CACHE status FROM `##module` WHERE module_name = 'fancy_treeview'"
)->fetchOneRow();

if($row->status === 'enabled') {	
	return new FancyTreeviewPdfModule;
}
