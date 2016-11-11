Patrick Tan
SID: 204 158 646
PatrickPTT@ucla.edu

I worked alone on this project, so no work needed to be divided. 

The most difficult portion was creating the php website. I started with the base example website, using the base HTML from the example for the website. Then, I added the php function that implemented the mysql query, using the get method. There is no input sanitation for this project, so it would be possible for some user to run some unknown commands. In reality, we should sanitize the inputs in order to limit the user to only have inputs we want. 

In order to print out the output in the same fashion (table) as the example, I first found the column names and put them in the table. Then, I populated each row with the sql query return. I used the same table format as the example, with the column names bolded and all values centered. 

The last issue was that the input in the textarea would disappear after submitting the query. This does not occur in either the calculator from 1A or the example query website. In order to have the textarea keep the same query, I added "<?php echo htmlspecialchars($_GET["query"]); ?>" to the textarea in order to fill the textarea with the current query value. htmlspecialchars prevents security issues.

The extra query I searched was: List all movies that some actor (we use Caroline Aaron) acted in.

The constraints I used were as follows:
Three primary key constraints
	1: Movie.id must be a primary key (unique)
	2: Actor.id must be a primary key (unique)
	3: Director.id must be a primary key (unique)
Six Referential integrity constraints
	1: MovieActor.mid refers to Movie.id
	2: MovieActor.aid refers to Actor.id
	3: MovieDirector.mid refers to Movie.id
	4: MovieDirector.did refers to Director.id
	5: MovieGenre.mid refers to Movie.id
	6: Review.mid refers to Movie.id
Three CHECK constraints
	1: Actor.dob earlier than Actor.dod OR Actor.dod is null (Actor.dob < Actor.dod OR Actor.dod IS NULL)
	2: Director.dob earlier than Director.dod OR Director.dod IS NULL (Director.dob < Director.dod OR Director.dod IS NULL)
	3: Review.rating must be at least zero and no greater than 5 (rating >= 0 AND rating < 5)