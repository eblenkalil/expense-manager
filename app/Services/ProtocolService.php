<?php

namespace App\Services;

use App\Models\Report;

class ProtocolService
{
    /**
     * Gera número de protocolo no formato REL-YYYY-XXXX
     * Ex: REL-2025-0001, REL-2025-0042
     */
    public static function generate(): string
    {
        $year = now()->year;

        $lastReport = Report::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;
        if ($lastReport) {
            // Extrai o número sequencial do protocolo existente
            $parts    = explode('-', $lastReport->protocol_number);
            $sequence = ((int) end($parts)) + 1;
        }

        return sprintf('REL-%d-%04d', $year, $sequence);
    }
}
