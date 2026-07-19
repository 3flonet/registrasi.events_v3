<?php

namespace App\Livewire\Admin\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $planId, $name, $slug, $price, $duration_days, $event_limit, $registrant_limit, $user_limit, $description, $is_active, $is_popular;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:subscription_plans,slug',
        'price' => 'required|numeric|min:0',
        'event_limit' => 'required|integer',
        'registrant_limit' => 'required|integer',
        'user_limit' => 'required|integer',
    ];

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function create()
    {
        $this->reset(['planId', 'name', 'slug', 'price', 'duration_days', 'event_limit', 'registrant_limit', 'user_limit', 'description', 'is_active', 'is_popular']);
        $this->event_limit = 1;
        $this->duration_days = 30;
        $this->registrant_limit = 100;
        $this->user_limit = 2;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->price = $plan->price;
        $this->duration_days = $plan->duration_days;
        $this->event_limit = $plan->event_limit;
        $this->registrant_limit = $plan->registrant_limit;
        $this->user_limit = $plan->user_limit;
        $this->description = $plan->description;
        $this->is_active = $plan->is_active;
        $this->is_popular = $plan->is_popular;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->planId) {
            $rules['slug'] = 'required|string|unique:subscription_plans,slug,' . $this->planId;
        }

        $this->validate($rules);

        SubscriptionPlan::updateOrCreate(
            ['id' => $this->planId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'price' => $this->price,
                'duration_days' => $this->duration_days,
                'event_limit' => $this->event_limit,
                'registrant_limit' => $this->registrant_limit,
                'user_limit' => $this->user_limit,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'is_popular' => $this->is_popular,
            ]
        );

        $this->showModal = false;
        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Saved',
            'text' => 'Subscription plan has been saved.',
        ]);
    }

    public function toggleStatus($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status updated for ' . $plan->name,
        ]);
    }

    public function render()
    {
        $plans = SubscriptionPlan::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.subscription-plan.index', [
            'plans' => $plans
        ])->layout('layouts.app');
    }
}
