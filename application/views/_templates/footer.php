			</section>
		</div> <!-- content -->

		<div id="ajaxSpinner" class="spinner"></div>

		<footer>
			<div id="remote">
				<?php if (Session::get('user_logged_in') == true && Session::get('user_account_type') === \model\Users::AdministratorRole ) :?>
					<?php
						$git = new utilities\Git(SYSTEM_PATH);
						$remote = $git->remoteStatus();
						if ( $remote != utilities\Git::UP_TO_DATE ): ?>
							<a href="<?php echo Config::Web('/upgrade/upgradeEligibility'); ?>">
								<?php echo $remote; ?>
							</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div id="github">
				<a href="https://github.com/vitolibrarius/contenta"> <?php echo $this->globalLabel("WebsiteLink", "Website"); ?></a> |
				<a href="https://github.com/vitolibrarius/contenta/wiki"> <?php echo $this->globalLabel("HelpLink", "Help"); ?></a> |
				<a href="https://github.com/vitolibrarius/contenta/issues/new"> <?php echo $this->globalLabel("AddIssueLink", "Report Problem"); ?></a>
			</div>
			<div id="version">
				<?php echo $this->globalLabel("Release"); ?>: <?php echo currentVersionNumber(); ?>
                <br />
				<em><?php echo currentVersionHash(); ?></em>
			</div>
		</footer>
	</div> <!-- container -->
</body>
</html>
