<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ReportAttachmentsController extends Controller
{
    public function download(Report $report)
    {
        if (! auth()->user()->isAdmin() && $report->user_id !== auth()->id()) {
            abort(403);
        }

        $report->load('expenses');

        $expenses = $report->expenses->filter(fn ($e) => $e->receipt_path);

        if ($expenses->isEmpty()) {
            return back()->with('error', 'Este relatório não possui anexos.');
        }

        $zipPath = storage_path('app/private/tmp/anexos-'.$report->protocol_number.'.zip');

        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Não foi possível criar o arquivo ZIP.');
        }

        foreach ($expenses as $expense) {
            $fullPath = Storage::disk('public')->path($expense->receipt_path);

            if (file_exists($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $name = ($expense->description
                    ? \Str::slug($expense->description)
                    : 'despesa-'.$expense->id
                ).'.'.$ext;

                $zip->addFile($fullPath, $name);
            }
        }

        $zip->close();

        return response()->download(
            $zipPath,
            "anexos-{$report->protocol_number}.zip",
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }
}
