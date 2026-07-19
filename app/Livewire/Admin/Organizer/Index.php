<?php

namespace App\Livewire\Admin\Organizer;

use App\Models\Organizer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $organizerId, $name, $slug, $description, $subscription_plan_id, $subscription_status, $subscription_expires_at;
    
    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'slug' => 'required|string|unique:organizers,slug',
        'description' => 'nullable|string',
        'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        'subscription_status' => 'required|string',
        'subscription_expires_at' => 'nullable|date',
    ];

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function create()
    {
        $this->reset(['organizerId', 'name', 'slug', 'description', 'subscription_plan_id', 'subscription_expires_at']);
        $this->subscription_status = 'active';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $organizer = Organizer::findOrFail($id);
        $this->organizerId = $organizer->id;
        $this->name = $organizer->name;
        $this->slug = $organizer->slug;
        $this->description = $organizer->description;
        $this->subscription_plan_id = $organizer->subscription_plan_id;
        $this->subscription_status = $organizer->subscription_status;
        $this->subscription_expires_at = $organizer->subscription_expires_at ? $organizer->subscription_expires_at->format('Y-m-d') : null;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->organizerId) {
            $rules['slug'] = 'required|string|unique:organizers,slug,' . $this->organizerId;
        }

        $this->validate($rules);

        Organizer::updateOrCreate(
            ['id' => $this->organizerId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'subscription_plan_id' => $this->subscription_plan_id,
                'subscription_status' => $this->subscription_status,
                'subscription_expires_at' => $this->subscription_expires_at,
            ]
        );

        $this->showModal = false;
        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Saved',
            'text' => 'Organizer has been saved successfully.',
        ]);
    }

    public function delete($id)
    {
        Organizer::findOrFail($id)->delete();
        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Deleted',
            'text' => 'Organizer has been removed.',
        ]);
    }

    public function render()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();

        $organizers = Organizer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('slug', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.organizer.index', [
            'organizers' => $organizers,
            'plans' => $plans
        ])->layout('layouts.app');
    }
}
