<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Network extends Model
{
	const TABLE =		'network';
	const id =			'id';
	const ipAddress =	'ip_address';
	const ipHash =		'ip_hash';
	const created =		'created';
	const disable =		'disable';

	public function tableName() { return Network::TABLE; }
	public function tablePK() { return Network::id; }
	public function sortOrder() { return array("desc" => array(Network::id)); }

	public function allColumnNames()
	{
		return array(Network::id, Network::ipAddress, Network::ipHash, Network::created, Network::disable);
	}

	function networkForAddress($value)
	{
		return $this->singleObjectForKeyValue(Network::ipAddress, $value);
	}

	function networkForAddressHash($value)
	{
		return $this->allObjectsForKeyValue(Network::ipHash, $value);
	}

	function allNetworkForIPAddressHashed($ipAddress)
	{
		$hash = $this->ipToHex($ipAddress);
		return $this->allObjectsForKeyValue(Network::ipHash, $hash);
	}

	function create( $ip )
	{
		$object = $this->networkForAddress($ip);
		if ( $object == false) {
			$hash = $this->ipToHex($ip);
			if ( $hash == false ) {
				$hash = 'Invalid IP address';
			}

			$object = $this->createObj(Network::TABLE, array(
				Network::created => time(),
				Network::ipAddress => $ip,
				Network::ipHash => $hash,
				Network::disable => false
				)
			);
		}

		return $object;
	}

	function ipToHex($ipAddress)
	{
		$hex = '';
		if (strpos($ipAddress, ',') !== false) {
			$splitIp = explode(',', $ipAddress);
			$ipAddress = trim($splitIp[0]);
		}
		$isIpV6 = false;
		$isIpV4 = false;
		if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$isIpV6 = true;
		}
		else if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			$isIpV4 = true;
		}
		if (!$isIpV4 && !$isIpV6) {
			return false;
		}

		// IPv4 format
		if ($isIpV4) {
			$parts = explode('.', $ipAddress);
			for($i = 0; $i < 4; $i++) {
				$parts[$i] = str_pad(dechex($parts[$i]), 2, '0', STR_PAD_LEFT);
			}
			$ipAddress = '::'.$parts[0].$parts[1].':'.$parts[2].$parts[3];
			$hex = join('', $parts);
		}
		// IPv6 format
		else {
			$parts = explode(':', $ipAddress);
			// If this is mixed IPv6/IPv4, convert end to IPv6 value
			if (filter_var($parts[count($parts) - 1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
				$partsV4 = explode('.', $parts[count($parts) - 1]);
				for($i = 0; $i < 4; $i++) {
					$partsV4[$i] = str_pad(dechex($partsV4[$i]), 2, '0', STR_PAD_LEFT);
				}
				$parts[count($parts) - 1] = $partsV4[0].$partsV4[1];
				$parts[] = $partsV4[2].$partsV4[3];
			}
			$numMissing = 8 - count($parts);
			$expandedParts = array();
			$expansionDone = false;
			foreach ($parts as $part) {
				if (!$expansionDone && $part == '') {
					for ($i = 0; $i <= $numMissing; $i++) {
						$expandedParts[] = '0000';
					}
					$expansionDone = true;
				}
				else {
					$expandedParts[] = $part;
				}
			}
			foreach ($expandedParts as &$part) {
				$part = str_pad($part, 4, '0', STR_PAD_LEFT);
			}
			$ipAddress = join(':', $expandedParts);
			$hex = join('', $expandedParts);
		}
		// Validate the final IP
		if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
			return false;
		}
		return strtolower(str_pad($hex, 32, '0', STR_PAD_LEFT));
	}
}
