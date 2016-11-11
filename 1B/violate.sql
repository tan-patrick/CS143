-- Three primary key constraints
-- 1: Movie.id must be a primary key (unique)
INSERT INTO Movie VALUES(272, 'Some Movie', 2014, 'G', 'Studio');
-- There is already a movie id of 272. It violates the Movie.id primary key, or uniqueness constraint, since there will be duplicate ids.
-- ERROR 1062 (23000): Duplicate entry '272' for key 1

-- 2: Actor.id must be a primary key (unique)
INSERT INTO Actor VALUES(1, 'Last', 'First', 'Male', 19220507, 20000525);
-- There is already a actor id of 1. It violates the Actor.id primary key, or uniqueness constraint, since there will be duplicate ids.
-- ERROR 1062 (23000): Duplicate entry '1' for key 1

-- 3: Director.id must be a primary key (unique)
INSERT INTO Director VALUES(37146, 'Last', 'First', 19220507, 20000525);
-- There is already a movie id of 37146. It violates the Director.id primary key, or uniqueness constraint, since there will be duplicate ids.
-- ERROR 1062 (23000): Duplicate entry '37146' for key 1


-- Six Referential integrity constraints
-- 1: MovieActor.mid refers to Movie.id
INSERT INTO MovieActor VALUES(10, 1, 'Role');
-- There is no movie of id 10. There is no movie id 10 in the referencing table Movie, so it violates the foreign key. 
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

-- 2: MovieActor.aid refers to Actor.id
INSERT INTO MovieActor VALUES(272, 13, 'Role');
-- There is no actor of id 13. There is no actor id 13 in the referencing table Actor, so it violates the foreign key. 
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))

-- 3: MovieDirector.mid refers to Movie.id
INSERT INTO MovieDirector VALUES(10, 37146);
-- There is no movie of id 10. There is no movie id 10 in the referencing table Movie, so it violates the foreign key. 
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

-- 4: MovieDirector.did refers to Director.id
INSERT INTO MovieDirector VALUES(272, 33);
-- There is no director of id 33. There is no director id 33 in the referencing table Director, so it violates the foreign key.
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))

-- 5: MovieGenre.mid refers to Movie.id
INSERT INTO MovieGenre VALUES(10, 'Drama');
-- There is no movie of id 10. There is no movie id 10 in the referencing table Movie, so it violates the foreign key. 
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

-- 6: Review.mid refers to Movie.id
INSERT INTO Review VALUES('Patrick', 1171502725, 10, 5, 'Best Movie of All Time');
-- There is no movie of id 10. There is no movie id 10 in the referencing table Movie, so it violates the foreign key. 
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143/Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))


-- Three CHECK constraints
-- 1: Actor.dob earlier than Actor.dod OR Actor.dod is null (Actor.dob < Actor.dod OR Actor.dod IS NULL)
INSERT INTO Actor VALUES(69000, 'Last', 'First', 'Male', 20000525, 19220507);

-- 2: Director.dob earlier than Director.dod OR Director.dod IS NULL (Director.dob < Director.dod OR Director.dod IS NULL)
INSERT INTO Director VALUES(69000, 'Last', 'First', 'Male', 20000525, 19220507);

-- 3: Review.rating must be at least zero and no greater than 5 (rating >= 0 AND rating < 5)
INSERT INTO Review VALUES('Patrick', time(), 272, 5, 'Best Movie of All Time');