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
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use JustCarmen\WebtreesAddOns\FancyTreeview\FancyTreeviewModule;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\AdminTemplate;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\PdfTemplate;

define('FTV_PDF_VERSION', '1.7.4-dev');
define('FTV_COMPATIBLE_VERSION', '1.7.4-dev');

/**
 * PDF extension for the Fancy Treeview module
 * 
 * First check if the correct version of the Fancy Treeview module is installed and enabled (Class won't exist if the module hasn't been installed or has been disabled.
 */
$ftv_module_status = Database::prepare("SELECT status FROM `##module` WHERE module_name = 'fancy_treeview'")->fetchOne();
if(!file_exists(WT_MODULES_DIR . 'fancy_treeview') || $ftv_module_status === 'disabled' || FTV_VERSION <> FTV_COMPATIBLE_VERSION) {
	FlashMessages::addMessage(I18N::translate('You have installed the Fancy Treeview PDF module. This module won’t work without the correct version of the Fancy Treeview module installed and enabled. Please install and enable Fancy Treeview version %s to use this module.', FTV_COMPATIBLE_VERSION));
	return;
}

class FancyTreeviewPdfModule extends FancyTreeviewModule {
	
	/** @var boolean. */
	var $access;
	
	/** {@inheritdoc} */
	public function __construct() {
		parent::__construct();
		
		$this->directory = WT_MODULES_DIR . $this->getName();
		
		if ($this->getSetting('FTV_PDF_ACCESS_LEVEL', '2') >= Auth::accessLevel($this->tree)) {
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
				$tmp_dir = WT_DATA_DIR . 'ftv_pdf_tmp/';
				if (file_exists($tmp_dir)) {
					File::delete($tmp_dir);
				}
				File::mkdir($tmp_dir);
				$template = new PdfTemplate();
				return $template->pageBody();
				
			case 'output_pdf':
				$tmp_dir = WT_DATA_DIR . 'ftv_pdf_tmp/';
				$pdf_file = Filter::get('title') . '.pdf';
				
				// see admin_trees_download (zip archive)
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $pdf_file . '"');
				header('Content-length: ' . filesize($tmp_dir . $pdf_file));
				readfile($tmp_dir . $pdf_file);
				File::delete($tmp_dir);
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

return new FancyTreeviewPdfModule;