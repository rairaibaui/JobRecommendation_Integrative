<?php
/**
 * Quick UI Update Script for Employer Pages
 * Run: php unify-employer-ui.php.
 */
$basePath = __DIR__.'/resources/views/employer/';
$pages = ['applicants', 'analytics', 'employees', 'history'];

foreach ($pages as $page) {
    $file = $basePath.$page.'.blade.php';

    if (!file_exists($file)) {
        echo "⚠️  Skipping $page - file not found\n";
        continue;
    }

    $content = file_get_contents($file);

    // Step 1: Add unified styles after Font Awesome link
    if (strpos($content, "@include('employer.partials.unified-styles')") === false) {
        $content = str_replace(
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">'."\n\n@include('employer.partials.unified-styles')",
            $content
        );
    }

    // Step 2: Replace body tag to remove inline styles
    $content = preg_replace('/<body[^>]*>/', '<body>', $content, 1);

    // Step 3: Find and remove old inline <style> tags (keep Chart.js script if present)
    // Remove everything from <style> to </style> that contains body styles
    if (preg_match('/<style>\s*\/\*.*?\*\/\s*\*\s*\{[^<]+body\s*\{[^<]+<\/style>/s', $content)) {
        $content = preg_replace('/<style>.*?<\/style>/s', '', $content, 1);
    }

    // Step 4: Replace old navbar
    $content = preg_replace(
        '/<div class="top-navbar">.*?<\/div>/s',
        "@include('employer.partials.navbar')",
        $content,
        1
    );

    // Step 5: Replace old sidebar (more aggressive pattern)
    $content = preg_replace(
        '/<div class="sidebar">.*?<\/div>\s*(?:@include\(\'partials\.logout-confirm\'\))?/s',
        '',
        $content,
        1
    );

    // Step 6: Replace main container opening
    $content = str_replace(
        '<div class="main">',
        "<div class=\"main-content\">\n  @include('employer.partials.sidebar')\n  \n  <div class=\"content-area\">",
        $content
    );

    // Step 7: Add page header if not exists
    if (strpos($content, 'page-header') === false && strpos($content, '<div class="content-area">') !== false) {
        $title = ucfirst($page);
        $icon = [
            'applicants' => 'users',
            'analytics' => 'chart-bar',
            'employees' => 'user-check',
            'history' => 'history',
        ][$page] ?? 'file';

        $pageHeader = "\n    <div class=\"page-header\">\n".
                     "      <h1 class=\"page-title\"><i class=\"fas fa-$icon\"></i> $title</h1>\n".
                     "    </div>\n\n    ";

        $content = str_replace(
            '<div class="content-area">',
            '<div class="content-area">'.$pageHeader,
            $content
        );
    }

    // Step 8: Close content-area and main-content properly
    // Find the last </div> before </body> and add proper closing
    $content = preg_replace(
        '/(<\/div>\s*<\/body>)/',
        "  </div><!-- content-area -->\n</div><!-- main-content -->\n\n@include('partials.logout-confirm')\n\n</body>",
        $content,
        1
    );

    file_put_contents($file, $content);
    echo "✅ Updated: $page.blade.php\n";
}

echo "\n🎉 All pages updated! Run: php artisan view:clear\n";
