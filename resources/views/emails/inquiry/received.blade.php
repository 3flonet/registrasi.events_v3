<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>New Inquiry Received!</h3>
    <p><strong>Type:</strong> {{ $submission->form->name }}</p>
    <p><strong>Date:</strong> {{ $submission->created_at->format('d M Y H:i') }}</p>

    @if($submission->category)
        <p><strong>Package Interest:</strong> {{ $submission->category->name }}</p>
    @endif
    
    @if($submission->agenda)
        <p><strong>Target Agenda:</strong> {{ $submission->agenda->title }}</p>
    @endif

    <h4>Submission Data:</h4>
    <table class="data-table">
        @foreach($submission->data as $key => $value)
            <tr>
                <th width="30%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                <td>
                    @if(is_array($value))
                        {{ json_encode($value) }}
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    
    <br>
    <p>
        <a href="{{ route('admin.inquiries.monitoring') }}">View details in Admin Dashboard</a>
    </p>
</body>
</html>
