<?php

namespace App\Livewire\Admin\SubscriptionVoucher;

use App\Models\SubscriptionVoucher;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showDeleteModal = false;
    public $voucherIdToDelete = null;

    public function confirmDelete($id)
    {
        $this->voucherIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->voucherIdToDelete) {
            $voucher = SubscriptionVoucher::findOrFail($this->voucherIdToDelete);
            $voucher->delete();
            $this->showDeleteModal = false;
            $this->voucherIdToDelete = null;
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Voucher Deleted']);
        }
    }

    public function toggleStatus($id)
    {
        $voucher = SubscriptionVoucher::findOrFail($id);
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();
    }

    public function render()
    {
        $vouchers = SubscriptionVoucher::where('code', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.subscription-voucher.index', [
            'vouchers' => $vouchers
        ])->layout('layouts.app');
    }
}
