<?php
/**
 * Comprehensive UI Fix for All Employer Pages
 * This script properly fixes the broken HTML structure.
 */
$basePath = __DIR__.'/resources/views/employer/';
$pages = ['applicants', 'analytics', 'employees', 'history'];

echo "🔧 Starting comprehensive UI fix...\n\n";

foreach ($pages as $page) {
    $file = $basePath.$page.'.blade.php';

    if (!file_exists($file)) {
        echo "⚠️  Skipping $page - file not found\n";
        continue;
    }

    $content = file_get_contents($file);

    // Remove everything after </head> and before the main content
    // Find where </head> is
    $headPos = strpos($content, '</head>');
    if ($headPos === false) {
        echo "⚠️  Could not find </head> in $page\n";
        continue;
    }

    // Find where the actual content starts (look for <div class="main-content"> or content-area)
    $mainContentPos = strpos($content, '<div class="main-content">');
    if ($mainContentPos === false) {
        $mainContentPos = strpos($content, '<div class="content-area">');
    }

    if ($mainContentPos === false) {
        echo "⚠️  Could not find main content in $page\n";
        continue;
    }

    // Extract the head section
    $headSection = substr($content, 0, $headPos + 7); // Include </head>

    // Extract the content section (from main-content to end)
    $contentSection = substr($content, $mainContentPos);

    // Find the </body> tag
    $bodyEndPos = strrpos($contentSection, '</body>');
    if ($bodyEndPos !== false) {
        $contentSection = substr($contentSection, 0, $bodyEndPos);
    }

    // Clean up the content section - remove any orphaned sidebar fragments
    $contentSection = preg_replace('/<div class="company-name"[^>]*>.*?<\/div>/s', '', $contentSection);
    $contentSection = preg_replace('/<div class="company-badge"[^>]*>.*?<\/div>/s', '', $contentSection);
    $contentSection = preg_replace('/<script>\s*function showEmpProfilePictureModal\(\).*?<\/script>/s', '', $contentSection);
    $contentSection = preg_replace('/<a href=".*?" class="sidebar-btn[^"]*">.*?<\/a>/s', '', $contentSection, 10);

    // Build the correct structure
    $newContent = $headSection."\n";
    $newContent .= "<body>\n\n";
    $newContent .= "@include('employer.partials.navbar')\n\n";
    $newContent .= "<div class=\"main-content\">\n";
    $newContent .= "  @include('employer.partials.sidebar')\n  \n";

    // If content section doesn't start with <div class="content-area">, add it
    if (strpos($contentSection, '<div class="content-area">') !== 0) {
        $newContent .= "  <div class=\"content-area\">\n";

        // Add page header
        $title = ucfirst($page);
        $icons = [
            'applicants' => 'users',
            'analytics' => 'chart-bar',
            'employees' => 'user-check',
            'history' => 'history',
        ];
        $icon = $icons[$page] ?? 'file';

        $newContent .= "    <div class=\"page-header\">\n";
        $newContent .= "      <h1 class=\"page-title\"><i class=\"fas fa-$icon\"></i> $title</h1>\n";
        $newContent .= "    </div>\n\n";

        $newContent .= $contentSection;
        $newContent .= "  </div><!-- content-area -->\n";
    } else {
        $newContent .= '  '.$contentSection;
        if (strpos($contentSection, '</div><!-- content-area -->') === false) {
            $newContent .= "\n  </div><!-- content-area -->\n";
        }
    }

    $newContent .= "</div><!-- main-content -->\n\n";
    $newContent .= "@include('partials.logout-confirm')\n\n";
    $newContent .= "</body>\n</html>\n";

    // Final cleanup - remove duplicate closing divs and ensure proper structure
    $newContent = preg_replace('/\n{3,}/', "\n\n", $newContent);

    file_put_contents($file, $newContent);
    echo "✅ Fixed: $page.blade.php\n";
}

echo "\n🎉 All pages fixed! Run: php artisan view:clear\n";
