#!/usr/bin/env php
<?php
/**
 * UI Unification Script
 * Updates all employer pages to use the unified design system
 */

$pages = [
    'applicants',
    'analytics',
    'employees',
    'history',
];

$basePath = __DIR__ . '/resources/views/employer/';

foreach ($pages as $page) {
    $filePath = $basePath . $page . '.blade.php';
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $filePath\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Replace the head section to include unified styles
    $content = preg_replace(
        '/<link rel="stylesheet"[^>]*font-awesome[^>]*>\s*<style>.*?<\/style>/s',
        '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">' . "\n\n" . 
        '@include(\'employer.partials.unified-styles\')' . "\n",
        $content
    );
    
    // Replace navbar
    $content = preg_replace(
        '/<div class="top-navbar">.*?<\/div>/s',
        '@include(\'employer.partials.navbar\')',
        $content
    );
    
    // Replace sidebar
    $content = preg_replace(
        '/<div class="sidebar">.*?<\/div>\s*@include\(\'partials\.logout-confirm\'\)/s',
        '',
        $content
    );
    
    // Update main container
    $content = str_replace('<div class="main">', '<div class="main-content">' . "\n    @include('employer.partials.sidebar')\n    \n    <div class="content-area\">', $content);
    $content = str_replace('</div><!-- main -->', '    </div><!-- content-area -->' . "\n  </div><!-- main-content -->", $content);
    
    file_put_contents($filePath, $content);
    
    echo "✅ Updated: $page.blade.php\n";
}

echo "\n🎉 All pages updated!\n";
echo "Run: php artisan view:clear\n";
