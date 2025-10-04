<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();
        
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'SuperAdmin User',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Create categories
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Latest technology trends and insights',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Web development tutorials and tips',
                'color' => '#10b981',
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'UI/UX design and creative inspiration',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Laravel framework tutorials and best practices',
                'color' => '#ef4444',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create tags
        $tags = [
            ['name' => 'PHP', 'slug' => 'php', 'color' => '#8b5cf6'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => '#f59e0b'],
            ['name' => 'React', 'slug' => 'react', 'color' => '#06b6d4'],
            ['name' => 'Vue.js', 'slug' => 'vue-js', 'color' => '#10b981'],
            ['name' => 'CSS', 'slug' => 'css', 'color' => '#3b82f6'],
            ['name' => 'HTML', 'slug' => 'html', 'color' => '#f97316'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'color' => '#06b6d4'],
            ['name' => 'Filament', 'slug' => 'filament', 'color' => '#f59e0b'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        // Create sample posts
        $posts = [
            [
                'title' => 'Getting Started with Laravel Filament',
                'slug' => 'getting-started-with-laravel-filament',
                'excerpt' => 'Learn how to build powerful admin panels with Laravel Filament in this comprehensive guide.',
                'content' => '<h2>Introduction to Laravel Filament</h2><p>Laravel Filament is a collection of beautiful full-stack components for Laravel. You can use this package to build a feature-complete admin panel for your Laravel application in minutes.</p><h3>Key Features</h3><ul><li>Beautiful, responsive interface</li><li>Rich form components</li><li>Powerful table builder</li><li>Customizable dashboard</li></ul><p>In this tutorial, we\'ll explore how to get started with Filament and build your first admin panel.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'category' => 'laravel',
                'tags' => ['php', 'filament'],
            ],
            [
                'title' => 'Modern CSS Techniques for Better Web Design',
                'slug' => 'modern-css-techniques-for-better-web-design',
                'excerpt' => 'Discover the latest CSS techniques that will help you create stunning, responsive web designs.',
                'content' => '<h2>CSS Grid and Flexbox</h2><p>Modern CSS offers powerful layout tools like CSS Grid and Flexbox that make creating complex layouts easier than ever.</p><h3>CSS Grid Benefits</h3><ul><li>Two-dimensional layouts</li><li>Better browser support</li><li>Responsive by design</li></ul><p>Let\'s explore how to use these techniques effectively in your projects.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_featured' => false,
                'category' => 'design',
                'tags' => ['css', 'html'],
            ],
            [
                'title' => 'JavaScript ES2024: New Features You Should Know',
                'slug' => 'javascript-es2024-new-features-you-should-know',
                'excerpt' => 'Explore the latest JavaScript features introduced in ES2024 and how they can improve your code.',
                'content' => '<h2>What\'s New in ES2024</h2><p>JavaScript continues to evolve with new features that make development more efficient and enjoyable.</p><h3>Key Updates</h3><ul><li>New array methods</li><li>Improved async/await syntax</li><li>Better error handling</li></ul><p>These features will help you write cleaner, more maintainable JavaScript code.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'is_featured' => true,
                'category' => 'web-development',
                'tags' => ['javascript'],
            ],
            [
                'title' => 'Building Responsive UIs with Tailwind CSS',
                'slug' => 'building-responsive-uis-with-tailwind-css',
                'excerpt' => 'Learn how to create beautiful, responsive user interfaces using Tailwind CSS utility classes.',
                'content' => '<h2>Why Tailwind CSS?</h2><p>Tailwind CSS is a utility-first CSS framework that provides low-level utility classes to build custom designs without leaving your HTML.</p><h3>Advantages</h3><ul><li>Rapid development</li><li>Consistent design system</li><li>Small bundle size</li><li>Easy customization</li></ul><p>Let\'s dive into building responsive components with Tailwind CSS.</p>',
                'status' => 'published',
                'published_at' => now()->subHours(12),
                'is_featured' => false,
                'category' => 'design',
                'tags' => ['tailwind-css', 'css'],
            ],
            [
                'title' => 'The Future of Web Development',
                'slug' => 'the-future-of-web-development',
                'excerpt' => 'A look into emerging technologies and trends that will shape the future of web development.',
                'content' => '<h2>Emerging Technologies</h2><p>The web development landscape is constantly evolving with new technologies and frameworks.</p><h3>Trends to Watch</h3><ul><li>WebAssembly adoption</li><li>Edge computing</li><li>Progressive Web Apps</li><li>AI integration</li></ul><p>Stay ahead of the curve by understanding these emerging trends.</p>',
                'status' => 'draft',
                'published_at' => null,
                'is_featured' => false,
                'category' => 'technology',
                'tags' => ['javascript', 'php'],
            ],
        ];

        foreach ($posts as $postData) {
            $category = Category::where('slug', $postData['category'])->first();
            $tagSlugs = $postData['tags'];
            
            unset($postData['category'], $postData['tags']);
            
            $post = Post::firstOrCreate(
                ['slug' => $postData['slug']],
                array_merge($postData, [
                    'user_id' => $adminUser->id,
                    'category_id' => $category->id,
                ])
            );
            
            // Attach tags
            $tags = Tag::whereIn('slug', $tagSlugs)->get();
            $post->tags()->sync($tags->pluck('id'));
        }
    }
}
