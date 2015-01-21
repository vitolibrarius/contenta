<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo Config::AppName(); ?> - <?php echo $this->viewTitle; ?></title>

	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS -->
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.2/themes/ui-lightness/jquery-ui.css" />
	<link rel="stylesheet" href="<?php echo Config::Url('/public/css/application.css'); ?>" />

	<!-- in case you wonder: That's the cool-kids-protocol-free way to load jQuery -->
	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script type="text/javascript" src="<?php echo Config::Url('/public/js/application.js'); ?>"></script>

	<!-- Custom component stylesheets and scripts -->
<?php
	if ( isset($this->additionalStyles)) {
		foreach ($this->additionalStyles as $key => $css) {
			echo '<link rel="stylesheet" href="' . Config::Url($css) . '" />';
		}
	}
	if ( isset($this->additionalScripts)) {
		foreach ($this->additionalScripts as $key => $script) {
			echo '<script type="text/javascript" src="' . Config::Url($script) . '"></script>';
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
			<div id="apptitle"><a href="<?php echo Config::Url() ?>/"><?php echo Config::AppName(); ?></a></div>
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
						$menu->add( '<< Back', '/index/back');
					}

					if (Session::get('user_account_type') === model\Users::AdministratorRole )
					{
						$menu->add( 'Admin', '/admin/index');
						$menu->add( 'Logs', '/admin/loglist');
					}
					$menu->add( 'Series', '/series/index' );
					$menu->add( 'Profile', '/profile/index' );
				}

				if (Session::get('user_logged_in') == false)
				{
					$menu->add('Login', '/login/index');
				}

				$menu->current = 'level-three.php';

				echo $menu;
				?>
			</nav>
			<div class="clear-both"></div>
		</div>
	</header>

	<div id="content">
		<h1><?php echo $this->viewTitle; ?></h1>

		<!-- echo out the system feedback (error and success messages) -->
		<?php $this->renderFeedbackMessages(); ?>

		<section class="main">

