-- Give me the names of all the actors in the movie 'Die Another Day'. Please also make sure actor names are in this format:  <firstname> <lastname>   (seperated by single space).

SELECT CONCAT(first, ' ', last) as Name
FROM Actor, Movie, MovieActor
WHERE Movie.title = 'Die Another Day' AND Movie.id = MovieActor.mid AND MovieActor.aid = Actor.id;

-- Give me the count of all the actors who acted in multiple movies.

SELECT COUNT(*)
FROM (SELECT id
FROM Actor, MovieActor
WHERE Actor.id = MovieActor.aid
GROUP BY Actor.id
HAVING COUNT(DISTINCT mid) > 1) MultiMovie;

-- List all movies that some actor (we use Caroline Aaron) acted in.

SELECT title
FROM Movie, MovieActor, Actor
WHERE Actor.id = MovieActor.aid AND Movie.id = MovieActor.mid AND Actor.last = 'Aaron' AND Actor.first = 'Caroline';