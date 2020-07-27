CREATE DATABASE IF NOT EXISTS beers;
USE beers;

CREATE TABLE users(
id          	int(11) auto_increment not null,
username       	varchar(255) 	not null,
password    	varchar(255) 	not null,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE types(
id         		int(11) auto_increment not null,
name       		varchar(255) 	not null,
CONSTRAINT pk_types PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE countries(
id         		int(11) auto_increment not null,
name       		varchar(255) 	not null,
CONSTRAINT pk_countries PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE breweries(
id          	int(11) auto_increment not null,
name       		varchar(255) 	not null,
url       		varchar(255) 	not null,
logo       		varchar(255),
address       	varchar(255) 	not null,
country_id    	int(11) 		not null,
CONSTRAINT pk_breweries PRIMARY KEY(id),
CONSTRAINT fk_brewery_country FOREIGN KEY(country_id) REFERENCES countries(id)
)ENGINE=InnoDb;

CREATE TABLE beers(
id              int(11) auto_increment not null,
name         	varchar(255) 	not null,
type_id         int(11) 		not null,
description     text,
abv             varchar(50) 	not null,
volume          varchar(50) 	not null,
misc      		text,
img      		varchar(255) 	not null,
CONSTRAINT pk_beers PRIMARY KEY(id),
CONSTRAINT fk_beer_type FOREIGN KEY(type_id) REFERENCES types(id),
)ENGINE=InnoDb;

CREATE TABLE beer_brewery
(
    beerId int NOT NULL,
    breweryId int NOT NULL,
    CONSTRAINT PK_beer_brewery PRIMARY KEY
    (
        beerId,
        breweryId
    ),
    FOREIGN KEY (beerId) REFERENCES beers (id),
    FOREIGN KEY (breweryId) REFERENCES breweries (id)
)ENGINE=InnoDb;