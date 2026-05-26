<?php

namespace App\Livewire\Hr;

use App\Models\Job;
use Livewire\Attributes\Validate;
use Livewire\Component;

class JobList extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:255')]
    public string $position = '';

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->position = '';
        $this->description = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $jobId): void
    {
        $job = Job::findOrFail($jobId);
        $this->editingId = $jobId;
        $this->title = $job->title;
        $this->position = $job->position;
        $this->description = $job->description ?? '';
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

        if ($this->editingId) {
            $job = Job::findOrFail($this->editingId);
            $job->update([
                'title' => $this->title,
                'position' => $this->position,
                'description' => $this->description ?: null,
            ]);
            $message = "Vaga \"{$job->title}\" atualizada.";
        } else {
            $job = Job::create([
                'title' => $this->title,
                'position' => $this->position,
                'description' => $this->description ?: null,
                'created_by' => auth()->id(),
            ]);
            $message = "Vaga \"{$job->title}\" criada.";
        }

        $this->closeModal();
        session()->flash('success', $message);
    }

    public function toggleStatus(int $jobId): void
    {
        $job = Job::findOrFail($jobId);
        $job->update(['status' => $job->status === 'open' ? 'closed' : 'open']);
    }

    public function render()
    {
        return view('livewire.hr.job-list', [
            'jobs' => Job::withCount([
                'candidates',
                'candidates as pending_count' => fn ($q) => $q->where('status', 'pending'),
                'candidates as interview_count' => fn ($q) => $q->where('status', 'interview'),
                'candidates as hired_count' => fn ($q) => $q->where('status', 'hired'),
                'candidates as discarded_count' => fn ($q) => $q->where('status', 'discarded'),
            ])->latest()->get(),
        ])->layout('layouts.app', ['title' => 'Vagas']);
    }
}
