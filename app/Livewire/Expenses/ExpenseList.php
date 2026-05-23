<?php

namespace App\Livewire\Expenses;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithFileUploads, WithPagination;

    // Filtros
    public string $search      = '';
    public string $categoryFilter = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';

    // Modal nova despesa
    public bool $showModal = false;

    #[Validate('required|date')]
    public string $expense_date = '';

    #[Validate('required|numeric|min:0.01')]
    public string $value = '';

    #[Validate('required|exists:categories,id')]
    public string $category_id = '';

    #[Validate('nullable|string|max:255')]
    public string $description = '';

    #[Validate('nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240')]
    public $receipt;

    // Modal preview
    public bool   $showPreview   = false;
    public string $previewUrl    = '';
    public string $previewType   = ''; // 'image' | 'pdf'

    public function mount(): void
    {
        $this->expense_date = today()->format('Y-m-d');
    }

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingCategoryFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void  { $this->resetPage(); }
    public function updatingDateTo(): void    { $this->resetPage(); }

    public function getExpensesProperty()
    {
        return Expense::with('category')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['available', 'locked'])
            ->when($this->search, fn($q) =>
                $q->where('description', 'like', "%{$this->search}%")
            )
            ->when($this->categoryFilter, fn($q) =>
                $q->where('category_id', $this->categoryFilter)
            )
            ->when($this->dateFrom, fn($q) =>
                $q->where('expense_date', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) =>
                $q->where('expense_date', '<=', $this->dateTo)
            )
            ->orderBy('expense_date', 'desc')
            ->paginate(15);
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset(['value', 'category_id', 'description', 'receipt']);
        $this->expense_date = today()->format('Y-m-d');
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $receiptPath = null;
        $receiptName = null;

        if ($this->receipt) {
            $receiptName = $this->receipt->getClientOriginalName();
            $receiptPath = $this->receipt->store('receipts', 'public');
        }

        Expense::create([
            'user_id'               => auth()->id(),
            'expense_date'          => $this->expense_date,
            'value'                 => $this->value,
            'category_id'           => $this->category_id,
            'description'           => $this->description,
            'receipt_path'          => $receiptPath,
            'receipt_original_name' => $receiptName,
        ]);

        $this->closeModal();
        $this->dispatch('expense-saved');
        session()->flash('success', 'Despesa cadastrada com sucesso!');
    }

    public function delete(int $id): void
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'available')
            ->firstOrFail();

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();
        session()->flash('success', 'Despesa excluída.');
    }

    public function preview(int $id): void
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! $expense->receipt_path) return;

        $this->previewUrl  = $expense->receipt_url;
        $this->previewType = in_array($expense->receipt_ext, ['jpg', 'jpeg', 'png', 'webp'])
            ? 'image'
            : 'pdf';
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewUrl  = '';
    }

    public function render()
    {
        return view('livewire.expenses.expense-list', [
            'expenses'   => $this->expenses,
            'categories' => Category::active()->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Minhas Despesas']);
    }
}
