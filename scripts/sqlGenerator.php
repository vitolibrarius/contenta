#! /usr/bin/env php
<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path). DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('DOCUMENTATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'documentation');
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application');

$models_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models';
realpath($models_path) || die( "Could not find 'dbo_models'" );

$templates_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';
realpath($templates_path) || die( "Could not find 'dbo_templates'" );

define('SCHEMA_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'schema_template.php');
define('INDEXES_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'indexes_template.php');

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'tests/_ResetConfig.php';

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
$tmp_dir = "/tmp/"; // sys_get_temp_dir();

SetConfigRoot( $root );

// load the parser class
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'schemaModelParser.php';

class SQLTemplate extends schemaModelParser
{
    public function generate()
    {
        ob_start();

        include $this->path_to_file;

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}

$schema_dir = appendPath( DOCUMENTATION_PATH, "schema" );
is_dir($schema_dir) ||  mkdir($schema_dir) || die( 'Failed to created directory ' . $schema_dir );

$schema_types = array( "sqlite" );
$schema_output_types = array( "Tables", "Indexes" );
$schema_output_files = array();
foreach ( $schema_types as $stype ) {
	foreach ( $schema_output_types as $output_type ) {
		$table_path = appendPath( $schema_dir, $stype . "_" . $output_type . ".sql" );
		$table_file = fopen( $table_path, "wr");
		if ( $table_file == false )  die("Unable to open file ! " . $table_path );
		$schema_output_files[$stype][$output_type] = $table_file;
	}
}

// generate headers
foreach ( $schema_types as $stype ) {
	foreach ( $schema_output_types as $output_type ) {
		$template_file = appendPath( $templates_path, "schema", $stype . "_" . $output_type . "HeaderTemplate.php" );
		if ( file_exists($template_file) ) {
			$Template = new SQLTemplate($template_file);
			$table_data = $Template->generate();

			$file = $schema_output_files[$stype][$output_type];
			fwrite($file, $table_data);
		}
		else {
			echo "No template found for $template_file" . PHP_EOL;
		}
	}
}

foreach ( $schema_types as $stype ) {
	$completedTables = array();
	$pendingModels = array();

	foreach ( $schema_output_types as $output_type ) {
		// append each model table
		foreach (glob($models_path . DIRECTORY_SEPARATOR . "*.json") as $file) {
			$model_meta = json_decode(file_get_contents($file), true);
			is_array($model_meta) || die("Failed to read $file" . PHP_EOL);

			/** generate Table */
			$template_file = appendPath( $templates_path, "schema", $stype . "_" . $output_type . "Template.php" );
			if ( file_exists($template_file) ) {
				$Template = new SQLTemplate($template_file);
				$Template->setModel($model_meta);

				$dependantTables = $Template->dependsOnTables();
				$ready = true;
				if ( empty($dependantTables) == false ) {
					$diff = array_diff($dependantTables, $completedTables );
					if (empty($diff) == false) {
						$ready = false;
					}
				}

				if ( $ready ) {
					$completedTables[] = $Template->tableName();
					$table_data = $Template->generate();

					$file = $schema_output_files[$stype][$output_type];
					fwrite($file, $table_data);

					// we wrote a table, see if we can catch up any pending
					$pendingTables = array_keys($pendingModels);
					foreach( $pendingTables as $table ) {
						$Template = $pendingModels[$table];
						$dependantTables = $Template->dependsOnTables();
						$ready = true;
						if ( empty($dependantTables) == false ) {
							$diff = array_diff($dependantTables, $completedTables );
							if (empty($diff) == false) {
								$ready = false;
							}

							if ( $ready ) {
								$completedTables[] = $Template->tableName();
								unset($pendingModels[$table]);

								$table_data = $Template->generate();

								$file = $schema_output_files[$stype][$output_type];
								fwrite($file, $table_data);
							}
						}
						else {
							echo "\t" . $Template->tableName() . " = " . var_export($diff, true) . PHP_EOL;
						}
					}
				}
				else {
					$pendingModels[$Template->tableName()] = $Template;
				}
			}
			else {
				echo "No template found for $template_file" . PHP_EOL;
			}
		}
	}
}

// append sql footers for file
foreach ( $schema_types as $stype ) {
	$template_file = appendPath( $templates_path, "schema", $stype . "_" . $output_type . "FooterTemplate.php" );
	if ( file_exists($template_file) ) {
		$Template = new SQLTemplate($template_file);
		$table_data = $Template->generate();

		$file = $schema_output_files[$stype][$output_type];
		fwrite($file, $table_data);
		fclose($file);
		unset($schema_output_files[$stype][$output_type]);
	}
	else {
		echo "No template found for $template_file" . PHP_EOL;
	}
}

?>
