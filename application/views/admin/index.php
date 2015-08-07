	<div class="admin_group">
		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-users.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminUsers/userlist'); ?>"><?php echo $this->label( "UsersLink", "name" ); ?></a>
				<p><?php echo $this->label( "UsersLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminUsers/editUser'); ?>"><?php echo $this->label( "AddUsersLink", "name" ); ?></a>
				<p><?php echo $this->label( "AddUsersLink", "desc" ); ?></p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-uploads.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/Upload/index'); ?>"><?php echo $this->label( "UploadLink", "name" ); ?></a>
				<p><?php echo $this->label( "UploadLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminUploadRepair/index'); ?>"><?php echo $this->label( "RepairLink", "name" ); ?></a>
				<p><?php echo $this->label( "RepairLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminWanted/index'); ?>">Wanted Publications</a>
				<p>Wanted</p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-daemons.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminJobs/index'); ?>"><?php echo $this->label( "JobsLink", "name" ); ?></a>
				<p><?php echo $this->label( "JobsLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminJobs/runningIndex'); ?>"><?php echo $this->label( "JobsRunningLink", "name" ); ?></a>
				<p><?php echo $this->label( "JobsRunningLink", "desc" ); ?></p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-publications.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminPublishers/index'); ?>">Publishers</a>
				<p>All Publishers</p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">Characters</a>
				<p>All Characters</p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminStoryArcs/index'); ?>">Story Arcs</a>
				<p>All Story Arcs</p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminSeries/index'); ?>">Series</a>
				<p>Flagged series</p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/AdminPublication/index'); ?>">Publications</a>
				<p>Flagged issues</p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-network.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/netconfig/index'); ?>">
					<?php echo $this->label( "EndpointsLink", "name" ); ?></a>
				<p><?php echo $this->label( "EndpointsLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/netconfig/edit'); ?>">
					<?php echo $this->label( "AddEndpointsLink", "name" ); ?></a>
				<p><?php echo $this->label( "AddEndpointsLink", "desc" ); ?></p>
			</div>
		</div>

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-processing.png'); ?>">
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/upgrade/index'); ?>">
					<?php echo $this->label( "VersionLink", "name" ); ?></a>
				<p><?php echo $this->label( "VersionLink", "desc" ); ?></p>
			</div>
			<div class="admin_card_item">
				<a href="<?php echo Config::Web('/upgrade/upgradeEligibility'); ?>">
					<?php echo $this->label( "UpgradeLink", "name" ); ?></a>
				<p><?php echo $this->label( "UpgradeLink", "desc" ); ?></p>
			</div>
		</div>

	</div>

