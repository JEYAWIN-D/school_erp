<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Syllabus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyllabusSeeder extends Seeder
{
    /**
     * Map subject names to valid enum types: theory, practical, activity, language
     */
    private function subjectType(string $name): string
    {
        $languageSubjects = ['English', 'Hindi', 'Sanskrit', 'Tamil', 'Telugu', 'Malayalam', 'Kannada', 'Marathi', 'Bengali', 'Urdu', 'French', 'German', 'Phonics', 'Language'];
        $practicalSubjects = ['Computer Science', 'Computer', 'IT', 'Information Technology', 'Robotics', 'Lab', 'AI'];
        $activitySubjects = ['Physical Education', 'Music', 'Dance', 'Sports', 'Art & Craft', 'Drawing', 'Coloring', 'Craft', 'Clay', 'Play & Motor', 'Action Songs', 'Yoga', 'Storytelling'];

        foreach ($languageSubjects as $l) {
            if (stripos($name, $l) !== false) return 'language';
        }
        foreach ($activitySubjects as $a) {
            if (stripos($name, $a) !== false) return 'activity';
        }
        foreach ($practicalSubjects as $p) {
            if (stripos($name, $p) !== false) return 'practical';
        }
        return 'theory';
    }

    public function run(): void
    {
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        if (!$currentYear) {
            $currentYear = AcademicYear::create([
                'name'       => '2025-2026',
                'start_date' => '2025-06-01',
                'end_date'   => '2026-04-30',
                'is_current' => true,
                'status'     => 'active',
            ]);
        }

        $allClasses = Classes::all();
        $syllabusData = $this->getFullCurriculum();
        $inserted = 0;
        $updated = 0;

        foreach ($syllabusData as $classKey => $subjectsData) {
            // Find class by exact name or numeric value
            $class = $allClasses->first(function ($c) use ($classKey) {
                return strcasecmp(trim($c->name), trim($classKey)) === 0
                    || strcasecmp(trim($c->getRawOriginal('name') ?? ''), trim($classKey)) === 0;
            });

            if (!$class) {
                // Try fallback matching
                $class = Classes::where('name', 'ilike', "%{$classKey}%")->first();
            }

            if (!$class) {
                $this->command->warn("Class not found for key: '{$classKey}' - skipping.");
                continue;
            }

            foreach ($subjectsData as $subjectName => $chapters) {
                // Find or create subject with proper type
                $subject = Subject::where('name', 'ilike', $subjectName)->first();
                if (!$subject) {
                    $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $subjectName), 0, 4));
                    $subject = Subject::create([
                        'name'      => $subjectName,
                        'code'      => $code ?: 'SUB',
                        'is_active' => true,
                        'type'      => $this->subjectType($subjectName),
                    ]);
                }

                foreach ($chapters as $idx => $chap) {
                    $existing = Syllabus::where('class_id', $class->id)
                        ->where('subject_id', $subject->id)
                        ->where('chapter_title', $chap['chapter_title'])
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'chapter_number' => $chap['chapter_number'] ?? (string)($idx + 1),
                            'topics'         => $chap['topics'] ?? null,
                            'description'    => $chap['description'] ?? null,
                            'term'           => $chap['term'] ?? 'Term 1',
                            'status'         => $chap['status'] ?? 'pending',
                            'planned_date'   => $chap['planned_date'] ?? null,
                            'completed_date' => ($chap['status'] ?? '') === 'completed' ? ($chap['completed_date'] ?? '2025-08-10') : null,
                            'sort_order'     => $idx + 1,
                        ]);
                        $updated++;
                    } else {
                        Syllabus::create([
                            'class_id'         => $class->id,
                            'subject_id'       => $subject->id,
                            'academic_year_id' => $currentYear->id,
                            'chapter_number'   => $chap['chapter_number'] ?? (string)($idx + 1),
                            'chapter_title'    => $chap['chapter_title'],
                            'topics'           => $chap['topics'] ?? null,
                            'description'      => $chap['description'] ?? null,
                            'term'             => $chap['term'] ?? 'Term 1',
                            'status'           => $chap['status'] ?? 'pending',
                            'planned_date'     => $chap['planned_date'] ?? null,
                            'completed_date'   => ($chap['status'] ?? '') === 'completed' ? ($chap['completed_date'] ?? '2025-08-10') : null,
                            'sort_order'       => $idx + 1,
                        ]);
                        $inserted++;
                    }
                }
            }
        }

        $this->command->info("Syllabus seeding complete: {$inserted} inserted, {$updated} updated.");
    }

    private function getFullCurriculum(): array
    {
        return [
            // =========================================================================
            // PRE-KG (Toddler / Early Explorers Curriculum - 3 Terms)
            // =========================================================================
            'Pre-KG' => [
                'English Rhymes & Phonics' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Nursery Rhymes & Listening Fun', 'topics' => 'Twinkle Twinkle, Johny Johny, Baa Baa Black Sheep, Animal sounds listening', 'description' => 'Auditory perception and joyful singing with gestures', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-20'],
                    ['chapter_number' => '2', 'chapter_title' => 'Phonics Sound Exploration (A to F)', 'topics' => 'Letter sounds /a/ as in Apple, /b/ as in Ball, /c/ as in Cat, picture flashcards', 'description' => 'Visual identification of initial alphabets through sensory cards', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-15'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Phonics Sound Exploration (G to N)', 'topics' => 'Letter sounds /g/ to /n/, Action songs, Standing & Sleeping line tracing on sand', 'description' => 'Tracing lines on sand trays & sensory recognition', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-10'],
                    ['chapter_number' => '4', 'chapter_title' => 'Action Rhymes & Body Parts Songs', 'topics' => 'Head Shoulders Knees and Toes, If You\'re Happy and You Know It', 'description' => 'Kinesthetic coordination through music and body movement', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-05'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Phonics Sound Exploration (O to Z)', 'topics' => 'Sounds /o/ to /z/, Object matching games, Slanting line tracing', 'description' => 'Complete alphabet phonics introduction with tactile objects', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-20'],
                    ['chapter_number' => '6', 'chapter_title' => 'Story Rhymes & Picture Naming', 'topics' => 'Hickory Dickory Dock, Humpty Dumpty, Naming common objects from picture books', 'description' => 'Vocabulary expansion and expressive speech', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-15'],
                ],
                'Number Play & Math Concepts' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Pre-Math Concepts: Big & Small', 'topics' => 'Comparing big balls vs small balls, heavy vs light, inside vs outside basket', 'description' => 'Concrete object comparison and spatial vocabulary', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Numbers 1 & 2 with Real Objects', 'topics' => 'Counting 1 sun, 2 eyes, clapping 1-2 times, matching numbers with counters', 'description' => 'One-to-one correspondence for early quantity sense', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-20'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Numbers 3, 4 & 5 Count & Claps', 'topics' => 'Counting 3 wheels, 4 chair legs, 5 fingers, finger counting rhymes', 'description' => 'Multi-sensory counting activities using beads and blocks', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-15'],
                    ['chapter_number' => '4', 'chapter_title' => 'Basic Shapes: Circle & Square', 'topics' => 'Spotting round sun, circular plates, square windows, shape sorting boxes', 'description' => 'Tactile shape sorting and visual geometry basics', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-12'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Numbers 6 to 10 Fun with Towers', 'topics' => 'Building 10-block towers, number song 1-10, counting steps', 'description' => 'Number sequence and building quantity awareness', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Colors & Sorting: Red, Yellow, Blue, Green', 'topics' => 'Color sorting rings, matching same color balls, rainbow song', 'description' => 'Primary and secondary color classification', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-20'],
                ],
                'General Awareness & EVS' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'All About Me & My Body', 'topics' => 'Self introduction (name, age), Body parts recognition (eyes, ears, hands, legs), Hygiene (handwash)', 'description' => 'Self-awareness and daily hygiene routines', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-18'],
                    ['chapter_number' => '2', 'chapter_title' => 'My Family & School Friends', 'topics' => 'Father, Mother, Sister, Brother, Teacher, Classroom routines and sharing toys', 'description' => 'Social bonding and emotional security', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-22'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Furry Friends: Domestic & Wild Animals', 'topics' => 'Dog, Cat, Cow, Lion, Elephant, Animal sounds, homes and baby animals', 'description' => 'Love for nature and animal identification through models', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Yummy Fruits & Healthy Vegetables', 'topics' => 'Apple, Banana, Mango, Carrot, Tomato, Fruit salad day activity', 'description' => 'Nutritional awareness through real fruit touch and taste', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-20'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Vehicles & Travel Fun', 'topics' => 'Car, Bus, Train, Aeroplane, Boat, Traffic light rhyme (Red, Yellow, Green)', 'description' => 'Modes of transport and road safety awareness', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-28'],
                    ['chapter_number' => '6', 'chapter_title' => 'Seasons, Sun & Rainy Days', 'topics' => 'Sunny days, Umbrella in rain, Cold days, Dress according to weather', 'description' => 'Observing weather patterns and dressing habits', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-25'],
                ],
                'Drawing, Coloring & Craft' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Free Scribbling & Crayon Grip', 'topics' => 'Chubby crayon grip, free hand scribbling on large paper, tracing paths', 'description' => 'Developing pincer grasp and palmar control', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-02'],
                    ['chapter_number' => '2', 'chapter_title' => 'Palm & Finger Printing Art', 'topics' => 'Butterfly palm print, caterpillar finger print, color dipping fun', 'description' => 'Sensory exploration with washable finger colors', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-08-05'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Paper Tearing & Collage Pasting', 'topics' => 'Tearing colored paper bits, pasting on outline of apple, balloon craft', 'description' => 'Bilateral coordination and fine motor strength', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-25'],
                    ['chapter_number' => '4', 'chapter_title' => 'Clay Modeling: Rolling Balls & Snakes', 'topics' => 'Non-toxic playdough, rolling spheres, long snakes, cookie cutter shapes', 'description' => 'Hand muscle strengthening and 3D sensory play', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-28'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Vegetable & Sponge Stamping', 'topics' => 'Ladyfinger flower prints, potato stamp shapes, sponge dab painting', 'description' => 'Pattern creation using natural print media', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-05'],
                    ['chapter_number' => '6', 'chapter_title' => 'Festival Craft & Card Making', 'topics' => 'Diwali diya coloring, Christmas tree cotton pasting, New Year flower card', 'description' => 'Festive cultural crafts and gifting joy', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-03-01'],
                ],
                'Play & Motor Skills' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Gross Motor: Walking on Straight & Curved Lines', 'topics' => 'Walking on balance beam, crawling through tunnel, hopping like frog', 'description' => 'Balance, core strength and spatial orientation', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-30'],
                    // Term 2
                    ['chapter_number' => '2', 'chapter_title' => 'Fine Motor: Large Bead Threading & Peg Boards', 'topics' => 'Threading giant wooden beads on string, sorting colored pegs, tongs pickup', 'description' => 'Hand-eye coordination and precision dexterity', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-20'],
                    // Term 3
                    ['chapter_number' => '3', 'chapter_title' => 'Life Skills: Buttoning, Zipping & Mat Rolling', 'topics' => 'Frame activities: zip, velcro, buttoning big buttons, rolling personal nap mats', 'description' => 'Montessori independence and self-reliance skills', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-10'],
                ],
            ],

            // =========================================================================
            // LKG (Lower Kindergarten - 3 Terms)
            // =========================================================================
            'LKG' => [
                'English & Phonics' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Pre-Writing Strokes & Capital Letters (A-H)', 'topics' => 'Standing lines, Sleeping lines, Slanting lines, Curves, Writing Letters A to H with phonics', 'description' => 'Foundation stroke mastery and letter formation', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-22'],
                    ['chapter_number' => '2', 'chapter_title' => 'Capital Letters (I-P) & Picture Association', 'topics' => 'Writing I to P, Vocabulary words (Igloo, Jug, Kite, Lion, Moon, Nest, Orange, Pen)', 'description' => 'Letter-sound-picture association and vocalization', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-28'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Capital Letters (Q-Z) & Sound Families', 'topics' => 'Writing Q to Z, Alphabet song with phonics sounds, Missing letter exercises', 'description' => 'Completing uppercase alphabets and phonetic awareness', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-12'],
                    ['chapter_number' => '4', 'chapter_title' => 'Introduction to Small Letters (a-m)', 'topics' => 'Lowercase letter recognition, matching Capital with Small letters', 'description' => 'Case pairing and early lowercase print familiarity', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-15'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Small Letters (n-z) & Initial Sounds', 'topics' => 'Writing small letters n to z, identifying starting sound of pictures', 'description' => 'Phonemic segmentation of initial consonants and vowels', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-22'],
                    ['chapter_number' => '6', 'chapter_title' => 'Sight Words & 2-Letter Blends', 'topics' => 'Sight words (is, am, in, on, to, he, me), Blending /a/ sounds: at, an, am', 'description' => 'Pre-reading sight word flashcards and early oral blending', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-25'],
                ],
                'Mathematics' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Pre-Math: Big/Small, Tall/Short, More/Less', 'topics' => 'Comparative attributes, Heavy/Light, Full/Empty, sorting by size', 'description' => 'Building visual and spatial vocabulary through concrete objects', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Numbers 1 to 10 Writing & Counting', 'topics' => 'Trace and write 1 to 10, Count objects, Match numbers with dots, Number rhymes', 'description' => 'Quantity association and correct stroke order for numbers', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-30'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Numbers 11 to 20 & Place Value Concept', 'topics' => 'Bundle of 10 and ones, Writing 11 to 20, Missing numbers, Count and circle', 'description' => 'Base-10 introduction using decade sticks and beads', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Shapes & Patterns: Circle, Square, Triangle, Rectangle', 'topics' => 'Identify 2D shapes, Color patterns (AB, AAB), Drawing basic shapes', 'description' => 'Geometric awareness and repeating sequence logic', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-20'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Numbers 21 to 50 & Backward Counting (10-1)', 'topics' => 'Writing 21-50, What comes After, Before, In-between, Reverse countdown', 'description' => 'Sequential order and relative magnitude of numbers', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Concept of Zero & Simple Additions with Pictures', 'topics' => 'Meaning of Zero (empty nest), Picture addition up to 5 (2 apples + 1 apple)', 'description' => 'Concrete addition modeling using pictures and manipulatives', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
                'Environmental Studies (EVS)' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Myself, My Sense Organs & Daily Routine', 'topics' => 'Name, age, gender, 5 senses (Sight, Hearing, Smell, Taste, Touch), Morning to night routines', 'description' => 'Personal identification and sensory functions', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-20'],
                    ['chapter_number' => '2', 'chapter_title' => 'My Home & Family Relationships', 'topics' => 'Rooms in a house, Family members, Kitchen safety, Good manners', 'description' => 'Social environment and household safety', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-25'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Animal Kingdom & Their Homes', 'topics' => 'Domestic animals, Wild animals, Water animals, Birds, Animal homes (Nest, Kennel, Den)', 'description' => 'Biodiversity and caring for living beings', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-22'],
                    ['chapter_number' => '4', 'chapter_title' => 'Plants, Trees, Flowers & Fruits', 'topics' => 'Parts of a plant (Root, Stem, Leaf, Flower), Common flowers (Rose, Lotus, Marigold), Fruits vs Veggies', 'description' => 'Botany basics and nature walks', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-25'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Community Helpers & Transport Modes', 'topics' => 'Doctor, Police, Firefighter, Teacher, Postman; Land, Air and Water vehicles', 'description' => 'Civic roles and appreciating helpers in society', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-02'],
                    ['chapter_number' => '6', 'chapter_title' => 'Good Habits, Golden Words & Road Safety', 'topics' => 'Please, Thank You, Sorry; Brushing twice, Zebra crossing, Green Earth & Cleanliness', 'description' => 'Value education, etiquette and civic hygiene', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-03-02'],
                ],
            ],

            // =========================================================================
            // UKG (Upper Kindergarten - 3 Terms)
            // =========================================================================
            'UKG' => [
                'English' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Vowels, Consonants & CVC Word Families (-at, -an, -ap)', 'topics' => '5 Vowels (A, E, I, O, U), Consonant blends, CVC words: bat, cat, mat, pan, cap', 'description' => 'Systematic phonics reading and word building', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Short Vowel Words (-en, -et, -in, -ip, -ot, -op, -ug)', 'topics' => 'Word ladders, rhyming words, reading short 3-letter sentences (A cat on a mat)', 'description' => 'Decodable reading and handwriting on four-lines', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-28'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Sight Words & Sentence Construction', 'topics' => 'This/That, These/Those, In/On/Under/Behind, Reading 20 key sight words', 'description' => 'Prepositions, demonstrative pronouns and simple sentence writing', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-15'],
                    ['chapter_number' => '4', 'chapter_title' => 'Opposites & One / Many (Plurals with -s)', 'topics' => 'Hot/Cold, Happy/Sad, Boy/Boys, Tree/Trees, Simple picture composition', 'description' => 'Vocabulary expansion and descriptive writing', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-18'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Action Words (Verbs) & Simple Questions', 'topics' => 'Run, Jump, Eat, Sleep, Reading "What is this?", "Who is he?", Creative talk', 'description' => 'Verbs with -ing and interactive dialogue skills', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Short Story Reading & Picture Comprehension', 'topics' => 'Reading 4-line mini stories, answering oral questions, sequencing story cards', 'description' => 'Early independent reading comprehension', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
                'Mathematics' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Numbers 1 to 50 & Number Names (One to Twenty)', 'topics' => 'Writing 1-50, Number names One to Twenty, Missing numbers, Number line jumps', 'description' => 'Numeration and numeral-to-word translation', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-28'],
                    ['chapter_number' => '2', 'chapter_title' => 'Comparing Numbers & Greater Than / Less Than (> < =)', 'topics' => 'Hungry crocodile concept for > and <, Equal to, Ordering numbers ascending/descending', 'description' => 'Relational mathematical operators and set comparison', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-31'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Numbers 51 to 100 & Skip Counting by 2s, 5s and 10s', 'topics' => '100s chart navigation, Skip count 2, 4, 6... and 5, 10, 15... Number names 21-50', 'description' => 'Patterns in numbers and readiness for multiplication', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-20'],
                    ['chapter_number' => '4', 'chapter_title' => 'Single Digit Addition (Horizontal & Vertical)', 'topics' => 'Addition using objects, fingers, and number line (e.g. 4 + 3 = 7), Story word problems', 'description' => 'Concept of addition as combining two sets', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-22'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Single Digit Subtraction (Take Away)', 'topics' => 'Subtraction using cross-out method, number line backwards (e.g. 8 - 3 = 5)', 'description' => 'Concept of subtraction as difference and taking away', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-28'],
                    ['chapter_number' => '6', 'chapter_title' => 'Time, Days of Week, Months & Money Basics', 'topics' => 'O\'clock reading on clock face, 7 days of week, 12 months, Coins (₹1, ₹2, ₹5, ₹10)', 'description' => 'Applied math for daily practical life', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
                'General Science & EVS' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Living vs Non-Living Things', 'topics' => 'Characteristics: Can grow, breathe, eat, feel vs Non-living objects', 'description' => 'Fundamental scientific classification', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-22'],
                    ['chapter_number' => '2', 'chapter_title' => 'Our Environment: Air, Water and Soil', 'topics' => 'Importance of clean air, sources of water, water cycle in a jar, saving water', 'description' => 'Ecological conservation and practical experiments', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-26'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Animal Habitats: Land, Water, Trees & Sky', 'topics' => 'Terrestrial, Aquatic, Arboreal and Aerial animals, Herbivores and Carnivores', 'description' => 'Ecological niches and animal adaptations', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Food We Eat & Sources (Plants vs Animals)', 'topics' => 'Healthy food vs Junk food, Meals of the day (Breakfast, Lunch, Dinner), Balanced diet', 'description' => 'Nutrition and healthy lifestyle awareness', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-24'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Our Universe: Sun, Moon, Stars and Earth', 'topics' => 'Day and Night cycle, Solar system overview, Why we have shadows', 'description' => 'Astronomy introduction for young learners', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-05'],
                    ['chapter_number' => '6', 'chapter_title' => 'National Symbols & Festivals of India', 'topics' => 'National Flag (Tiranga), National Bird (Peacock), Animal (Tiger), National Anthem, Festivals', 'description' => 'Patriotism, unity in diversity, and cultural heritage', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-03-05'],
                ],
            ],

            // =========================================================================
            // STANDARD II (Class 2 - 3 Terms)
            // =========================================================================
            'II' => [
                'English' => [
                    ['chapter_number' => '1', 'chapter_title' => 'First Day at School & Haldi\'s Adventure', 'topics' => 'Poems, Feelings, Adventure, Action words, Simple sentences', 'description' => 'Reading fluency and emotional expression', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-20'],
                    ['chapter_number' => '2', 'chapter_title' => 'I am Lucky! & I Want', 'topics' => 'Thankfulness, Animal body parts, Magic wand story, Singular/Plural', 'description' => 'Self-esteem building and creative story reading', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-25'],
                    ['chapter_number' => '3', 'chapter_title' => 'A Smile & The Wind and the Sun', 'topics' => 'Smile poem, Aesop fable, Opposites (Hot/Cold, Strong/Weak), Comparative adjectives', 'description' => 'Moral fable and comparative vocabulary', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-15'],
                    ['chapter_number' => '4', 'chapter_title' => 'Rain & Storm in the Garden', 'topics' => 'Rain poem, Snail story (Sunu-sunu), Sound words (Kazaam, Pit-pat), Prepositions', 'description' => 'Onomatopoeia and nature observations', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-20'],
                    ['chapter_number' => '5', 'chapter_title' => 'Zoo Manners & Funny Bunny', 'topics' => 'Animal respect, Story about rumors, Compound words, Capital letters and full stops', 'description' => 'Social etiquette and punctuation basics', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-20'],
                    ['chapter_number' => '6', 'chapter_title' => 'Mr. Nobody & Curlylocks and the Three Bears', 'topics' => 'Mischief poem, Fairy tale comprehension, Goldilocks adaptation, Gender words', 'description' => 'Classic story comprehension and gender nouns', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-25'],
                ],
                'Mathematics' => [
                    ['chapter_number' => '1', 'chapter_title' => 'What is Long, What is Round? & Counting in Groups', 'topics' => 'Rolling/Sliding objects, Skip counting in 2s and 5s, Guessing quantities', 'description' => '3D geometry and group estimation', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'How Much Can You Carry? & Counting in Tens', 'topics' => 'Weight comparisons (Heavier/Lighter), Bundles of 10s and ones, Place value 1-99', 'description' => 'Measurement and decimal place value', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-28'],
                    ['chapter_number' => '3', 'chapter_title' => 'Patterns & Footprints (2D Shapes)', 'topics' => 'Repeating color/shape patterns, Tracing object footprints, Edges and corners', 'description' => 'Visual patterns and polygon geometry', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Jugs and Mugs (Capacity) & Tens and Ones', 'topics' => 'Measuring capacity with glasses, 2-digit addition and subtraction without carry', 'description' => 'Liquid volume and early columnar arithmetic', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-22'],
                    ['chapter_number' => '5', 'chapter_title' => 'Lines and Lines & Give and Take', 'topics' => 'Straight, Slanting and Curved lines, Dancing lines, 2-digit addition with carry over', 'description' => 'Geometric lines and regrouping addition', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'The Longest Step & How Many Ponytails?', 'topics' => 'Measuring length with finger/footspan, Data tables, Pictographs and counting charts', 'description' => 'Non-standard length measurement and statistical charts', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
                'EVS' => [
                    ['chapter_number' => '1', 'chapter_title' => 'About Myself & My Extended Family', 'topics' => 'Growing up, Baby to child, Grandparents, Family tree, Helping each other at home', 'description' => 'Human growth stages and family heritage', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-22'],
                    ['chapter_number' => '2', 'chapter_title' => 'Food We Eat & Sources of Water', 'topics' => 'Meals of the day, Healthy eating habits, Rivers, Wells, Rainwater harvesting', 'description' => 'Nutrition and precious water resources', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-30'],
                    ['chapter_number' => '3', 'chapter_title' => 'Our Clothes & Types of Houses', 'topics' => 'Cotton, Woolen, Silk, Synthetic; Kutcha vs Pucca houses, Caravans, Igloos', 'description' => 'Shelter and textile diversity in India', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-16'],
                    ['chapter_number' => '4', 'chapter_title' => 'Plant World & Animal Care', 'topics' => 'Herbs, Shrubs, Trees, Climbers, Creepers; Pets, Farm animals and their shelters', 'description' => 'Plant types and compassion towards fauna', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-25'],
                    ['chapter_number' => '5', 'chapter_title' => 'Means of Transport & Communication', 'topics' => 'Roadways, Railways, Airways, Waterways; Letters, Phone, Internet, Email', 'description' => 'Mobility and connecting with the world', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-28'],
                    ['chapter_number' => '6', 'chapter_title' => 'The Earth, Sun, Moon & Our Festivals', 'topics' => 'Landforms (Hills, Plains, Valleys), Day & Night, National festivals (Independence Day, Republic Day)', 'description' => 'Physical geography basics and patriotic celebrations', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-03-05'],
                ],
            ],

            // =========================================================================
            // STANDARD XI (Class 11 - Science & Commerce - 3 Terms)
            // =========================================================================
            'XI' => [
                'Physics' => [
                    ['chapter_number' => '1', 'chapter_title' => 'Units and Measurements & Motion in a Straight Line', 'topics' => 'Dimensional analysis, Significant figures, Kinematics equations, Velocity-time calculus', 'description' => 'Foundational mechanics and dimensional consistency', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Motion in a Plane & Laws of Motion', 'topics' => 'Vector calculus, Projectile motion, Circular motion, Newton\'s laws, Friction & Banking of roads', 'description' => '2D dynamics and forces', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-30'],
                    ['chapter_number' => '3', 'chapter_title' => 'Work, Energy and Power & System of Particles (Rotation)', 'topics' => 'Work-Energy Theorem, Elastic/Inelastic collisions, Center of Mass, Moment of Inertia, Torque', 'description' => 'Conservative forces and rotational dynamics', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-20'],
                    ['chapter_number' => '4', 'chapter_title' => 'Gravitation & Mechanical Properties of Solids/Fluids', 'topics' => 'Kepler\'s laws, Escape velocity, Hooke\'s law, Young\'s modulus, Pascal\'s law, Bernoulli\'s theorem', 'description' => 'Planetary orbits and continuum mechanics', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-28'],
                    ['chapter_number' => '5', 'chapter_title' => 'Thermal Properties of Matter & Thermodynamics', 'topics' => 'Specific heat, Calorimetry, Heat engines, First & Second Laws of Thermodynamics, Carnot cycle', 'description' => 'Heat transfer and thermodynamic state functions', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Kinetic Theory of Gases & Oscillations and Waves', 'topics' => 'Ideal gas equation, Degrees of freedom, Simple Harmonic Motion (SHM), Wave resonance, Doppler effect', 'description' => 'Statistical physics and wave dynamics', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
                'Chemistry' => [
                    ['chapter_number' => '1', 'chapter_title' => 'Some Basic Concepts of Chemistry & Structure of Atom', 'topics' => 'Mole concept, Stoichiometry, Empirical formulas, Bohr model, Quantum numbers, Electronic config', 'description' => 'Quantitative chemistry and quantum atomic model', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-28'],
                    ['chapter_number' => '2', 'chapter_title' => 'Classification of Elements & Chemical Bonding (VSEPR, Hybridisation)', 'topics' => 'Periodic trends (IE, EA, Electronegativity), Lewis structures, VSEPR shapes, MO theory, Hydrogen bond', 'description' => 'Periodicity and molecular structure theories', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-31'],
                    ['chapter_number' => '3', 'chapter_title' => 'Chemical Thermodynamics & Equilibrium (Chemical & Ionic)', 'topics' => 'Enthalpy, Entropy, Gibbs Free Energy, Le Chatelier principle, pH calculations, Buffer solutions', 'description' => 'Chemical energetics and reversible reactions', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-22'],
                    ['chapter_number' => '4', 'chapter_title' => 'Redox Reactions & Organic Chemistry: Basic Principles', 'topics' => 'Oxidation numbers, Balancing redox, IUPAC nomenclature, Inductive/Resonance effects, Carbocations', 'description' => 'Electron transfer and organic reaction mechanisms', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-30'],
                    ['chapter_number' => '5', 'chapter_title' => 'Hydrocarbons: Alkanes, Alkenes, Alkynes & Aromaticity', 'topics' => 'Conformations, Electrophilic addition, Markownikoff rule, Benzene electrophilic substitution', 'description' => 'Functional aliphatic and aromatic synthesis', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-28'],
                    ['chapter_number' => '6', 'chapter_title' => 'Environmental Chemistry & Green Laboratory Practices', 'topics' => 'Atmospheric pollution, Acid rain, Smog, Ozone hole, Green chemistry 12 principles', 'description' => 'Environmental conservation and sustainable chemistry', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-03-02'],
                ],
            ],

            // =========================================================================
            // STANDARD XII (Class 12 - Science & Board Revisions - 3 Terms)
            // =========================================================================
            'XII' => [
                'Physics' => [
                    ['chapter_number' => '1', 'chapter_title' => 'Electric Charges and Fields & Electrostatic Potential', 'topics' => 'Gauss\'s law, Electric flux, Dipole field, Capacitors in series/parallel, Dielectric polarisation', 'description' => 'Classical electrostatics and energy storage', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Current Electricity & Moving Charges and Magnetism', 'topics' => 'Kirchhoff\'s laws, Wheatstone bridge, Potentiometer, Biot-Savart law, Cyclotron, Ampere loop', 'description' => 'DC circuits and magnetic fields of currents', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-28'],
                    ['chapter_number' => '3', 'chapter_title' => 'Electromagnetic Induction & Alternating Current (AC)', 'topics' => 'Faraday laws, Lenz law, Eddy currents, LCR series circuit, Resonance, Transformer efficiency', 'description' => 'Time-varying fields and AC power engineering', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Ray Optics & Wave Optics (Huygens Principle, Interference)', 'topics' => 'TIR, Lens maker formula, Prism dispersion, Young double slit experiment, Diffraction, Polarisation', 'description' => 'Geometrical and physical wave optics', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-25'],
                    ['chapter_number' => '5', 'chapter_title' => 'Dual Nature of Matter & Atoms and Nuclei', 'topics' => 'Photoelectric effect, Einstein equation, De Broglie wavelength, Bohr atom model, Nuclear binding energy, Fission/Fusion', 'description' => 'Modern physics and quantum particle duality', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-20'],
                    ['chapter_number' => '6', 'chapter_title' => 'Semiconductor Electronics & Digital Logic', 'topics' => 'P-N junction diode, Rectifiers, Zener diode, Solar cells, Transistors and Logic gates', 'description' => 'Solid state electronics and digital foundations', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-22'],
                ],
                'Mathematics' => [
                    ['chapter_number' => '1', 'chapter_title' => 'Relations and Functions & Inverse Trigonometry', 'topics' => 'Equivalence relations, Bijective functions, Composite functions, Principal value branches', 'description' => 'Abstract function spaces and inverse transforms', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-28'],
                    ['chapter_number' => '2', 'chapter_title' => 'Matrices and Determinants & Continuity/Differentiability', 'topics' => 'Inverse of matrix, Cramer rule, Chain rule, Implicit differentiation, Logarithmic differentiation', 'description' => 'Linear algebra and differential calculus', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-30'],
                    ['chapter_number' => '3', 'chapter_title' => 'Applications of Derivatives & Indefinite/Definite Integrals', 'topics' => 'Increasing/Decreasing, Maxima-Minima, Integration by parts, Partial fractions, Definite properties', 'description' => 'Optimization and integral calculus techniques', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-20'],
                    ['chapter_number' => '4', 'chapter_title' => 'Differential Equations & Vector Algebra', 'topics' => 'Variable separable, Homogeneous DE, Linear DE with IF, Dot/Cross products, Scalar triple product', 'description' => 'Dynamical equations and 3D vector spaces', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-28'],
                    ['chapter_number' => '5', 'chapter_title' => 'Three Dimensional Geometry (3D) & Linear Programming', 'topics' => 'Direction cosines, Equations of lines and planes in 3D, Angle between planes, Corner point method LPP', 'description' => 'Space geometry and operational optimization', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Probability: Conditional Probability & Bayes\' Theorem', 'topics' => 'Multiplication theorem, Independent events, Bayes theorem, Random variable probability distribution', 'description' => 'Advanced conditional probability and Bayesian inference', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-28'],
                ],
            ],

            // =========================================================================
            // STANDARD X (Class 10 - Board Prep Curriculum - 3 Terms)
            // =========================================================================
            'X' => [
                'Science' => [
                    // Term 1 (Mid-Term)
                    ['chapter_number' => '1', 'chapter_title' => 'Chemical Reactions and Equations', 'topics' => 'Balancing equations, Types: Combination, Decomposition, Displacement, Redox, Corrosion & Rancidity', 'description' => 'Chemical stoichiometry and reaction kinetics', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-25'],
                    ['chapter_number' => '2', 'chapter_title' => 'Life Processes', 'topics' => 'Autotrophic/Heterotrophic nutrition, Human Respiration, Circulatory system, Nephron Excretion', 'description' => 'Comprehensive physiology of living organisms', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-20'],
                    ['chapter_number' => '3', 'chapter_title' => 'Light: Reflection and Refraction', 'topics' => 'Spherical mirrors, Mirror formula, Snell\'s law, Lens formula, Power of lenses, Ray diagrams', 'description' => 'Geometrical optics and analytical ray tracing', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-08-15'],
                    // Term 2 (Pre-Board 1)
                    ['chapter_number' => '4', 'chapter_title' => 'Acids, Bases and Salts & Metals/Non-Metals', 'topics' => 'pH scale, Chlor-alkali process, Plaster of Paris, Reactivity series, Metallurgy, Ionic bonding', 'description' => 'Inorganic chemistry and extraction metallurgy', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-10'],
                    ['chapter_number' => '5', 'chapter_title' => 'Electricity & Magnetic Effects of Current', 'topics' => 'Ohm\'s law, Resistivity, Series/Parallel circuits, Joule\'s heating, Solenoid, Fleming\'s left hand rule, Motor', 'description' => 'Electromagnetism and circuit analysis', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-15'],
                    ['chapter_number' => '6', 'chapter_title' => 'Control & Coordination and How Organisms Reproduce', 'topics' => 'Nervous system, Phytohormones, Reflex arc, Asexual fission, Sexual reproduction in angiosperms, Human reproductive health', 'description' => 'Endocrinology, genetics and developmental biology', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-12-10'],
                    // Term 3 (Final Board Revisions)
                    ['chapter_number' => '7', 'chapter_title' => 'Carbon and its Compounds', 'topics' => 'Covalent bonding, Versatile nature, Homologous series, Functional groups, Saponification, Isomerism', 'description' => 'Organic chemistry foundations for Class 10', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-15'],
                    ['chapter_number' => '8', 'chapter_title' => 'Heredity & Our Environment (Ecology)', 'topics' => 'Mendel\'s monohybrid & dihybrid cross, Sex determination, Trophic levels, 10% law, Ozone depletion, Waste management', 'description' => 'Classical genetics and ecological preservation', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-10'],
                ],
                'Mathematics' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Real Numbers & Polynomials', 'topics' => 'Fundamental Theorem of Arithmetic, Irrationality proofs, Geometrical zeroes, Quadratic polynomials', 'description' => 'Number theory and polynomial zeroes', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-28'],
                    ['chapter_number' => '2', 'chapter_title' => 'Pair of Linear Equations in Two Variables', 'topics' => 'Graphical method, Substitution & Elimination methods, Word problems (Age, Speed, Fractions)', 'description' => 'Simultaneous algebraic equations', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-25'],
                    ['chapter_number' => '3', 'chapter_title' => 'Introduction to Trigonometry & Triangles', 'topics' => 'Trig ratios, Standard angles (0°-90°), Trig identities (sin²+cos²=1), BPT theorem, Similarity criteria', 'description' => 'Trigonometric ratios and geometric proofs', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-08-28'],
                    // Term 2
                    ['chapter_number' => '4', 'chapter_title' => 'Quadratic Equations & Arithmetic Progressions', 'topics' => 'Factorization, Quadratic formula, Discriminant & nature of roots, AP nth term, Sum of n terms', 'description' => 'Second-degree equations and numerical progressions', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-15'],
                    ['chapter_number' => '5', 'chapter_title' => 'Coordinate Geometry & Some Applications of Trigonometry', 'topics' => 'Distance formula, Section formula, Angles of elevation & depression, Heights and distances', 'description' => 'Analytic geometry and real-world trigonometry', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-20'],
                    ['chapter_number' => '6', 'chapter_title' => 'Circles & Areas Related to Circles', 'topics' => 'Tangent theorems, Length of tangents, Sector area, Segment area, Perimeter calculations', 'description' => 'Circle geometry and mensuration of planar curves', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-12-15'],
                    // Term 3
                    ['chapter_number' => '7', 'chapter_title' => 'Surface Areas and Volumes', 'topics' => 'Combination of solids (Cylinder, Cone, Hemisphere, Sphere), Conversion of shapes, Frustum', 'description' => '3D solid mensuration and volumetric formulas', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-20'],
                    ['chapter_number' => '8', 'chapter_title' => 'Statistics & Probability', 'topics' => 'Mean (Direct & Assumed mean), Median, Mode of grouped data, Empirical probability, Dice/Cards problems', 'description' => 'Data analysis and theoretical probability', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-15'],
                ],
                'Social Science' => [
                    // Term 1
                    ['chapter_number' => '1', 'chapter_title' => 'Rise of Nationalism in Europe & Resources/Development', 'topics' => 'French Revolution impact, Unification of Germany/Italy, Resource planning, Soil conservation', 'description' => 'European modern history and geographic resource base', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-30'],
                    ['chapter_number' => '2', 'chapter_title' => 'Power Sharing & Federalism (Civics)', 'topics' => 'Belgium and Sri Lanka models, 3-tier federalism in India, Decentralization, Coalition govt', 'description' => 'Democratic governance and institutional structures', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-30'],
                    // Term 2
                    ['chapter_number' => '3', 'chapter_title' => 'Nationalism in India & Sectors of Indian Economy', 'topics' => 'Non-Cooperation, Civil Disobedience, Gandhi, Primary/Secondary/Tertiary sectors, GDP, MGNREGA', 'description' => 'Indian freedom struggle and macroeconomic sectors', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-18'],
                    ['chapter_number' => '4', 'chapter_title' => 'Agriculture & Money and Credit', 'topics' => 'Kharif/Rabi crops, Institutional reforms, Formal vs Informal credit sources, SHGs, RBI role', 'description' => 'Agrarian economy and financial systems', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-25'],
                    // Term 3
                    ['chapter_number' => '5', 'chapter_title' => 'Manufacturing Industries & Political Parties', 'topics' => 'Textile, Iron & Steel, Location factors, Pollution; National and State parties in India', 'description' => 'Industrial geography and democratic party politics', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-25'],
                    ['chapter_number' => '6', 'chapter_title' => 'Globalisation & Outcomes of Democracy', 'topics' => 'MNCs, Foreign trade, WTO, Enabling factors; Accountable, responsive and legitimate government', 'description' => 'International economics and political evaluation', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-20'],
                ],
                'English' => [
                    ['chapter_number' => '1', 'chapter_title' => 'A Letter to God & Dust of Snow (Poem)', 'topics' => 'Faith, Irony, Robert Frost poems, Rhyme scheme, Reading comprehension', 'description' => 'Short prose analysis and poetic symbolism', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-06-22'],
                    ['chapter_number' => '2', 'chapter_title' => 'Nelson Mandela: Long Walk to Freedom & Fire and Ice', 'topics' => 'Apartheid, Courage, Metaphor, Analytical writing, Formal letter to editor', 'description' => 'Autobiographical discourse and expressive poetry', 'term' => 'Term 1', 'status' => 'completed', 'planned_date' => '2025-07-28'],
                    ['chapter_number' => '3', 'chapter_title' => 'Two Stories About Flying & A Tiger in the Zoo', 'topics' => 'His First Flight, Black Aeroplane, Animal captivity poem, Tenses revision', 'description' => 'Themes of courage and freedom with grammar focus', 'term' => 'Term 2', 'status' => 'in_progress', 'planned_date' => '2025-10-12'],
                    ['chapter_number' => '4', 'chapter_title' => 'From the Diary of Anne Frank & The Ball Poem', 'topics' => 'Holocaust reflection, Diary genre, Grief and growing up, Modals & determiners', 'description' => 'Historical diary analysis and modern poetry', 'term' => 'Term 2', 'status' => 'pending', 'planned_date' => '2025-11-20'],
                    ['chapter_number' => '5', 'chapter_title' => 'Glimpses of India & Madam Rides the Bus', 'topics' => 'Baker from Goa, Coorg, Tea from Assam, Tamil rural journey, Reported speech', 'description' => 'Cultural travelogue of India and tender coming-of-age prose', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-01-22'],
                    ['chapter_number' => '6', 'chapter_title' => 'The Sermon at Benares & The Proposal (Drama)', 'topics' => 'Buddha\'s philosophy of mortality, Chekhov\'s one-act farce drama, Analytical essay', 'description' => 'Philosophical prose and Russian satirical theater', 'term' => 'Term 3', 'status' => 'pending', 'planned_date' => '2026-02-22'],
                ],
            ],
        ];
    }
}
