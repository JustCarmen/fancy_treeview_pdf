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

use Composer\Autoload\ClassLoader;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use JustCarmen\WebtreesAddOns\FancyTreeview\FancyTreeviewModule;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\AdminTemplate;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template\PdfTemplate;

/**
 * This module works with Fancy Treeview version 1.7.9 or higher.
 *
 */
const FTV_COMPATIBLE_VERSION = '2.0.0-dev';

/**
 * PDF extension for the Fancy Treeview module
 *
 * First check if the correct version of the Fancy Treeview module is installed and enabled (Class won't exist if the module hasn't been installed or has been disabled.
 * The function to check the correct version is moved to the main module since version 1.7.9, so if the function doesn't exist there is an older version of Fancy Treeview installed.
 */
$message1 = I18N::translate('You have installed the Fancy Treeview PDF module. This module won’t work without the correct version of the Fancy Treeview module installed and enabled. Please install and enable Fancy Treeview version %s or higher to use this module. <a href="http://www.justcarmen.nl/fancy-modules/fancy-treeview/">Click here to download the latest release</a>.', FTV_COMPATIBLE_VERSION);

$message2 = I18N::translate('You have installed the Fancy Treeview PDF module. This module won’t work without the Fancy Treeview module enabled. Please enable Fancy Treeview to use this module.');

if (method_exists('JustCarmen\WebtreesAddOns\FancyTreeview\FancyTreeviewModule', 'intVersion')) {
	if (!file_exists(WT_MODULES_DIR . 'fancy_treeview') || (file_exists(WT_MODULES_DIR . 'fancy_treeview') && str_replace(".", "", FTV_COMPATIBLE_VERSION) > FancyTreeviewModule::intVersion())) {
		if (Auth::isAdmin() && WT_SCRIPT_NAME === 'admin_modules.php') {
			FlashMessages::addMessage($message1);
		}
		return;
	}
} else {
	if (Auth::isAdmin() && WT_SCRIPT_NAME === 'admin_modules.php') {
		FlashMessages::addMessage($message1);
	}
}

$ftv_module_status = Database::prepare("SELECT status FROM `##module` WHERE module_name = 'fancy_treeview'")->fetchOne();
if (file_exists(WT_MODULES_DIR . 'fancy_treeview') && $ftv_module_status === 'disabled') {
	if (Auth::isAdmin() && WT_SCRIPT_NAME === 'admin_modules.php') {
		FlashMessages::addMessage($message2);
	}
	return;
}

class FancyTreeviewPdfModule extends FancyTreeviewModule {
	const CUSTOM_VERSION = '2.0.0-dev';
	const CUSTOM_WEBSITE = 'http://www.justcarmen.nl/fancy-modules/fancy-treeview-pdf/';

	/** {@inheritdoc} */
	public function __construct() {
		parent::__construct();

		$this->directory = WT_MODULES_DIR . $this->getName();

		// register the namespaces
		$loader = new ClassLoader();
		$loader->addPsr4('JustCarmen\\WebtreesAddOns\\FancyTreeviewPdf\\', $this->directory . '/app');
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
		return Html::url('module.php', [
			'mod'        => $this->getName(),
			'mod_action' => 'admin_config',
		]);
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
	  case 'admin_config':
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setPreference('FTV_PDF_ACCESS_LEVEL', Filter::postInteger('NEW_FTV_PDF_ACCESS_LEVEL'));
			$this->setPreference('FTV_PDF_TAB', Filter::postBool('NEW_FTV_PDF_TAB'));
			Log::addConfigurationLog($this->getTitle() . ' config updated');

			FlashMessages::addMessage(I18N::translate('The settings for this module are saved'), 'success');
		}
		$template = new AdminTemplate;
		return $template->pageContent();

	  case 'full_pdf':
		echo $this->module()->printPage();
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
		$file = WT_DATA_DIR . 'ftv_pdf_tmp/' . Filter::get('title') . '.pdf';

		if (file_exists($file)) {
			ob_start();
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . basename($file) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			ob_clean();
			ob_end_flush();
			readfile($file);
			File::delete(dirname($file));
		} else {
			FlashMessages::addMessage(I18N::translate('The file %s could not be created.', basename($file)), 'danger');
			Header('Location:' . WT_BASE_URL . 'module.php?mod=fancy_treeview&mod_action=page&rootid=' . Filter::get('rootid') . '&ged=' . Filter::get('ged'));
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

	protected function access() {
		global $WT_TREE;
		if ($this->getPreference('FTV_PDF_ACCESS_LEVEL', '2') >= Auth::accessLevel($WT_TREE)) {
			return true;
		}
	}

	protected function tab() {
		if ($this->getPreference('FTV_PDF_TAB')) {
			return true;
		}
	}
}

return new FancyTreeviewPdfModule;
