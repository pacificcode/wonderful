-- # ID,Airport Name,City,Country,IATA/FAA,ICAO,Latitude,Longitude,Altitude,Timezone
DROP TABLE IF EXISTS `airport`;
CREATE TABLE `airport` (
  `auto_inc_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_airport` int(5) NOT NULL DEFAULT '0' COMMENT "ID passed in from datasource",
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `iata` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `icao` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lat` decimal(10,6) NOT NULL DEFAULT '00.0000',
  `lon` decimal(10,6) NOT NULL DEFAULT '00.0000',
  `position` POINT NOT NULL SRID 4326,
  `alt` int(6) NOT NULL DEFAULT '0',
  `tzone` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stamp_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stamp_modify` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `stamp_delete` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`auto_inc_id`),
  INDEX `idx_id_airport`(`id_airport`),
  INDEX `idx_country`(`country`),
  INDEX `idx_lat`(`lat`),
  INDEX `idx_lon`(`lon`),
  SPATIAL INDEX(`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Airport - Base airport data.';

DROP TABLE IF EXISTS `log_error`;
CREATE TABLE `log_error` (
  `auto_inc_id` int(11) NOT NULL AUTO_INCREMENT,
  `json_session` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stack_trace` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `request_uri` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `msg_0` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `msg_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stamp_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stamp_modify` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`auto_inc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Airport - error log.';

DROP PROCEDURE IF EXISTS `get_airport`;
DELIMITER // 
CREATE PROCEDURE get_airport(IN id_airport1 int)
BEGIN
	SELECT id_airport, name, city, country, lat, lon, iata, icao, alt, tzone
	FROM airport
	WHERE id_airport = id_airport1;
END //
DELIMITER ;

-- call get_airport(3830 )

DROP PROCEDURE IF EXISTS `distance_two_airport`;
DELIMITER // 
CREATE PROCEDURE distance_two_airport(IN id_airport1 int , IN id_airport2 int )
BEGIN
	SELECT a.id_airport AS id_airport1, a.name AS airport_1, a.lat AS lat_1, a.lon AS lon_1, 
	b.id_airport AS id_airport2, b.name AS airport_2,  b.lat AS lat_2, b.lon AS lon_2,
	ST_Distance_Sphere( a.position , b.position ) / 1609.34 AS `distance_m`
	FROM airport AS a, airport AS b
	WHERE a.id_airport = id_airport1 && b.id_airport = id_airport2;
END //
DELIMITER ;

-- call distance_two_airport(5723, 3830 )

DROP PROCEDURE IF EXISTS `distance_airport_to_radius`;
DELIMITER // 
CREATE PROCEDURE distance_airport_to_radius(IN latitude DECIMAL(10,6), IN longitude DECIMAL(10,6), IN radius int )
BEGIN
	SET @location = ST_GeomFromText( CONCAT("POINT(",latitude," ",longitude,")"), 4326 );
	SELECT  `id_airport`,`name`,`city`,`country`,`lat`,`lon`,`iata`,`icao`,`alt`,`tzone`,
	ST_Distance_Sphere(`position`, @location ) / 1609.34 AS `distance_m`
	FROM airport
	WHERE ST_Distance_Sphere(`position`, @location) <= (1609.34 * radius)
	ORDER BY `distance_m` ASC;
END //
DELIMITER ;

-- call distance_airport_to_radius(33.984305, -118.463262, 20 )

DROP PROCEDURE IF EXISTS `plot_route`;
DELIMITER // 
CREATE PROCEDURE plot_route(IN id_airport1 int , IN id_airport2 int)
BEGIN

SET @lat1 = 0;
SET @lon1 = 0;
SET @position1 = 0;
SET @lat2 = 0;
SET @lon2 = 0;
SET @distance_m = 0;

	SELECT a.lat AS lat1, a.lon AS lon1, a.position AS position1, b.lat AS lat2, b.lon AS lon2,
	ST_Distance_Sphere( a.position , b.position ) / 1609.34 AS `distance_m`
	INTO @lat1, @lon1, @position1, @lat2, @lon2, @distance_m
	FROM airport AS a, airport AS b
	WHERE a.id_airport = id_airport1 && b.id_airport = id_airport2;

	SELECT `id_airport`, `name`, `city`, `country`,`iata`,`icao`,`lat`,`lon`,`alt`,`tzone`,
	ST_Distance_Sphere( position , @position1 ) / 1609.34 AS `distance_m`
	FROM airport 
	WHERE id_airport != id_airport1
	&& lat BETWEEN @lat1 AND @lat2 
	&& lon BETWEEN @lon1 AND @lon2
	&& ST_Distance_Sphere( position , @position1 ) / 1609.34 < 500
	ORDER BY `distance_m` DESC
	LIMIT 1;
    
END //
DELIMITER ;

-- call plot_route(5723, 3830)\G

DROP PROCEDURE IF EXISTS `airport_by_country`;
DELIMITER // 
CREATE PROCEDURE airport_by_country(IN country1 VARCHAR(128), IN country2 VARCHAR(128))
BEGIN

	SELECT a.id_airport AS `id_airport_begin`, a.name AS `airport_begin`, a.city AS `city_begin`, a.country AS `country_begin`, 
	a.lat AS `lat_begin`, a.lon AS `lon_begin`,
	b.id_airport AS `id_airport_end`, b.name AS `airport_end`, b.city AS `city_end`, b.country AS `country_end`,
	b.lat AS `lat_end`, b.lon AS `lon_end`,
	ST_Distance_Sphere( a.position , b.position ) / 1609.34 AS `distance_m`
	FROM airport AS a, airport AS b
	WHERE a.country = country1 && b.country = country2
	ORDER BY `distance_m` ASC
	LIMIT 1;

END //
DELIMITER ;

-- call airport_by_country("United States", "Canada")\G
