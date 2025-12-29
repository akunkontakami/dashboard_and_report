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
use Illuminate\Support\Facades\DB;

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
        
            if($this->type == 'inbound'){
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
            }else
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
                    // dd($this->data->isEmpty());
                    if ($this->data->isEmpty()) {
                        // Nama file harus string, bukan array
                        $filename = "REPORT-TICKET-FORM-EMPTY";

                        // Kirim data minimal ke view agar Blade tidak error
                        ExportPdf::data([
                            'row'           => (object)[],   // pakai object kosong agar aman di Blade
                            'category'      => $this->type ?? null,
                            'verifications' => [],
                            'data'          => collect(),    // aman untuk foreach di Blade
                        ])
                        ->view("exports.reports.ticket-form-pdf")
                        ->save($filename, $folder);

                        // Nama file ZIP
                        $zipFileName = "{$folder}/{$zipBaseName}.zip";
                        $filePath = storage_path('app/' . $zipFileName);

                        $zip = new ZipArchive;

                        if ($zip->open($filePath, ZipArchive::CREATE) === TRUE) {

                            // Pastikan file PDF sudah dibuat sebelum dimasukkan ke ZIP
                            $pdfPath = "{$folder}/{$filename}.pdf";

                            if (Storage::disk('local')->exists($pdfPath)) {
                                // Masukkan ke ZIP
                                $zip->addFromString(
                                    "{$filename}.pdf", 
                                    Storage::disk('local')->get($pdfPath)
                                );

                                // Hapus file PDF setelah sukses dimasukkan ke ZIP
                                Storage::disk('local')->delete($pdfPath);
                            }

                            // Tutup ZIP
                            $zip->close();
                        }


                    }else {
                    
                        foreach ($this->data as $row) {
                            //  \DB::enableQueryLog();
                                            // ambil groups (sama seperti sebelumnya)
                                    $groups = DB::table('outbound_verification_form_fields as fvf')
                                        ->select([
                                            'fvf.group_name',
                                            'f.id',
                                            'f.name as verification_name',
                                        ])
                                        ->join('outbound_verification_forms as f', 'f.id', '=', 'fvf.outbound_verification_form_id')
                                        ->where('f.company_id', $this->company->id)
                                        ->whereBetween('f.created_at', [
                                            @$this->filter['created_start'] . " 00:00:00",
                                            @$this->filter['created_end'] . " 23:59:59"
                                        ])
                                        ->groupBy('fvf.group_name', 'f.id', 'f.name')
                                        ->orderBy('fvf.group_sorting', 'asc')
                                        ->get();

                                    $result = [];

                                    foreach ($groups as $rowVer) {

                                        // ambil semua fields di group ini (TIDAK dibatasi hanya 1 id)
                                        $fields = DB::table('outbound_verification_form_fields as fvf')
                                            ->select('fvf.*')
                                            ->where('fvf.outbound_verification_form_id', $rowVer->id)
                                            ->where('fvf.group_name', $rowVer->group_name)
                                            ->orderBy('fvf.sorting', 'asc')
                                            ->get();

                                        // data JSON user (sumber nilai)
                                        $inputData = json_decode($row->data, true) ?? [];

                                        // helper: cari value di inputData berdasarkan slug dengan fallback
                                        $findValue = function(array $input, string $slug) {
                                            // 1) exact match
                                            if (array_key_exists($slug, $input)) {
                                                return $input[$slug];
                                            }

                                            // 2) try common suffixes _1, _2
                                            if (array_key_exists($slug . '_1', $input)) {
                                                return $input[$slug . '_1'];
                                            }
                                            if (array_key_exists($slug . '_2', $input)) {
                                                return $input[$slug . '_2'];
                                            }

                                            // 3) try stripped numbers from input keys (e.g. country_code_1 vs country_code)
                                            foreach ($input as $k => $v) {
                                                // jika slug sama-sama bagian dari key (case-insensitive)
                                                if (stripos($k, $slug) !== false) {
                                                    return $v;
                                                }
                                            }

                                            // 4) fallback: try kebab/snake/camel variations
                                            $variants = [
                                                Str::snake($slug),
                                                Str::camel($slug),
                                                Str::kebab($slug),
                                                str_replace(['-',' '], ['_','_'], $slug),
                                            ];
                                            foreach ($variants as $var) {
                                                if (array_key_exists($var, $input)) {
                                                    return $input[$var];
                                                }
                                            }

                                            return null;
                                        };

                                        $mappedFields = [];

                                        foreach ($fields as $field) {
                                            // dapatkan value dengan fallback
                                            $value = $findValue($inputData, $field->slug);

                                            // jika null, tampilkan '-' (atau kosong sesuai preferensi)
                                            $mappedFields[] = [
                                                'label' => $field->label,
                                                'slug'  => $field->slug,
                                                'value' => $value !== null ? $value : '-',
                                            ];
                                        }

                                        $result[] = [
                                            'group_name'        => $rowVer->group_name,
                                            'verification_name' => $rowVer->verification_name,
                                            'fields'            => $mappedFields,
                                        ];
                                    }
                                    // dd($result);
                                    //  dd(\DB::getQueryLog());
                            $filename = $row->ticket_number;
                        // $html = view("exports.reports.ticket-form-pdf", [
                        //         'row'           => $row,
                        //         'category'      => $this->type,
                        //         'verifications' => $result,
                        //     ])->render();

                        //     echo $html;
                        //     exit; // wajib, biar job tidak lanjut ke proses PDF

                            ExportPdf::data([
                                'row' => $row,
                                'category' => $this->type,
                                'verifications' => $result,
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
