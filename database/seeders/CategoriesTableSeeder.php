<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Design', 'slug' => 'design'],
            ['name' => 'Development', 'slug' => 'development'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Management', 'slug' => 'management'],
            ['name' => 'Finance', 'slug' => 'finance'],
            ['name' => 'HR', 'slug' => 'hr'],
            ['name' => 'Legal', 'slug' => 'legal'],
            ['name' => 'IT', 'slug' => 'it'],
            ['name' => 'Other', 'slug' => 'other'],
            ['name' => 'Video & Audio', 'slug' => 'video-audio'],
            ['name' => 'Programming & Tech', 'slug' => 'programming-tech'],
            ['name' => 'Business', 'slug' => 'business'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
            ['name' => 'Accounting & Consulting', 'slug' => 'accounting-consulting'],
            ['name' => 'Admin Support', 'slug' => 'admin-support'],
            ['name' => 'Customer Service', 'slug' => 'customer-service'],
            ['name' => 'Data Science & Analytics', 'slug' => 'data-science-analytics'],
            ['name' => 'Design & Creative', 'slug' => 'design-creative'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Human Resources', 'slug' => 'human-resources'],
            ['name' => 'Marketing & Sales', 'slug' => 'marketing-sales'],
            ['name' => 'Project Management', 'slug' => 'project-management'],
            ['name' => 'Real Estate', 'slug' => 'real-estate'],
            ['name' => 'Social Media', 'slug' => 'social-media'],
            ['name' => 'Software Development', 'slug' => 'software-development'],
            ['name' => 'Writing & Translation', 'slug' => 'writing-translation'],
            ['name' => 'Systems & Networking', 'slug' => 'systems-networking'],
            ['name' => 'Translation', 'slug' => 'translation'],
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Engineering & Architecture', 'slug' => 'engineering-architecture'],
            ['name' => 'IT & Network', 'slug' => 'it-network']
        ];
        
        \App\Models\Category::insert($categories);
    }
}
