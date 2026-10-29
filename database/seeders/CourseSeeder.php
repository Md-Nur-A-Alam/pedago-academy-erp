<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Fullstack Web Development with Laravel & Vue',
                'code' => 'FSWD-101',
                'description' => 'Comprehensive enterprise web engineering program covering backend APIs, Blade, Vue 3, and MySQL database architecture.',
                'conditions' => 'Basic computer literacy and understanding of basic programming principles.',
                'subject' => 'Programming',
                'version' => 'Bangla',
                'category' => 'Software Engineering',
                'base_fee' => 15000.00,
                'is_active' => true,
            ],
            [
                'title' => 'Spoken English & Communication Mastery',
                'code' => 'ENG-201',
                'description' => 'Fluency training, corporate presentation skills, and accent reduction workshops with native audio drills.',
                'conditions' => 'Open to all students and professionals.',
                'subject' => 'English',
                'version' => 'Bangla',
                'category' => 'Language & Communication',
                'base_fee' => 6000.00,
                'is_active' => true,
            ],
            [
                'title' => 'Data Science & Applied Machine Learning',
                'code' => 'DSML-301',
                'description' => 'Python for data analysis, statistical modeling, machine learning pipelines, and predictive analytics.',
                'conditions' => 'Basic high school mathematics and fundamental logic.',
                'subject' => 'Science',
                'version' => 'English',
                'category' => 'Artificial Intelligence',
                'base_fee' => 20000.00,
                'is_active' => true,
            ],
            [
                'title' => 'IELTS Academic Comprehensive Prep',
                'code' => 'IELTS-401',
                'description' => 'Reading, Writing, Listening, and Speaking modules with certified examiners and mock test evaluations.',
                'conditions' => 'Intermediate English proficiency.',
                'subject' => 'English',
                'version' => 'English',
                'category' => 'Test Preparation',
                'base_fee' => 10000.00,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
