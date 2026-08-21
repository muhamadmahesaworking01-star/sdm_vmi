<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="text-muted mb-4">{{ $description }}</p>

        @isset($items)
            <div class="row g-3">
                @foreach ($items as $item)
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h6 class="mb-1">{{ $item['title'] }}</h6>
                            <p class="mb-0 text-muted small">{{ $item['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endisset
    </div>
</div>
