NOTE: Read this file in RAW for a better view of the JSON formatting of API responses.
**Problem 1:**

Provide a detailed description of the full stack that you would choose to build this API, complete with descriptive strategies for the following:

- Hosting
- Language
- Framework (if applicable)
- Storage
- Performance
- Misc (anything not covered above)

Also, provide estimates on the scalability and monthly costs of this environment.
************************************
Hosting:
AWS

Language:
PHP, Python, Java or Golang

Framework: 
Not applicable

Storage:
Not Applicable (Unless other features were added on at a later time i.e. data for analytics)

Performance:
As per requirements of 500 avg. requests/sec with spikes of 800 requests/sec.
Round robin DNS (Route53) configured for with 2 primary WAN I.P's pointing to
Dual ELB's routing requests to 3 High CPU backend servers each OR routing to configured Lambda instances:
LB1 -> APIServer1 APIServer2 APIServer3
LB2 -> APIServer4 APIServer5 APIServer6

Database:
Mysql 8.0.x
1 Master
4-6 Read only replication instances (More/Less replication instances can easily be added/removed as needed)
API servers configured to balance DB connections to replication servers.

Estimates:
Scalability: The above will be easily scalable for higher/lower traffic estimates.
Base pricing would be based on:
DNS queries
Dual ELB
Base number of API servers (Or Lambda instances)
DB Master and base number of replication servers.

**Problem 2:**

Write and document an endpoint that is able to efficiently return a JSON-formatted response of airports within a given radius of a specific coordinate. The iOS-supplied information should be:

- Latitude
- Longitude
- Radius

Request:
## Get airports within radius of given lat lon
curl -d '{"lat":33.984305, "lon":-118.463262, "radius":10}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/airport_by_radius

{
  "lat": 33.984305,
  "lon": -118.463262,
  "radius": 50
}

Response:
{
  "data": [
    {
      "id_airport": "7647",
      "name": "Santa Monica Municipal Airport",
      "city": "Santa Monica",
      "country": "United States",
      "lat": "34.015833",
      "lon": "-118.451306",
      "iata": "SMO",
      "icao": "KSMO",
      "alt": "177",
      "tzone": "America/Los_Angeles",
      "distance_m": "2.2835273170319166"
    }
   ],
  "response_execution": 0.0666,
  "response_epoch": 1583802490.21631,
  "response_type": "1",
  "response_message": "Success"
}

**Problem 3:**

Write and document an endpoint that is able to return a JSON-formatted response with the distance between two supplied airport id’s. The iOS-supplied information should be:

- Airport 1 ID
- Airport 2 ID

Request:
## Get distance between 2 airports
curl -d '{"id_airport1":3484, "id_airport2":9067}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/distance_between

{
  "id_airport1": 3484,
  "id_airport2": 3830
}

Response:
{
  "data": {
    "id_airport1": "3484",
    "airport_1": "Los Angeles Intl",
    "lat_1": "33.942536",
    "lon_1": "-118.408075",
    "id_airport2": "3830",
    "airport_2": "Chicago Ohare Intl",
    "lat_2": "41.978603",
    "lon_2": "-87.904842",
    "distance_m": "1741.161625587827"
  },
  "response_execution": 0.0018,
  "response_epoch": 1583802664.078443,
  "response_type": "1",
  "response_message": "Success"
}

**Problem 4:**
Write and document an endpoint that is able to return a JSON-formatted response with the geographically closest airports between two countries. For example, if tasked to compare the airports in the United States and Mexico, the endpoint would find the 1 airport in each country that is the shortest distance from the airport in the opposite country. The iOS supplied information should be:

- Country 1 Name
- Country 2 Name

Request:
## Get closest airports from 2 countries based on country name
curl -d '{"country1":"United States", "country2":"Russia"}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/airport_by_country

{
  "country1": "United States",
  "country2": "Russia"
}

Response:
{
  "data": {
    "id_airport_begin": "6715",
    "airport_begin": "Gambell Airport",
    "city_begin": "Gambell",
    "country_begin": "United States",
    "lat_begin": "63.766800",
    "lon_begin": "-171.733000",
    "id_airport_end": "2929",
    "airport_end": "Provideniya Bay",
    "city_end": "Provideniya Bay",
    "country_end": "Russia",
    "lat_end": "64.378139",
    "lon_end": "-173.243306",
    "distance_m": "62.17385914224217"
  },
  "response_execution": 4.0003,
  "response_epoch": 1583802879.950809,
  "response_type": "1",
  "response_message": "Success"
}

**Problem 5:**
Write and document an endpoint that is able to return a JSON-formatted list of instructions as to how to fly the shortest possible route (in terms of airport stops) from one airport to another. When generating these instructions, assume that an airplane can only travel 500 miles before requiring a stop to refuel. Therefore, the returned instructions should read as a list of airport stops, and the distance between each stop. The iOS supplied information should be:

- Airport 1 ID
- Airport 2 ID

Request:
## Plot route between 2 id_airports
curl -d '{"id_airport1":3484, "id_airport2":9067}' -H "Content-Type: application/json" -X POST http://wonderful.pacificcode.com/api/plot_route

{
  "id_airport1": 3484,
  "id_airport2": 3830
}

{
  "data": [
    {
      "id_airport": "3484",
      "name": "Los Angeles Intl",
      "city": "Los Angeles",
      "country": "United States",
      "lat": "33.942536",
      "lon": "-118.408075",
      "iata": "LAX",
      "icao": "KLAX",
      "alt": "126",
      "tzone": "America/Los_Angeles"
    },
    {
      "id_airport": "7581",
      "name": "Delta Municipal Airport",
      "city": "Delta",
      "country": "United States",
      "iata": "DTA",
      "icao": "KDTA",
      "lat": "39.380639",
      "lon": "-112.507715",
      "alt": "4759",
      "tzone": "America/Denver",
      "distance_m": "497.87483253561265"
    },
    {
      "id_airport": "5723",
      "name": "Western Nebraska Regional Airport",
      "city": "Scottsbluff",
      "country": "United States",
      "iata": "BFF",
      "icao": "KBFF",
      "lat": "41.874000",
      "lon": "-103.596000",
      "alt": "3967",
      "tzone": "America/Denver",
      "distance_m": "497.76843902531"
    },
    {
      "id_airport": "3830",
      "name": "Chicago Ohare Intl",
      "city": "Chicago",
      "country": "United States",
      "lat": "41.978603",
      "lon": "-87.904842",
      "iata": "ORD",
      "icao": "KORD",
      "alt": "668",
      "tzone": "America/Chicago"
    }
  ],
  "response_execution": 0.0099,
  "response_epoch": 1583803057.722843,
  "response_type": "1",
  "response_message": "Success"
}
