<?php

namespace App\Livewire\Hr;

use App\Models\Candidate;
use App\Models\CandidateEvent;
use Livewire\Component;

class CandidateDetail extends Component
{
    public Candidate $candidate;

    public bool $showCvPreview = false;

    public string $newComment = '';

    public ?int $editingCommentId = null;

    public string $editingCommentContent = '';

    // Modal de mudança de status
    public bool $showStatusModal = false;

    public string $newStatus = '';

    public string $statusReason = '';

    public int $ratingValue = 0;

    public string $ratingComment = '';

    public string $interviewDate = '';

    public string $interviewType = '';

    public function mount(Candidate $candidate): void
    {
        $this->candidate = $candidate->load(['job', 'events.user']);
    }

    public function openStatusModal(string $status): void
    {
        $this->newStatus = $status;
        $this->statusReason = '';
        $this->ratingValue = 0;
        $this->ratingComment = '';
        $this->interviewDate = '';
        $this->interviewType = '';
        $this->showStatusModal = true;
        $this->resetValidation();
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
    }

    public function openCvPreview(): void
    {
        $this->showCvPreview = true;
    }

    public function closeCvPreview(): void
    {
        $this->showCvPreview = false;
    }

    public function confirmStatusChange(): void
    {
        $rules = ['newStatus' => 'required|in:pending,interview,second_interview,hired,discarded'];

        if (in_array($this->newStatus, ['hired', 'discarded'])) {
            $rules['statusReason'] = 'required|string|min:3|max:1000';
        } else {
            $rules['statusReason'] = 'nullable|string|max:1000';
        }

        if (in_array($this->newStatus, ['interview', 'second_interview'])) {
            $rules['interviewDate'] = 'nullable|string|max:50';
            $rules['interviewType'] = 'nullable|in:presencial,online,telefone';
            $rules['ratingValue'] = 'nullable|integer|between:0,5';
            $rules['ratingComment'] = 'nullable|string|max:1000';
        }

        $this->validate($rules);

        $previousStatus = $this->candidate->status;

        $this->candidate->update(['status' => $this->newStatus]);

        $statusLabels = [
            'pending' => 'Aguardando',
            'interview' => '1ª Entrevista',
            'second_interview' => '2ª Entrevista',
            'hired' => 'Contratado',
            'discarded' => 'Descartado',
        ];

        $prevLabel = $statusLabels[$previousStatus] ?? $previousStatus;
        $newLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $content = "Status alterado de \"{$prevLabel}\" para \"{$newLabel}\"";

        if (in_array($this->newStatus, ['interview', 'second_interview'])) {
            if ($this->interviewDate) {
                $content .= ". Data: {$this->interviewDate}";
            }
            if ($this->interviewType) {
                $typeLabels = ['presencial' => 'Presencial', 'online' => 'Online', 'telefone' => 'Telefone'];
                $content .= '. Modalidade: '.($typeLabels[$this->interviewType] ?? $this->interviewType);
            }
        }

        if ($this->statusReason) {
            $content .= ". Observação: {$this->statusReason}";
        }

        CandidateEvent::create([
            'candidate_id' => $this->candidate->id,
            'user_id' => auth()->id(),
            'type' => 'status_change',
            'content' => $content,
            'previous_status' => $previousStatus,
            'new_status' => $this->newStatus,
        ]);

        if (in_array($this->newStatus, ['interview', 'second_interview']) && $this->ratingValue > 0) {
            $ratingContent = "Avaliação: {$this->ratingValue}/5";
            if ($this->ratingComment) {
                $ratingContent .= " — {$this->ratingComment}";
            }

            CandidateEvent::create([
                'candidate_id' => $this->candidate->id,
                'user_id' => auth()->id(),
                'type' => 'rating',
                'content' => $ratingContent,
                'rating' => $this->ratingValue,
            ]);
        }

        $this->closeStatusModal();
        $this->candidate->refresh()->load(['job', 'events.user']);
        session()->flash('success', 'Status atualizado com sucesso.');
    }

    public function addComment(): void
    {
        $this->validate(['newComment' => 'required|string|min:1|max:2000']);

        CandidateEvent::create([
            'candidate_id' => $this->candidate->id,
            'user_id' => auth()->id(),
            'type' => 'comment',
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->candidate->refresh()->load(['job', 'events.user']);
    }

    public function openEditComment(int $eventId): void
    {
        $event = CandidateEvent::where('id', $eventId)
            ->where('user_id', auth()->id())
            ->where('type', 'comment')
            ->firstOrFail();

        $this->editingCommentId = $eventId;
        $this->editingCommentContent = $event->content;
    }

    public function saveEditComment(): void
    {
        $this->validate(['editingCommentContent' => 'required|string|min:1|max:2000']);

        CandidateEvent::where('id', $this->editingCommentId)
            ->where('user_id', auth()->id())
            ->where('type', 'comment')
            ->firstOrFail()
            ->update(['content' => $this->editingCommentContent]);

        $this->editingCommentId = null;
        $this->editingCommentContent = '';
        $this->candidate->refresh()->load(['job', 'events.user']);
    }

    public function cancelEditComment(): void
    {
        $this->editingCommentId = null;
        $this->editingCommentContent = '';
    }

    public function render()
    {
        $previousCandidacies = Candidate::where('email', $this->candidate->email)
            ->where('id', '!=', $this->candidate->id)
            ->with('job')
            ->get();

        return view('livewire.hr.candidate-detail', [
            'previousCandidacies' => $previousCandidacies,
        ])->layout('layouts.app', ['title' => $this->candidate->name]);
    }
}
