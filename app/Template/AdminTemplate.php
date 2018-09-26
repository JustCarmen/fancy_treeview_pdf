<?php
/*
 * webtrees: online genealogy
 * Copyright (C) 2018 JustCarmen (http://justcarmen.nl)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace JustCarmen\WebtreesAddOns\FancyTreeviewPdf\Template;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use JustCarmen\WebtreesAddOns\FancyTreeviewPdf\FancyTreeviewPdfClass;

class AdminTemplate extends FancyTreeviewPdfClass {
	protected function pageContent() {
		$controller = new PageController;
		return
		$this->pageHeader($controller) .
		$this->pageBody($controller);
	}

	private function pageHeader(PageController $controller) {
		$controller
		->restrictAccess(Auth::isAdmin())
		->setPageTitle(I18N::translate('Fancy Treeview PDF'))
		->pageHeader();
	}

	private function pageBody(PageController $controller) {
		echo Bootstrap4::breadcrumbs([
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
		], $controller->getPageTitle()); ?>

		<div class="fancy-treeview fancy-treeview-pdf">
		  <div class="fancy-treeview-pdf-admin">
			<h1><?= $controller->getPageTitle() ?></h1>
			<form class="form-horizontal" method="post">
			  <?= Filter::getCsrf() ?>
			  <input type="hidden" name="save" value="1">
			  <!-- PDF ACCESS LEVEL -->
			  <div class="row form-group">
				<label class="col-form-label col-sm-4">
				  <?= I18N::translate('Access level') ?>
				</label>
				<div class="col-sm-8">
				  <?= Bootstrap4::select(FunctionsEdit::optionsAccessLevels(), $this->getPreference('FTV_PDF_ACCESS_LEVEL'), ['name' => 'NEW_FTV_PDF_ACCESS_LEVEL']) ?>
				</div>
			  </div>
			  <!-- PDF TAB ICON -->
			  <div class="row form-group">
				<label class="col-form-label col-sm-4">
				  <?= I18N::translate('Show a PDF icon in the Fancy Treeview tab') ?>
				</label>
				<div class="col-sm-8">
				  <?php
				  if (!$this->getPreference('FTV_PDF_TAB')) {
				  	$this->setPreference('FTV_PDF_TAB', 0);
				  } ?>
				  <?= Bootstrap4::radioButtons('NEW_FTV_PDF_TAB', FunctionsEdit::optionsNoYes(), $this->getPreference('FTV_PDF_TAB'), true) ?>
				</div>
				<p class="col-sm-8 offset-sm-4 small text-muted">
				  <?= /* I18N: Help text for the “Show a PDF icon in the Fancy Treeview tab” configuration setting */ I18N::translate('By default the PDF icon is visible on the Fancy Treeview page. If you enable this option, a PDF icon is also displayed in the Fancy Treeview tab on the individual page.') ?>
				</p>
			  </div>
			  <!-- BUTTONS -->
			  <button class="btn btn-primary" type="submit">
				<i class="fa fa-check"></i>
				<?= I18N::translate('save') ?>
			  </button>
			</form>
		  </div>
		</div>
		<?php
	}
}
