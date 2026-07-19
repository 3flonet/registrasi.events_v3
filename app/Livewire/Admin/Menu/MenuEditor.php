<?php

namespace App\Livewire\Admin\Menu;

use App\Models\MenuItem;
use Livewire\Component;

class MenuEditor extends Component
{
    public bool $isEditMode = false;
    public $menu; 
    public $menuId;
    
    // Form fields
    public $label_en, $label_id, $link, $parent_id, $target;
    public $location;
    public $showLocationSelector = false;

    public $parentOptions;

    /**
     * Kita hilangkan Type Hinting di sini agar Laravel tidak bingung 
     * saat rutenya tidak membawa parameter apa pun.
     */
    public function mount($menu = null)
    {
        // Jika yang masuk adalah ID (dari rute edit)
        if ($menu && !($menu instanceof MenuItem)) {
            $menu = MenuItem::find($menu);
        }

        if ($menu && $menu->exists) {
            $this->isEditMode = true;
            $this->menu = $menu;
            $this->menuId = $menu->id;
            $this->label_en = $menu->getTranslation('label', 'en');
            $this->label_id = $menu->getTranslation('label', 'id');
            $this->link = $menu->link;
            $this->parent_id = $menu->parent_id;
            $this->target = $menu->target;
            $this->location = $menu->location;
            $this->showLocationSelector = !empty($menu->location);
        } else {
            $this->resetForm();
        }

        $this->parentOptions = MenuItem::whereNull('parent_id')
            ->get();
    }

    private function resetForm()
    {
        $this->isEditMode = false;
        $this->menuId = null;
        $this->label_en = '';
        $this->label_id = '';
        $this->link = '#';
        $this->parent_id = null;
        $this->target = '_self';
        $this->location = null;
        $this->showLocationSelector = false;
    }

    public function save()
    {
        $this->validate([
            'label_en' => 'required|string|max:255',
            'label_id' => 'required|string|max:255',
            'link' => 'required|string',
            'target' => 'required|in:_self,_blank',
        ]);

        $data = [
            'label' => [
                'en' => $this->label_en,
                'id' => $this->label_id,
            ],
            'link' => $this->link,
            'parent_id' => $this->parent_id ?: null,
            'target' => $this->target,
            'location' => $this->showLocationSelector ? $this->location : null,
        ];

        if ($this->isEditMode) {
            $this->menu->update($data);
            session()->flash('message', 'Menu item updated successfully.');
        } else {
            MenuItem::create($data);
            session()->flash('message', 'Menu item created successfully.');
        }

        return redirect()->route('admin.menus.index');
    }

    public function render()
    {
        return view('livewire.admin.menu.form')
            ->layout('layouts.app');
    }
}
