<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $phoneNumberId;
    protected $wabaId;
    protected $baseUrl = 'https://graph.facebook.com/v20.0';

    public function __construct($organizerId = null)
    {
        $this->resolveSettings($organizerId);
    }

    protected function resolveSettings($organizerId = null)
    {
        $token = null;
        $phoneNumberId = null;
        $wabaId = null;

        // 1. Tentukan token dari organizerId (misal dari Job)
        if ($organizerId) {
            $token = \App\Models\Setting::withoutGlobalScopes()
                ->where('organizer_id', $organizerId)
                ->where('key', 'whatsapp_business_token')
                ->first()?->value;
            $phoneNumberId = \App\Models\Setting::withoutGlobalScopes()
                ->where('organizer_id', $organizerId)
                ->where('key', 'whatsapp_phone_number_id')
                ->first()?->value;
            $wabaId = \App\Models\Setting::withoutGlobalScopes()
                ->where('organizer_id', $organizerId)
                ->where('key', 'whatsapp_waba_id')
                ->first()?->value;
        }

        // 2. Jika belum terisi, coba ambil dari scope tenant saat ini
        if (!$token || !$phoneNumberId) {
            $tenantService = app(\App\Services\TenantService::class);
            if ($tenantService->isTenantScope()) {
                $token = \App\Models\Setting::where('key', 'whatsapp_business_token')->first()?->value;
                $phoneNumberId = \App\Models\Setting::where('key', 'whatsapp_phone_number_id')->first()?->value;
                $wabaId = \App\Models\Setting::where('key', 'whatsapp_waba_id')->first()?->value;
            }
        }

        // 3. Fallback terakhir: Global setting
        if (!$token || !$phoneNumberId) {
            $token = \App\Models\Setting::withoutGlobalScopes()
                ->whereNull('organizer_id')
                ->where('key', 'whatsapp_business_token')
                ->first()?->value ?: env('META_WA_TOKEN');
            $phoneNumberId = \App\Models\Setting::withoutGlobalScopes()
                ->whereNull('organizer_id')
                ->where('key', 'whatsapp_phone_number_id')
                ->first()?->value ?: env('META_WA_PHONE_NUMBER_ID');
            $wabaId = \App\Models\Setting::withoutGlobalScopes()
                ->whereNull('organizer_id')
                ->where('key', 'whatsapp_waba_id')
                ->first()?->value ?: env('META_WA_WABA_ID');
        }

        $this->token = $token;
        $this->phoneNumberId = $phoneNumberId;
        $this->wabaId = $wabaId;
    }

    /**
     * Format phone number to international format (628...)
     */
    public function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        
        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        if (str_starts_with($number, '8') && strlen($number) >= 9 && strlen($number) <= 13) {
            return '62' . $number;
        }

        return $number;
    }

    /**
     * Kirim template message resmi via Meta WhatsApp Cloud API
     */
    public function sendTemplateMessage($to, $templateName, $languageCode, array $parameters)
    {
        if (!$this->token || !$this->phoneNumberId) {
            Log::error('Meta WhatsApp API Error: Token atau Phone Number ID belum dikonfigurasi.');
            return [
                'status' => false,
                'reason' => 'Configuration Missing'
            ];
        }

        try {
            $formattedPhone = $this->formatPhoneNumber($to);
            $endpoint = "{$this->baseUrl}/{$this->phoneNumberId}/messages";

            $components = [];

            // 1. Header Parameter (Document / Image / Video)
            if (isset($parameters['header']) && $parameters['header']) {
                $headerParam = $parameters['header'];
                $headerParam['value'] = self::resolveMediaUrl($headerParam['value'] ?? '');
                if ($headerParam['type'] === 'document') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => [
                                    'link' => $headerParam['value'],
                                    'filename' => $headerParam['filename'] ?? 'ETicket.pdf'
                                ]
                            ]
                        ]
                    ];
                } elseif ($headerParam['type'] === 'image') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $headerParam['value']
                                ]
                            ]
                        ]
                    ];
                } elseif ($headerParam['type'] === 'video') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'video',
                                'video' => [
                                    'link' => $headerParam['value']
                                ]
                            ]
                        ]
                    ];
                }
            }

            // 2. Body Parameters
            if (isset($parameters['body']) && !empty($parameters['body'])) {
                $bodyParams = [];
                foreach ($parameters['body'] as $val) {
                    $bodyParams[] = [
                        'type' => 'text',
                        'text' => (string) $val
                    ];
                }
                $components[] = [
                    'type' => 'body',
                    'parameters' => $bodyParams
                ];
            }

            // 3. Button Parameters
            if (isset($parameters['buttons']) && !empty($parameters['buttons'])) {
                foreach ($parameters['buttons'] as $btn) {
                    // Hanya kirim parameter jika tipe tombol adalah dynamic URL
                    $isUrl = isset($btn['type']) && $btn['type'] === 'url';
                    $isDynamic = isset($btn['url_type']) && $btn['url_type'] === 'dynamic';
                    
                    // Fallback untuk model tombol lama yang hanya menyimpan ['type' => 'url', 'value' => '...']
                    $shouldSend = $isUrl && (!isset($btn['url_type']) || $isDynamic);

                    if ($shouldSend) {
                        $components[] = [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => (string) ($btn['index'] ?? 0),
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => (string) ($btn['value'] ?? '') // Suffix URL
                                ]
                            ]
                        ];
                    }
                }
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode
                    ],
                    'components' => $components
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $payload);

            $result = $response->json();

            if (isset($result['messages'][0]['id'])) {
                return [
                    'status' => true,
                    'message_id' => $result['messages'][0]['id']
                ];
            }

            Log::error('Meta WhatsApp API Error response: ', $result);
            return [
                'status' => false,
                'reason' => $result['error']['message'] ?? 'Unknown Error'
            ];

        } catch (\Exception $e) {
            Log::error('Meta WhatsApp API Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'reason' => $e->getMessage()
            ];
        }
    }

    /**
     * Backward compatibility helper
     */
    public function sendMessage($to, $message)
    {
        Log::warning('Fonnte sendMessage dipanggil, dialihkan ke log karena Meta API hanya mendukung Template.');
        return [
            'status' => false,
            'reason' => 'Free-text message not supported in business-initiated Meta API. Use sendTemplateMessage.'
        ];
    }

    /**
     * Get Device / Account Info from Meta WABA (Optional dashboard info)
     */
    public function getDeviceStatus()
    {
        if (!$this->token || !$this->wabaId) {
            return [
                'connected' => false,
                'status_text' => 'Belum dikonfigurasi',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/{$this->wabaId}");

            $result = $response->json();

            if (isset($result['id'])) {
                return [
                    'connected' => true,
                    'status_text' => 'Aktif (Connected)',
                    'name' => $result['name'] ?? 'Akun Meta WA',
                    'device' => 'Meta Cloud API',
                    'package' => 'Official Cloud API',
                    'quota' => 'Unlimited',
                    'expired' => 'Permanent',
                ];
            }

            return [
                'connected' => false,
                'status_text' => $result['error']['message'] ?? 'Invalid Token/WABA ID',
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'status_text' => 'Koneksi Gagal',
            ];
        }
    }

    /**
     * Fetch all template statuses from Meta WABA
     */
    public function getMetaTemplatesStatus()
    {
        if (!$this->token || !$this->wabaId) {
            return [
                'status' => false,
                'reason' => 'Configuration Missing'
            ];
        }

        try {
            $endpoint = "{$this->baseUrl}/{$this->wabaId}/message_templates";
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get($endpoint, [
                'limit' => 1000,
                'fields' => 'id,name,status,category,language,rejected_reason'
            ]);

            $result = $response->json();

            if (isset($result['data'])) {
                $statuses = [];
                foreach ($result['data'] as $tpl) {
                    $statuses[$tpl['name']] = [
                        'status' => $tpl['status'] ?? 'PENDING',
                        'rejected_reason' => $tpl['rejected_reason'] ?? null
                    ];
                }
                return [
                    'status' => true,
                    'data' => $statuses
                ];
            }

            return [
                'status' => false,
                'reason' => $result['error']['message'] ?? 'Unknown Error'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'reason' => $e->getMessage()
            ];
        }
    }

    /**
     * Resolve App ID dynamically from access token
     */
    protected function getAppId()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/debug_token", [
                'input_token' => $this->token
            ]);

            if ($response->successful()) {
                return $response->json()['data']['app_id'] ?? null;
            }
            
            Log::error('Meta debug_token Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Meta getAppId Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload dynamic file URL to Meta to obtain a session upload handle (header_handle)
     */
    protected function getHeaderHandle($url)
    {
        try {
            $appId = $this->getAppId();
            if (!$appId) {
                Log::error('Meta Upload Failed: Could not resolve App ID from token.');
                return null;
            }

            $fileBytes = null;
            $mimeType = 'image/jpeg';

            // Check if it is a local storage file path
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            
            // Check if path starts with '/storage/'
            if (str_starts_with($path, '/storage/')) {
                $relativePath = substr($path, strlen('/storage/'));
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                    $fileBytes = \Illuminate\Support\Facades\Storage::disk('public')->get($relativePath);
                    $rawMime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($relativePath) ?: 'image/jpeg';
                    $mimeType = trim(explode(';', $rawMime)[0]);
                }
            }

            // Fallback to HTTP download if not found locally
            if (!$fileBytes) {
                $response = Http::get($url);
                if (!$response->successful()) {
                    Log::error('Meta Upload Fetch URL Failed: ' . $url);
                    return null;
                }
                $fileBytes = $response->body();
                $rawMime = $response->header('Content-Type') ?: 'application/pdf';
                $mimeType = trim(explode(';', $rawMime)[0]);
            }

            $fileSize = strlen($fileBytes);

            // 1. Initiate upload session using App ID
            $initEndpoint = "{$this->baseUrl}/{$appId}/uploads";
            $initResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($initEndpoint, [
                'file_length' => $fileSize,
                'file_type' => $mimeType,
            ]);

            if (!$initResponse->successful()) {
                Log::error('Meta Upload Init Session Failed: ' . $initResponse->body());
                return null;
            }

            $uploadId = $initResponse->json()['id'] ?? null;
            if (!$uploadId) {
                return null;
            }

            // 2. Upload the binary bytes
            $uploadEndpoint = "{$this->baseUrl}/{$uploadId}";
            $uploadResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'file_offset' => 0,
            ])->withBody($fileBytes, $mimeType)
              ->post($uploadEndpoint);

            if (!$uploadResponse->successful()) {
                Log::error('Meta Upload Binary Failed: ' . $uploadResponse->body());
                return null;
            }

            return $uploadResponse->json()['h'] ?? null;
        } catch (\Exception $e) {
            Log::error('Meta getHeaderHandle Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Submit local template structure to Meta Graph API
     */
    public function createTemplate($localTemplate)
    {
        if (!$this->token || !$this->wabaId) {
            return [
                'status' => false,
                'reason' => 'Configuration Missing (WABA ID or Token)'
            ];
        }

        $components = [];

        // 1. HEADER
        $params = $localTemplate->parameters ?? [];
        if (isset($params['header']['type']) && $params['header']['type'] !== 'none') {
            $format = strtoupper($params['header']['type']); // IMAGE, VIDEO, DOCUMENT
            $headerComp = [
                'type' => 'HEADER',
                'format' => $format,
            ];
            
            // Add example URL if we have a default value
            if (!empty($params['header']['value'])) {
                $rawVal = $params['header']['value'];
                if ($rawVal === 'ticket_pdf') {
                    $rawVal = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
                }
                $resolvedHeaderUrl = self::resolveMediaUrl($rawVal);
                if (filter_var($resolvedHeaderUrl, FILTER_VALIDATE_URL)) {
                    $handle = $this->getHeaderHandle($resolvedHeaderUrl);
                    if ($handle) {
                        $headerComp['example'] = [
                            'header_handle' => [$handle]
                        ];
                    }
                }
            }
            $components[] = $headerComp;
        }

        // 2. BODY
        $bodyText = $localTemplate->body_preview;
        $bodyComp = [
            'type' => 'BODY',
            'text' => $bodyText
        ];
        
        // Count variables {{1}}, {{2}}, etc.
        preg_match_all('/\{\{(\d+)\}\}/', $bodyText, $matches);
        if (!empty($matches[1])) {
            $placeholderCount = count(array_unique($matches[1]));
            $samples = [];
            for ($i = 1; $i <= $placeholderCount; $i++) {
                $samples[] = match($i) {
                    1 => 'Budi Santoso',
                    2 => 'Seminar Teknologi 2026',
                    3 => 'REG-12345',
                    4 => 'Auditorium Lantai 3',
                    default => 'Sample text'
                };
            }
            $bodyComp['example'] = [
                'body_text' => [$samples]
            ];
        }
        $components[] = $bodyComp;

        // 3. FOOTER
        if (!empty($params['footer'])) {
            $footerText = is_array($params['footer']) ? ($params['footer']['text'] ?? '') : $params['footer'];
            if (!empty($footerText)) {
                $components[] = [
                    'type' => 'FOOTER',
                    'text' => (string) $footerText
                ];
            }
        }

        // 4. BUTTONS
        if (!empty($params['buttons'])) {
            $metaButtons = [];
            foreach ($params['buttons'] as $btn) {
                if (($btn['type'] ?? '') === 'quick_reply') {
                    $metaButtons[] = [
                        'type' => 'QUICK_REPLY',
                        'text' => $btn['text'] ?? 'Saya Hadir'
                    ];
                } elseif (($btn['type'] ?? '') === 'url') {
                    $isDynamic = ($btn['url_type'] ?? 'static') === 'dynamic';
                    $btnText = $btn['text'] ?? 'Buka Website';
                    
                    if ($isDynamic) {
                        $metaButtons[] = [
                            'type' => 'URL',
                            'text' => $btnText,
                            'url' => 'https://registrasi.events/ticket/{{1}}',
                            'example' => ['sample-uuid']
                        ];
                    } else {
                        $staticUrl = $btn['static_url'] ?? 'https://registrasi.events';
                        if (!str_starts_with($staticUrl, 'http')) {
                            $staticUrl = 'https://' . $staticUrl;
                        }
                        // Detect and rewrite wa.me URL to avoid Meta's direct WhatsApp link policy restriction
                        if (str_contains($staticUrl, 'wa.me/')) {
                            $parts = explode('wa.me/', $staticUrl);
                            $phone = preg_replace('/[^0-9]/', '', end($parts));
                            // Use the main production domain (registrasi.events) to prevent Meta from instantly rejecting temporary free tunnel domains (like *.pinggy-free.link)
                            $staticUrl = 'https://registrasi.events/chat-wa/' . $phone;
                        }
                        $metaButtons[] = [
                            'type' => 'URL',
                            'text' => $btnText,
                            'url' => $staticUrl
                        ];
                    }
                }
            }
            if (!empty($metaButtons)) {
                $components[] = [
                    'type' => 'BUTTONS',
                    'buttons' => $metaButtons
                ];
            }
        }

        // Map Category to Meta supported values: UTILITY, MARKETING, AUTHENTICATION
        $metaCategory = strtoupper($localTemplate->meta_category ?? 'UTILITY');

        $sanitizedName = preg_replace('/[^a-z0-9_]/', '_', strtolower($localTemplate->name));
        $payload = [
            'name' => $sanitizedName,
            'category' => $metaCategory,
            'language' => $localTemplate->language_code ?? 'id',
            'components' => $components
        ];

        try {
            $endpoint = "{$this->baseUrl}/{$this->wabaId}/message_templates";
            
            Log::info('Meta Template Payload: ' . json_encode($payload));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $payload);

            $result = $response->json();
            Log::info('Meta Template Response: ' . json_encode($result));

            if ($response->successful()) {
                return [
                    'status' => true,
                    'meta_id' => $result['id'] ?? null,
                    'meta_status' => $result['status'] ?? 'PENDING'
                ];
            }

            return [
                'status' => false,
                'reason' => $result['error']['message'] ?? 'Unknown API Error'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'reason' => $e->getMessage()
            ];
        }
    }

    /**
     * Resolve saved media URL/path dynamically to current APP_URL
     */
    public static function resolveMediaUrl($value)
    {
        if (empty($value)) {
            return '';
        }

        // If it contains /storage/whatsapp_templates/
        if (preg_match('/storage\/(whatsapp_templates\/.*)/', $value, $matches)) {
            return asset('storage/' . $matches[1]);
        }

        // If it contains /storage/event-whatsapp-headers/
        if (preg_match('/storage\/(event-whatsapp-headers\/.*)/', $value, $matches)) {
            return asset('storage/' . $matches[1]);
        }

        // If it's a relative path starting with whatsapp_templates/
        if (str_starts_with($value, 'whatsapp_templates/')) {
            return asset('storage/' . $value);
        }

        // If it's a relative path starting with event-whatsapp-headers/
        if (str_starts_with($value, 'event-whatsapp-headers/')) {
            return asset('storage/' . $value);
        }

        return $value;
    }
}
