<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo Config::AppName(); ?> - <?php echo $this->viewTitle(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1.0, width=device-width" />

	<link rel="icon" type="image/png" href="<?php echo Config::Web('public/img/Logo_favicon.png'); ?>" />

	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/lib/select2.css'); ?>" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/contenta.css'); ?>" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/contenta-card.css'); ?>" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/contenta-forms.css'); ?>" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/contenta-modal.css'); ?>" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/contenta-tables.css'); ?>" />

	<!-- Scripts -->
	<script type="text/javascript" src="<?php echo Config::Web('/public/js/lib/jquery-2.1.4.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Config::Web('/public/js/lib/select2.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Config::Web('/public/js/contenta.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Config::Web('/public/js/contenta-modal.js'); ?>"></script>

	<!-- Custom component stylesheets and scripts -->
<?php
	if ( isset($this->additionalStyles)) {
		foreach ($this->additionalStyles as $key => $css) {
			echo '<link rel="stylesheet" href="' . Config::Web($css) . '" />';
		}
	}
	if ( isset($this->additionalScripts)) {
		foreach ($this->additionalScripts as $key => $script) {
			echo '<script type="text/javascript" src="' . Config::Web($script) . '"></script>';
		}
	}
?>
<script type="text/javascript">
<!--
	webRoot="<?php echo Config::Web(); ?>";
//-->
</script>
</head>


<body>
<!-- header area -->
    <header class="wrapper clearfix">
        <div id="banner">
        	<div id="logo"><a href="<?php echo Config::Web() ?>/">
        		<img src="<?php echo Config::Web('/public/img/ContentaHeader.png'); ?>" alt="logo"></a>
        	</div>
        </div>

        <!-- main navigation -->
        <nav id="topnav" role="navigation">
          <div class="toggle">Menu</div>
			<?php
				include 'menu.php';

				$menu = menu::factory();
				$menu->class = "menu";
				$menu->id = "menu-main";

				if (Session::get('user_logged_in') == true)
				{
					if (Session::get('user_account_type') === model\Users::AdministratorRole )
					{
						$menu->add( Localized::GlobalLabel("Menu", "Admin"), '/admin/index');
						$menu->add( Localized::GlobalLabel("Menu", "Logs"), '/logs/index');
					}
					$menu->add( Localized::GlobalLabel("Menu", "Series"), '/DisplaySeries/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Story Arcs"), '/DisplayStories/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Profile"), '/profile/index' );
				}

				if (Session::get('user_logged_in') == false)
				{
					$menu->add(Localized::GlobalLabel("Menu", "Login"), '/login/index');
				}

				$menu->current = Config::Web($this->controllerName, (isset($this->controllerAction) ? $this->controllerAction : 'index'));

				echo $menu;
			?>
		</nav><!-- end main navigation -->
    </header><!-- end header -->


	<section id="page-header" class="clearfix">
		<div class="wrapper">
		   <div class="row">
			<div class="grid_12">
				<h1>
				<?php if (isset($this->controllerName)): ?>
					<a href="<?php echo Config::Web($this->controllerName, (isset($this->controllerAction) ? $this->controllerAction : null)); ?>">
				<?php endif; ?>
					<?php echo $this->viewTitle(); ?>
				<?php if (isset($this->controllerName)): ?>
					</a>
				<?php endif; ?>
				</h1>
			</div>
		</div><!-- end row -->
	   </div><!-- end wrapper -->
	</section><!-- end page-header area -->

	<!-- main content area -->
	<div id="main-content" class="wrapper">
