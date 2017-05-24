/**
	SQLite table creation script
 */


/** ARTIST */
CREATE TABLE IF NOT EXISTS artist (
			id INTEGER PRIMARY KEY,
			created INTEGER,
			name TEXT,
			desc TEXT,
			gender TEXT,
			birth_date INTEGER,
			death_date INTEGER,
			pub_wanted INTEGER,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER
		);

/** ARTIST_ALIAS */
CREATE TABLE IF NOT EXISTS artist_alias (
			id INTEGER PRIMARY KEY,
			name TEXT,
			artist_id INTEGER,
			FOREIGN KEY ( artist_id ) REFERENCES artist ( id )
		);

/** ARTIST_ROLE */
CREATE TABLE IF NOT EXISTS artist_role (
			code TEXT PRIMARY KEY,
			name TEXT,
			enabled INTEGER
		);

/** ENDPOINT_TYPE */
CREATE TABLE IF NOT EXISTS endpoint_type (
			code TEXT PRIMARY KEY,
			name TEXT,
			comments TEXT,
			data_type TEXT,
			site_url TEXT,
			api_url TEXT,
			favicon_url TEXT,
			throttle_hits INTEGER,
			throttle_time INTEGER
		);

/** ENDPOINT */
CREATE TABLE IF NOT EXISTS endpoint (
			id INTEGER PRIMARY KEY,
			type_code TEXT,
			name TEXT,
			base_url TEXT,
			api_key TEXT,
			username TEXT,
			error_count INTEGER,
			parameter TEXT,
			enabled INTEGER,
			compressed INTEGER,
			FOREIGN KEY ( type_code ) REFERENCES endpoint_type ( code )
		);

/** FLUX */
CREATE TABLE IF NOT EXISTS flux (
			id INTEGER PRIMARY KEY,
			created INTEGER,
			name TEXT,
			flux_hash TEXT,
			flux_error INTEGER,
			src_endpoint INTEGER,
			src_guid TEXT,
			src_url TEXT,
			src_status TEXT,
			src_pub_date INTEGER,
			dest_endpoint INTEGER,
			dest_guid TEXT,
			dest_status TEXT,
			dest_submission INTEGER,
			FOREIGN KEY ( src_endpoint ) REFERENCES endpoint ( id ),
			FOREIGN KEY ( dest_endpoint ) REFERENCES endpoint ( id )
		);

/** JOB_TYPE */
CREATE TABLE IF NOT EXISTS job_type (
			code TEXT PRIMARY KEY,
			name TEXT,
			desc TEXT,
			processor TEXT,
			parameter TEXT,
			scheduled INTEGER,
			requires_endpoint INTEGER
		);

/** JOB */
CREATE TABLE IF NOT EXISTS job (
			id INTEGER PRIMARY KEY,
			type_code TEXT,
			endpoint_id INTEGER,
			enabled INTEGER,
			one_shot INTEGER,
			fail_count INTEGER,
			elapsed INTEGER,
			minute TEXT,
			hour TEXT,
			dayOfWeek TEXT,
			parameter TEXT,
			next INTEGER,
			last_run INTEGER,
			last_fail INTEGER,
			created INTEGER,
			FOREIGN KEY ( type_code ) REFERENCES job_type ( code ),
			FOREIGN KEY ( endpoint_id ) REFERENCES endpoint ( id )
		);

/** JOB_RUNNING */
CREATE TABLE IF NOT EXISTS job_running (
			id INTEGER PRIMARY KEY,
			job_id INTEGER,
			type_code TEXT,
			processor TEXT,
			guid TEXT,
			pid INTEGER,
			desc TEXT,
			created INTEGER,
			FOREIGN KEY ( job_id ) REFERENCES job ( id ),
			FOREIGN KEY ( type_code ) REFERENCES job_type ( code )
		);

/** LOG_LEVEL */
CREATE TABLE IF NOT EXISTS log_level (
			code TEXT PRIMARY KEY,
			name TEXT
		);

/** LOG */
CREATE TABLE IF NOT EXISTS log (
			id INTEGER PRIMARY KEY,
			trace TEXT,
			trace_id TEXT,
			context TEXT,
			context_id TEXT,
			message TEXT,
			session TEXT,
			level_code TEXT,
			created INTEGER,
			FOREIGN KEY ( level_code ) REFERENCES log_level ( code )
		);

/** MEDIA_TYPE */
CREATE TABLE IF NOT EXISTS media_type (
			code TEXT PRIMARY KEY,
			name TEXT
		);

/** NETWORK */
CREATE TABLE IF NOT EXISTS network (
			id INTEGER PRIMARY KEY,
			ip_address TEXT,
			ip_hash TEXT,
			created INTEGER,
			disable INTEGER
		);

/** PUBLISHER */
CREATE TABLE IF NOT EXISTS publisher (
			id INTEGER PRIMARY KEY,
			name TEXT,
			created INTEGER,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER
		);

/** CHARACTER */
CREATE TABLE IF NOT EXISTS character (
			id INTEGER PRIMARY KEY,
			publisher_id INTEGER,
			created INTEGER,
			name TEXT,
			realname TEXT,
			desc TEXT,
			popularity INTEGER,
			gender TEXT,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER,
			FOREIGN KEY ( publisher_id ) REFERENCES publisher ( id )
		);

/** CHARACTER_ALIAS */
CREATE TABLE IF NOT EXISTS character_alias (
			id INTEGER PRIMARY KEY,
			name TEXT,
			character_id INTEGER,
			FOREIGN KEY ( character_id ) REFERENCES character ( id )
		);

/** PULL_LIST */
CREATE TABLE IF NOT EXISTS pull_list (
			id INTEGER PRIMARY KEY,
			name TEXT,
			etag TEXT,
			created INTEGER,
			published INTEGER,
			endpoint_id INTEGER,
			FOREIGN KEY ( endpoint_id ) REFERENCES endpoint ( id )
		);

/** PULL_LIST_EXCL */
CREATE TABLE IF NOT EXISTS pull_list_excl (
			id INTEGER PRIMARY KEY,
			pattern TEXT,
			type TEXT,
			created INTEGER,
			endpoint_type_code TEXT,
			FOREIGN KEY ( endpoint_type_code ) REFERENCES endpoint_type ( code )
		);

/** PULL_LIST_EXPANSION */
CREATE TABLE IF NOT EXISTS pull_list_expansion (
			id INTEGER PRIMARY KEY,
			pattern TEXT,
			replace TEXT,
			sequence INTEGER,
			created INTEGER,
			endpoint_type_code TEXT,
			FOREIGN KEY ( endpoint_type_code ) REFERENCES endpoint_type ( code )
		);

/** PULL_LIST_GROUP */
CREATE TABLE IF NOT EXISTS pull_list_group (
			id INTEGER PRIMARY KEY,
			name TEXT,
			data TEXT,
			created INTEGER
		);

/** PULL_LIST_ITEM */
CREATE TABLE IF NOT EXISTS pull_list_item (
			id INTEGER PRIMARY KEY,
			data TEXT,
			created INTEGER,
			search_name TEXT,
			name TEXT,
			issue TEXT,
			year INTEGER,
			pull_list_id INTEGER,
			pull_list_group_id INTEGER,
			FOREIGN KEY ( pull_list_group_id ) REFERENCES pull_list_group ( id ),
			FOREIGN KEY ( pull_list_id ) REFERENCES pull_list ( id )
		);

/** RSS */
CREATE TABLE IF NOT EXISTS rss (
			id INTEGER PRIMARY KEY,
			endpoint_id INTEGER,
			created INTEGER,
			title TEXT,
			desc TEXT,
			pub_date INTEGER,
			guid TEXT,
			clean_name TEXT,
			clean_issue TEXT,
			clean_year INTEGER,
			enclosure_url TEXT,
			enclosure_length INTEGER,
			enclosure_mime TEXT,
			enclosure_hash TEXT,
			enclosure_password INTEGER,
			FOREIGN KEY ( endpoint_id ) REFERENCES endpoint ( id )
		);

/** SERIES */
CREATE TABLE IF NOT EXISTS series (
			id INTEGER PRIMARY KEY,
			publisher_id INTEGER,
			created INTEGER,
			name TEXT,
			search_name TEXT,
			desc TEXT,
			start_year INTEGER,
			issue_count INTEGER,
			pub_active INTEGER,
			pub_wanted INTEGER,
			pub_available INTEGER,
			pub_cycle INTEGER,
			pub_count INTEGER,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER,
			FOREIGN KEY ( publisher_id ) REFERENCES publisher ( id )
		);

/** PUBLICATION */
CREATE TABLE IF NOT EXISTS publication (
			id INTEGER PRIMARY KEY,
			series_id INTEGER,
			created INTEGER,
			name TEXT,
			desc TEXT,
			pub_date INTEGER,
			issue_num TEXT,
			issue_order INTEGER,
			media_count INTEGER,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER,
			search_date INTEGER,
			FOREIGN KEY ( series_id ) REFERENCES series ( id )
		);

/** PUBLICATION_ARTIST */
CREATE TABLE IF NOT EXISTS publication_artist (
			id INTEGER PRIMARY KEY,
			publication_id INTEGER,
			artist_id INTEGER,
			role_code TEXT,
			FOREIGN KEY ( publication_id ) REFERENCES publication ( id ),
			FOREIGN KEY ( artist_id ) REFERENCES artist ( id ),
			FOREIGN KEY ( role_code ) REFERENCES artist_role ( code )
		);

/** PUBLICATION_CHARACTER */
CREATE TABLE IF NOT EXISTS publication_character (
			id INTEGER PRIMARY KEY,
			publication_id INTEGER,
			character_id INTEGER,
			FOREIGN KEY ( publication_id ) REFERENCES publication ( id ),
			FOREIGN KEY ( character_id ) REFERENCES character ( id )
		);

/** SERIES_ALIAS */
CREATE TABLE IF NOT EXISTS series_alias (
			id INTEGER PRIMARY KEY,
			name TEXT,
			series_id INTEGER,
			FOREIGN KEY ( series_id ) REFERENCES series ( id )
		);

/** MEDIA */
CREATE TABLE IF NOT EXISTS media (
			id INTEGER PRIMARY KEY,
			publication_id INTEGER,
			type_code TEXT,
			filename TEXT,
			original_filename TEXT,
			checksum TEXT,
			created INTEGER,
			size INTEGER,
			FOREIGN KEY ( type_code ) REFERENCES media_type ( code ),
			FOREIGN KEY ( publication_id ) REFERENCES publication ( id )
		);

/** SERIES_ARTIST */
CREATE TABLE IF NOT EXISTS series_artist (
			id INTEGER PRIMARY KEY,
			series_id INTEGER,
			artist_id INTEGER,
			role_code TEXT,
			FOREIGN KEY ( series_id ) REFERENCES series ( id ),
			FOREIGN KEY ( artist_id ) REFERENCES artist ( id ),
			FOREIGN KEY ( role_code ) REFERENCES artist_role ( code )
		);

/** SERIES_CHARACTER */
CREATE TABLE IF NOT EXISTS series_character (
			id INTEGER PRIMARY KEY,
			series_id INTEGER,
			character_id INTEGER,
			FOREIGN KEY ( series_id ) REFERENCES series ( id ),
			FOREIGN KEY ( character_id ) REFERENCES character ( id )
		);

/** STORY_ARC */
CREATE TABLE IF NOT EXISTS story_arc (
			id INTEGER PRIMARY KEY,
			publisher_id INTEGER,
			created INTEGER,
			name TEXT,
			desc TEXT,
			pub_active INTEGER,
			pub_wanted INTEGER,
			pub_cycle INTEGER,
			pub_available INTEGER,
			pub_count INTEGER,
			xurl TEXT,
			xsource TEXT,
			xid TEXT,
			xupdated INTEGER,
			FOREIGN KEY ( publisher_id ) REFERENCES publisher ( id )
		);

/** STORY_ARC_CHARACTER */
CREATE TABLE IF NOT EXISTS story_arc_character (
			id INTEGER PRIMARY KEY,
			story_arc_id INTEGER,
			character_id INTEGER,
			FOREIGN KEY ( story_arc_id ) REFERENCES story_arc ( id ),
			FOREIGN KEY ( character_id ) REFERENCES character ( id )
		);

/** STORY_ARC_PUBLICATION */
CREATE TABLE IF NOT EXISTS story_arc_publication (
			id INTEGER PRIMARY KEY,
			story_arc_id INTEGER,
			publication_id INTEGER,
			FOREIGN KEY ( story_arc_id ) REFERENCES story_arc ( id ),
			FOREIGN KEY ( publication_id ) REFERENCES publication ( id )
		);

/** STORY_ARC_SERIES */
CREATE TABLE IF NOT EXISTS story_arc_series (
			id INTEGER PRIMARY KEY,
			story_arc_id INTEGER,
			series_id INTEGER,
			FOREIGN KEY ( story_arc_id ) REFERENCES story_arc ( id ),
			FOREIGN KEY ( series_id ) REFERENCES series ( id )
		);

/** USERS */
CREATE TABLE IF NOT EXISTS users (
			id INTEGER PRIMARY KEY,
			name TEXT,
			email TEXT,
			active INTEGER,
			account_type TEXT,
			rememberme_token TEXT,
			api_hash TEXT,
			password_hash TEXT,
			password_reset_hash TEXT,
			activation_hash TEXT,
			failed_logins INTEGER,
			created INTEGER,
			last_login_timestamp INTEGER,
			last_failed_login INTEGER,
			password_reset_timestamp INTEGER
		);

/** READING_ITEM */
CREATE TABLE IF NOT EXISTS reading_item (
			id INTEGER PRIMARY KEY,
			user_id INTEGER,
			publication_id INTEGER,
			created INTEGER,
			read_date INTEGER,
			mislabeled INTEGER,
			FOREIGN KEY ( user_id ) REFERENCES users ( id ),
			FOREIGN KEY ( publication_id ) REFERENCES publication ( id )
		);

/** READING_QUEUE */
CREATE TABLE IF NOT EXISTS reading_queue (
			id INTEGER PRIMARY KEY,
			user_id INTEGER,
			series_id INTEGER,
			story_arc_id INTEGER,
			created INTEGER,
			title TEXT,
			favorite INTEGER,
			pub_count INTEGER,
			pub_read INTEGER,
			queue_order INTEGER,
			FOREIGN KEY ( user_id ) REFERENCES users ( id ),
			FOREIGN KEY ( series_id ) REFERENCES series ( id ),
			FOREIGN KEY ( story_arc_id ) REFERENCES story_arc ( id )
		);

/** USER_NETWORK */
CREATE TABLE IF NOT EXISTS user_network (
			id INTEGER PRIMARY KEY,
			user_id INTEGER,
			network_id INTEGER,
			FOREIGN KEY ( user_id ) REFERENCES users ( id ),
			FOREIGN KEY ( network_id ) REFERENCES network ( id )
		);

/** VERSION */
CREATE TABLE IF NOT EXISTS version (
			id INTEGER PRIMARY KEY,
			code TEXT,
			major INTEGER,
			minor INTEGER,
			patch INTEGER,
			created INTEGER
		);

/** PATCH */
CREATE TABLE IF NOT EXISTS patch (
			id INTEGER PRIMARY KEY,
			name TEXT,
			created INTEGER,
			version_id INTEGER,
			FOREIGN KEY ( version_id ) REFERENCES version ( id )
		);
