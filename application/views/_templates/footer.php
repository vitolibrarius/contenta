	    <div id="notification_content" data-href="<?php echo Config::Web('/Api/notifications'); ?>"></div>
	</div>
	<!-- #end div #main .wrapper -->

	<div id="ajaxSpinner" class="spinner"></div>

	<!-- footer area -->
	<footer>
		<div id="colophon" class="wrapper clearfix">
			<div class="grid_4 left">
				<a target="ContentWeb" href="https://github.com/vitolibrarius/contenta">
					<?php echo Localized::GlobalLabel("WebsiteLink"); ?>
				</a> |
				<a target="ContentWiki" href="https://github.com/vitolibrarius/contenta/wiki">
					<?php echo Localized::GlobalLabel("HelpLink"); ?>
				</a> |
				<a target="ContentIssues"
					href="https://github.com/vitolibrarius/contenta/issues/new?body=<?php
						echo urlencode('Problem on ' . Config::Web($this->controllerName, (isset($this->controllerAction) ?
							$this->controllerAction : 'index'))); ?>"
					><?php echo Localized::GlobalLabel("AddIssueLink"); ?>
				</a>
			</div>
			<div class="grid_4 middle">
				<?php echo Localized::GlobalLabel("Release"); ?>: <?php echo currentVersionNumber(); ?>
			</div>
			<div class="grid_4 right">
			</div>
		</div>
	</footer>
	<!-- #end footer area -->
</body>
</html>
