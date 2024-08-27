<?php

namespace App\Jobs\Report;

use App\Models\Util\QueueActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\ExportPdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DonwloadFormTicketPdfReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $data,
        public $filter,
        public $company,
        public $type,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $queueAction = "report-ticket-form-pdf-{$this->type}";

        QueueActionLog::query()
            ->where('action', $queueAction)
            ->where('company_id', $this->company->id)
            ->delete();
        $queue = QueueActionLog::create([
            'company_id' => $this->company->id,
            'action' => $queueAction,
            'start_at' => now()
        ]);

        $folder = "REPORT-TICKET";
        $zipBaseName = "Report Ticket Form";
        $zipBaseName = $zipBaseName . " " . $this->company->name . " " . @$this->filter['created_start'] . " - " . @$this->filter['created_end'];
        $zipBaseName = strtoupper(Str::slug($zipBaseName,'-'));

        foreach ($this->data as $row) {
            $filename = $row->ticket_number;
            ExportPdf::data([
                'row' => $row,
                'category' => $this->type
            ])
                ->view("exports.reports.ticket-form-pdf")
                ->save($filename, $folder);

            $zipFileName = "{$folder}/{$zipBaseName}.zip";

            $filePath = storage_path('app/' . $zipFileName);
            $zip = new ZipArchive;
            if ($zip->open($filePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFromString("{$filename}.pdf", Storage::disk('local')->get("{$folder}/{$filename}.pdf"));
                $zip->close();
                Storage::disk('local')->delete("{$folder}/{$filename}.pdf");
            }
        }

        $queue->update([
            'done_at' => now(),
            'data' => [
                'file' => "{$folder}/{$zipBaseName}.zip",
            ]
        ]);
    }
}
