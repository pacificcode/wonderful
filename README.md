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

