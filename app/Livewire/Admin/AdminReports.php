<?php

namespace App\Livewire\Admin;

use App\Mail\ReportPaidMail;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AdminReports extends Component
{
    use WithFileUploads, WithPagination;

    public string $statusFilter = 'submitted';

    public string $search = '';

    // Modal pagar
    public bool $showPayModal = false;

    public ?int $payingReportId = null;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240')]
    public $paymentReceipt;

    // Modal reprovar
    public bool $showRejectModal = false;

    public ?int $rejectingReportId = null;

    #[Validate('required|string|min:10|max:1000')]
    public string $rejectionReason = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getReportsProperty()
    {
        return Report::with('user')
            ->withCount('expenses')
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('title', 'like', "%{$this->search}%")
                ->orWhere('protocol_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))
            ))
            ->latest('submitted_at')
            ->paginate(20);
    }

    public function openPayModal(int $reportId): void
    {
        $this->payingReportId = $reportId;
        $this->paymentReceipt = null;
        $this->showPayModal = true;
        $this->resetValidation('paymentReceipt');
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->payingReportId = null;
    }

    public function confirmPay(): void
    {
        $report = Report::with('user', 'expenses')->findOrFail($this->payingReportId);

        if ($report->status !== 'submitted') {
            session()->flash('error', 'Relatório não está pendente de pagamento.');
            $this->closePayModal();

            return;
        }

        $this->validate(['paymentReceipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240']);

        $receiptPath = null;
        $receiptName = null;

        if ($this->paymentReceipt) {
            $receiptName = $this->paymentReceipt->getClientOriginalName();
            $receiptPath = $this->paymentReceipt->store('payments', 'public');
        }

        DB::transaction(function () use ($report, $receiptPath, $receiptName) {
            $report->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_receipt_path' => $receiptPath,
                'payment_receipt_name' => $receiptName,
            ]);

            $report->expenses()->update(['status' => 'archived']);
        });

        if ($report->user->notify_email) {
            Mail::to($report->user->email)->queue(new ReportPaidMail($report));
        }

        $this->closePayModal();
        session()->flash('success', "Relatório {$report->protocol_number} marcado como pago.");
    }

    public function openRejectModal(int $reportId): void
    {
        $this->rejectingReportId = $reportId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
        $this->resetValidation('rejectionReason');
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectingReportId = null;
    }

    public function confirmReject(): void
    {
        $this->validateOnly('rejectionReason', ['rejectionReason' => 'required|string|min:10|max:1000']);

        $report = Report::with('expenses')->findOrFail($this->rejectingReportId);

        if ($report->status !== 'submitted') {
            session()->flash('error', 'Relatório não está pendente de pagamento.');
            $this->closeRejectModal();

            return;
        }

        DB::transaction(function () use ($report) {
            $report->update([
                'status' => 'rejected',
                'rejection_reason' => $this->rejectionReason,
                'rejected_at' => now(),
            ]);

            $report->expenses()->update(['status' => 'available']);
        });

        $this->closeRejectModal();
        session()->flash('success', "Relatório {$report->protocol_number} reprovado.");
    }

    public function render()
    {
        return view('livewire.admin.admin-reports', [
            'reports' => $this->reports,
        ]);
    }
}
