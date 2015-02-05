			</section>
		</div> <!-- content -->

		<div id="ajaxSpinner" class="spinner"></div>

		<footer>
			<div id="github">
				<a href="https://github.com/vitolibrarius/contenta"> <?php echo $this->globalLabel("WebsiteLink", "Website"); ?></a> |
				<a href="https://github.com/vitolibrarius/contenta/wiki"> <?php echo $this->globalLabel("HelpLink", "Help"); ?></a> |
				<a href="https://github.com/vitolibrarius/contenta/issues/new"> <?php echo $this->globalLabel("AddIssueLink", "Report Problem"); ?></a>
			</div>
			<div id="version">
				<?php echo $this->globalLabel("Version"); ?>: <?php echo currentVersionNumber(); ?>
                <br>
				<em><?php echo currentVersionHash(); ?></em>
			</div>
		</footer>
	</div> <!-- container -->
</body>
</html>
