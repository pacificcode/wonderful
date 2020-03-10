#!/usr/bin/php
<?php

	function api_curl_send($url, $json = "")
	{
		$ch = curl_init();

		$httpheader = array(
		'Content-Type: ' . 'application/json',
		'Accept: ' . 'application/json'
		);

		$curl_post = 0;
		if($json != "")
		{
			$curl_post = 1;
			curl_setopt($ch, CURLOPT_POST, $curl_post);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

		$resp = curl_exec($ch);
		$info = curl_getinfo($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
	return $resp;
	}

/* CALL PLOT ROUTE FROM AIRPORT ONE TO AIRPORT TWO */
// curl -d '{"id_airport1":3484, "id_airport2":9067}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/plot_route
$url = "http://wonderful.pacificcode.com/api/plot_route";
$obj = new stdClass();
$obj->id_airport1 = 3484; // LAX
$obj->id_airport2 = 3830; // Chicago O'Hare

/* CALL GET CLOSET AIRPORTS BY COUNTRY NAME */
// curl -d '{"country1":"United States", "country2":"Russia"}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/airport_by_country
// $url = "http://wonderful.pacificcode.com/api/airport_by_country";
// $obj = new stdClass();
// $obj->country1 = "United States";
// $obj->country2 = "Russia";

/* CALL GET CLOSET AIRPORTS BY RADIUS */
// curl -d '{"lat":33.984305, "lon":-118.463262, "radius":10}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/airport_by_radius
// $url = "http://wonderful.pacificcode.com/api/airport_by_radius";
// $obj = new stdClass();
// $obj->lat = 33.984305;
// $obj->lon = -118.463262;
// $obj->radius = 10;

/* CALL GET DISTANCE BETWEEN TWO AIRPORTS BY ID_AIRPORT */
// curl -d '{"id_airport1":3484, "id_airport2":9067}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/distance_between
// $url = "http://wonderful.pacificcode.com/api/distance_between";
// $obj = new stdClass();
// $obj->id_airport1 = 3484; // LAX
// $obj->id_airport2 = 3830; // Chicago O'Hare

$json = json_encode($obj);
$response = api_curl_send($url, $json);
print_r($response);
?>