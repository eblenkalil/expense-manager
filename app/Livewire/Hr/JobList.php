<?php

namespace App\Livewire\Hr;

use App\Models\Job;
use App\Models\Position;
use Livewire\Attributes\Validate;
use Livewire\Component;

class JobList extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public bool $showLinkModal = false;

    public string $linkUrl = '';

    public string $positionFilter = '';

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|integer|exists:positions,id')]
    public ?int $position_id = null;

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->position_id = null;
        $this->description = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $jobId): void
    {
        $job = Job::findOrFail($jobId);
        $this->editingId = $jobId;
        $this->title = $job->title;
        $this->position_id = $job->position_id;
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
                'position_id' => $this->position_id,
                'description' => $this->description ?: null,
            ]);
            $message = "Vaga \"{$job->title}\" atualizada.";
        } else {
            $job = Job::create([
                'title' => $this->title,
                'position_id' => $this->position_id,
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

    public function showLink(int $jobId): void
    {
        $job = Job::findOrFail($jobId);
        $this->linkUrl = route('jobs.apply', $job->public_token);
        $this->showLinkModal = true;
    }

    public function closeLinkModal(): void
    {
        $this->showLinkModal = false;
        $this->linkUrl = '';
    }

    public function render()
    {
        $jobs = Job::with('position')
            ->withCount([
                'candidates',
                'candidates as pending_count' => fn ($q) => $q->where('status', 'pending'),
                'candidates as interview_count' => fn ($q) => $q->whereIn('status', ['interview', 'second_interview']),
                'candidates as hired_count' => fn ($q) => $q->where('status', 'hired'),
                'candidates as discarded_count' => fn ($q) => $q->where('status', 'discarded'),
            ])
            ->when($this->positionFilter, fn ($q) => $q->where('position_id', $this->positionFilter))
            ->latest()
            ->get();

        return view('livewire.hr.job-list', [
            'jobs' => $jobs,
            'positions' => Position::active()->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Vagas']);
    }
}
