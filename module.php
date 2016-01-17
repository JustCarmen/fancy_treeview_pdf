<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * Copyright (C) 2015 JustCarmen
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
		
		if ($this->getSetting('Access Level') >= Auth::accessLevel($this->tree)) {
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
		return /* I18N: Name of the module */ I18N::translate('Fancy Treeview PDF');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the module */ I18N::translate('Extend the Fancy Treeview module with  a PDF option.');
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

			case 'show_pdf':
				$template = new PdfTemplate();
				return $template->pageBody();

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
	
}

return new FancyTreeviewPdfModule;
