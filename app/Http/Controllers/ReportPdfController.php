<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function download(Report $report)
    {
        // Colaborador só acessa seus relatórios; admin acessa todos
        if (! auth()->user()->isAdmin() && $report->user_id !== auth()->id()) {
            abort(403);
        }

        $report->load(['user', 'expenses.category']);

        $pdf = Pdf::loadView('reports.pdf', compact('report'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("relatorio-{$report->protocol_number}.pdf");
    }
}
