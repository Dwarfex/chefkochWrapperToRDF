<?php
include_once 'utils.php';
require 'EasyRdf.php';
function getReweSearch($suchbegriff) {
	// $suchbegriff = "milch";
	$suchbegriff = preg_replace ( '/[^a-zA-Z0-9]+/', '', $suchbegriff );
	$url = "https://api.import.io/store/connector/478996fb-27a4-45ec-a1d8-2cc119d8d4a2/_query?input=webpage/url:https%3A%2F%2Fshop.rewe.de%2FproductList%3Fsearch%3D" . $suchbegriff . "&&_apikey=40581aa5770c4331b477ca6b191549da6a6ebe6117061cab22ffe286a3bec687a28dd324679b4b65c0e05e44c47739c45a4e56111fd9762d4e3dc345e60732bfe413ad4f56724681aeb33b4055f1e54a";
	$result = json_decode ( curlData ( $url ), true );
	// print_r($result);
	foreach ( $result ["results"] as $resultItem => $resultValue ) {
		foreach ( $result ["outputProperties"] as $key => $value ) {
			$formattetArray [$resultItem] [preg_replace ( '/[^a-zA-Z0-9]+/', '-', strtolower ( $value ["name"] ) )] = $result ["results"] [$resultItem] [$value ["name"]];
		}
		$formattetArray [$resultItem] ["identifier"] = "searchResult";
		$formattetArray [$resultItem] ["suchbegriff"] = $suchbegriff;
	}
	// print_r($formattetArray);
	$graph = new EasyRdf_Graph ();
	
	foreach ( $formattetArray as $key => $value ) {
		if ($key < 11) {
			buildTree ( $graph, $value ["headlineitem-link"], $value );
		}
	}
	echo $graph->serialise ( "turtle" );
}
