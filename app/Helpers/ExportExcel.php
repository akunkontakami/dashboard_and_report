<?php
namespace App\Helpers;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportExcel implements FromView
{
    protected $view;
    protected $data;
    protected $verifications; // ← TAMBAHKAN
    public function __construct($args){
        $this->view = $args['view'];
        $this->data = $args['data'];
         $this->verifications = $args['verifications'] ?? []; // ← TAMBAHKAN
    }

    public function view(): View
    {
        return view($this->view, [
            'data' => $this->data,
            'type' => 'excel',
             'verifications' => $this->verifications // ← KIRIM KE VIEW
        ]);
    }
}
