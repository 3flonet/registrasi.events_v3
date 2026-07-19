<?php

namespace App\Livewire\Admin\WhatsAppTemplate;

use App\Models\WhatsAppTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public bool $showDeleteModal = false;
    public ?int $deletingTemplateId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deletingTemplateId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingTemplateId) {
            $tpl = WhatsAppTemplate::findOrFail($this->deletingTemplateId);
            $tpl->delete();
            
            session()->flash('message', 'WhatsApp Template deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->deletingTemplateId = null;
    }

    public function render()
    {
        $query = WhatsAppTemplate::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('body_preview', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterCategory)) {
            $query->where('category', $this->filterCategory);
        }

        $templates = $query->orderBy('name', 'asc')->paginate(10);

        return view('livewire.admin.whatsapp-template.index', [
            'templates' => $templates
        ])->layout('layouts.app');
    }

    public function syncStatus()
    {
        $ws = app(\App\Services\WhatsAppService::class);
        $res = $ws->getMetaTemplatesStatus();

        if ($res['status']) {
            $metaStatuses = $res['data'];
            
            $localTemplates = WhatsAppTemplate::all();
            $updatedCount = 0;

            foreach ($localTemplates as $tpl) {
                $metaData = $metaStatuses[$tpl->name] ?? null;
                $newStatus = $metaData ? $metaData['status'] : 'DRAFT';
                $newReason = $metaData ? $metaData['rejected_reason'] : null;

                if ($tpl->meta_status !== $newStatus || $tpl->rejected_reason !== $newReason) {
                    $tpl->update([
                        'meta_status' => $newStatus,
                        'rejected_reason' => $newReason
                    ]);
                    $updatedCount++;
                }
            }

            session()->flash('message', "Selesai! Status {$updatedCount} template berhasil disinkronkan dengan Meta.");
        } else {
            session()->flash('error', 'Gagal sinkronisasi: ' . ($res['reason'] ?? 'Unknown Error'));
        }
    }

    public function submitToMeta($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        
        $ws = app(\App\Services\WhatsAppService::class);
        $res = $ws->createTemplate($template);

        if ($res['status']) {
            $template->update([
                'meta_status' => $res['meta_status'] ?? 'PENDING'
            ]);
            session()->flash('message', 'Sukses! Template "' . $template->name . '" berhasil diajukan ke Meta.');
        } else {
            session()->flash('error', 'Gagal mengajukan ke Meta: ' . ($res['reason'] ?? 'Unknown Error'));
        }
    }
}
