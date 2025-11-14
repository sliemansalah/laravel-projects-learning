<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'مرحباً بك في مدونتي',
                'excerpt' => 'هذا هو المقال الأول في المدونة',
                'content' => 'مرحباً بك في مدونتي الشخصية المبنية بإطار عمل Laravel. سأشارك هنا تجاربي وأفكاري في مجال البرمجة والتطوير.',
                'published' => true,
            ],
            [
                'title' => 'تعلم Laravel من الصفر',
                'excerpt' => 'دليل شامل للمبتدئين',
                'content' => 'Laravel هو إطار عمل PHP قوي وسهل الاستخدام. في هذا المقال سنتعرف على أساسيات Laravel وكيفية البدء في بناء تطبيقات ويب احترافية.',
                'published' => true,
            ],
            [
                'title' => 'أفضل ممارسات البرمجة',
                'excerpt' => 'نصائح لكتابة كود نظيف',
                'content' => 'كتابة كود نظيف ومنظم هو أساس أي مشروع ناجح. في هذا المقال سنستعرض أفضل الممارسات التي يجب اتباعها عند كتابة الأكواد البرمجية.',
                'published' => true,
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
