<?php
/*
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * Copyright (C) 2015 JustCarmen
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
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;

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
			->setPageTitle(I18N::translate('Fancy Treeview'))
			->pageHeader();

		// add javascript and styleseheet
		$this->includeJs($controller, 'admin');
		echo $this->getStylesheet();
	}

	private function pageBody(PageController $controller) {
		$FTV_SETTINGS = unserialize($this->getSetting('FTV_SETTINGS'));
		?>
		<!-- ADMIN PAGE CONTENT -->
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<h2><?php echo $controller->getPageTitle(); ?></h2>
		<form class="form-horizontal" method="post">								
			<!-- SHOW PDF -->
			<div class="form-group">
				<label class="control-label col-sm-4">
					<?php echo I18N::translate('Show PDF icon?'); ?>
				</label>
				<div class="col-sm-4">
					<?php echo FunctionsEdit::editFieldAccessLevel('NEW_FTV_OPTIONS[SHOW_PDF_ICON]', $this->options('show_pdf_icon'), 'class="form-control"'); ?>
				</div>
			</div>								
			<!-- BUTTONS -->
			<button name="save-options" class="btn btn-primary" type="submit">
				<i class="fa fa-check"></i>
				<?php echo I18N::translate('save'); ?>
			</button>
			<button name="reset-options" class="btn btn-primary" type="reset">
				<i class="fa fa-recycle"></i>
				<?php echo I18N::translate('reset'); ?>
			</button>
		</form>
		<?php
	}

}
