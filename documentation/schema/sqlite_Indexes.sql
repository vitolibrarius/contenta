
/** ARTIST */
DROP INDEX IF EXISTS artist_name;
DROP INDEX IF EXISTS artist_name_01;
CREATE  INDEX IF NOT EXISTS artist_name_01 on artist (name COLLATE NOCASE);
DROP INDEX IF EXISTS artist_xidxsource;
DROP INDEX IF EXISTS artist_xidxsource_02;
CREATE UNIQUE INDEX IF NOT EXISTS artist_xidxsource_02 on artist (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** ARTIST_ALIAS */
DROP INDEX IF EXISTS artist_aliasArtist_fk;
DROP INDEX IF EXISTS artist_aliasArtist_a_01_fk;
CREATE INDEX IF NOT EXISTS artist_aliasArtist_a_01_fk on artist_alias (artist_id);
DROP INDEX IF EXISTS artist_alias_artist_idname;
DROP INDEX IF EXISTS artist_alias_artist__02;
CREATE UNIQUE INDEX IF NOT EXISTS artist_alias_artist__02 on artist_alias (artist_id,name COLLATE NOCASE);

/** ARTIST_ROLE */
DROP INDEX IF EXISTS artist_role_name;
DROP INDEX IF EXISTS artist_role_name_01;
CREATE  INDEX IF NOT EXISTS artist_role_name_01 on artist_role (name COLLATE NOCASE);

/** BOOK */
DROP INDEX IF EXISTS bookMedia_Type_fk;
DROP INDEX IF EXISTS bookMedia_Type_type__01_fk;
CREATE INDEX IF NOT EXISTS bookMedia_Type_type__01_fk on book (type_code);
DROP INDEX IF EXISTS book_name;
DROP INDEX IF EXISTS book_name_02;
CREATE  INDEX IF NOT EXISTS book_name_02 on book (name COLLATE NOCASE);
DROP INDEX IF EXISTS book_author;
DROP INDEX IF EXISTS book_author_03;
CREATE  INDEX IF NOT EXISTS book_author_03 on book (author COLLATE NOCASE);
DROP INDEX IF EXISTS book_filename;
DROP INDEX IF EXISTS book_filename_04;
CREATE  INDEX IF NOT EXISTS book_filename_04 on book (filename);
DROP INDEX IF EXISTS book_checksum;
DROP INDEX IF EXISTS book_checksum_05;
CREATE UNIQUE INDEX IF NOT EXISTS book_checksum_05 on book (checksum COLLATE NOCASE);

/** CHARACTER */
DROP INDEX IF EXISTS characterPublisher_fk;
DROP INDEX IF EXISTS characterPublisher_p_01_fk;
CREATE INDEX IF NOT EXISTS characterPublisher_p_01_fk on character (publisher_id);
DROP INDEX IF EXISTS character_name;
DROP INDEX IF EXISTS character_name_02;
CREATE  INDEX IF NOT EXISTS character_name_02 on character (name COLLATE NOCASE);
DROP INDEX IF EXISTS character_namepopularity;
DROP INDEX IF EXISTS character_namepopula_03;
CREATE  INDEX IF NOT EXISTS character_namepopula_03 on character (name COLLATE NOCASE,popularity);
DROP INDEX IF EXISTS character_realname;
DROP INDEX IF EXISTS character_realname_04;
CREATE  INDEX IF NOT EXISTS character_realname_04 on character (realname COLLATE NOCASE);
DROP INDEX IF EXISTS character_xidxsource;
DROP INDEX IF EXISTS character_xidxsource_05;
CREATE UNIQUE INDEX IF NOT EXISTS character_xidxsource_05 on character (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** CHARACTER_ALIAS */
DROP INDEX IF EXISTS character_aliasCharacter_fk;
DROP INDEX IF EXISTS character_aliasChara_01_fk;
CREATE INDEX IF NOT EXISTS character_aliasChara_01_fk on character_alias (character_id);
DROP INDEX IF EXISTS character_alias_character_idname;
DROP INDEX IF EXISTS character_alias_char_02;
CREATE UNIQUE INDEX IF NOT EXISTS character_alias_char_02 on character_alias (character_id,name COLLATE NOCASE);

/** ENDPOINT */
DROP INDEX IF EXISTS endpointEndpoint_Type_fk;
DROP INDEX IF EXISTS endpointEndpoint_Typ_01_fk;
CREATE INDEX IF NOT EXISTS endpointEndpoint_Typ_01_fk on endpoint (type_code);

/** ENDPOINT_TYPE */
DROP INDEX IF EXISTS endpoint_type_name;
DROP INDEX IF EXISTS endpoint_type_name_01;
CREATE UNIQUE INDEX IF NOT EXISTS endpoint_type_name_01 on endpoint_type (name COLLATE NOCASE);

/** FLUX */
DROP INDEX IF EXISTS fluxEndpoint_fk;
DROP INDEX IF EXISTS fluxEndpoint_src_end_01_fk;
CREATE INDEX IF NOT EXISTS fluxEndpoint_src_end_01_fk on flux (src_endpoint);
DROP INDEX IF EXISTS fluxEndpoint_fk;
DROP INDEX IF EXISTS fluxEndpoint_dest_en_02_fk;
CREATE INDEX IF NOT EXISTS fluxEndpoint_dest_en_02_fk on flux (dest_endpoint);
DROP INDEX IF EXISTS flux_src_guid;
DROP INDEX IF EXISTS flux_src_guid_03;
CREATE UNIQUE INDEX IF NOT EXISTS flux_src_guid_03 on flux (src_guid COLLATE NOCASE);
DROP INDEX IF EXISTS flux_dest_guid;
DROP INDEX IF EXISTS flux_dest_guid_04;
CREATE UNIQUE INDEX IF NOT EXISTS flux_dest_guid_04 on flux (dest_guid COLLATE NOCASE);
DROP INDEX IF EXISTS flux_flux_hash;
DROP INDEX IF EXISTS flux_flux_hash_05;
CREATE  INDEX IF NOT EXISTS flux_flux_hash_05 on flux (flux_hash COLLATE NOCASE);

/** JOB */
DROP INDEX IF EXISTS jobJob_Type_fk;
DROP INDEX IF EXISTS jobJob_Type_type_cod_01_fk;
CREATE INDEX IF NOT EXISTS jobJob_Type_type_cod_01_fk on job (type_code);
DROP INDEX IF EXISTS jobEndpoint_fk;
DROP INDEX IF EXISTS jobEndpoint_endpoint_02_fk;
CREATE INDEX IF NOT EXISTS jobEndpoint_endpoint_02_fk on job (endpoint_id);

/** JOB_RUNNING */
DROP INDEX IF EXISTS job_runningJob_fk;
DROP INDEX IF EXISTS job_runningJob_job_i_01_fk;
CREATE INDEX IF NOT EXISTS job_runningJob_job_i_01_fk on job_running (job_id);
DROP INDEX IF EXISTS job_runningJob_Type_fk;
DROP INDEX IF EXISTS job_runningJob_Type__02_fk;
CREATE INDEX IF NOT EXISTS job_runningJob_Type__02_fk on job_running (type_code);
DROP INDEX IF EXISTS job_running_pid;
DROP INDEX IF EXISTS job_running_pid_03;
CREATE UNIQUE INDEX IF NOT EXISTS job_running_pid_03 on job_running (pid);

/** JOB_TYPE */
DROP INDEX IF EXISTS job_type_name;
DROP INDEX IF EXISTS job_type_name_01;
CREATE UNIQUE INDEX IF NOT EXISTS job_type_name_01 on job_type (name COLLATE NOCASE);

/** LOG */
DROP INDEX IF EXISTS logLog_Level_fk;
DROP INDEX IF EXISTS logLog_Level_level_c_01_fk;
CREATE INDEX IF NOT EXISTS logLog_Level_level_c_01_fk on log (level_code);
DROP INDEX IF EXISTS log_level_code;
DROP INDEX IF EXISTS log_level_code_02;
CREATE  INDEX IF NOT EXISTS log_level_code_02 on log (level_code);
DROP INDEX IF EXISTS log_created;
DROP INDEX IF EXISTS log_created_03;
CREATE  INDEX IF NOT EXISTS log_created_03 on log (created);
DROP INDEX IF EXISTS log_tracetrace_id;
DROP INDEX IF EXISTS log_tracetrace_id_04;
CREATE  INDEX IF NOT EXISTS log_tracetrace_id_04 on log (trace COLLATE NOCASE,trace_id COLLATE NOCASE);
DROP INDEX IF EXISTS log_contextcontext_id;
DROP INDEX IF EXISTS log_contextcontext_i_05;
CREATE  INDEX IF NOT EXISTS log_contextcontext_i_05 on log (context COLLATE NOCASE,context_id COLLATE NOCASE);

/** LOG_LEVEL */
DROP INDEX IF EXISTS log_level_name;
DROP INDEX IF EXISTS log_level_name_01;
CREATE UNIQUE INDEX IF NOT EXISTS log_level_name_01 on log_level (name COLLATE NOCASE);

/** MEDIA */
DROP INDEX IF EXISTS mediaMedia_Type_fk;
DROP INDEX IF EXISTS mediaMedia_Type_type_01_fk;
CREATE INDEX IF NOT EXISTS mediaMedia_Type_type_01_fk on media (type_code);
DROP INDEX IF EXISTS mediaPublication_fk;
DROP INDEX IF EXISTS mediaPublication_pub_02_fk;
CREATE INDEX IF NOT EXISTS mediaPublication_pub_02_fk on media (publication_id);
DROP INDEX IF EXISTS media_filename;
DROP INDEX IF EXISTS media_filename_03;
CREATE  INDEX IF NOT EXISTS media_filename_03 on media (filename);
DROP INDEX IF EXISTS media_checksum;
DROP INDEX IF EXISTS media_checksum_04;
CREATE UNIQUE INDEX IF NOT EXISTS media_checksum_04 on media (checksum COLLATE NOCASE);

/** MEDIA_TYPE */
DROP INDEX IF EXISTS media_type_name;
DROP INDEX IF EXISTS media_type_name_01;
CREATE  INDEX IF NOT EXISTS media_type_name_01 on media_type (name COLLATE NOCASE);

/** NETWORK */
DROP INDEX IF EXISTS network_ip_address;
DROP INDEX IF EXISTS network_ip_address_01;
CREATE UNIQUE INDEX IF NOT EXISTS network_ip_address_01 on network (ip_address COLLATE NOCASE);
DROP INDEX IF EXISTS network_ip_hash;
DROP INDEX IF EXISTS network_ip_hash_02;
CREATE UNIQUE INDEX IF NOT EXISTS network_ip_hash_02 on network (ip_hash COLLATE NOCASE);

/** PATCH */
DROP INDEX IF EXISTS patchVersion_fk;
DROP INDEX IF EXISTS patchVersion_version_01_fk;
CREATE INDEX IF NOT EXISTS patchVersion_version_01_fk on patch (version_id);
DROP INDEX IF EXISTS patch_name;
DROP INDEX IF EXISTS patch_name_02;
CREATE UNIQUE INDEX IF NOT EXISTS patch_name_02 on patch (name COLLATE NOCASE);

/** PUBLICATION */
DROP INDEX IF EXISTS publicationSeries_fk;
DROP INDEX IF EXISTS publicationSeries_se_01_fk;
CREATE INDEX IF NOT EXISTS publicationSeries_se_01_fk on publication (series_id);
DROP INDEX IF EXISTS publication_name;
DROP INDEX IF EXISTS publication_name_02;
CREATE  INDEX IF NOT EXISTS publication_name_02 on publication (name COLLATE NOCASE);
DROP INDEX IF EXISTS publication_issue_orderpub_date;
DROP INDEX IF EXISTS publication_issue_or_03;
CREATE  INDEX IF NOT EXISTS publication_issue_or_03 on publication (issue_order,pub_date);
DROP INDEX IF EXISTS publication_xidxsource;
DROP INDEX IF EXISTS publication_xidxsour_04;
CREATE UNIQUE INDEX IF NOT EXISTS publication_xidxsour_04 on publication (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** PUBLICATION_ARTIST */
DROP INDEX IF EXISTS publication_artistPublication_fk;
DROP INDEX IF EXISTS publication_artistPu_01_fk;
CREATE INDEX IF NOT EXISTS publication_artistPu_01_fk on publication_artist (publication_id);
DROP INDEX IF EXISTS publication_artistArtist_fk;
DROP INDEX IF EXISTS publication_artistAr_02_fk;
CREATE INDEX IF NOT EXISTS publication_artistAr_02_fk on publication_artist (artist_id);
DROP INDEX IF EXISTS publication_artistArtist_Role_fk;
DROP INDEX IF EXISTS publication_artistAr_03_fk;
CREATE INDEX IF NOT EXISTS publication_artistAr_03_fk on publication_artist (role_code);
DROP INDEX IF EXISTS publication_artist_publication_idartist_idrole_code;
DROP INDEX IF EXISTS publication_artist_p_04;
CREATE UNIQUE INDEX IF NOT EXISTS publication_artist_p_04 on publication_artist (publication_id,artist_id,role_code);

/** PUBLICATION_CHARACTER */
DROP INDEX IF EXISTS publication_characterPublication_fk;
DROP INDEX IF EXISTS publication_characte_01_fk;
CREATE INDEX IF NOT EXISTS publication_characte_01_fk on publication_character (publication_id);
DROP INDEX IF EXISTS publication_characterCharacter_fk;
DROP INDEX IF EXISTS publication_characte_02_fk;
CREATE INDEX IF NOT EXISTS publication_characte_02_fk on publication_character (character_id);
DROP INDEX IF EXISTS publication_character_publication_idcharacter_id;
DROP INDEX IF EXISTS publication_characte_03;
CREATE UNIQUE INDEX IF NOT EXISTS publication_characte_03 on publication_character (publication_id,character_id);

/** PUBLISHER */
DROP INDEX IF EXISTS publisher_name;
DROP INDEX IF EXISTS publisher_name_01;
CREATE  INDEX IF NOT EXISTS publisher_name_01 on publisher (name COLLATE NOCASE);
DROP INDEX IF EXISTS publisher_xidxsource;
DROP INDEX IF EXISTS publisher_xidxsource_02;
CREATE UNIQUE INDEX IF NOT EXISTS publisher_xidxsource_02 on publisher (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** PULL_LIST */
DROP INDEX IF EXISTS pull_listEndpoint_fk;
DROP INDEX IF EXISTS pull_listEndpoint_en_01_fk;
CREATE INDEX IF NOT EXISTS pull_listEndpoint_en_01_fk on pull_list (endpoint_id);
DROP INDEX IF EXISTS pull_list_etag;
DROP INDEX IF EXISTS pull_list_etag_02;
CREATE UNIQUE INDEX IF NOT EXISTS pull_list_etag_02 on pull_list (etag COLLATE NOCASE);

/** PULL_LIST_EXCL */
DROP INDEX IF EXISTS pull_list_exclEndpoint_Type_fk;
DROP INDEX IF EXISTS pull_list_exclEndpoi_01_fk;
CREATE INDEX IF NOT EXISTS pull_list_exclEndpoi_01_fk on pull_list_excl (endpoint_type_code);

/** PULL_LIST_EXPANSION */
DROP INDEX IF EXISTS pull_list_expansionEndpoint_Type_fk;
DROP INDEX IF EXISTS pull_list_expansionE_01_fk;
CREATE INDEX IF NOT EXISTS pull_list_expansionE_01_fk on pull_list_expansion (endpoint_type_code);

/** PULL_LIST_GROUP */
DROP INDEX IF EXISTS pull_list_group_data;
DROP INDEX IF EXISTS pull_list_group_data_01;
CREATE UNIQUE INDEX IF NOT EXISTS pull_list_group_data_01 on pull_list_group (data COLLATE NOCASE);
DROP INDEX IF EXISTS pull_list_group_name;
DROP INDEX IF EXISTS pull_list_group_name_02;
CREATE  INDEX IF NOT EXISTS pull_list_group_name_02 on pull_list_group (name COLLATE NOCASE);

/** PULL_LIST_ITEM */
DROP INDEX IF EXISTS pull_list_itemPull_List_Group_fk;
DROP INDEX IF EXISTS pull_list_itemPull_L_01_fk;
CREATE INDEX IF NOT EXISTS pull_list_itemPull_L_01_fk on pull_list_item (pull_list_group_id);
DROP INDEX IF EXISTS pull_list_itemPull_List_fk;
DROP INDEX IF EXISTS pull_list_itemPull_L_02_fk;
CREATE INDEX IF NOT EXISTS pull_list_itemPull_L_02_fk on pull_list_item (pull_list_id);
DROP INDEX IF EXISTS pull_list_item_name;
DROP INDEX IF EXISTS pull_list_item_name_03;
CREATE  INDEX IF NOT EXISTS pull_list_item_name_03 on pull_list_item (name COLLATE NOCASE);
DROP INDEX IF EXISTS pull_list_item_search_name;
DROP INDEX IF EXISTS pull_list_item_searc_04;
CREATE  INDEX IF NOT EXISTS pull_list_item_searc_04 on pull_list_item (search_name COLLATE NOCASE);

/** READING_ITEM */
DROP INDEX IF EXISTS reading_itemUsers_fk;
DROP INDEX IF EXISTS reading_itemUsers_us_01_fk;
CREATE INDEX IF NOT EXISTS reading_itemUsers_us_01_fk on reading_item (user_id);
DROP INDEX IF EXISTS reading_itemPublication_fk;
DROP INDEX IF EXISTS reading_itemPublicat_02_fk;
CREATE INDEX IF NOT EXISTS reading_itemPublicat_02_fk on reading_item (publication_id);
DROP INDEX IF EXISTS reading_item_user_idpublication_id;
DROP INDEX IF EXISTS reading_item_user_id_03;
CREATE UNIQUE INDEX IF NOT EXISTS reading_item_user_id_03 on reading_item (user_id,publication_id);
DROP INDEX IF EXISTS reading_item_read_date;
DROP INDEX IF EXISTS reading_item_read_da_04;
CREATE  INDEX IF NOT EXISTS reading_item_read_da_04 on reading_item (read_date);

/** READING_QUEUE */
DROP INDEX IF EXISTS reading_queueUsers_fk;
DROP INDEX IF EXISTS reading_queueUsers_u_01_fk;
CREATE INDEX IF NOT EXISTS reading_queueUsers_u_01_fk on reading_queue (user_id);
DROP INDEX IF EXISTS reading_queueSeries_fk;
DROP INDEX IF EXISTS reading_queueSeries__02_fk;
CREATE INDEX IF NOT EXISTS reading_queueSeries__02_fk on reading_queue (series_id);
DROP INDEX IF EXISTS reading_queueStory_Arc_fk;
DROP INDEX IF EXISTS reading_queueStory_A_03_fk;
CREATE INDEX IF NOT EXISTS reading_queueStory_A_03_fk on reading_queue (story_arc_id);
DROP INDEX IF EXISTS reading_queue_user_idseries_idstory_arc_id;
DROP INDEX IF EXISTS reading_queue_user_i_04;
CREATE UNIQUE INDEX IF NOT EXISTS reading_queue_user_i_04 on reading_queue (user_id,series_id,story_arc_id);

/** RSS */
DROP INDEX IF EXISTS rssEndpoint_fk;
DROP INDEX IF EXISTS rssEndpoint_endpoint_01_fk;
CREATE INDEX IF NOT EXISTS rssEndpoint_endpoint_01_fk on rss (endpoint_id);
DROP INDEX IF EXISTS rss_clean_nameclean_issueclean_year;
DROP INDEX IF EXISTS rss_clean_nameclean__02;
CREATE  INDEX IF NOT EXISTS rss_clean_nameclean__02 on rss (clean_name COLLATE NOCASE,clean_issue COLLATE NOCASE,clean_year);
DROP INDEX IF EXISTS rss_guid;
DROP INDEX IF EXISTS rss_guid_03;
CREATE UNIQUE INDEX IF NOT EXISTS rss_guid_03 on rss (guid COLLATE NOCASE);
DROP INDEX IF EXISTS rss_created;
DROP INDEX IF EXISTS rss_created_04;
CREATE  INDEX IF NOT EXISTS rss_created_04 on rss (created);

/** SERIES */
DROP INDEX IF EXISTS seriesPublisher_fk;
DROP INDEX IF EXISTS seriesPublisher_publ_01_fk;
CREATE INDEX IF NOT EXISTS seriesPublisher_publ_01_fk on series (publisher_id);
DROP INDEX IF EXISTS series_name;
DROP INDEX IF EXISTS series_name_02;
CREATE  INDEX IF NOT EXISTS series_name_02 on series (name COLLATE NOCASE);
DROP INDEX IF EXISTS series_namestart_year;
DROP INDEX IF EXISTS series_namestart_yea_03;
CREATE  INDEX IF NOT EXISTS series_namestart_yea_03 on series (name COLLATE NOCASE,start_year);
DROP INDEX IF EXISTS series_search_name;
DROP INDEX IF EXISTS series_search_name_04;
CREATE  INDEX IF NOT EXISTS series_search_name_04 on series (search_name COLLATE NOCASE);
DROP INDEX IF EXISTS series_search_namepub_wanted;
DROP INDEX IF EXISTS series_search_namepu_05;
CREATE  INDEX IF NOT EXISTS series_search_namepu_05 on series (search_name COLLATE NOCASE,pub_wanted);
DROP INDEX IF EXISTS series_xidxsource;
DROP INDEX IF EXISTS series_xidxsource_06;
CREATE UNIQUE INDEX IF NOT EXISTS series_xidxsource_06 on series (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** SERIES_ALIAS */
DROP INDEX IF EXISTS series_aliasSeries_fk;
DROP INDEX IF EXISTS series_aliasSeries_s_01_fk;
CREATE INDEX IF NOT EXISTS series_aliasSeries_s_01_fk on series_alias (series_id);
DROP INDEX IF EXISTS series_alias_series_idname;
DROP INDEX IF EXISTS series_alias_series__02;
CREATE UNIQUE INDEX IF NOT EXISTS series_alias_series__02 on series_alias (series_id,name COLLATE NOCASE);

/** SERIES_ARTIST */
DROP INDEX IF EXISTS series_artistSeries_fk;
DROP INDEX IF EXISTS series_artistSeries__01_fk;
CREATE INDEX IF NOT EXISTS series_artistSeries__01_fk on series_artist (series_id);
DROP INDEX IF EXISTS series_artistArtist_fk;
DROP INDEX IF EXISTS series_artistArtist__02_fk;
CREATE INDEX IF NOT EXISTS series_artistArtist__02_fk on series_artist (artist_id);
DROP INDEX IF EXISTS series_artistArtist_Role_fk;
DROP INDEX IF EXISTS series_artistArtist__03_fk;
CREATE INDEX IF NOT EXISTS series_artistArtist__03_fk on series_artist (role_code);
DROP INDEX IF EXISTS series_artist_series_idartist_idrole_code;
DROP INDEX IF EXISTS series_artist_series_04;
CREATE UNIQUE INDEX IF NOT EXISTS series_artist_series_04 on series_artist (series_id,artist_id,role_code);

/** SERIES_CHARACTER */
DROP INDEX IF EXISTS series_characterSeries_fk;
DROP INDEX IF EXISTS series_characterSeri_01_fk;
CREATE INDEX IF NOT EXISTS series_characterSeri_01_fk on series_character (series_id);
DROP INDEX IF EXISTS series_characterCharacter_fk;
DROP INDEX IF EXISTS series_characterChar_02_fk;
CREATE INDEX IF NOT EXISTS series_characterChar_02_fk on series_character (character_id);
DROP INDEX IF EXISTS series_character_series_idcharacter_id;
DROP INDEX IF EXISTS series_character_ser_03;
CREATE UNIQUE INDEX IF NOT EXISTS series_character_ser_03 on series_character (series_id,character_id);

/** STORY_ARC */
DROP INDEX IF EXISTS story_arcPublisher_fk;
DROP INDEX IF EXISTS story_arcPublisher_p_01_fk;
CREATE INDEX IF NOT EXISTS story_arcPublisher_p_01_fk on story_arc (publisher_id);
DROP INDEX IF EXISTS story_arc_name;
DROP INDEX IF EXISTS story_arc_name_02;
CREATE  INDEX IF NOT EXISTS story_arc_name_02 on story_arc (name COLLATE NOCASE);
DROP INDEX IF EXISTS story_arc_xidxsource;
DROP INDEX IF EXISTS story_arc_xidxsource_03;
CREATE UNIQUE INDEX IF NOT EXISTS story_arc_xidxsource_03 on story_arc (xid COLLATE NOCASE,xsource COLLATE NOCASE);

/** STORY_ARC_CHARACTER */
DROP INDEX IF EXISTS story_arc_characterStory_Arc_fk;
DROP INDEX IF EXISTS story_arc_characterS_01_fk;
CREATE INDEX IF NOT EXISTS story_arc_characterS_01_fk on story_arc_character (story_arc_id);
DROP INDEX IF EXISTS story_arc_characterCharacter_fk;
DROP INDEX IF EXISTS story_arc_characterC_02_fk;
CREATE INDEX IF NOT EXISTS story_arc_characterC_02_fk on story_arc_character (character_id);
DROP INDEX IF EXISTS story_arc_character_story_arc_idcharacter_id;
DROP INDEX IF EXISTS story_arc_character__03;
CREATE UNIQUE INDEX IF NOT EXISTS story_arc_character__03 on story_arc_character (story_arc_id,character_id);

/** STORY_ARC_PUBLICATION */
DROP INDEX IF EXISTS story_arc_publicationStory_Arc_fk;
DROP INDEX IF EXISTS story_arc_publicatio_01_fk;
CREATE INDEX IF NOT EXISTS story_arc_publicatio_01_fk on story_arc_publication (story_arc_id);
DROP INDEX IF EXISTS story_arc_publicationPublication_fk;
DROP INDEX IF EXISTS story_arc_publicatio_02_fk;
CREATE INDEX IF NOT EXISTS story_arc_publicatio_02_fk on story_arc_publication (publication_id);
DROP INDEX IF EXISTS story_arc_publication_story_arc_idpublication_id;
DROP INDEX IF EXISTS story_arc_publicatio_03;
CREATE UNIQUE INDEX IF NOT EXISTS story_arc_publicatio_03 on story_arc_publication (story_arc_id,publication_id);

/** STORY_ARC_SERIES */
DROP INDEX IF EXISTS story_arc_seriesStory_Arc_fk;
DROP INDEX IF EXISTS story_arc_seriesStor_01_fk;
CREATE INDEX IF NOT EXISTS story_arc_seriesStor_01_fk on story_arc_series (story_arc_id);
DROP INDEX IF EXISTS story_arc_seriesSeries_fk;
DROP INDEX IF EXISTS story_arc_seriesSeri_02_fk;
CREATE INDEX IF NOT EXISTS story_arc_seriesSeri_02_fk on story_arc_series (series_id);
DROP INDEX IF EXISTS story_arc_series_story_arc_idseries_id;
DROP INDEX IF EXISTS story_arc_series_sto_03;
CREATE UNIQUE INDEX IF NOT EXISTS story_arc_series_sto_03 on story_arc_series (story_arc_id,series_id);

/** USER_NETWORK */
DROP INDEX IF EXISTS user_networkUsers_fk;
DROP INDEX IF EXISTS user_networkUsers_us_01_fk;
CREATE INDEX IF NOT EXISTS user_networkUsers_us_01_fk on user_network (user_id);
DROP INDEX IF EXISTS user_networkNetwork_fk;
DROP INDEX IF EXISTS user_networkNetwork__02_fk;
CREATE INDEX IF NOT EXISTS user_networkNetwork__02_fk on user_network (network_id);
DROP INDEX IF EXISTS user_network_user_idnetwork_id;
DROP INDEX IF EXISTS user_network_user_id_03;
CREATE UNIQUE INDEX IF NOT EXISTS user_network_user_id_03 on user_network (user_id,network_id);

/** USERS */
DROP INDEX IF EXISTS users_rememberme_token;
DROP INDEX IF EXISTS users_rememberme_tok_01;
CREATE UNIQUE INDEX IF NOT EXISTS users_rememberme_tok_01 on users (rememberme_token COLLATE NOCASE);
DROP INDEX IF EXISTS users_namepassword_hash;
DROP INDEX IF EXISTS users_namepassword_h_02;
CREATE UNIQUE INDEX IF NOT EXISTS users_namepassword_h_02 on users (name COLLATE NOCASE,password_hash COLLATE NOCASE);
DROP INDEX IF EXISTS users_activation_hash;
DROP INDEX IF EXISTS users_activation_has_03;
CREATE UNIQUE INDEX IF NOT EXISTS users_activation_has_03 on users (activation_hash COLLATE NOCASE);
DROP INDEX IF EXISTS users_api_hash;
DROP INDEX IF EXISTS users_api_hash_04;
CREATE UNIQUE INDEX IF NOT EXISTS users_api_hash_04 on users (api_hash COLLATE NOCASE);
DROP INDEX IF EXISTS users_email;
DROP INDEX IF EXISTS users_email_05;
CREATE UNIQUE INDEX IF NOT EXISTS users_email_05 on users (email COLLATE NOCASE);
DROP INDEX IF EXISTS users_name;
DROP INDEX IF EXISTS users_name_06;
CREATE UNIQUE INDEX IF NOT EXISTS users_name_06 on users (name COLLATE NOCASE);

/** VERSION */
DROP INDEX IF EXISTS version_code;
DROP INDEX IF EXISTS version_code_01;
CREATE UNIQUE INDEX IF NOT EXISTS version_code_01 on version (code COLLATE NOCASE);
DROP INDEX IF EXISTS version_majorminorpatch;
DROP INDEX IF EXISTS version_majorminorpa_02;
CREATE  INDEX IF NOT EXISTS version_majorminorpa_02 on version (major,minor,patch);

analyze;
