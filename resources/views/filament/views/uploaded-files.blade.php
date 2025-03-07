<div class="grid grid-cols-2 gap-4">
    @if($getState())


        <div class="col-span-1">
            @if (Str::endsWith($getState(), '.pdf'))
                <iframe src="{{ asset('storage/' . $getState()) }}" width="100%" height="400px"></iframe>
            @else
                <img src="{{ asset('storage/' . $getState()) }}" alt="Uploaded File" style="max-width: 100%;">
            @endif
        </div>

    @endif
</div>
