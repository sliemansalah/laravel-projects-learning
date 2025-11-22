<?php
// database/seeders/QuranSeeder.php

namespace Database\Seeders;

use App\Models\Surah;
use App\Models\Ayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuranSeeder extends Seeder
{
    public function run(): void
    {
        // مثال: بيانات السور الأولى
        // يمكنك تحميل البيانات من ملف JSON أو API خارجي

         $surahs = [
            ['name' => 'الفاتحة', 'name_en' => 'Al-Fatiha', 'number' => 1, 'ayah_count' => 7, 'revelation_place' => 'مكي', 'juz_number' => 1],
            ['name' => 'البقرة', 'name_en' => 'Al-Baqarah', 'number' => 2, 'ayah_count' => 286, 'revelation_place' => 'مدني', 'juz_number' => 1],
            ['name' => 'آل عمران', 'name_en' => 'Al-Imran', 'number' => 3, 'ayah_count' => 200, 'revelation_place' => 'مدني', 'juz_number' => 3],
            ['name' => 'النساء', 'name_en' => 'An-Nisa', 'number' => 4, 'ayah_count' => 176, 'revelation_place' => 'مدني', 'juz_number' => 4],
            ['name' => 'المائدة', 'name_en' => 'Al-Maidah', 'number' => 5, 'ayah_count' => 120, 'revelation_place' => 'مدني', 'juz_number' => 6],
            ['name' => 'الأنعام', 'name_en' => 'Al-Anam', 'number' => 6, 'ayah_count' => 165, 'revelation_place' => 'مكي', 'juz_number' => 7],
            ['name' => 'الأعراف', 'name_en' => 'Al-Araf', 'number' => 7, 'ayah_count' => 206, 'revelation_place' => 'مكي', 'juz_number' => 8],
            ['name' => 'الأنفال', 'name_en' => 'Al-Anfal', 'number' => 8, 'ayah_count' => 75, 'revelation_place' => 'مدني', 'juz_number' => 9],
            ['name' => 'التوبة', 'name_en' => 'At-Tawbah', 'number' => 9, 'ayah_count' => 129, 'revelation_place' => 'مدني', 'juz_number' => 10],
            ['name' => 'يونس', 'name_en' => 'Yunus', 'number' => 10, 'ayah_count' => 109, 'revelation_place' => 'مكي', 'juz_number' => 11],
            ['name' => 'هود', 'name_en' => 'Hud', 'number' => 11, 'ayah_count' => 123, 'revelation_place' => 'مكي', 'juz_number' => 11],
            ['name' => 'يوسف', 'name_en' => 'Yusuf', 'number' => 12, 'ayah_count' => 111, 'revelation_place' => 'مكي', 'juz_number' => 12],
            ['name' => 'الرعد', 'name_en' => 'Ar-Rad', 'number' => 13, 'ayah_count' => 43, 'revelation_place' => 'مدني', 'juz_number' => 13],
            ['name' => 'إبراهيم', 'name_en' => 'Ibrahim', 'number' => 14, 'ayah_count' => 52, 'revelation_place' => 'مكي', 'juz_number' => 13],
            ['name' => 'الحجر', 'name_en' => 'Al-Hijr', 'number' => 15, 'ayah_count' => 99, 'revelation_place' => 'مكي', 'juz_number' => 14],
            ['name' => 'النحل', 'name_en' => 'An-Nahl', 'number' => 16, 'ayah_count' => 128, 'revelation_place' => 'مكي', 'juz_number' => 14],
            ['name' => 'الإسراء', 'name_en' => 'Al-Isra', 'number' => 17, 'ayah_count' => 111, 'revelation_place' => 'مكي', 'juz_number' => 15],
            ['name' => 'الكهف', 'name_en' => 'Al-Kahf', 'number' => 18, 'ayah_count' => 110, 'revelation_place' => 'مكي', 'juz_number' => 15],
            ['name' => 'مريم', 'name_en' => 'Maryam', 'number' => 19, 'ayah_count' => 98, 'revelation_place' => 'مكي', 'juz_number' => 16],
            ['name' => 'طه', 'name_en' => 'Taha', 'number' => 20, 'ayah_count' => 135, 'revelation_place' => 'مكي', 'juz_number' => 16],
            ['name' => 'الأنبياء', 'name_en' => 'Al-Anbiya', 'number' => 21, 'ayah_count' => 112, 'revelation_place' => 'مكي', 'juz_number' => 17],
            ['name' => 'الحج', 'name_en' => 'Al-Hajj', 'number' => 22, 'ayah_count' => 78, 'revelation_place' => 'مدني', 'juz_number' => 17],
            ['name' => 'المؤمنون', 'name_en' => 'Al-Muminun', 'number' => 23, 'ayah_count' => 118, 'revelation_place' => 'مكي', 'juz_number' => 18],
            ['name' => 'النور', 'name_en' => 'An-Nur', 'number' => 24, 'ayah_count' => 64, 'revelation_place' => 'مدني', 'juz_number' => 18],
            ['name' => 'الفرقان', 'name_en' => 'Al-Furqan', 'number' => 25, 'ayah_count' => 77, 'revelation_place' => 'مكي', 'juz_number' => 18],
            ['name' => 'الشعراء', 'name_en' => 'Ash-Shuara', 'number' => 26, 'ayah_count' => 227, 'revelation_place' => 'مكي', 'juz_number' => 19],
            ['name' => 'النمل', 'name_en' => 'An-Naml', 'number' => 27, 'ayah_count' => 93, 'revelation_place' => 'مكي', 'juz_number' => 19],
            ['name' => 'القصص', 'name_en' => 'Al-Qasas', 'number' => 28, 'ayah_count' => 88, 'revelation_place' => 'مكي', 'juz_number' => 20],
            ['name' => 'العنكبوت', 'name_en' => 'Al-Ankabut', 'number' => 29, 'ayah_count' => 69, 'revelation_place' => 'مكي', 'juz_number' => 20],
            ['name' => 'الروم', 'name_en' => 'Ar-Rum', 'number' => 30, 'ayah_count' => 60, 'revelation_place' => 'مكي', 'juz_number' => 21],
            ['name' => 'لقمان', 'name_en' => 'Luqman', 'number' => 31, 'ayah_count' => 34, 'revelation_place' => 'مكي', 'juz_number' => 21],
            ['name' => 'السجدة', 'name_en' => 'As-Sajdah', 'number' => 32, 'ayah_count' => 30, 'revelation_place' => 'مكي', 'juz_number' => 21],
            ['name' => 'الأحزاب', 'name_en' => 'Al-Ahzab', 'number' => 33, 'ayah_count' => 73, 'revelation_place' => 'مدني', 'juz_number' => 21],
            ['name' => 'سبأ', 'name_en' => 'Saba', 'number' => 34, 'ayah_count' => 54, 'revelation_place' => 'مكي', 'juz_number' => 22],
            ['name' => 'فاطر', 'name_en' => 'Fatir', 'number' => 35, 'ayah_count' => 45, 'revelation_place' => 'مكي', 'juz_number' => 22],
            ['name' => 'يس', 'name_en' => 'Ya-Sin', 'number' => 36, 'ayah_count' => 83, 'revelation_place' => 'مكي', 'juz_number' => 22],
            ['name' => 'الصافات', 'name_en' => 'As-Saffat', 'number' => 37, 'ayah_count' => 182, 'revelation_place' => 'مكي', 'juz_number' => 23],
            ['name' => 'ص', 'name_en' => 'Sad', 'number' => 38, 'ayah_count' => 88, 'revelation_place' => 'مكي', 'juz_number' => 23],
            ['name' => 'الزمر', 'name_en' => 'Az-Zumar', 'number' => 39, 'ayah_count' => 75, 'revelation_place' => 'مكي', 'juz_number' => 23],
            ['name' => 'غافر', 'name_en' => 'Ghafir', 'number' => 40, 'ayah_count' => 85, 'revelation_place' => 'مكي', 'juz_number' => 24],
            ['name' => 'فصلت', 'name_en' => 'Fussilat', 'number' => 41, 'ayah_count' => 54, 'revelation_place' => 'مكي', 'juz_number' => 24],
            ['name' => 'الشورى', 'name_en' => 'Ash-Shura', 'number' => 42, 'ayah_count' => 53, 'revelation_place' => 'مكي', 'juz_number' => 25],
            ['name' => 'الزخرف', 'name_en' => 'Az-Zukhruf', 'number' => 43, 'ayah_count' => 89, 'revelation_place' => 'مكي', 'juz_number' => 25],
            ['name' => 'الدخان', 'name_en' => 'Ad-Dukhan', 'number' => 44, 'ayah_count' => 59, 'revelation_place' => 'مكي', 'juz_number' => 25],
            ['name' => 'الجاثية', 'name_en' => 'Al-Jathiyah', 'number' => 45, 'ayah_count' => 37, 'revelation_place' => 'مكي', 'juz_number' => 25],
            ['name' => 'الأحقاف', 'name_en' => 'Al-Ahqaf', 'number' => 46, 'ayah_count' => 35, 'revelation_place' => 'مكي', 'juz_number' => 26],
            ['name' => 'محمد', 'name_en' => 'Muhammad', 'number' => 47, 'ayah_count' => 38, 'revelation_place' => 'مدني', 'juz_number' => 26],
            ['name' => 'الفتح', 'name_en' => 'Al-Fath', 'number' => 48, 'ayah_count' => 29, 'revelation_place' => 'مدني', 'juz_number' => 26],
            ['name' => 'الحجرات', 'name_en' => 'Al-Hujurat', 'number' => 49, 'ayah_count' => 18, 'revelation_place' => 'مدني', 'juz_number' => 26],
            ['name' => 'ق', 'name_en' => 'Qaf', 'number' => 50, 'ayah_count' => 45, 'revelation_place' => 'مكي', 'juz_number' => 26],
            ['name' => 'الذاريات', 'name_en' => 'Ad-Dariyat', 'number' => 51, 'ayah_count' => 60, 'revelation_place' => 'مكي', 'juz_number' => 27],
            ['name' => 'الطور', 'name_en' => 'At-Tur', 'number' => 52, 'ayah_count' => 49, 'revelation_place' => 'مكي', 'juz_number' => 27],
            ['name' => 'النجم', 'name_en' => 'An-Najm', 'number' => 53, 'ayah_count' => 62, 'revelation_place' => 'مكي', 'juz_number' => 27],
            ['name' => 'القمر', 'name_en' => 'Al-Qamar', 'number' => 54, 'ayah_count' => 55, 'revelation_place' => 'مكي', 'juz_number' => 27],
            ['name' => 'الرحمن', 'name_en' => 'Ar-Rahman', 'number' => 55, 'ayah_count' => 78, 'revelation_place' => 'مدني', 'juz_number' => 27],
            ['name' => 'الواقعة', 'name_en' => 'Al-Waqiah', 'number' => 56, 'ayah_count' => 96, 'revelation_place' => 'مكي', 'juz_number' => 27],
            ['name' => 'الحديد', 'name_en' => 'Al-Hadid', 'number' => 57, 'ayah_count' => 29, 'revelation_place' => 'مدني', 'juz_number' => 27],
            ['name' => 'المجادلة', 'name_en' => 'Al-Mujadalah', 'number' => 58, 'ayah_count' => 22, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الحشر', 'name_en' => 'Al-Hashr', 'number' => 59, 'ayah_count' => 24, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الممتحنة', 'name_en' => 'Al-Mumtahanah', 'number' => 60, 'ayah_count' => 13, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الصف', 'name_en' => 'As-Saff', 'number' => 61, 'ayah_count' => 14, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الجمعة', 'name_en' => 'Al-Jumah', 'number' => 62, 'ayah_count' => 11, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'المنافقون', 'name_en' => 'Al-Munafiqun', 'number' => 63, 'ayah_count' => 11, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'التغابن', 'name_en' => 'At-Taghabun', 'number' => 64, 'ayah_count' => 18, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الطلاق', 'name_en' => 'At-Talaq', 'number' => 65, 'ayah_count' => 12, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'التحريم', 'name_en' => 'At-Tahrim', 'number' => 66, 'ayah_count' => 12, 'revelation_place' => 'مدني', 'juz_number' => 28],
            ['name' => 'الملك', 'name_en' => 'Al-Mulk', 'number' => 67, 'ayah_count' => 30, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'القلم', 'name_en' => 'Al-Qalam', 'number' => 68, 'ayah_count' => 52, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'الحاقة', 'name_en' => 'Al-Haqqah', 'number' => 69, 'ayah_count' => 52, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'المعارج', 'name_en' => 'Al-Maarij', 'number' => 70, 'ayah_count' => 44, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'نوح', 'name_en' => 'Nuh', 'number' => 71, 'ayah_count' => 28, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'الجن', 'name_en' => 'Al-Jinn', 'number' => 72, 'ayah_count' => 28, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'المزمل', 'name_en' => 'Al-Muzzammil', 'number' => 73, 'ayah_count' => 20, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'المدثر', 'name_en' => 'Al-Muddaththir', 'number' => 74, 'ayah_count' => 56, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'القيامة', 'name_en' => 'Al-Qiyamah', 'number' => 75, 'ayah_count' => 40, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'الإنسان', 'name_en' => 'Al-Insan', 'number' => 76, 'ayah_count' => 31, 'revelation_place' => 'مدني', 'juz_number' => 29],
            ['name' => 'المرسلات', 'name_en' => 'Al-Mursalat', 'number' => 77, 'ayah_count' => 50, 'revelation_place' => 'مكي', 'juz_number' => 29],
            ['name' => 'النبأ', 'name_en' => 'An-Naba', 'number' => 78, 'ayah_count' => 40, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'النازعات', 'name_en' => 'An-Naziat', 'number' => 79, 'ayah_count' => 46, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'عبس', 'name_en' => 'Abasa', 'number' => 80, 'ayah_count' => 42, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'التكوير', 'name_en' => 'At-Takwir', 'number' => 81, 'ayah_count' => 29, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الانفطار', 'name_en' => 'Al-Infitar', 'number' => 82, 'ayah_count' => 19, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'المطففين', 'name_en' => 'Al-Mutaffifin', 'number' => 83, 'ayah_count' => 36, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الانشقاق', 'name_en' => 'Al-Inshiqaq', 'number' => 84, 'ayah_count' => 25, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'البروج', 'name_en' => 'Al-Buruj', 'number' => 85, 'ayah_count' => 22, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الطارق', 'name_en' => 'At-Tariq', 'number' => 86, 'ayah_count' => 17, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الأعلى', 'name_en' => 'Al-Ala', 'number' => 87, 'ayah_count' => 19, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الغاشية', 'name_en' => 'Al-Ghashiyah', 'number' => 88, 'ayah_count' => 26, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الفجر', 'name_en' => 'Al-Fajr', 'number' => 89, 'ayah_count' => 30, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'البلد', 'name_en' => 'Al-Balad', 'number' => 90, 'ayah_count' => 20, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الشمس', 'name_en' => 'Ash-Shams', 'number' => 91, 'ayah_count' => 15, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الليل', 'name_en' => 'Al-Lail', 'number' => 92, 'ayah_count' => 21, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الضحى', 'name_en' => 'Ad-Duha', 'number' => 93, 'ayah_count' => 11, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الشرح', 'name_en' => 'Ash-Sharh', 'number' => 94, 'ayah_count' => 8, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'التين', 'name_en' => 'At-Tin', 'number' => 95, 'ayah_count' => 8, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'العلق', 'name_en' => 'Al-Alaq', 'number' => 96, 'ayah_count' => 19, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'القدر', 'name_en' => 'Al-Qadr', 'number' => 97, 'ayah_count' => 5, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'البينة', 'name_en' => 'Al-Bayyinah', 'number' => 98, 'ayah_count' => 8, 'revelation_place' => 'مدني', 'juz_number' => 30],
            ['name' => 'الزلزلة', 'name_en' => 'Az-Zalzalah', 'number' => 99, 'ayah_count' => 8, 'revelation_place' => 'مدني', 'juz_number' => 30],
            ['name' => 'العاديات', 'name_en' => 'Al-Adiyat', 'number' => 100, 'ayah_count' => 11, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'القارعة', 'name_en' => 'Al-Qariah', 'number' => 101, 'ayah_count' => 11, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'التكاثر', 'name_en' => 'At-Takathur', 'number' => 102, 'ayah_count' => 8, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'العصر', 'name_en' => 'Al-Asr', 'number' => 103, 'ayah_count' => 3, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الهمزة', 'name_en' => 'Al-Humazah', 'number' => 104, 'ayah_count' => 9, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الفيل', 'name_en' => 'Al-Fil', 'number' => 105, 'ayah_count' => 5, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'قريش', 'name_en' => 'Quraysh', 'number' => 106, 'ayah_count' => 4, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الماعون', 'name_en' => 'Al-Maun', 'number' => 107, 'ayah_count' => 7, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الكوثر', 'name_en' => 'Al-Kawthar', 'number' => 108, 'ayah_count' => 3, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الكافرون', 'name_en' => 'Al-Kafirun', 'number' => 109, 'ayah_count' => 6, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'النصر', 'name_en' => 'An-Nasr', 'number' => 110, 'ayah_count' => 3, 'revelation_place' => 'مدني', 'juz_number' => 30],
            ['name' => 'المسد', 'name_en' => 'Al-Masad', 'number' => 111, 'ayah_count' => 5, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الإخلاص', 'name_en' => 'Al-Ikhlas', 'number' => 112, 'ayah_count' => 4, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الفلق', 'name_en' => 'Al-Falaq', 'number' => 113, 'ayah_count' => 5, 'revelation_place' => 'مكي', 'juz_number' => 30],
            ['name' => 'الناس', 'name_en' => 'An-Nas', 'number' => 114, 'ayah_count' => 6, 'revelation_place' => 'مكي', 'juz_number' => 30],
         ];
        foreach ($surahs as $surahData) {
            $surah = Surah::create($surahData);

            // إضافة الآيات لكل سورة
            $this->seedAyahs($surah);

            $this->command->info("تم إضافة سورة {$surah->name} مع {$surah->ayah_count} آية");
        }
    }

    // دالة مساعدة لإضافة الآيات
    private function seedAyahs(Surah $surah): void
    {
        try {
            // جلب البيانات من API
            // نستخدم api.alquran.cloud للحصول على النص العربي
            $response = Http::timeout(30)->get("https://api.alquran.cloud/v1/surah/{$surah->number}");

            if (!$response->successful()) {
                $this->command->warn("فشل في جلب بيانات سورة {$surah->name}");
                return;
            }

            $data = $response->json();

            if (!isset($data['data']['ayahs'])) {
                $this->command->warn("لا توجد آيات في البيانات المستلمة لسورة {$surah->name}");
                return;
            }

            $ayahs = $data['data']['ayahs'];

            foreach ($ayahs as $ayahData) {
                Ayah::create([
                    'surah_id' => $surah->id,
                    'number' => $ayahData['numberInSurah'],
                    'text' => $ayahData['text'],
                    'transliteration' => null, // يمكن إضافته من مصدر آخر
                    'translation' => null, // يمكن إضافته من مصدر آخر
                    'page_number' => $ayahData['page'] ?? null,
                    'line_number' => null,
                    'juz_number' => $ayahData['juz'] ?? null,
                    'hizb' => $ayahData['hizbQuarter'] ?? null,
                    'quarter' => null,
                ]);
            }

            // إضافة تأخير صغير لتجنب تحميل API بشكل زائد
            usleep(200000); // 200ms delay

        } catch (\Exception $e) {
            $this->command->error("خطأ في جلب بيانات سورة {$surah->name}: " . $e->getMessage());
            Log::error("Error seeding ayahs for surah {$surah->name}", [
                'error' => $e->getMessage(),
                'surah_id' => $surah->id
            ]);
        }
    }
}
