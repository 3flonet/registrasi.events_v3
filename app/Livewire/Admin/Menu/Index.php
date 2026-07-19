<?php

namespace App\Livewire\Admin\Menu;

use App\Models\MenuItem;
use Livewire\Component;

class Index extends Component
{
    public $menuItems;

    public function mount()
    {
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        $this->menuItems = MenuItem::whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
    }

    public function delete($id)
    {
        MenuItem::findOrFail($id)->delete();
        session()->flash('message', 'Menu item deleted successfully.');
        $this->loadMenuItems();
    }

    public function updateMenuOrder($items)
    {
        $this->updateOrderRecursive($items);
        session()->flash('message', 'Menu order updated successfully.');
    }

    protected function updateOrderRecursive($menuItems, $parentId = null)
    {
        foreach ($menuItems as $index => $item) {
            $itemId = (int) $item['value'];

            MenuItem::find($itemId)->update([
                'order' => $index + 1,
                'parent_id' => $parentId
            ]);

            if (!empty($item['items'])) {
                $this->updateOrderRecursive($item['items'], $itemId);
            }
        }
    }

    public function render()
    {
        $this->loadMenuItems();
        return view('livewire.admin.menu.index')
            ->layout('layouts.app');
    }
}
