<?php
namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportPdf
{
     private static $papersize = 'Letter';
     private static $paperOrientation = 'portrait';
     private static $viewFile;
     private static array $dataView = [];

     public static function paperSize($size = 'Letter')
     {
          self::$papersize = $size;
          return new static();
     }

     public static function paperOrientation($orientation = 'portrait')
     {
          self::$paperOrientation = $orientation;
          return new static();
     }

     public static function view($viewFile)
     {
          self::$viewFile = $viewFile;
          return new static();
     }

     public static function data($data = [])
     {
          self::$dataView = $data;
          return new static();
     }

     public static function download($filename)
     {
          $pdf = Pdf::loadView(self::$viewFile, [
               ...self::$dataView,
               'type' => 'pdf'
          ]);
          $pdf->setPaper(self::$papersize, self::$paperOrientation);
          return $pdf->download("{$filename}.pdf");
     }

     public static function stream($filename)
     {
          $pdf = Pdf::loadView(self::$viewFile, [
               ...self::$dataView,
               'type' => 'pdf'
          ]);
          $pdf->setPaper(self::$papersize, self::$paperOrientation);
          return $pdf->stream("{$filename}.pdf");
     }

     public static function save($filename,$path)
     {
          $pdf = Pdf::loadView(self::$viewFile, [
               ...self::$dataView,
               'type' => 'pdf'
          ]);
          $pdf->setPaper(self::$papersize, self::$paperOrientation);
          Storage::put("{$path}/{$filename}.pdf", $pdf->output());
     }
}