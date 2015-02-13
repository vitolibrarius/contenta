<script>
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo $this->label("modal", "Confirm Upgrade"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "index", "MigrateDescription"); ?>',
				confirm: '<?php echo $this->label( "index", "MigrateConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("MigrateButton"); ?>',
				action: '<?php echo Config::Web("/Upgrade/migrate"); ?>'
			});
			e.preventDefault();
		});
	});
</script>

<?php if (isset($this->latestVersion, $this->latestVersion->code) == false
		|| strlen($this->latestVersion->code) == 0
		|| $this->latestVersion->code != currentVersionNumber()) : ?>
<div class="paging">
	<ul>
		<li><a class="confirm" href="#"><?php echo $this->label("Upgrade to") . ' ' . currentVersionNumber(); ?></a></li>
	</ul>
</div>
<?php endif; ?>

<h2><?php echo $this->label("Current Configuration"); ?></h2>

<div class="mediaData config">
	<table>
		<tr>
			<th><?php echo $this->label("Application Version"); ?></th>
			<td>
				<?php
					$status = "";
					if (isset($this->latestVersion, $this->latestVersion->code) == false
						|| strlen($this->latestVersion->code) == 0
						|| $this->latestVersion->code != currentVersionNumber())
					{
						$status = "problem";
					}

					echo "<span class='" . $status . "'>" . currentVersionNumber() . '</span><br />';
					echo "<span>" . currentVersionHash() . '</span>';
				?>
			</td>
			<td><span>
				<?php if (isset($this->latestVersion, $this->latestVersion->code) == false
					|| strlen($this->latestVersion->code) == 0) : ?>
					<p>You database does not appear to be initialized.  Please review your configuration and run the migration processor.</p>
				<?php elseif ($this->latestVersion->code != currentVersionNumber()) : ?>
					<p>Your database is not up to date, please review your configuration and run the migration processor.</p>
					<p>Current upgrade level is: <?php echo $this->latestVersion->code; ?>.</p>
				<?php else : ?>
					<p>Your database is up to date</p>
				<?php endif; ?>
			</span></td>
		</tr>
		<tr>
			<th><?php echo $this->label("PHP error log"); ?></th>
			<td>
				<?php
					$status = "";
					echo "<span class='" . $status . "'>" . ini_get("error_log") . '</span><br />';
				?>
			</td>
			<td><span>
					<p>You can alter this value by either editing the full path defined in <em>.htaccess</em>
						or by setting a configuration value for <em>Logging/path</em></p>
<pre>----- in .htaccess
php_value error_log /var/log/php-scripts.log
</pre>
<pre>----- in contenta.ini
[Logging]
path=logs  (results in $repositoryRoot/logs/script.log)
</pre>
			</span></td>
		</tr>
		<tr>
			<th><?php echo $this->label("PHP Memory"); ?></th>
			<td><?php
					$iniMemory = convertToBytes(ini_get('memory_limit')) / 1024;
					echo "<span class='" . (($iniMemory < 65536) ? "problem" : "") . "'>"
						. ini_get('memory_limit') . '</span>';
				?>
			</td>
			<td><span>
				<?php if ($iniMemory < 65536): ?>
					<p>Your current PHP memory limit seems low.  Please consider raising it.</p>

					<p>The easiest way to resolve this file is to use the <em>.htaccess</em> file included in the
					application root directory.  Please read the
					<a href="https://github.com/vitolibrarius/contenta/wiki/Configuration">Wiki</a> for more details.
					</p>
				<?php endif; ?>
			</span></td>
		</tr>
		<tr>
			<th><?php echo $this->label("PHP POST size"); ?></th>
			<td><?php
					$iniMaxPost = convertToBytes(ini_get('post_max_size')) / 1024;
					$iniMaxFile = convertToBytes(ini_get('upload_max_filesize')) / 1024;

					echo "<span class='" . (($iniMaxPost < 65536) ? "problem" : "") . "'>"
						. ini_get('post_max_size') . "</span>";
				?>
			</td>
			<td rowspan="2"><span>
				<?php if ($iniMaxPost < 65536 || $iniMaxFile < 65536): ?>
					<p>Your current PHP limit for POST size will restrict you to only accepting media content that is sized
					less than <em>
					<?php echo ($iniMaxPost < $iniMaxFile ? ini_get('post_max_size') : ini_get('upload_max_filesize')); ?>.
					</em>  Many documents will be larger so a higher limit is recommended.  Due to this limit in PHP, the
					issue will manifest as a <em>400 - No file selected</em> error when PHP discards the POST before the
					application can process it.</p>

					<p>The easiest way to resolve this file is to use the <em>.htaccess</em> file included in the
					application root directory.  Please read the
					<a href="https://github.com/vitolibrarius/contenta/wiki/Configuration">Wiki</a> for more details.
					</p>
				<?php endif; ?>
			</span></td>
		</tr>
		<tr>
			<th><?php echo $this->label("PHP Upload Filesize"); ?></th>
			<td><?php
					echo "<span class='" . (($iniMaxFile < 65536) ? "problem" : "") . "'>"
						. ini_get('upload_max_filesize') . "</span>";
				?>
			</td>
		</tr>
	</table>
</div>

<h2>contenta.ini</h2>

<div class="mediaData config">
	<table>
		<tr>
			<th colspan="3">Internet</th>
		</tr>
		<tr>
			<th>appname</th>
			<td>
				<?php echo Config::Get("Internet/appname"); ?>
			</td>
			<td><span>Displayable application name</span></td>
		</tr>
		<tr>
			<th>web_dir</th>
			<td>
				<?php echo Config::Get("Internet/web_dir"); ?>
			</td>
			<td><span>The name of the defined directory.  This value must match what you use in the
			application root <em>.htaccess</em> file for the Apache rewriting rules will work properly.</span></td>
		</tr>
		<tr>
			<th>web_url</th>
			<td>
				<?php echo Config::Get("Internet/web_url"); ?>
			</td>
			<td><span>Full URL for the application.  Specify your hostname and domain here.</span></td>
		</tr>

		<tr>
			<th colspan="3">Repository</th>
		</tr>
		<tr>
			<th>path</th>
			<td>
				<?php echo Config::Get("Repository/path"); ?>
			</td>
			<td><span>
				<p>Absolute path to the repository.  Please ensure this path is accessible to the user running the
				apache process.  Usually <em>_www</em> or <em>http</em></p>

				<p>It looks like you are <em><?php echo exec('whoami'); ?></em></p>
				<p>Media files are stored in <em><?php echo Config::Get("Repository/path"); ?>/media</em></p>

			</span></td>
		</tr>
		<tr>
			<th>cache</th>
			<td>
				<?php echo Config::Get("Repository/cache"); ?>
			</td>
			<td><span>
				<p>May be a relative or an absolute path for the system cache directory.  Nothing is stored in the
				cache thant cannot be re-created so it may be useful to store this content where it does not need to
				be backed up.</p>

				<p>Full path is <em><?php echo Config::GetPath("Repository/cache", "-not set-");  ?></em></p>
			</span></td>
		</tr>
		<tr>
			<th>processing</th>
			<td>
				<?php echo Config::Get("Repository/processing"); ?>
			</td>
			<td><span>
				<p>May be a relative or an absolute path for the system processing directory.  Uploaded content
				that cannot be automatically identified is store here along with other scratch files used
				by the system.</p>

				<p>Full path is <em><?php echo Config::GetPath("Repository/processing", "-not set-");  ?></em></p>
			</span></td>
		</tr>

		<tr>
			<th colspan="3">Database</th>
		</tr>
		<tr>
			<th>type</th>
			<td>
				<?php echo Config::Get("Database/type"); ?>
			</td>
			<td><span>Currently configured database type</span></td>
		</tr>

		<?php if (Config::Get("Database/type") == "sqlite") : ?>
			<tr>
				<th>path</th>
				<td>
					<?php echo Config::Get("Database/path"); ?>
				</td>
				<td><span>
					<p>Used only when database type is <em>sqlite</em>.
					May be a relative or an absolute path for the system sqlite database directory.
					The recommended value is relative to the Repository so the data and the media content are
					stored together.</p>

					<p>Full path is <em>
						<?php echo Config::GetPath("Database/path", "-not set-");  ?>/contenta.sqlite</em>
					</p>
				</span></td>
			</tr>
		<?php endif; ?>

		<tr>
			<th colspan="3">Logging</th>
		</tr>
		<tr>
			<th>type</th>
			<td>
				<?php echo Config::Get("Logging/type"); ?>
			</td>
			<td>
				<span>Currently configured log destination.</span>
				<span>Supported values <ul>
					<li><em>Print</em> - prints all logs into the page content.
						This is useful for debugging, but will seriously mess up the content</li>
					<li><em>File</em> - stores the log messages in date stamped files</li>
					<li><em>Database</em> - stores the log messages in in a database table.  This is the recommended
						value once the system is configured and running correctly</li>
				</ul></span>
			</td>
		</tr>
		<?php if (Config::Get("Logging/type") == "File") : ?>
			<tr>
				<th>path</th>
				<td>
					<?php echo Config::Get("Logging/path"); ?>
				</td>
				<td><span>
					<p>Used only when log type is <em>File</em>. May be a relative or an absolute path for the logging
					directory.	The recommended value is relative to the Repository so the logs and the media content are
					stored together.</p>

					<p>Full path is <em>
						<?php echo Config::GetPath("Logging/path", "-not set-");  ?>/log_2015-01-20.txt</em>
					</p>
				</span></td>
			</tr>
		<?php endif; ?>

	</table>
</div>

<h2>Revision History</h2>
<div class="change_log">
	<pre><?php echo currentChangeLog(); ?></pre>
</div>
