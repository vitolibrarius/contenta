<?xml version="1.0" encoding="utf-8" ?>
<D:multistatus xmlns:D="DAV:">
<?php if ( isset( $this->props_requested, $this->resources)) : ?>
<?php foreach( $this->resources as $resource ) : ?>
	<?php
		$supported = array();
		$unsupported = array();
		foreach( $this->props_requested as $propertyArray ) {
			list( $propName, $nodename, $nsString ) = $propertyArray;
			$propvalue = $resource->propertyValue( $propName );
			if ( is_null( $propvalue ) ) {
				$unsupported[$nodename] = $nsString;
			}
			else {
				$supported[$nodename] = array($nsString, $propvalue);
			}
		}
	?>

	<D:response>
		<D:href><?php echo appendPath($this->href, $resource->hrefName()); ?></D:href>

<?php if ( count( $supported) > 0 ) : ?>
		<D:propstat>
		<D:prop>
<?php foreach( $supported as $nodename => $value ) : ?>
	<?php list($nsString, $propValue) = $value;
		switch ($nodename) {
			case "D:creationdate":
				echo "\t\t\t<D:creationdate ns0:dt=\"dateTime.tz\">"
					. gmdate("Y-m-d\\TH:i:s\\Z", $propValue)
					. "</D:creationdate>\n";
				break;
			case "D:getlastmodified":
				echo "\t\t\t<D:getlastmodified ns0:dt=\"dateTime.rfc1123\">"
					. gmdate("D, d M Y H:i:s ", $propValue)
					. "GMT</D:getlastmodified>\n";
				break;
			case "D:resourcetype":
				if ( empty($propValue) ) {
					echo "\t\t\t<D:resourcetype/>\n";
				}
				else {
					echo "\t\t\t<D:resourcetype><D:".$propValue."/></D:resourcetype>\n";
				}
				break;
			case "D:supportedlock":
				echo "\t\t\t<D:supportedlock>".$propValue."</D:supportedlock>\n";
				break;
			case "D:lockdiscovery":
				echo "\t\t\t<D:lockdiscovery>".$propValue."</D:lockdiscovery>\n";
				break;
			default:
				echo "\t\t\t<".$nodename . $nsString.">"
					. htmlspecialchars($propValue)
					. "</".$nodename.">\n";
				break;
		}
	?>
<?php endforeach; ?>
		</D:prop>
		<D:status>HTTP/1.1 200 OK</D:status>
		</D:propstat>
<?php endif; ?>

<?php if ( count( $unsupported) > 0 ) : ?>
		<D:propstat>
		<D:prop>
<?php foreach( $unsupported as $nodename => $nsString ) {
		echo "\t\t\t<" . $nodename . $nsString . "/>".PHP_EOL;
	} ?>
		</D:prop>
		<D:status>HTTP/1.1 404 Not Found</D:status>
		</D:propstat>
<?php endif; ?>

	</D:response>

<?php endforeach; ?>
<?php endif; ?>
</D:multistatus>


