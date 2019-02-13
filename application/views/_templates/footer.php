	    <div id="notification_content" data-href="<?php echo Config::Web('/Api/notifications'); ?>"></div>
	</div>
	<!-- #end div #main .wrapper -->

	<div id="ajaxSpinner" class="spinner"></div>

	<!-- footer area -->
	<footer>
		<div id="colophon" class="clearfix">
    	<table width="100%">
    	<tr>
    	<td>
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
		</td>
		<td style="text-align:right;">
				<?php echo Localized::GlobalLabel("Release"); ?>: <?php echo currentVersionNumber(); ?>
		</td>
		</tr>
		</table>
	</footer>
	<!-- #end footer area -->

<!-- header area -->
    <header class="clearfix">
    	<table width="100%">
    	<tr>
    	<td>
			<div id="banner">
				<div id="logo"><a href="<?php echo Config::Web() ?>/">
					<img src="<?php echo Config::Web('/public/img/ContentaHeader.png'); ?>" alt="logo"></a>
				</div>
			</div>
		</td>
		<td>
        <!-- main navigation -->
        <nav id="topnav" role="navigation">
          <div class="toggle">Menu</div>
			<?php
				include 'menu.php';

				$menu = menu::factory();
				$menu->class = "menu";
				$menu->id = "menu-main";

				if (\http\Session::get('user_logged_in') == true)
				{
					if (\http\Session::get('user_account_type') === \model\user\Users::AdministratorRole )
					{
						$menu->addCallback( "Daemons", function() {
								return "<div id='daemons' data-href='" . Config::Web("/AdminJobs/json_running")
									. "' data-page='" . Config::Web("/AdminJobs/runningIndex") ."'>"
									. "<span class='badge'>123</span></div>";
							}
						);
						$menu->add( Localized::GlobalLabel("Menu", "Admin"), '/admin/index');
						$menu->add( Localized::GlobalLabel("Menu", "Logs"), '/logs/index');
					}
					$menu->add( Localized::GlobalLabel("Menu", "Books"), '/DisplayBook/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Series"), '/DisplaySeries/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Story Arcs"), '/DisplayStories/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Profile"), '/profile/index' );
				}

				if (\http\Session::get('user_logged_in') == false)
				{
					$menu->add(Localized::GlobalLabel("Menu", "Login"), '/login/index');
				}

				$menu->current = Config::Web($this->controllerName, (isset($this->controllerAction) ? $this->controllerAction : 'index'));

				echo $menu;
			?>
		</nav><!-- end main navigation -->
		</td>
		</tr>
		</table>
    </header><!-- end header -->
</body>
</html>
