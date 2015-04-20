<?php if (is_array($this->issue) && count($this->issue) > 0): ?>
<div class="mediaData">
	<table>
		<tr>
			<th>Issue</th>
			<th>Published</th>
			<th>Name</th>
			<th></th>
		</tr>
		<?php if ( count(array_filter(array_keys($this->issue), 'is_string')) > 0 ): ?>
			<tr>
				<td class="issue"><?php echo $this->issue['issue_number']; ?></td>
				<td class="published">
					<img src="<?php echo $this->issue['image']['thumb_url'] ?>" class="thumbnail" /><br>
					<nobr><?php echo $this->issue['cover_date']; ?></nobr>
				</td>
				<td class="name">
					<h3>
						<a target="comicvine" href="<?php echo $this->issue['site_detail_url']; ?>">
							<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
								alt="ComicVine">
						</a>
						<?php echo $this->issue['volume']['name']; ?>
					</h3>
					<h4><?php echo $this->issue['name']; ?></h4>
					<span><?php echo $this->issue['description']; ?></span>				</td>
				<td><?php
					echo '<a class="btn" href="'. Config::Web('/processing/comicVine_accept/', $this->key, $this->issue['id'])
					. '">Accept</a>';
					?></td>
			</tr>
		<?php else: ?>
			<?php foreach ($this->issue as $idx => $item): ?>
			<tr>
				<td class="issue"><?php echo $item['issue_number']; ?></td>
				<td class="published">
					<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" /><br>
					<nobr><?php echo $item['cover_date']; ?></nobr>
				</td>
				<td class="name">
					<h3>
						<a target="comicvine" href="<?php echo $item['site_detail_url']; ?>">
							<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
								alt="ComicVine">
						</a>
						<?php echo $item['volume']['name']; ?>
					</h3>
					<h4><?php echo $item['name']; ?></h4>
					<span><?php
						if ( isset($item['deck']) && strlen($item['deck']) > 0) { echo $item['deck']; }
						else { echo $item['description']; }
					?></span>
				</td>
				<td>
					<a class="btn" href="<?php echo Config::Web('/processing/comicVine_accept/', $this->key, $item['id']); ?>">Accept</a>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
</div>

<?php else: ?>
No results found
<?php endif; ?>

