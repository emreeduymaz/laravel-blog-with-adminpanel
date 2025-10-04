<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-700">Log Name</h3>
            <p class="text-sm text-gray-900">{{ $record->log_name }}</p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-700">Description</h3>
            <p class="text-sm text-gray-900">{{ $record->description }}</p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-700">Subject</h3>
            <p class="text-sm text-gray-900">
                {{ $record->subject_type ? class_basename($record->subject_type) : 'N/A' }}
                @if($record->subject_id)
                    (ID: {{ $record->subject_id }})
                @endif
            </p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-700">User</h3>
            <p class="text-sm text-gray-900">
                {{ $record->causer?->name ?? 'System' }}
                @if($record->causer)
                    ({{ $record->causer->email }})
                @endif
            </p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-700">Event</h3>
            <p class="text-sm text-gray-900">{{ $record->event ?? 'N/A' }}</p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-700">Date</h3>
            <p class="text-sm text-gray-900">{{ $record->created_at->format('M j, Y H:i:s') }}</p>
        </div>
    </div>
    
    @if($record->properties && $record->properties->count() > 0)
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-2">Properties</h3>
            <div class="bg-gray-50 rounded-md p-3">
                <pre class="text-xs text-gray-800 whitespace-pre-wrap">{{ json_encode($record->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif
</div>
