<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $filters = [
        'family_id' => '',
        'sub_family_id' => '',
        'unit_id' => '',
        'packaging_id' => '',
        'vat_id' => '',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function getArticlesProperty()
    {
        return Article::with(['family', 'subFamily', 'unit', 'packaging', 'vat'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('brand', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['family_id'], function ($query) {
                $query->where('family_id', $this->filters['family_id']);
            })
            ->when($this->filters['sub_family_id'], function ($query) {
                $query->where('sub_family_id', $this->filters['sub_family_id']);
            })
            ->when($this->filters['unit_id'], function ($query) {
                $query->where('unit_id', $this->filters['unit_id']);
            })
            ->when($this->filters['packaging_id'], function ($query) {
                $query->where('packaging_id', $this->filters['packaging_id']);
            })
            ->when($this->filters['vat_id'], function ($query) {
                $query->where('vat_id', $this->filters['vat_id']);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filters']);
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.article-table', [
            'articles' => $this->articles,
        ]);
    }
}
