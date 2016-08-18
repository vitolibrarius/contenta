<h1 style="color: red;">
	<?php echo Localized::GlobalLabel( "Error", "title" ); ?>
</h1>

<section>
    <div class="wrapper">
		<div class="row data">
			<div class="grid_12">
				<?php echo (isset($this->message) ? $this->message : ''); ?>
<?php if ( isset($this->url) ) : ?>
				<br>
				<a href="<?php echo $this->url; ?>"><?php echo (isset($this->url_title) ? $this->url_title : 'link'); ?></a>
<?php endif; ?>
			</div>
		</div>
	</div>
</section>
