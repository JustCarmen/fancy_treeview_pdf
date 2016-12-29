<?php
/*
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * Copyright (C) 2016 JustCarmen
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
		?>
		<!-- ADMIN PAGE CONTENT -->
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel') ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration') ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle() ?></li>
		</ol>
		<h2><?php echo $controller->getPageTitle() ?></h2>
		<form class="form-inline" method="post">
			<?php echo Filter::getCsrf() ?>
			<input type="hidden" name="save" value="1">
			<!-- PDF ACCESS LEVEL -->
			<div class="form-group">
				<label class="control-label col-sm-4">
					<?php echo I18N::translate('Access level') ?>
				</label>
				<div class="col-sm-4">
					<?php echo FunctionsEdit::editFieldAccessLevel('NEW_FTV_PDF_ACCESS_LEVEL', $this->getSetting('FTV_PDF_ACCESS_LEVEL'), 'class="form-control"') ?>
				</div>
				<div class="col-sm-4">
			</div>
			<!-- PDF TAB ICON -->
			<div class="form-group">
				<label class="control-label col-sm-4">
					<?php echo I18N::translate('Show a PDF icon in the Fancy Treeview tab') ?>
				</label>
				<div class="col-sm-8">
					<?php
					if (!$this->getSetting('FTV_PDF_TAB')) {
						$this->setSetting('FTV_PDF_TAB', 0);
					}
					?>
					<?php echo FunctionsEdit::editFieldYesNo('NEW_FTV_PDF_TAB', $this->getSetting('FTV_PDF_TAB'), 'class="radio-inline"') ?>
				</div>
				<p class="col-sm-8 col-sm-offset-4 small text-muted">
					<?php echo /* I18N: Help text for the “Show a PDF icon in the Fancy Treeview tab” configuration setting */ I18N::translate('By default the PDF icon is visible on the Fancy Treeview page. If you enable this option, a PDF icon is also displayed on the Fancy Treeview tab on the individual page.') ?>
				</p>
			</div>
			<!-- BUTTONS -->
			<button class="btn btn-primary" type="submit">
				<i class="fa fa-check"></i>
				<?php echo I18N::translate('save') ?>
			</button>
		</form>
		<?php
	}

}
