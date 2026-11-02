<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@movie.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $userList = [
            ['Test User', 'user@movie.com'],
            ['Andi', 'andi@movie.com'],
            ['Budi', 'budi@movie.com'],
            ['Citra', 'citra@movie.com'],
            ['Dewi', 'dewi@movie.com'],
            ['Eko', 'eko@movie.com'],
            ['Fajar', 'fajar@movie.com'],
        ];

        $users = [];
        foreach ($userList as [$name, $email]) {
            $users[] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => 'password',
                'role' => 'user',
            ]);
        }

        $genreList = [
            'Action' => 'Fast paced movies with fights and chases',
            'Drama' => 'Serious stories about people and life',
            'Sci-Fi' => 'Stories about science, space and the future',
            'Comedy' => 'Light movies made to make you laugh',
            'Horror' => 'Scary movies made to frighten you',
            'Animation' => 'Animated movies for all ages',
            'Romance' => 'Love stories and relationships',
            'Thriller' => 'Tense stories full of suspense',
        ];

        $genres = [];
        foreach ($genreList as $name => $description) {
            $genres[$name] = Genre::create(['name' => $name, 'description' => $description]);
        }

        $movies = [
            ['Sci-Fi', 'Inception', 'Christopher Nolan', 2010, 148, 'A thief enters dreams to steal secrets.'],
            ['Sci-Fi', 'Interstellar', 'Christopher Nolan', 2014, 169, 'Explorers travel through a wormhole to save humanity.'],
            ['Sci-Fi', 'The Matrix', 'Lana and Lilly Wachowski', 1999, 136, 'A hacker learns that reality is a simulation.'],
            ['Sci-Fi', 'Blade Runner 2049', 'Denis Villeneuve', 2017, 164, 'A young blade runner uncovers a long buried secret.'],
            ['Action', 'Mad Max: Fury Road', 'George Miller', 2015, 120, 'A woman rebels against a tyrant in a desert wasteland.'],
            ['Action', 'The Dark Knight', 'Christopher Nolan', 2008, 152, 'Batman faces the Joker in Gotham City.'],
            ['Action', 'John Wick', 'Chad Stahelski', 2014, 101, 'A retired hitman seeks revenge for his stolen car and dog.'],
            ['Action', 'Gladiator', 'Ridley Scott', 2000, 155, 'A betrayed general becomes a gladiator to take revenge.'],
            ['Action', 'Die Hard', 'John McTiernan', 1988, 132, 'A police officer fights terrorists in a skyscraper.'],
            ['Drama', 'The Shawshank Redemption', 'Frank Darabont', 1994, 142, 'Two prisoners build a deep friendship over many years.'],
            ['Drama', 'Forrest Gump', 'Robert Zemeckis', 1994, 142, 'A kind man lives through decades of American history.'],
            ['Drama', 'Parasite', 'Bong Joon-ho', 2019, 132, 'A poor family slowly enters the life of a rich family.'],
            ['Drama', 'The Godfather', 'Francis Ford Coppola', 1972, 175, 'The aging head of a crime family passes power to his son.'],
            ['Drama', 'Whiplash', 'Damien Chazelle', 2014, 107, 'A young drummer is pushed to the limit by a harsh teacher.'],
            ['Comedy', 'The Grand Budapest Hotel', 'Wes Anderson', 2014, 99, 'A hotel concierge and his lobby boy get into a wild adventure.'],
            ['Comedy', 'Superbad', 'Greg Mottola', 2007, 113, 'Two friends try to enjoy one last wild night before graduation.'],
            ['Comedy', 'Home Alone', 'Chris Columbus', 1990, 103, 'A boy defends his house from two burglars.'],
            ['Comedy', 'The Hangover', 'Todd Phillips', 2009, 100, 'Three friends wake up after a party and cannot find the groom.'],
            ['Horror', 'Get Out', 'Jordan Peele', 2017, 104, 'A young man visits his girlfriend family and finds a dark secret.'],
            ['Horror', 'A Quiet Place', 'John Krasinski', 2018, 90, 'A family must live in silence to hide from deadly creatures.'],
            ['Horror', 'The Conjuring', 'James Wan', 2013, 112, 'Paranormal investigators help a family terrorized by a dark presence.'],
            ['Horror', 'Hereditary', 'Ari Aster', 2018, 127, 'A family is haunted after the death of their grandmother.'],
            ['Animation', 'Toy Story', 'John Lasseter', 1995, 81, 'Toys come to life when their owner is not around.'],
            ['Animation', 'Spirited Away', 'Hayao Miyazaki', 2001, 125, 'A girl enters a world of spirits to save her parents.'],
            ['Animation', 'The Lion King', 'Roger Allers and Rob Minkoff', 1994, 88, 'A young lion prince must take his place as king.'],
            ['Animation', 'Coco', 'Lee Unkrich', 2017, 105, 'A boy travels to the Land of the Dead to find his family history.'],
            ['Romance', 'Titanic', 'James Cameron', 1997, 195, 'Two people from different classes fall in love on a doomed ship.'],
            ['Romance', 'La La Land', 'Damien Chazelle', 2016, 128, 'A musician and an actress chase their dreams in Los Angeles.'],
            ['Romance', 'The Notebook', 'Nick Cassavetes', 2004, 123, 'A couple fights to stay together across many years.'],
            ['Romance', 'Pride and Prejudice', 'Joe Wright', 2005, 129, 'Elizabeth Bennet learns to see past first impressions.'],
            ['Thriller', 'Se7en', 'David Fincher', 1995, 127, 'Two detectives hunt a killer who uses the seven deadly sins.'],
            ['Thriller', 'Gone Girl', 'David Fincher', 2014, 149, 'A man becomes the main suspect when his wife disappears.'],
            ['Thriller', 'Shutter Island', 'Martin Scorsese', 2010, 138, 'A marshal investigates a disappearance at a hospital on an island.'],
            ['Thriller', 'The Silence of the Lambs', 'Jonathan Demme', 1991, 118, 'A young agent asks a jailed killer for help to catch another one.'],
        ];

        foreach ($movies as [$genre, $title, $director, $year, $minutes, $synopsis]) {
            Movie::create([
                'genre_id' => $genres[$genre]->id,
                'title' => $title,
                'synopsis' => $synopsis,
                'director' => $director,
                'release_year' => $year,
                'duration_minutes' => $minutes,
            ]);
        }

        $comments = [
            1 => ['Very disappointing.', 'I could not finish it.'],
            2 => ['Not for me.', 'Too slow and a bit boring.'],
            3 => ['Good but not great.', 'Funny in some parts, strange in others.'],
            4 => ['Really enjoyed it.', 'Great story and good acting.'],
            5 => ['One of my favorites.', 'Amazing from start to end.'],
        ];

        $allMovies = Movie::all();
        $ratingPool = collect([1, 2, 3, 3, 4, 4, 4, 5, 5, 5]);

        foreach ($users as $user) {
            foreach ($allMovies->random(rand(8, 14)) as $movie) {
                $rating = $ratingPool->random();

                Review::create([
                    'user_id' => $user->id,
                    'movie_id' => $movie->id,
                    'rating' => $rating,
                    'comment' => collect($comments[$rating])->random(),
                ]);
            }
        }
    }
}
