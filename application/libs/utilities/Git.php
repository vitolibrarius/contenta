<?php

namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use utilities\ShellCommand as ShellCommand;

class Git
{
	const UP_TO_DATE = "Up-to-date";

	protected static $binary = '/usr/bin/git';
	protected $repository = null;

	public function __construct($path = null) {
		if (is_string($path)) {
			$this->setPath($path);
		}
	}

	private function setPath($path)
	{
		$fullpath = realpath($path);
		if (is_dir($fullpath)) {
			$gitDir = appendPath($fullpath, ".git");
			if (file_exists($gitDir) && is_dir($gitDir)) {
				$this->repository = $fullpath;
			}
			else {
				throw new Exception( '"' . $fullpath . '" is not a git repository');
			}
		}
		else {
			throw new Exception('"' . $fullpath . '" is not a directory');
		}
	}

	protected function run($command)
	{
		$shell = ShellCommand::create(Git::$binary . ' ' . $command);
		$status = $shell->exec();
		return array(
			"status" => $status,
			"stdout" => $shell->stdout(),
			"stderr" => $shell->stderr()
		);
	}

	protected function checkOwnershipRecursive( $badFilesFound, $ownerid, $directory,  $limit, $exclusions )
	{
		if (is_dir($directory)) {
			foreach (scandir($directory) as $file)
			{
				if ($file == '.' || $file == '..') continue;

				$path = $directory . DIRECTORY_SEPARATOR . $file;
				if ( fileowner($path) != $ownerid && in_array($file, $exclusions) == false) {
					$badFilesFound[] = $path;
				}

				if (is_dir($path) === true)
				{
					$badFilesFound = $this->checkOwnershipRecursive( $badFilesFound, $ownerid, $path,  $limit, $exclusions );
				}

				if ( count($badFilesFound) >= $limit ) {
					return $badFilesFound;
				}
			}
		}

		return $badFilesFound;
	}

	public function checkUpgradeEligibility()
	{
		$local_results = $this->status();
		$remote_results =
		$ownership_results = $this->checkRepositoryOwnership(10, array("contenta.ini", ".htaccess", ".DS_Store") );

	$file_test_class = ($files['status'] == 0 ? 'success' : 'failure');
	$git_test = $git->status();
	$git_test_out = preg_split('/\n|\r/', $git_test['stdout'], -1, PREG_SPLIT_NO_EMPTY);
	$git_test_class = ($git_test['status'] == 0 && count($git_test_out) == 1 ? 'success' : 'failure');
	$git_eligible = ($files['status'] == 0 && $git_test['status'] == 0 && count($git_test_out) == 1);

	}

	public function checkRepositoryOwnership( $limit = 10, $exclusions = null )
	{
		$uid = 0;
		if ( function_exists("posix_geteuid") == true ) {
			$uid = posix_geteuid();
		}
		else {
			$uid = `id -u`;
		}

		$badFiles = array();
		$excluded = (is_array($exclusions) ? $exclusions : array());

		$badFiles = $this->checkOwnershipRecursive( $badFiles, $uid, $this->repository,  $limit, $excluded );

		return array(
			"status" => count($badFiles),
			"badFiles" => $badFiles
		);
	}

	public function remoteStatus()
	{
		$local_rev = $this->currentHash();
		$x_rev = $this->run("rev-parse @{u}");
		$base_rev = $this->run("merge-base @ @{u}");

		$commits = $this->jsonRequest('https://api.github.com/repos/vitolibrarius/contenta/commits/master');
		if ( isset($commits, $commits['sha']) )
		{
			$remote_rev = $commits['sha'];
			if ( $local_rev != $remote_rev ) {
				// https://developer.github.com/v3/repos/commits/#compare-two-commits
				$diffs = $this->jsonRequest('https://api.github.com/repos/vitolibrarius/contenta/compare/'.$local_rev.'...'.$remote_rev);
				if ( isset($diffs, $diffs['total_commits']) ) {
					if ( $diffs['total_commits'] > 0 ) {
						return "New version available, you are " . $diffs['total_commits'] . " behind.";
					}
					else if ($diffs['total_commits'] == 0) {
						return Git::UP_TO_DATE;
					}
				}
			}
		}

		return "Unknown version.";
	}

	private function jsonRequest($url = null)
	{
		if ( is_null($url) == false )
		{
			$json = Cache::Fetch( $url, false, Cache::TTL_WEEK );
			if ( $json != false ) {
				return $json;
			}

			if ( function_exists('curl_version') == true) {
				$cacheKey = Cache::MakeKey( "Cookies" . parse_url($url, PHP_URL_HOST));
				$cookie = Cache::Fetch( $cacheKey, "" );

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
// 				curl_setopt($ch, CURLOPT_USERAGENT, APP_NAME . "/" . APP_VERSION);
				curl_setopt($ch, CURLOPT_URL, $url );
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt($ch, CURLOPT_ENCODING, "" );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($ch, CURLOPT_AUTOREFERER, true );
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10 );
				curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

				$data = curl_exec($ch);
				if ( $data == false ) {
					\Logger::logError( 'Error (' . curl_error($ch) . ')', get_class($this), $url);
				}
				curl_close($ch);
				Cache::Store( $cacheKey, $cookie );
			}
			else {
				$data = file_get_contents($url);
			}

			if ( $data != false )
			{
				$json = json_decode($data, true);
				if ( json_last_error() != 0 )
				{
					throw new \Exception(jsonErrorString(json_last_error()));
				}

				Cache::Store( $url, $json );
				return $json;
			}
		}
		return null;
	}

	public function status()
	{
		return $this->run("status -sb");
	}

	public function pull()
	{
		return $this->run("pull origin master");
	}

	public function currentHash()
	{
		$shell = $this->run("rev-parse --verify HEAD");
		if ( $shell['status'] == 0 ) {
			return trim($shell['stdout']);
		}
		return "Error";
	}
}
