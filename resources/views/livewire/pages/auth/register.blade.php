<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Mail\UserRegistered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use PragmaRX\Countries\Package\Countries;

// ======================================================
// MODEL BARU DITAMBAHKAN
// ======================================================
use App\Models\Country as InstitutionCountry;
use App\Models\State as InstitutionState;
use App\Models\City as InstitutionCity;
use Illuminate\Database\Eloquent\Collection;

new #[Layout('layouts.guest')] class extends Component
{

    protected $messages = [
        'consent_ehef_info.accepted' => 'The consent field must be accepted.',
        'consent_embassy_info.accepted' => 'The consent field must be accepted.',
        'consent_sponsor_info.accepted' => 'The consent field must be accepted.',
    ];

    // State untuk mengontrol langkah
    public int $currentStep = 1;

    // STEP 1: Personal Information
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $country = '';
    public string $city = '';
    public bool $is_in_indonesia = true;

    // STEP 2: Institution Information
    public string $type_institution = '';
    public string $other_institution = '';
    public string $media_type = '';
    public string $institution = '';
    public string $occupation = '';
    public string $institution_country = '';
    public string $institution_state = '';
    public string $institution_city = '';

    // ======================================================
    // PERUBAHAN TIPE DATA PROPERTI (String -> Nullable Int)
    // Properti ini sekarang akan menyimpan ID, bukan nama
    // ======================================================
    // Properti untuk menampung data dropdown (Array, bukan Collection DB)
    public array $allInstitutionCountries = [];
    public array $allInstitutionStates = [];
    public array $allInstitutionCities = [];


    // STEP 3: Account Information & Consent
    public string $info_source = '';
    public string $info_source_other = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $consent_ehef_info = false;
    public bool $consent_embassy_info = false;
    public bool $consent_sponsor_info = false;

    /**
     * Fungsi Mount: Dipanggil saat komponen pertama kali dimuat.
     * Kita ambil semua data negara di sini.
     */
    public function mount(): void
    {
        $this->allInstitutionCountries = (new \PragmaRX\Countries\Package\Countries)
            ->all()
            ->pluck('name.common')
            ->sortBy(fn($name) => $name)
            ->toArray();
        
        $this->allInstitutionStates = [];
        $this->allInstitutionCities = [];
    }

    public function updatedInstitutionCountry($value): void
    {
        if (empty($value)) {
            $this->allInstitutionStates = [];
            $this->institution_state = '';
            $this->institution_city = '';
            return;
        }

        try {
            // Gunakan koleksi yang sudah ada untuk mempercepat pencarian
            $all = (new \PragmaRX\Countries\Package\Countries)->all();
            
            $country = $all->filter(function($c) use ($value) {
                $cName = data_get($c, 'name.common') ?? data_get($c, 'name.default');
                return $cName === $value;
            })->first();

            if ($country) {
                // Ambil daftar provinsi/state
                $states = $country->hydrateStates()->states;
                
                if ($states) {
                    $this->allInstitutionStates = $states->map(function($state) {
                        $name = data_get($state, 'name');
                        return is_string($name) ? $name : (data_get($name, 'common') ?? data_get($name, 'default') ?? 'Unknown');
                    })
                    ->filter()
                    ->sort()
                    ->toArray();
                } else {
                    $this->allInstitutionStates = [];
                }
            } else {
                $this->allInstitutionStates = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching states: ' . $e->getMessage());
            $this->allInstitutionStates = [];
        }

        $this->institution_state = '';
        $this->institution_city = '';
        $this->allInstitutionCities = [];
    }

    /**
     * Hook: Saat Provinsi Institusi berubah.
     */
    public function updatedInstitutionState($value): void
    {
        if (empty($value) || empty($this->institution_country)) {
            $this->allInstitutionCities = [];
            $this->institution_city = '';
            return;
        }

        try {
            $all = (new \PragmaRX\Countries\Package\Countries)->all();
            
            $country = $all->filter(function($c) {
                $cName = data_get($c, 'name.common') ?? data_get($c, 'name.default');
                return $cName === $this->institution_country;
            })->first();

            if ($country) {
                $states = $country->hydrateStates()->states;
                $state = $states->filter(function($s) use ($value) {
                    $sName = data_get($s, 'name');
                    $sNameStr = is_string($sName) ? $sName : (data_get($sName, 'common') ?? data_get($sName, 'default') ?? '');
                    return $sNameStr === $value;
                })->first();

                // Dapatkan semua kota di negara ini sebagai fallback besar
                $allCities = $country->hydrateCities()->cities;

                if ($state && $allCities) {
                    $stateA2 = (string) data_get($state, 'a2');
                    $stateAdm1 = (string) data_get($state, 'adm1_code');

                    $this->allInstitutionCities = $allCities->filter(function($city) use ($stateA2, $stateAdm1) {
                        $cityState = (string) data_get($city, 'adm1_code');
                        $cityStateId = (string) data_get($city, 'state_id');

                        return ($stateA2 && ($cityState === $stateA2 || $cityStateId === $stateA2)) || 
                               ($stateAdm1 && ($cityState === $stateAdm1 || $cityStateId === $stateAdm1));
                    })
                    ->map(function($city) {
                        $name = data_get($city, 'name');
                        return is_string($name) ? $name : (data_get($name, 'common') ?? data_get($name, 'default') ?? 'Unknown');
                    })
                    ->filter(fn($name) => !empty($name) && $name !== 'Unknown')
                    ->unique()
                    ->sort()
                    ->toArray();
                } else {
                    $this->allInstitutionCities = [];
                }
            } else {
                $this->allInstitutionCities = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error filtering cities with fallback: ' . $e->getMessage());
            $this->allInstitutionCities = [];
        }

        $this->institution_city = '';
    }


    /**
     * Pindah ke langkah berikutnya
     */
    public function nextStep(): void
    {
        $this->validateStep($this->currentStep);
        $this->currentStep++;
    }

    /**
     * Kembali ke langkah sebelumnya
     */
    public function previousStep(): void
    {
        $this->currentStep--;
    }

    /**
     * Validasi data per langkah
     */
    public function validateStep(int $step): void
    {
        $rules = [];
        if ($step === 1) {
            $validCountries = (new \PragmaRX\Countries\Package\Countries)
                ->all()
                ->pluck('name.common')
                ->toArray();
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'phone_number' => ['required', 'string', 'max:20'],
                'date_of_birth' => ['required', 'numeric'],
                'gender' => ['required', 'string', Rule::in(['Male', 'Female', 'Prefer not to say'])],
                'country' => ['required', 'string', Rule::in($validCountries)],
                'city' => ['required', 'string', 'max:100'],
            ];
        } elseif ($step === 2) {
            // ======================================================
            // PERUBAHAN VALIDASI STEP 2 (Menggunakan ID dan exists)
            // ======================================================
            $rules = [
                'type_institution' => ['required', 'string', Rule::in(['Government', 'Company', 'Association', 'NGOs', 'Academic', 'Media', 'Other'])],
                'other_institution' => ['required_if:type_institution,Other', 'string', 'max:255'],
                'media_type' => ['required_if:type_institution,Media', 'string', 'max:255'],
                'institution' => ['required', 'string', 'max:255'],
                'occupation' => ['required', 'string', 'max:255'],
                'institution_country' => ['required', 'string'],
                'institution_state' => ['required', 'string'],
                'institution_city' => ['required', 'string'],
            ];
            // ======================================================
            // BATAS AKHIR PERUBAHAN VALIDASI
            // ======================================================
        }

        $this->validate($rules);
    }

    /**
     * Handle registrasi di langkah terakhir
     */
    public function register(): void
    {
        // Validasi untuk langkah terakhir
        $this->validate([
            'info_source' => ['required', 'string'],
            'info_source_other' => ['required_if:info_source,Other', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'consent_ehef_info' => ['accepted'],
            'consent_embassy_info' => ['accepted'],
            'consent_sponsor_info' => ['accepted'],
        ]);

        // Kumpulkan data profil tambahan
        $profileData = [
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'country' => $this->country,
            'city' => $this->city,
            'is_in_indonesia' => $this->is_in_indonesia,

            // Data Step 2 Baru
            'type_institution' => $this->type_institution,
            'other_institution' => $this->other_institution,
            'media_type' => $this->media_type,
            'institution' => $this->institution,
            'occupation' => $this->occupation,
            // Simpan NAMA, bukan ID, agar data JSON mudah dibaca
            'institution_country' => $this->institution_country,
            'institution_state' => $this->institution_state,
            'institution_city' => $this->institution_city,

            // Data Step 3 (Info Source)
            'info_source' => $this->info_source,
            'info_source_other' => $this->info_source_other,

            // Data Step 3 (Consent)
            'consents' => [
                'ehef' => $this->consent_ehef_info,
                'embassy' => $this->consent_embassy_info,
                'sponsor' => $this->consent_sponsor_info,
            ],
        ];

        // Buat user dengan data utama dan data profil
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'password' => Hash::make($this->password),
            'profile_data' => $profileData, // Simpan data tambahan di sini
        ]);

        event(new Registered($user));
        Mail::to($user->email)->send(new UserRegistered($user));
        Auth::login($user);

        $this->dispatch('registration-successful', redirectUrl: route('dashboard'));
    }
}; ?>

{{-- ====================================================== --}}
{{-- ====================================================== --}}
{{-- BAGIAN HTML (BLADE)                                    --}}
{{-- ====================================================== --}}
{{-- ====================================================== --}}


<main class="flex-grow flex flex-col items-center justify-center px-4 pt-36 pb-10">
    <div class="w-full sm:max-w-2xl bg-white shadow-md overflow-hidden rounded-xl p-6">
        {{-- Judul dan Progress Bar --}}
        <div class="flex flex-col items-center mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mt-4 mb-2 text-center uppercase">
                {{ __('auth.register_visitor') }}
            </h2>
            <p class="text-gray-600 text-center text-sm mb-2">
                {{ __('auth.One Step Closer to Study in Europe') }} <br>
                {{ __('auth.Let ehef.id help you ‘get ready’ before you fly to Europe!') }}
            </p>
            <p class="text-gray-600 text-center text-sm">
                {{ __('auth.after_completing') }}
            </p>
            <hr class="w-1/2 border-t border-gray-300 my-4">
            <p class="text-gray-600 text-center text-sm mb-4">
                {{ __('auth.Step') }} {{ $currentStep }} {{ __('auth.from') }} 3
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($currentStep / 3) * 100 }}%"></div>
            </div>
        </div>


        <form wire:submit="register">

            {{-- STEP 1: Personal Information --}}
            @if ($currentStep === 1)
            <div wire:key="step-1">
                <h3 class="text-lg font-semibold mb-4">{{ __('auth.Personal_Information') }}</h3>

                <div>
                    <x-input-label for="name" :value="__('auth.full_name')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" :value="__('auth.email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="phone_number" :value="__('auth.phone_number')" />
                    <x-text-input wire:model="phone_number" id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" required />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="date_of_birth" value="{{ __('auth.age') }}" />
                    <x-text-input wire:model="date_of_birth" id="date_of_birth" class="block mt-1 w-full" type="number" name="date_of_birth" required />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="gender" value="{{ __('auth.Gender') }}" />
                    <select wire:model="gender" id="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        <option value="Male">{{ __('auth.Male') }}</option>
                        <option value="Female">{{ __('auth.Female') }}</option>
                        <option value="Prefer not to say">{{ __('auth.Prefer_not_to_say') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="country" value="{{ __('auth.Country') }}" />
                    <select wire:model="country" id="country" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        {{-- Loop untuk menampilkan semua negara dari package --}}
                        @foreach ( (new PragmaRX\Countries\Package\Countries)->all()->sortBy('name.common') as $country)
                        <option value="{{ $country->name->common }}">{{ $country->name->common }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="city" value="{{ __('auth.City_of_Residence') }}" />
                    <x-text-input wire:model="city" id="city" class="block mt-1 w-full" type="text" name="city" required />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>

            </div>
            @endif

            {{-- ====================================================== --}}
            {{-- ====================================================== --}}
            {{-- STEP 2: INSTITUTION INFORMATION (DIROMBAK TOTAL)     --}}
            {{-- Menghapus Alpine.js, menggunakan murni Livewire     --}}
            {{-- ====================================================== --}}
            {{-- ====================================================== --}}
            @if ($currentStep === 2)
            <div wire:key="step-2" x-data="{ type: @entangle('type_institution') }"> {{-- x-data HANYA untuk field kondisional --}}

                {{-- Judul Baru --}}
                <h3 class="text-lg font-semibold mb-4">{{ __('auth.institution_information') }}</h3>

                {{-- Institution Categories --}}
                <div class="mt-4">
                    <x-input-label for="type_institution" value="{{ __('auth.institution_category') }}" />
                    <select wire:model="type_institution" id="type_institution" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        <option value="Government">Government</option>
                        <option value="Company">Company</option>
                        <option value="Association">Association</option>
                        <option value="NGOs">Non-Government Organizations (NGOs)</option>
                        <option value="Academic">Academic</option>
                        <option value="Media">Media</option>
                        <option value="Other">{{ __('auth.Other_Level') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('type_institution')" class="mt-2" />
                </div>

                {{-- Other Institution (Conditional) --}}
                <div x-show="type === 'Other'" x-transition class="mt-4">
                    <x-input-label for="other_institution" value="{{ __('auth.please_specify') }}" />
                    <x-text-input wire:model="other_institution" id="other_institution" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('other_institution')" class="mt-2" />
                </div>

                {{-- Media Type (Conditional) --}}
                <div x-show="type === 'Media'" x-transition class="mt-4 space-y-2">
                    <x-input-label value="{{ __('auth.media_type') }}" />
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Newspaper" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Newspaper</span>
                    </label>
                    {{-- ... (tambahkan opsi radio lainnya jika perlu) ... --}}
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Magazine" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Magazine</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Radio" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Radio</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Tabloid" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Tabloid</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Television" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Television</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="media_type" type="radio" name="media_type" value="Online" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Online</span>
                    </label>
                    <x-input-error :messages="$errors->get('media_type')" class="mt-2" />
                </div>

                {{-- Institution Name --}}
                <div class="mt-4">
                    <x-input-label for="institution" value="{{ __('auth.institution_name') }}" />
                    <x-text-input wire:model="institution" id="institution" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('institution')" class="mt-2" />
                </div>

                {{-- Occupation --}}
                <div class="mt-4">
                    <x-input-label for="occupation" value="{{ __('auth.position') }}" />
                    <x-text-input wire:model="occupation" id="occupation" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                </div>

                {{-- ====================================================== --}}
                {{-- DROPDOWN DATABASE-DRIVEN BARU --}}
                {{-- ====================================================== --}}

                {{-- Institution Country --}}
                <div class="mt-4">
                    <x-input-label for="institution_country" value="{{ __('auth.institution_country') }}" />
                    <select wire:model.live="institution_country"
                        wire:key="select-country-{{ time() }}"
                        wire:loading.attr="disabled"
                        id="institution_country"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        @foreach ($allInstitutionCountries as $countryName)
                        <option value="{{ $countryName }}">{{ $countryName }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('institution_country')" class="mt-2" />
                </div>

                {{-- Institution State --}}
                <div class="mt-4">
                    <x-input-label for="institution_state" value="{{ __('auth.institution_state') }}" />
                    {{-- Menampilkan spinner saat data state sedang diambil --}}
                    <div wire:loading wire:target="institution_country" class="text-xs text-gray-500 mt-1">
                        Loading states...
                    </div>
                    <select wire:model.live="institution_state"
                        wire:key="select-state-{{ count($allInstitutionStates) }}"
                        wire:loading.attr="disabled"
                        id="institution_state"
                        @disabled(!$institution_country || empty($allInstitutionStates))
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100">
                        <option value="">{{ __('auth.Choose') }}</option>
                        @foreach ($allInstitutionStates as $stateName)
                        <option value="{{ $stateName }}">{{ $stateName }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('institution_state')" class="mt-2" />
                </div>

                {{-- Institution City --}}
                <div class="mt-4">
                    <x-input-label for="institution_city" value="{{ __('auth.institution_city') }}" />
                    
                    {{-- Spinner Loading --}}
                    <div wire:loading wire:target="institution_state" class="text-xs text-gray-500 mb-1">
                        Loading cities...
                    </div>

                    <div wire:loading.remove wire:target="institution_state">
                        @if(!empty($allInstitutionCities))
                        <select wire:model="institution_city"
                            wire:key="select-city-dropdown-{{ count($allInstitutionCities) }}"
                            wire:loading.attr="disabled"
                            id="institution_city"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-[42px]">
                            <option value="">{{ __('auth.Choose') }}</option>
                            @foreach ($allInstitutionCities as $cityName)
                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                            @endforeach
                        </select>
                        @else
                        {{-- Gunakan input standar dengan styling eksplisit agar terlihat sempurna --}}
                        <input wire:model="institution_city" 
                            id="institution_city" 
                            type="text" 
                            placeholder="Type your city name here..."
                            @disabled(empty($institution_state))
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-[42px] px-3 disabled:bg-gray-100 disabled:text-gray-500" />
                        @endif
                    </div>

                    <x-input-error :messages="$errors->get('institution_city')" class="mt-2" />
                </div>

            </div>
            @endif
            {{-- ====================================================== --}}
            {{-- BATAS AKHIR PERUBAHAN STEP 2                         --}}
            {{-- ====================================================== --}}


            {{-- STEP 3: Account Information & Consent --}}
            @if ($currentStep === 3)
            <div wire:key="step-3">
                <h3 class="text-lg font-semibold mb-4">{{ __('auth.Account_Information_Consent') }}</h3>

                <div class="mt-4" x-data="{ selectedInfoSource: @entangle('info_source') }">
                    <x-input-label for="info_source" value="{{ __('auth.info_source') }}" />
                    <select wire:model="info_source" id="info_source" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        <option value="Social Media">{{ __('auth.Social_Media') }}</option>
                        <option value="Website">{{ __('auth.Website') }}</option>
                        <option value="Friends/Family">{{ __('auth.Friends_Family') }}</option>
                        <option value="School/University">{{ __('auth.School_University') }}</option>
                        <option value="Other">{{ __('auth.Other_Source') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('info_source')" class="mt-2" />

                    <div x-show="selectedInfoSource === 'Other'" x-transition class="mt-4">
                        <x-input-label for="info_source_other" value="{{ __('auth.please_specify') }}" />
                        <x-text-input wire:model="info_source_other" id="info_source_other" class="block mt-1 w-full" type="text" name="info_source_other" />
                        <x-input-error :messages="$errors->get('info_source_other')" class="mt-2" />
                    </div>
                </div>

                <hr class="my-6">

                {{-- ====================================================== --}}
                {{-- PERBAIKAN LIHAT PASSWORD (Pola Exhibitor) --}}
                {{-- ====================================================== --}}

                {{-- Password Field --}}
                <div class="mt-4" x-data="{ showPassword: false }">
                    <x-input-label for="password" :value="__('auth.password_label')" />
                    <div class="relative">

                        {{-- Input type="password" (default) --}}
                        <div x-show="!showPassword">
                            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                                type="password"
                                name="password" required />
                        </div>
                        {{-- Input type="text" (saat terlihat) --}}
                        <div x-show="showPassword" style="display: none;">
                            <x-text-input wire:model="password" id="password_visible" class="block mt-1 w-full"
                                type="text"
                                name="password_visible" required />
                        </div>

                        {{-- Tombol Toggle Ikon --}}
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye" x-show="!showPassword"></i>
                            <i class="fas fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Password Confirmation Field --}}
                <div class="mt-4" x-data="{ showPassword: false }">
                    <x-input-label for="password_confirmation" :value="__('auth.password_confirmation')" />
                    <div class="relative">

                        {{-- Input type="password" (default) --}}
                        <div x-show="!showPassword">
                            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
                        </div>
                        {{-- Input type="text" (saat terlihat) --}}
                        <div x-show="showPassword" style="display: none;">
                            <x-text-input wire:model="password_confirmation" id="password_confirmation_visible" class="block mt-1 w-full"
                                type="text"
                                name="password_confirmation_visible" required />
                        </div>

                        {{-- Tombol Toggle Ikon --}}
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye" x-show="!showPassword"></i>
                            <i class="fas fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                {{-- ====================================================== --}}
                {{-- AKHIR PERBAIKAN LIHAT PASSWORD --}}
                {{-- ====================================================== --}}


                {{-- Consent Section --}}
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('auth.Consent') }}</h3>
                    <div class="space-y-4">
                        <label for="consent_ehef_info" class="flex items-start">
                            <input id="consent_ehef_info" type="checkbox" wire:model="consent_ehef_info" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">{{ __('auth.consent_ehef_info') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('consent_ehef_info')" class="mt-1" />

                        <label for="consent_embassy_info" class="flex items-start">
                            <input id="consent_embassy_info" type="checkbox" wire:model="consent_embassy_info" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">{{ __('auth.consent_embassy_info') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('consent_embassy_info')" class="mt-1" />

                        <label for="consent_sponsor_info" class="flex items-start">
                            <input id="consent_sponsor_info" type="checkbox" wire:model="consent_sponsor_info" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">{{ __('auth.consent_sponsor_info') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('consent_sponsor_info')" class="mt-1" />
                    </div>
                </div>
            </div>
            @endif

            {{-- Navigation Buttons --}}
            <div class="flex items-center justify-between mt-8">
                {{-- Tombol Login --}}
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('auth.already_registered') }}
                </a>

                <div class="flex items-center space-x-4">
                    {{-- Tombol Kembali --}}
                    @if ($currentStep > 1)
                    <x-secondary-button type="button" wire:click="previousStep">
                        {{ __('auth.Back') }}
                    </x-secondary-button>
                    @endif

                    {{-- Tombol Berikutnya --}}
                    @if ($currentStep < 3)
                        <x-primary-button type="button" wire:click="nextStep" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="nextStep">
                            {{ __('auth.Next_Step') }}
                        </span>
                        <span wire:loading wire:target="nextStep">
                            {{ __('auth.Validate') }}...
                        </span>
                        </x-primary-button>
                        @endif

                        {{-- Tombol Register (di langkah terakhir) --}}
                        @if ($currentStep === 3)
                        <x-primary-button class="ms-4" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="register">
                                {{ __('auth.register') }}
                            </span>
                            <span wire:loading wire:target="register">
                                {{ __('auth.loading') }}
                            </span>
                        </x-primary-button>
                        @endif
                </div>
            </div>
        </form>
    </div>
</main>