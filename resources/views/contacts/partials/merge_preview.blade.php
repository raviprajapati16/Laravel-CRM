<!-- resources/views/contacts/partials/merge_preview.blade.php -->
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Field</th>
                <th>Master Contact Value</th>
                <th>Merged Contact Value</th>
                <th>Result After Merge</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Name</td>
                <td>{{ $master->name }}</td>
                <td>{{ $merged->name }}</td>
                <td>{{ $master->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $master->email }}</td>
                <td>{{ $merged->email }}</td>
                <td>{{ $master->email }}</td>
            </tr>
            <!-- Add other fields similarly -->
            
            @foreach($master->customFields as $field)
            <tr>
                <td>{{ $field->name }}</td>
                <td>{{ $field->pivot->value }}</td>
                <td>{{ $merged->customFields->firstWhere('id', $field->id)?->pivot->value ?? '' }}</td>
                <td>{{ $field->pivot->value }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>