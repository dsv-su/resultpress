<div class="py-1 list-group list my-n3">
    @foreach ($notifications as $notification)
        <a
            href="{{ $notification->data['link'] }}" 
            class="list-group-item list-group-item-action list-group-item-light d-flex justify-content-between text-decoration-none
                @if ($notification->read_at)
                    list-group-item-dark
                    text-muted
                @else
                    list-group-item-light
                    font-weight-bold
                @endif
            "
            wire:click="markAsRead('{{ $notification->id }}')"
        >
            {{ $notification->data['message'] ?? 'Empty' }}
            <div class="text-right">
            @if (!$notification->read_at)
                <div class="badge badge-primary badge-pill" style="height: 22px;">New</div>
            @endif
                <div class="pl-2 text-muted font-size-xs font-weight-light text-nowrap">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
        </a>
    @endforeach
</div>
