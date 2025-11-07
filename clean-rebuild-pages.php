<?php
/**
 * Final Clean Fix - Remove Duplicates and Rebuild Properly.
 */
$basePath = __DIR__.'/resources/views/employer/';
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

echo "🧹 Deep cleaning and rebuilding pages...\n\n";

foreach ($pages as $page) {
    $file = $basePath.$page.'.blade.php';

    if (!file_exists($file)) {
        echo "⚠️  File not found: $page\n";
        continue;
    }

    $content = file_get_contents($file);

    // Find </head> tag
    $headEndPos = strpos($content, '</head>');
    if ($headEndPos === false) {
        echo "⚠️  No </head> found in $page\n";
        continue;
    }

    // Extract just the head section
    $head = substr($content, 0, $headEndPos + 7);

    // Make sure unified-styles is included
    if (strpos($head, "include('employer.partials.unified-styles')") === false) {
        $head = str_replace(
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">'."\n\n@include('employer.partials.unified-styles')",
            $head
        );
    }

    // Remove any <style> blocks from head
    $head = preg_replace('/<style>.*?<\/style>/s', '', $head);

    // Get everything after </head>
    $afterHead = substr($content, $headEndPos + 7);

    // Extract the actual page content
    // Look for common content markers
    $contentMarkers = [
        '@if(session(\'success\'))',
        '@if(session(\'error\'))',
        '<div class="card">',
        '<form',
        '@foreach',
        '<div class="stat-grid">',
        '<div class="filters">',
        'class="flash-message"',
    ];

    $contentStart = false;
    foreach ($contentMarkers as $marker) {
        $pos = strpos($afterHead, $marker);
        if ($pos !== false && ($contentStart === false || $pos < $contentStart)) {
            $contentStart = $pos;
        }
    }

    if ($contentStart === false) {
        echo "⚠️  Could not find content in $page\n";
        continue;
    }

    // Extract actual content
    $actualContent = substr($afterHead, $contentStart);

    // Remove everything after last meaningful content before </html>
    $htmlPos = strrpos($actualContent, '</html>');
    if ($htmlPos !== false) {
        $actualContent = substr($actualContent, 0, $htmlPos);
    }

    // Clean up trailing divs and script tags
    $actualContent = preg_replace('/@include\(\'partials\.(logout-confirm|custom-modals)\'\)\s*<\/body>\s*<\/html>\s*$/s', '', $actualContent);
    $actualContent = preg_replace('/<\/body>\s*<\/html>\s*$/s', '', $actualContent);
    $actualContent = preg_replace('/(<\/div>\s*){3,}<\/body>/s', '', $actualContent);

    // Find and preserve scripts at the end
    $scripts = '';
    if (preg_match('/<script>.*?<\/script>\s*$/s', $actualContent, $matches)) {
        $scripts = $matches[0];
        $actualContent = preg_replace('/<script>.*?<\/script>\s*$/s', '', $actualContent);
    }

    // Preserve @include for custom-modals
    $includes = '';
    if (preg_match('/@include\(\'partials\.(custom-modals|logout-confirm)\'\)/', $actualContent, $matches)) {
        $includes = $matches[0];
        $actualContent = str_replace($matches[0], '', $actualContent);
    }

    // Page metadata
    $titles = [
        'applicants' => 'Applicants',
        'analytics' => 'Analytics',
        'employees' => 'Employees',
        'history' => 'Application History',
        'job-create' => 'Post New Job',
        'job-edit' => 'Edit Job Posting',
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

    $title = $titles[$page] ?? ucfirst(str_replace('-', ' ', $page));
    $icon = $icons[$page] ?? 'file';

    // Build complete new file
    $newContent = $head."\n";
    $newContent .= "<body>\n\n";
    $newContent .= "@include('employer.partials.navbar')\n\n";
    $newContent .= "<div class=\"main-content\">\n";
    $newContent .= "  @include('employer.partials.sidebar')\n  \n";
    $newContent .= "  <div class=\"content-area\">\n";

    // Add page header
    $newContent .= "    <div class=\"page-header\">\n";
    $newContent .= "      <h1 class=\"page-title\"><i class=\"fas fa-$icon\"></i> $title</h1>\n";
    $newContent .= "    </div>\n\n";

    // Add actual content
    $newContent .= '    '.trim($actualContent)."\n";

    // Add preserved scripts
    if ($scripts) {
        $newContent .= "\n".$scripts."\n";
    }

    // Add preserved includes
    if ($includes) {
        $newContent .= "\n  ".$includes."\n";
    }

    $newContent .= "  </div><!-- content-area -->\n";
    $newContent .= "</div><!-- main-content -->\n\n";
    $newContent .= "@include('partials.logout-confirm')\n\n";
    $newContent .= "</body>\n</html>\n";

    // Clean up excessive whitespace
    $newContent = preg_replace('/\n{3,}/', "\n\n", $newContent);

    file_put_contents($file, $newContent);
    echo "✅ Rebuilt: $page.blade.php\n";
}

echo "\n🎉 All pages rebuilt cleanly!\n";
echo "Run: php artisan view:clear\n";
