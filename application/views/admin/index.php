<section>
<div class="row">
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-publications.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/AdminPublishers/index'); ?>">
				<div class="admin_card_item">
					<h3>Publishers</h3>
					<p>All Publishers</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">
				<div class="admin_card_item">
					<h3>Characters</h3>
					<p>All Characters</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminStoryArcs/index'); ?>">
				<div class="admin_card_item">
					<h3>Story Arcs</h3>
					<p>All Story Arcs</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminSeries/index'); ?>">
				<div class="admin_card_item">
					<h3>Series</h3>
					<p>Flagged series</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminPublication/index'); ?>">
				<div class="admin_card_item">
					<h3>Publications</h3>
					<p>Flagged issues</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminMedia/index'); ?>">
				<div class="admin_card_item">
					<h3>Media</h3>
					<p>Manage Media Files</p>
				</div>
			</a>
		</div>

</div>

<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-uploads.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/Upload/index'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "UploadLink", "name" ); ?></h3>
					<p><?php echo $this->label( "UploadLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminUploadRepair/index'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "RepairLink", "name" ); ?></h3>
					<p><?php echo $this->label( "RepairLink", "desc" ); ?></p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-search.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/AdminWanted/index'); ?>">
				<div class="admin_card_item">
					<h3>Wanted Publications</h3>
					<p>Shows wanted publications grouped by date published</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminWanted/index_series'); ?>">
				<div class="admin_card_item">
					<h3>Wanted Series</h3>
					<p>Shows wanted publications grouped by Series</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminWanted/index_story_arc'); ?>">
				<div class="admin_card_item">
					<h3>Wanted Story Arcs</h3>
					<p>Shows wanted publications grouped by Story Arcs</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminWanted/newznab'); ?>">
				<div class="admin_card_item">
					<h3>Manual Search (NZB)</h3>
					<p>Search any configured Newznab sites for specific content</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminWanted/enqueued'); ?>">
				<div class="admin_card_item">
					<h3>Enqueued Search</h3>
					<p>Publication enqueued for automated NZB searching</p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-rss.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/AdminPullList/rssindex'); ?>">
				<div class="admin_card_item">
					<h3>RSS</h3>
					<p>Review rss items</p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminPullList/index'); ?>">
				<div class="admin_card_item">
					<h3>Pull Lists</h3>
					<p>Review lists of newly published comics</p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-users.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/AdminUsers/userlist'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "UsersLink", "name" ); ?></h3>
					<p><?php echo $this->label( "UsersLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminUsers/editUser'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "AddUsersLink", "name" ); ?></h3>
					<p><?php echo $this->label( "AddUsersLink", "desc" ); ?></p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-daemons.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/AdminJobs/index'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "JobsLink", "name" ); ?></h3>
					<p><?php echo $this->label( "JobsLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/Admin/updatePending'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "PendingUpdatesLink", "name" ); ?></h3>
					<p><?php echo $this->label( "PendingUpdatesLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/AdminJobs/runningIndex'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "JobsRunningLink", "name" ); ?></h3>
					<p><?php echo $this->label( "JobsRunningLink", "desc" ); ?></p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-network.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/netconfig/index'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "EndpointsLink", "name" ); ?></h3>
					<p><?php echo $this->label( "EndpointsLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/netconfig/edit'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "AddEndpointsLink", "name" ); ?></h3>
					<p><?php echo $this->label( "AddEndpointsLink", "desc" ); ?></p>
				</div>
			</a>
		</div>

</div>
<div class="grid_3">

		<div class="admin_card">
			<div class="admin_card_top">
				<img src="<?php echo Config::Web('/public/img/admin-processing.png'); ?>">
			</div>
			<a href="<?php echo Config::Web('/upgrade/index'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "VersionLink", "name" ); ?></h3>
					<p><?php echo $this->label( "VersionLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/upgrade/upgradeEligibility'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "UpgradeLink", "name" ); ?></h3>
					<p><?php echo $this->label( "UpgradeLink", "desc" ); ?></p>
				</div>
			</a>
			<a href="<?php echo Config::Web('/upgrade/reviewDefaultData'); ?>">
				<div class="admin_card_item">
					<h3><?php echo $this->label( "ReviewDefaultDataLink", "name" ); ?></h3>
					<p><?php echo $this->label( "ReviewDefaultDataLink", "desc" ); ?></p>
				</div>
			</a>
		</div>


</div>
</div>
</section>

