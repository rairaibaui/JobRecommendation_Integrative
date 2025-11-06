<?php
/**
 * Complete UI Fix - All Employer Pages
 * Handles ALL employer pages with old inline styles.
 */
$basePath = __DIR__.'/resources/views/employer/';

// All pages that need fixing
$pages = [
    'applicants',
    'analytics',
    'employees',
    'history',
    'job-create',
    'job-edit',
    'audit-logs',
    'applicant-profile',
];

echo "🔧 Fixing ALL employer pages...\n\n";

foreach ($pages as $page) {
    $file = $basePath.$page.'.blade.php';

    if (!file_exists($file)) {
        echo "⚠️  Skipping $page - file not found\n";
        continue;
    }

    $content = file_get_contents($file);
    $original = $content;

    // Step 1: Check if already has unified-styles
    $hasUnifiedStyles = strpos($content, "include('employer.partials.unified-styles')") !== false;

    if (!$hasUnifiedStyles) {
        // Add unified styles after Font Awesome
        $content = str_replace(
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">'."\n\n@include('employer.partials.unified-styles')",
            $content
        );
    }

    // Step 2: Remove all inline <style> blocks
    $content = preg_replace('/<style>.*?<\/style>/s', '', $content);

    // Step 3: Fix the body structure
    // Find </head> position
    $headPos = strpos($content, '</head>');
    if ($headPos === false) {
        continue;
    }

    // Find </html> position
    $htmlEndPos = strrpos($content, '</html>');
    if ($htmlEndPos === false) {
        $htmlEndPos = strlen($content);
    }

    // Extract head
    $head = substr($content, 0, $headPos + 7);

    // Extract body content (everything between </head> and </html>)
    $bodyContent = substr($content, $headPos + 7, $htmlEndPos - ($headPos + 7));

    // Clean up body content - remove old structure tags
    $bodyContent = preg_replace('/<body[^>]*>/', '', $bodyContent);
    $bodyContent = preg_replace('/<\/body>/', '', $bodyContent);
    $bodyContent = preg_replace('/<div class="top-navbar">.*?<\/div>/s', '', $bodyContent);
    $bodyContent = preg_replace('/<div class="sidebar">.*?@include\(\'partials\.logout-confirm\'\)/s', '', $bodyContent);
    $bodyContent = preg_replace('/<div class="main"[^>]*>/', '', $bodyContent);

    // Remove orphaned closing divs at the end
    $bodyContent = preg_replace('/\s*<\/div>\s*(<\/div>\s*)*$/', '', $bodyContent);

    // Remove any @include('partials.logout-confirm') from content
    $bodyContent = str_replace("@include('partials.logout-confirm')", '', $bodyContent);

    // Determine page title and icon
    $titles = [
        'applicants' => 'Applicants',
        'analytics' => 'Analytics',
        'employees' => 'Employees',
        'history' => 'History',
        'job-create' => 'Post New Job',
        'job-edit' => 'Edit Job',
        'audit-logs' => 'Audit Logs',
        'applicant-profile' => 'Applicant Profile',
    ];

    $icons = [
        'applicants' => 'users',
        'analytics' => 'chart-bar',
        'employees' => 'user-check',
        'history' => 'history',
        'job-create' => 'plus-circle',
        'job-edit' => 'edit',
        'audit-logs' => 'clipboard-list',
        'applicant-profile' => 'user',
    ];

    $title = $titles[$page] ?? ucfirst($page);
    $icon = $icons[$page] ?? 'file';

    // Build new structure
    $newBody = "<body>\n\n";
    $newBody .= "@include('employer.partials.navbar')\n\n";
    $newBody .= "<div class=\"main-content\">\n";
    $newBody .= "  @include('employer.partials.sidebar')\n  \n";
    $newBody .= "  <div class=\"content-area\">\n";

    // Add page header if content doesn't already have one
    if (strpos($bodyContent, 'page-header') === false) {
        $newBody .= "    <div class=\"page-header\">\n";
        $newBody .= "      <h1 class=\"page-title\"><i class=\"fas fa-$icon\"></i> $title</h1>\n";
        $newBody .= "    </div>\n\n";
    }

    // Add cleaned body content
    $newBody .= trim($bodyContent)."\n";
    $newBody .= "  </div><!-- content-area -->\n";
    $newBody .= "</div><!-- main-content -->\n\n";
    $newBody .= "@include('partials.logout-confirm')\n\n";
    $newBody .= "</body>\n";

    // Combine
    $newContent = $head."\n".$newBody."</html>\n";

    // Clean up extra whitespace
    $newContent = preg_replace('/\n{3,}/', "\n\n", $newContent);

    // Only write if content changed
    if ($newContent !== $original) {
        file_put_contents($file, $newContent);
        echo "✅ Fixed: $page.blade.php\n";
    } else {
        echo "⏭️  Skipped: $page.blade.php (already correct)\n";
    }
}

echo "\n🎉 All pages fixed! Run: php artisan view:clear\n";
