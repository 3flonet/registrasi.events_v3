<?php

namespace App\Livewire\Admin\Media;

use App\Models\Album;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MediaPicker extends Component
{
    public $showModal = false;
    public $selectedAlbumId;
    public $currentPath = '/';
    public $search = '';
    public $disk = 'google';
    public $currentSource = 'drive'; 
    public $breadcrumbs = [];

    #[On('open-media-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->refresh();
    }

    public function selectSource($source)
    {
        $this->currentSource = $source;
        $this->currentPath = '/';
        $this->selectedAlbumId = null;
        $this->refresh();
    }

    public function selectAlbum($id)
    {
        $this->selectedAlbumId = $id;
        $this->currentSource = 'db';
        $this->currentPath = '/';
    }

    public function navigate($path)
    {
        $this->currentSource = 'drive';
        $this->currentPath = $path;
        $this->selectedAlbumId = null;
        $this->refresh();
    }

    public function navigateUp()
    {
        if ($this->currentPath == '/' || empty($this->currentPath)) return;
        $parts = explode('/', $this->currentPath);
        array_pop($parts);
        $this->currentPath = implode('/', $parts) ?: '/';
        $this->refresh();
    }

    public function refresh()
    {
        $this->generateBreadcrumbs();
    }

    private function generateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        if (empty($this->currentPath) || $this->currentPath == '/') return;

        $parts = explode('/', $this->currentPath);
        $buildPath = '';
        foreach ($parts as $part) {
            if(empty($part)) continue;
            $buildPath .= ($buildPath == '' ? '' : '/') . $part;
            $this->breadcrumbs[] = ['name' => $part, 'path' => $buildPath];
        }
    }

    public function selectMedia($url)
    {
        $this->dispatch('media-selected', $url);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function getDriveContents($path)
    {
        try {
            $normalizedPath = $path == '/' ? '' : $path;
            $contents = Storage::disk($this->disk)->listContents($normalizedPath);
            
            $dirs = [];
            $files = [];
            
            foreach ($contents as $item) {
                $name = basename($item['path']);
                if ($item['type'] === 'dir') {
                    $dirs[] = [
                        'type' => 'dir',
                        'name' => $name,
                        'path' => $item['path'],
                        'id' => md5($item['path'])
                    ];
                } else {
                    $mime = $item['mime_type'] ?? '';
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                        $files[] = [
                            'type' => 'file',
                            'name' => $name,
                            'url' => route('admin.files.stream', ['path' => $item['path']]),
                            'thumb' => route('admin.files.stream', ['path' => $item['path']]),
                            'source' => 'drive',
                            'id' => md5($item['path'])
                        ];
                    }
                }
            }

            usort($dirs, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
            usort($files, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
            
            return array_merge($dirs, $files);
        } catch (\Exception $e) {
            Log::error('MediaPicker Drive Error: ' . $e->getMessage());
            return [];
        }
    }

    public function render()
    {
        $dbAlbums = Album::withCount('media')->latest()->get();
        
        $driveFolders = [];
        if ($this->currentSource == 'drive') {
            $rootContents = $this->getDriveContents('/');
            foreach($rootContents as $item) {
                if ($item['type'] == 'dir') $driveFolders[] = $item;
            }
        }

        $items = [];
        $displayTitle = 'Media Registry';

        if ($this->currentSource === 'db') {
            if ($this->selectedAlbumId) {
                $album = Album::find($this->selectedAlbumId);
                if ($album) {
                    $displayTitle = $album->name;
                    foreach($album->all_photos as $idx => $photo) {
                        $items[] = array_merge($photo, [
                            'type' => 'file',
                            'id' => 'db-' . ($photo['id'] ?? $idx)
                        ]);
                    }
                }
            } else {
                $displayTitle = 'Select Album';
                $items = []; // Ensure empty if no album selected in DB mode
            }
        } else {
            $displayTitle = $this->currentPath == '/' ? 'Cloud Root' : basename($this->currentPath);
            $items = $this->getDriveContents($this->currentPath);
        }

        if ($this->search) {
            $items = collect($items)->filter(function($item) {
                return str_contains(strtolower($item['name'] ?? ''), strtolower($this->search));
            })->toArray();
        }

        return view('livewire.admin.media.media-picker', [
            'dbAlbums' => $dbAlbums,
            'driveFolders' => $driveFolders,
            'items' => $items,
            'displayTitle' => $displayTitle
        ]);
    }
}
