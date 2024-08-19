<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InboundReportController extends Controller
{
    use ReportController;
    public $type = 'inbound';
}
