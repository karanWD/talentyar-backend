<?php

namespace Database\Seeders;

use App\Models\Ads;
use App\Models\City;
use App\Models\Industry;
use App\Models\JobGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing records to use as foreign keys
        $jobGroups = JobGroup::all();
        $industries = Industry::all();
        $cities = City::all();

        if ($jobGroups->isEmpty() || $industries->isEmpty() || $cities->isEmpty()) {
            $this->command->warn('Please seed JobGroups, Industries, and Cities first.');
            return;
        }

        // Sample Persian job ad titles
        $ads = [
            [
                'title' => 'برنامه‌نویس Full Stack - Laravel و Vue.js',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'کارشناس بازاریابی دیجیتال',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'طراح UI/UX خلاق و با تجربه',
                'seniority_level' => ' کارشناس ارشد',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'مدیر پروژه IT',
                'seniority_level' => 'مدیر میانی',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'حسابدار با تجربه در نرم‌افزارهای مالی',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'فروشنده حرفه‌ای محصولات نرم‌افزاری',
                'seniority_level' => 'کارمند',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'مدیر منابع انسانی',
                'seniority_level' => 'مدیر میانی',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'تکنسین فنی و تعمیرات سخت‌افزار',
                'seniority_level' => 'کارگر',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'کارشناس SEO و بهینه‌سازی سایت',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'پاره وقت',
                'remote_possibility' => true,
            ],
            [
                'title' => 'مترجم حرفه‌ای انگلیسی به فارسی',
                'seniority_level' => 'کارمند',
                'cooperation_type' => 'پاره وقت',
                'remote_possibility' => true,
            ],
            [
                'title' => 'مهندس نرم‌افزار ارشد - Backend Developer',
                'seniority_level' => ' کارشناس ارشد',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'عکاس صنعتی و تبلیغاتی',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'قراردادی/ پروژه ای',
                'remote_possibility' => false,
            ],
            [
                'title' => 'معاون مدیر عامل',
                'seniority_level' => 'معاونت',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'کارشناس شبکه و DevOps',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'مدیر ارشد فناوری اطلاعات',
                'seniority_level' => 'مدیر ارشد',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'تست کننده نرم‌افزار (QA Tester)',
                'seniority_level' => 'کارمند',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'گرافیست و طراح خلاق',
                'seniority_level' => 'کارشناس',
                'cooperation_type' => 'پاره وقت',
                'remote_possibility' => true,
            ],
            [
                'title' => 'کارشناس پشتیبانی فنی',
                'seniority_level' => 'کارمند',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => false,
            ],
            [
                'title' => 'تحلیل‌گر داده و Data Scientist',
                'seniority_level' => ' کارشناس ارشد',
                'cooperation_type' => 'تمام وقت ',
                'remote_possibility' => true,
            ],
            [
                'title' => 'کارشناس تولید محتوا',
                'seniority_level' => 'کارمند',
                'cooperation_type' => 'پاره وقت',
                'remote_possibility' => true,
            ],
        ];

        foreach ($ads as $adData) {
            Ads::create([
                'title' => $adData['title'],
                'job_group_id' => $jobGroups->random()->id,
                'industry_id' => $industries->random()->id,
                'city_id' => $cities->random()->id,
                'seniority_level' => $adData['seniority_level'],
                'cooperation_type' => $adData['cooperation_type'],
                'remote_possibility' => $adData['remote_possibility'],
            ]);
        }

        $this->command->info('Ads seeded successfully with Persian values.');
    }
}

