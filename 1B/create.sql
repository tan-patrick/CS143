CREATE TABLE Movie(
	id int NOT NULL, 
	title varchar(100) NOT NULL, 
	year int, 
	rating varchar(10), 
	company varchar(50),
	PRIMARY KEY(id)) ENGINE=INNODB; -- Movie.id must be a primary key (unique)

CREATE TABLE Actor(
	id int NOT NULL, 
	last varchar(20), 
	first varchar(20), 
	sex varchar(6), 
	dob date NOT NULL, 
	dod date DEFAULT NULL, 
	PRIMARY KEY(id), -- Actor.id must be a primary key (unique)
	CHECK(dod IS NULL OR dob < dob)) ENGINE=INNODB; -- Actor.dob earlier than Actor.dod OR Actor.dod is null

CREATE TABLE Director(
	id int NOT NULL, 
	last varchar(20), 
	first varchar(20), 
	dob date NOT NULL, 
	dod date DEFAULT NULL, 
	PRIMARY KEY(id), -- Director.id must be a primary key (unique)
	CHECK(dod IS NULL OR dob < dob)) ENGINE=INNODB; -- Director.dob earlier than Director.dod OR Director.dod IS NULL

CREATE TABLE MovieGenre(
	mid int NOT NULL, 
	genre varchar(20) NOT NULL,
	FOREIGN KEY (mid) REFERENCES Movie(id)) ENGINE=INNODB; -- MovieGenre.mid refers to Movie.id

CREATE TABLE MovieDirector(
	mid int NOT NULL, 
	did int NOT NULL,
	FOREIGN KEY (mid) REFERENCES Movie(id), -- MovieDirector.mid refers to Movie.id
	FOREIGN KEY (did) REFERENCES Director(id)) ENGINE=INNODB; -- MovieDirector.did refers to Director.id

CREATE TABLE MovieActor(
	mid int NOT NULL, 
	aid int NOT NULL, 
	role varchar(50),
	FOREIGN KEY (mid) REFERENCES Movie(id), -- MovieActor.mid refers to Movie.id
	FOREIGN KEY (aid) REFERENCES Actor(id)) ENGINE=INNODB; -- MovieActor.aid refers to Actor.id

CREATE TABLE Review(
	name varchar(20) NOT NULL, 
	time timestamp, 
	mid int NOT NULL, 
	rating int NOT NULL, 
	comment varchar(500),
	FOREIGN KEY (mid) REFERENCES Movie(id), -- Review.mid refers to Movie.id
	CHECK(rating >= 0 AND rating <= 5)) ENGINE=INNODB; -- Review.rating must be at least zero and no greater than 5

CREATE TABLE MaxPersonID(
	id int) ENGINE=INNODB;

CREATE TABLE MaxMovieID(
	id int) ENGINE=INNODB;