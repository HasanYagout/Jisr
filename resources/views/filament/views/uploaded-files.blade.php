<div class="grid grid-cols-2 gap-4">
    @foreach(json_decode($getState()) as $state)
        <div class="col-span-1">
            @if (Str::endsWith($state, '.pdf'))
                <iframe src="{{ asset('storage/uploads/' . $state) }}" width="100%" height="400px"></iframe>
            @else
                <img src="{{ asset('storage/uploads/' . $state) }}" alt="Uploaded File" style="max-width: 100%;">
            @endif
        </div>
    @endforeach
</div>
