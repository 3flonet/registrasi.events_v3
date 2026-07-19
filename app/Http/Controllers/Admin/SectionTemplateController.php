<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SectionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class SectionTemplateController extends Controller
{
    public function preview(SectionTemplate $template)
    {
        // Generate dummy data based on fields
        $fields = $template->fields ?? [];
        $dummyData = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            
            switch ($type) {
                case 'image':
                    $dummyData[$name] = 'https://placehold.co/1200x600/1a1235/ffffff?text=Preview+Image';
                    break;
                case 'textarea':
                    $dummyData[$name] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
                    break;
                default:
                    $dummyData[$name] = $field['label'] ?? 'Dummy Text';
            }
        }

        $htmlContent = $template->html_content;
        $cssContent = $template->css_content;

        // Render the template with dummy data
        $renderedHtml = Blade::render($htmlContent, $dummyData);
        $renderedCss = Blade::render($cssContent, $dummyData);

        return view('admin.section-template.preview', [
            'template' => $template,
            'html' => $renderedHtml,
            'css' => $renderedCss
        ]);
    }
}
