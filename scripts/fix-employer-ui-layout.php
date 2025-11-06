<?php
/**
 * Fix UI Layout for Employer Pages
 * Remove inline styles and apply unified design system classes.
 */
$viewsPath = __DIR__.'/../resources/views/employer/';

$fixes = [
    // Replace inline styles with utility classes
    'patterns' => [
        // Card headers
        '/style="display:\s*flex;\s*justify-content:\s*space-between;\s*align-items:\s*center;?(?:[^"]*)"/' => 'class="d-flex justify-content-between align-items-center"',

        // Margins and padding
        '/style="margin-bottom:\s*16px;?[^"]*"/' => 'class="mb-3"',
        '/style="margin-bottom:\s*20px;?[^"]*"/' => 'class="mb-4"',
        '/style="margin-bottom:\s*24px;?[^"]*"/' => 'class="mb-4"',
        '/style="margin:\s*0\s+0\s+15px\s+0;?[^"]*"/' => 'class="mb-3"',
        '/style="margin-top:\s*30px;?[^"]*"/' => 'class="mt-5"',

        // Flex layouts
        '/style="display:\s*flex;\s*flex-direction:\s*column;\s*gap:\s*16px;?[^"]*"/' => 'class="d-flex flex-column gap-3"',
        '/style="display:\s*flex;\s*flex-direction:\s*column;\s*gap:\s*12px;?[^"]*"/' => 'class="d-flex flex-column gap-2"',

        // Grid layouts
        '/style="display:\s*grid;\s*grid-template-columns:\s*repeat\(auto-fit,\s*minmax\(150px,\s*1fr\)\);\s*gap:\s*15px;?[^"]*"/' => 'class="stats-grid"',

        // Titles and headings - preserve Poppins font but use classes
        '/style="font-family:\s*\'Poppins\',\s*sans-serif;\s*font-size:\s*22px;\s*color:\s*#334A5E;?[^"]*"/' => 'class="section-title"',
        '/style="font-family:\s*\'Poppins\',\s*sans-serif;\s*color:\s*#334A5E;\s*margin-bottom:\s*20px;?[^"]*"/' => 'class="section-title mb-4"',

        // Progress bars - keep inline for dynamic widths but clean up
        '/style="width:\s*(\d+)%;\s*background:\s*(#[a-fA-F0-9]{3,6});?[^"]*"/' => 'style="width:$1%; background:$2;"',

        // Border colors for stats
        '/style="border-left-color:\s*(#[a-fA-F0-9]{3,6});?[^"]*"/' => 'style="border-left-color:$1;"',

        // Search inputs
        '/style="width:\s*100%;\s*padding:\s*12px[^"]*border:\s*2px\s+solid\s+#e0e0e0[^"]*"/' => 'class="search-input"',

        // Position absolute for icons in search
        '/style="position:\s*absolute;\s*left:\s*16px;\s*top:\s*50%;\s*transform:\s*translateY\(-50%\);[^"]*"/' => 'class="search-icon"',
    ],

    // Files to process
    'files' => [
        'applicants.blade.php',
        'analytics.blade.php',
        'employees.blade.php',
        'history.blade.php',
    ],
];

// Additional CSS to add to unified-styles for new utility classes
$additionalCSS = <<<'CSS'

/* Additional Utility Classes for Employer Pages */

/* Flexbox utilities */
.d-flex { display: flex !important; }
.flex-column { flex-direction: column !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }

/* Spacing utilities */
.gap-2 { gap: 12px !important; }
.gap-3 { gap: 16px !important; }
.mb-2 { margin-bottom: 12px !important; }
.mb-3 { margin-bottom: 16px !important; }
.mb-4 { margin-bottom: 24px !important; }
.mt-5 { margin-top: 30px !important; }

/* Section titles */
.section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 22px;
  color: #334A5E;
  font-weight: 600;
  margin: 0;
}

/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 16px;
}

/* Search input */
.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 14px;
  font-family: 'Roboto', sans-serif;
  transition: all 0.3s;
  background: #fff;
}

.search-input:focus {
  outline: none;
  border-color: #648EB5;
  box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
  font-size: 16px;
  pointer-events: none;
}

/* Employee cards and job posting cards - consistent styling */
.employee-card, .job-posting-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.employee-card:hover, .job-posting-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  border-color: #648EB5;
}

/* Timeline for history */
.timeline-item {
  position: relative;
  padding-left: 40px;
  padding-bottom: 24px;
  border-left: 2px solid #e5e7eb;
}

.timeline-item:last-child {
  border-left: 2px solid transparent;
}

.timeline-icon {
  position: absolute;
  left: -12px;
  top: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  background: #fff;
  border: 2px solid;
}

.timeline-icon.hired {
  color: #28a745;
  border-color: #28a745;
}

.timeline-icon.rejected {
  color: #dc3545;
  border-color: #dc3545;
}

.timeline-icon.terminated {
  color: #6c757d;
  border-color: #6c757d;
}

.timeline-icon.resigned {
  color: #ffc107;
  border-color: #ffc107;
}

/* Chart containers */
.chart-container {
  position: relative;
  height: 300px;
  margin-top: 20px;
}

/* Progress bars in analytics */
.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

/* Stat display in cards */
.stat-display {
  background: #fff;
  border-radius: 10px;
  padding: 12px 16px;
  border-left: 4px solid;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-display .value {
  font-size: 24px;
  color: #334A5E;
  font-weight: 700;
  margin: 0;
}

.stat-display .label {
  font-size: 12px;
  color: #666;
  margin: 4px 0 0 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .section-title {
    font-size: 18px;
  }
  
  .chart-container {
    height: 250px;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .d-flex.justify-content-between {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 12px;
  }
}

CSS;

echo "🔧 Fixing Employer UI Layout Issues...\n\n";

// First, add additional CSS to unified-styles
$unifiedStylesPath = $viewsPath.'partials/unified-styles.blade.php';
if (file_exists($unifiedStylesPath)) {
    $content = file_get_contents($unifiedStylesPath);

    // Check if additional CSS is already added
    if (strpos($content, '/* Additional Utility Classes for Employer Pages */') === false) {
        // Find the closing </style> tag and insert before it
        $content = str_replace('</style>', $additionalCSS."\n</style>", $content);
        file_put_contents($unifiedStylesPath, $content);
        echo "✅ Added utility classes to unified-styles.blade.php\n";
    } else {
        echo "ℹ️  Utility classes already exist in unified-styles.blade.php\n";
    }
}

// Process each file
foreach ($fixes['files'] as $file) {
    $filePath = $viewsPath.$file;

    if (!file_exists($filePath)) {
        echo "⚠️  File not found: {$file}\n";
        continue;
    }

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Apply pattern replacements
    foreach ($fixes['patterns'] as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    // Specific fixes for each file
    if ($file === 'applicants.blade.php') {
        // Fix card header
        $content = preg_replace(
            '/<div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">/',
            '<div class="card-header d-flex justify-content-between align-items-center">',
            $content
        );

        // Fix stat grid
        $content = preg_replace(
            '/<div class="stat-grid" style="margin-bottom:16px;">/',
            '<div class="stat-grid">',
            $content
        );
    }

    if ($file === 'analytics.blade.php') {
        // Clean up section titles
        $content = preg_replace(
            '/<h3 style="font-family:\'Poppins\', sans-serif; color:#334A5E; margin-bottom:20px;">/',
            '<h3 class="section-title mb-4">',
            $content
        );
    }

    if ($file === 'employees.blade.php') {
        // Fix employee header
        $content = preg_replace(
            '/<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">/',
            '<div class="d-flex justify-content-between align-items-center mb-3">',
            $content
        );

        // Fix stat display
        $content = preg_replace(
            '/<div style="background:#fff; border-radius:10px; padding:12px 16px; border-left:4px solid #0f5132;">/',
            '<div class="stat-display" style="border-left-color:#0f5132;">',
            $content
        );

        $content = preg_replace(
            '/<div style="font-size:24px; color:#334A5E; font-weight:700;">/',
            '<div class="value">',
            $content
        );

        $content = preg_replace(
            '/<div style="font-size:12px; color:#666;">/',
            '<div class="label">',
            $content
        );
    }

    if ($file === 'history.blade.php') {
        // Fix history header
        $content = preg_replace(
            '/<h2 style="margin:0 0 15px 0; color:#334A5E;">/',
            '<h2 class="section-title mb-3">',
            $content
        );
    }

    // Save if changes were made
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "✅ Fixed: {$file}\n";
    } else {
        echo "ℹ️  No changes needed: {$file}\n";
    }
}

echo "\n✨ UI layout fixes completed!\n";
echo "📝 Next steps:\n";
echo "   1. Clear cache: php artisan view:clear\n";
echo "   2. Test each page in browser\n";
echo "   3. Verify responsive layout on mobile\n";
