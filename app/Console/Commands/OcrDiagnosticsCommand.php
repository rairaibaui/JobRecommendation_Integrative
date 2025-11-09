<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OcrDiagnosticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnostics:ocr {--notify : Send notifications to admins if issues are found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for presence of OCR-related binaries (pdftotext, pdftoppm, tesseract) and optionally notify admins.';

    public function handle()
    {
        $diag = app(\App\Services\OcrDiagnosticService::class);
        $result = $diag->checkBinaries();

        if ($result['ok']) {
            $this->info('OCR diagnostics: OK — all required binaries present.');
            return 0;
        }

        $this->warn('OCR diagnostics: missing binaries: '.implode(', ', $result['missing']));

        if ($this->option('notify')) {
            $diag->warnAdminsOnce($result['missing']);
            $this->info('Admin notifications sent (throttled).');
        } else {
            $this->info('Run with --notify to inform admins (throttled once per day).');
        }

        return 2;
    }
}
