<?php

namespace App\Livewire\Reports;

use App\Mail\ReportPaidMail;
use App\Mail\ReportSubmittedMail;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReportDetail extends Component
{
    use WithFileUploads;

    public Report $report;

    // Preview inline do recibo
    public bool $showPreview = false;

    public string $previewUrl = '';

    public string $previewType = '';

    // Upload comprovante pagamento (admin)
    #[Validate('nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240')]
    public $paymentReceipt;

    public function mount(Report $report): void
    {
        // Verifica acesso
        if (! auth()->user()->isAdmin() && $report->user_id !== auth()->id()) {
            abort(403);
        }
        $this->report = $report->load(['user', 'expenses.category']);
    }

    public function submit(): void
    {
        if ($this->report->status !== 'draft' || $this->report->user_id !== auth()->id()) {
            session()->flash('error', 'Ação inválida.');

            return;
        }

        $this->report->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Notifica admins que tenham notify_email ativo
        User::where('role', 'admin')
            ->where('notify_email', true)
            ->get()
            ->each(fn ($admin) => Mail::to($admin->email)->queue(
                new ReportSubmittedMail($this->report)
            ));

        $this->report->refresh();
        session()->flash('success', 'Relatório entregue! Aguardando pagamento.');
    }

    public function markAsPaid(): void
    {
        if (! auth()->user()->isAdmin() || $this->report->status !== 'submitted') {
            session()->flash('error', 'Ação inválida.');

            return;
        }

        $this->validate(['paymentReceipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240']);

        $receiptPath = null;
        $receiptName = null;

        if ($this->paymentReceipt) {
            $receiptName = $this->paymentReceipt->getClientOriginalName();
            $receiptPath = $this->paymentReceipt->store('payments', 'public');
        }

        DB::transaction(function () use ($receiptPath, $receiptName) {
            $this->report->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_receipt_path' => $receiptPath,
                'payment_receipt_name' => $receiptName,
            ]);

            // Arquiva as despesas
            $this->report->expenses()->update(['status' => 'archived']);
        });

        // Notifica colaborador
        if ($this->report->user->notify_email) {
            Mail::to($this->report->user->email)->queue(
                new ReportPaidMail($this->report)
            );
        }

        $this->report->refresh()->load(['user', 'expenses.category']);
        session()->flash('success', 'Pagamento confirmado com sucesso!');
    }

    public function previewReceipt(int $expenseId): void
    {
        $expense = $this->report->expenses->find($expenseId);
        if (! $expense?->receipt_path) {
            return;
        }

        $this->previewUrl = $expense->receipt_url;
        $this->previewType = in_array($expense->receipt_ext, ['jpg', 'jpeg', 'png', 'webp'])
            ? 'image' : 'pdf';
        $this->showPreview = true;
    }

    public function discardRejected(): void
    {
        if ($this->report->status !== 'rejected' || $this->report->user_id !== auth()->id()) {
            session()->flash('error', 'Ação inválida.');

            return;
        }

        $this->report->expenses()->detach();

        session()->flash('success', 'Despesas liberadas. O relatório reprovado foi mantido no histórico.');
        $this->redirect(route('expenses.index'));
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function render()
    {
        return view('livewire.reports.report-detail')
            ->layout('layouts.app', ['title' => $this->report->protocol_number]);
    }
}
