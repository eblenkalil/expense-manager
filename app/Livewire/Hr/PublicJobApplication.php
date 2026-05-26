<?php

namespace App\Livewire\Hr;

use App\Models\Candidate;
use App\Models\CandidateEvent;
use App\Models\Job;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PublicJobApplication extends Component
{
    use WithFileUploads;

    public Job $job;

    public bool $submitted = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:14')]
    public string $cpf = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|url|max:255')]
    public string $linkedin = '';

    #[Validate('nullable|numeric|min:0')]
    public string $salary_expectation = '';

    #[Validate('required|file|mimes:pdf|max:10240')]
    public $cv;

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    public function mount(string $token): void
    {
        $this->job = Job::with('position')->where('public_token', $token)->firstOrFail();
    }

    public function submit(): void
    {
        if ($this->job->status !== 'open') {
            return;
        }

        $this->validate();

        if (! Candidate::isValidCpf($this->cpf)) {
            $this->addError('cpf', 'CPF inválido.');

            return;
        }

        $cvPath = $this->cv->store('curriculos', 'public');

        $candidate = Candidate::create([
            'job_id' => $this->job->id,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf ? Candidate::formatCpf($this->cpf) : null,
            'phone' => $this->phone ?: null,
            'linkedin' => $this->linkedin ?: null,
            'salary_expectation' => $this->salary_expectation ?: null,
            'cv_path' => $cvPath,
            'notes' => $this->notes ?: null,
            'source' => 'public_form',
            'created_by' => null,
        ]);

        CandidateEvent::create([
            'candidate_id' => $candidate->id,
            'user_id' => null,
            'type' => 'registration',
            'content' => 'Candidato inscrito via formulário público'.($this->notes ? ". Comentário: {$this->notes}" : ''),
            'new_status' => 'pending',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.hr.public-job-application')
            ->layout('layouts.guest');
    }
}
