	<div class="admin_group">
		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-users.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="#"><?php echo $this->localizedLabel( "UsersLink", "Users" ); ?></a>
				<p><?php echo $this->localizedLabel( "UsersLink-desc", "User List and administration" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="#"><?php echo $this->localizedLabel( "AddUsersLink", "Add User" ); ?></a>
				<p><?php echo $this->localizedLabel( "UsersLink-desc", "Register new users" ); ?></p>
			</div>
		</div>


		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-publications.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="#"><?php echo $this->localizedLabel( "UploadLink", "Upload" ); ?></a>
				<p><?php echo $this->localizedLabel( "UploadLink-desc", "Upload new media content" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="#"><?php echo $this->localizedLabel( "RepairLink", "Repair Processing" ); ?></a>
				<p><?php echo $this->localizedLabel( "RepairLink-desc",
					"Uploaded content that is stuck in processing.  Usually due to a failure to automatically identify the content" );
				 ?></p>
			</div>
			<div class="admin_card_item">
				<a href="#">Series</a>
				<p>Flagged series</p>
			</div>
			<div class="admin_card_item">
				<a href="#">Publications</a>
				<p>Flagged issues</p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-network.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/netconfig/index'); ?>">
					<?php echo $this->localizedLabel( "EndpointsLink", "Endpoints" ); ?></a>
				<p><?php echo $this->localizedLabel( "EndpointsLink-desc", "Configured network endpoints (RSS, ComicVine, ..)" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/netconfig/edit'); ?>">
					<?php echo $this->localizedLabel( "AddEndpointsLink", "Add New Endpoint" ); ?></a>
				<p><?php echo $this->localizedLabel( "AddEndpointsLink-desc", "Create a new connection" ); ?></p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-processing.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/upgrade/index'); ?>">
					<?php echo $this->localizedLabel( "VersionLink", "Check Configuration" ); ?></a>
				<p><?php echo $this->localizedLabel( "VersionLink-desc", "Review current version and configuration values" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/upgrade/upgradeEligibility'); ?>">
					<?php echo $this->localizedLabel( "UpgradeLink", "Check Version" ); ?></a>
				<p><?php echo $this->localizedLabel( "UpgradeLink-desc",
					"Check for new versions of this software and upgrade if necessary." );
				 ?></p>
			</div>
		</div>

	</div>

