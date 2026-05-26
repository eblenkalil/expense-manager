<?php

namespace App\Livewire\Hr;

use App\Models\Candidate;
use App\Models\CandidateEvent;
use App\Models\Job;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateList extends Component
{
    use WithFileUploads, WithPagination;

    public Job $job;

    public string $statusFilter = '';

    public string $sourceFilter = '';

    public bool $showModal = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|url|max:255')]
    public string $linkedin = '';

    #[Validate('nullable|numeric|min:0')]
    public string $salary_expectation = '';

    #[Validate('required|file|mimes:pdf|max:10240')]
    public $cv;

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    public function mount(Job $job): void
    {
        $this->job = $job;
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSourceFilter(): void
    {
        $this->resetPage();
    }

    public function getCandidatesProperty()
    {
        return Candidate::where('job_id', $this->job->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->sourceFilter, fn ($q) => $q->where('source', $this->sourceFilter))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function getCountsProperty(): array
    {
        return Candidate::where('job_id', $this->job->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function openModal(): void
    {
        $this->reset(['name', 'email', 'phone', 'linkedin', 'salary_expectation', 'notes', 'cv']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $this->validate();

        $duplicate = Candidate::where('email', $this->email)
            ->where('job_id', '!=', $this->job->id)
            ->with('job')
            ->first();

        $cvPath = $this->cv->store('curriculos', 'public');

        $candidate = Candidate::create([
            'job_id' => $this->job->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'linkedin' => $this->linkedin ?: null,
            'salary_expectation' => $this->salary_expectation ?: null,
            'cv_path' => $cvPath,
            'notes' => $this->notes ?: null,
            'source' => 'manual',
            'created_by' => auth()->id(),
        ]);

        CandidateEvent::create([
            'candidate_id' => $candidate->id,
            'user_id' => auth()->id(),
            'type' => 'registration',
            'content' => 'Candidato cadastrado manualmente por '.auth()->user()->name,
            'new_status' => 'pending',
        ]);

        $this->closeModal();

        if ($duplicate) {
            session()->flash('warning', "Candidato cadastrado. Atenção: {$this->email} já se inscreveu na vaga \"{$duplicate->job->title}\".");
        } else {
            session()->flash('success', 'Candidato cadastrado com sucesso.');
        }
    }

    public function exportCsv(): StreamedResponse
    {
        $candidates = Candidate::where('job_id', $this->job->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->sourceFilter, fn ($q) => $q->where('source', $this->sourceFilter))
            ->orderByDesc('created_at')
            ->get();

        $slug = str()->slug($this->job->title);
        $date = now()->format('Y-m-d');
        $filename = "candidatos-{$slug}-{$date}.csv";

        return response()->streamDownload(function () use ($candidates) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nome', 'E-mail', 'Telefone', 'Pretensão Salarial', 'Status', 'Origem', 'Data de Inscrição']);
            foreach ($candidates as $c) {
                fputcsv($out, [
                    $c->name,
                    $c->email,
                    $c->phone ?? '',
                    $c->salary_expectation ? 'R$ '.number_format($c->salary_expectation, 2, ',', '.') : '',
                    $c->status_label,
                    $c->source_label,
                    $c->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        return view('livewire.hr.candidate-list', [
            'candidates' => $this->candidates,
            'counts' => $this->counts,
        ])->layout('layouts.app', ['title' => 'Candidatos — '.$this->job->title]);
    }
}
