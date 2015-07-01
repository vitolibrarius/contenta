<script>
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "userlist", "DeleteDescription"); ?>',
				confirm: '<?php echo $this->label( "userlist", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('action')
			});
			e.preventDefault();
		});
	});
</script>
<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "active" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "email" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "last_login_timestamp" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "last_failed_login" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "api_hash" ); ?></th>
			<th colspan=2></th>
		</tr>
	<?php
		if ($this->users) {
			foreach($this->users as $key => $value) {
				echo '<tr>
					<td><span class="icon ' . ($value->isActive() ? 'true' : 'false') . '"></span></td>
					<td>' . htmlentities($value->name) . '</td>
					<td>' . $value->email .'</td>
					<td>' . $value->account_type .'</td>
					<td>' . $value->lastLoginDate() .'</td>
					<td>' . $value->lastFailedLoginDate() .'</td>
					<td>' . $value->api_hash .'</td>
					<td><a href="'. Config::Web('/AdminUsers/editUser/') . $value->id . '"><span class="icon edit"></span></a></td>
					<td><a class="confirm" action="'. Config::Web('/AdminUsers/deleteUser/') . $value->id . '" href="#"><span class="icon recycle"></span></a></td>
				</tr>';
			}
		} else {
			echo 'No users yet. Create some !';
		}
	?>
	</table>
</div>

