<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo Config::AppName(); ?> - <?php echo $this->viewTitle(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="icon" type="image/png" href="<?php echo Config::Web('public/img/Logo_favicon.png'); ?>" />

	<!-- CSS -->
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.2/themes/ui-lightness/jquery-ui.css" />
	<link rel="stylesheet" href="<?php echo Config::Web('/public/css/application.css'); ?>" />

	<!-- in case you wonder: That's the cool-kids-protocol-free way to load jQuery -->
	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script type="text/javascript" src="<?php echo Config::Web('/public/js/application.js'); ?>"></script>

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

<div id="container">
	<header>
		<div class='title-box'>
			<div id="apptitle"><a href="<?php echo Config::Web() ?>/"><?php echo Config::AppName(); ?></a></div>
			<nav>
				<a href="#" id="menu-icon"></a>
				<?php
				include 'menu.php';

				$menu = menu::factory();

				if (Session::get('user_logged_in') == true)
				{
					$backtr = Session::peekCurrentPage();
					if (isset( $backtr ) == true)
					{
						$menu->add( ' Back', '/index/back');
					}

					if (Session::get('user_account_type') === model\Users::AdministratorRole )
					{
						$menu->add( Localized::GlobalLabel("Menu", "Admin"), '/admin/index');
						$menu->add( Localized::GlobalLabel("Menu", "Logs"), '/logs/index');
					}
					$menu->add( Localized::GlobalLabel("Menu", "Series"), '/series/index' );
					$menu->add( Localized::GlobalLabel("Menu", "Profile"), '/profile/index' );
				}

				if (Session::get('user_logged_in') == false)
				{
					$menu->add(Localized::GlobalLabel("Menu", "Login"), '/login/index');
				}

				$menu->current = 'level-three.php';

				echo $menu;
				?>
			</nav>
			<div class="clear-both"></div>
		</div>
	</header>

	<div id="content">
		<h1><?php echo $this->viewTitle(); ?></h1>

		<!-- echo out the system feedback (error and success messages) -->
		<?php $this->renderFeedbackMessages(); ?>

		<section class="main">

