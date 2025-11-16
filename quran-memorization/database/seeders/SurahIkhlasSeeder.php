<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Surah;
use App\Models\Ayah;
use App\Models\Word;

class SurahIkhlasSeeder extends Seeder
{
    public function run(): void
    {
        // Create Surah Al-Ikhlas
        $surah = Surah::create([
            'number' => 112,
            'name_arabic' => 'الإخلاص',
            'name_english' => 'The Sincerity',
            'name_transliteration' => 'Al-Ikhlas',
            'total_ayahs' => 4,
            'revelation_type' => 'meccan',
            'revelation_order' => 22
        ]);

        // Ayah 1: قُلْ هُوَ اللَّهُ أَحَدٌ
        $ayah1 = Ayah::create([
            'surah_id' => $surah->id,
            'number' => 1,
            'text_arabic' => 'قُلْ هُوَ اللَّهُ أَحَدٌ',
            'text_uthmani' => 'قُلۡ هُوَ ٱللَّهُ أَحَدٌ',
            'translation_en' => 'Say, "He is Allah, [who is] One',
            'translation_ar' => 'قل يا محمد: الله واحد لا شريك له',
            'tafseer' => 'أمر الله نبيه محمد صلى الله عليه وسلم أن يخبر الناس بأن الله واحد أحد، ليس له شريك في ملكه ولا في عبادته',
            'audio_url' => 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6236.mp3'
        ]);

        // Words for Ayah 1
        $words1 = [
            ['position' => 1, 'text_arabic' => 'قُلْ', 'text_uthmani' => 'قُلۡ', 'transliteration' => 'Qul', 'translation' => 'Say', 'meaning' => 'فعل أمر بمعنى: تكلم وأخبر'],
            ['position' => 2, 'text_arabic' => 'هُوَ', 'text_uthmani' => 'هُوَ', 'transliteration' => 'Huwa', 'translation' => 'He', 'meaning' => 'ضمير منفصل يعود على الله'],
            ['position' => 3, 'text_arabic' => 'اللَّهُ', 'text_uthmani' => 'ٱللَّهُ', 'transliteration' => 'Allah', 'translation' => 'Allah', 'meaning' => 'اسم الجلالة، الإله المعبود بحق'],
            ['position' => 4, 'text_arabic' => 'أَحَدٌ', 'text_uthmani' => 'أَحَدٌ', 'transliteration' => 'Ahad', 'translation' => 'One', 'meaning' => 'واحد لا شريك له ولا مثيل'],
        ];

        foreach ($words1 as $wordData) {
            Word::create(array_merge(['ayah_id' => $ayah1->id], $wordData));
        }

        // Ayah 2: اللَّهُ الصَّمَدُ
        $ayah2 = Ayah::create([
            'surah_id' => $surah->id,
            'number' => 2,
            'text_arabic' => 'اللَّهُ الصَّمَدُ',
            'text_uthmani' => 'ٱللَّهُ ٱلصَّمَدُ',
            'translation_en' => 'Allah, the Eternal Refuge',
            'translation_ar' => 'الله الذي يُقصد في الحوائج',
            'tafseer' => 'الله هو السيد الذي يُقصد في جميع الحوائج والنوائب، الذي لا جوف له، الذي لم يلد ولم يولد',
            'audio_url' => 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6237.mp3'
        ]);

        $words2 = [
            ['position' => 1, 'text_arabic' => 'اللَّهُ', 'text_uthmani' => 'ٱللَّهُ', 'transliteration' => 'Allah', 'translation' => 'Allah', 'meaning' => 'اسم الجلالة'],
            ['position' => 2, 'text_arabic' => 'الصَّمَدُ', 'text_uthmani' => 'ٱلصَّمَدُ', 'transliteration' => 'As-Samad', 'translation' => 'The Eternal', 'meaning' => 'السيد المقصود في الحوائج، الذي لا جوف له'],
        ];

        foreach ($words2 as $wordData) {
            Word::create(array_merge(['ayah_id' => $ayah2->id], $wordData));
        }

        // Ayah 3: لَمْ يَلِدْ وَلَمْ يُولَدْ
        $ayah3 = Ayah::create([
            'surah_id' => $surah->id,
            'number' => 3,
            'text_arabic' => 'لَمْ يَلِدْ وَلَمْ يُولَدْ',
            'text_uthmani' => 'لَمۡ يَلِدۡ وَلَمۡ يُولَدۡ',
            'translation_en' => 'He neither begets nor is born',
            'translation_ar' => 'لم يلد ولداً ولم يكن له والد',
            'tafseer' => 'لم يلد الله أحداً لأنه منزه عن الصاحبة والولد، ولم يولد لأنه الأول الذي ليس قبله شيء',
            'audio_url' => 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6238.mp3'
        ]);

        $words3 = [
            ['position' => 1, 'text_arabic' => 'لَمْ', 'text_uthmani' => 'لَمۡ', 'transliteration' => 'Lam', 'translation' => 'Not', 'meaning' => 'أداة نفي وجزم'],
            ['position' => 2, 'text_arabic' => 'يَلِدْ', 'text_uthmani' => 'يَلِدۡ', 'transliteration' => 'Yalid', 'translation' => 'Begets', 'meaning' => 'فعل مضارع مجزوم، بمعنى: ينجب ولداً'],
            ['position' => 3, 'text_arabic' => 'وَلَمْ', 'text_uthmani' => 'وَلَمۡ', 'transliteration' => 'Wa Lam', 'translation' => 'And not', 'meaning' => 'الواو للعطف، ولم للنفي'],
            ['position' => 4, 'text_arabic' => 'يُولَدْ', 'text_uthmani' => 'يُولَدۡ', 'transliteration' => 'Yulad', 'translation' => 'Is born', 'meaning' => 'فعل مضارع مبني للمجهول، بمعنى: يكون مولوداً'],
        ];

        foreach ($words3 as $wordData) {
            Word::create(array_merge(['ayah_id' => $ayah3->id], $wordData));
        }

        // Ayah 4: وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ
        $ayah4 = Ayah::create([
            'surah_id' => $surah->id,
            'number' => 4,
            'text_arabic' => 'وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ',
            'text_uthmani' => 'وَلَمۡ يَكُن لَّهُۥ كُفُوًا أَحَدُۢ',
            'translation_en' => 'Nor is there to Him any equivalent',
            'translation_ar' => 'وليس له مثيل ولا نظير',
            'tafseer' => 'ليس لله تعالى كفء ولا مثيل ولا نظير، فهو الفرد الصمد الذي لا شبيه له ولا نديد',
            'audio_url' => 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6239.mp3'
        ]);

        $words4 = [
            ['position' => 1, 'text_arabic' => 'وَلَمْ', 'text_uthmani' => 'وَلَمۡ', 'transliteration' => 'Wa Lam', 'translation' => 'And not', 'meaning' => 'الواو للعطف، ولم للنفي'],
            ['position' => 2, 'text_arabic' => 'يَكُن', 'text_uthmani' => 'يَكُن', 'transliteration' => 'Yakun', 'translation' => 'Is', 'meaning' => 'فعل ماض ناقص من كان'],
            ['position' => 3, 'text_arabic' => 'لَّهُ', 'text_uthmani' => 'لَّهُۥ', 'transliteration' => 'Lahu', 'translation' => 'For Him', 'meaning' => 'جار ومجرور، اللام حرف جر والهاء ضمير'],
            ['position' => 4, 'text_arabic' => 'كُفُوًا', 'text_uthmani' => 'كُفُوًا', 'transliteration' => 'Kufuwan', 'translation' => 'Equivalent', 'meaning' => 'مثيل أو نظير أو شبيه'],
            ['position' => 5, 'text_arabic' => 'أَحَدٌ', 'text_uthmani' => 'أَحَدُۢ', 'transliteration' => 'Ahad', 'translation' => 'Anyone', 'meaning' => 'أي شخص أو شيء'],
        ];

        foreach ($words4 as $wordData) {
            Word::create(array_merge(['ayah_id' => $ayah4->id], $wordData));
        }

        $this->command->info('✅ تم إضافة سورة الإخلاص بنجاح!');
    }
}
