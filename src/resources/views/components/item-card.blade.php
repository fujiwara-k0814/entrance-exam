<a href="{{ request('page') === 'transaction'
    ? "/message/$item->id" 
    : "/item/$item->id" }}" class="item-detail__link">
    <div class="item-card">
        <div class="item-image__content">
            @if (request('page') === 'transaction' && $item->unread_messages_count > 0)
                <div class="notification-label__wrapper">
                    <div class="notification-label__content">
                        <span class="notification-label">{{ $item->unread_messages_count }}</span>
                    </div>
                    <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
                </div>
            @elseif (request('page') !== 'transaction' && $item->delivery_address)
                <div class="sold-label__wrapper">
                    <div class="sold-label__content">
                        <span class="sold-label">Sold</span>
                    </div>
                    <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
                </div>
            @else
                <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
            @endif
        </div>
        <span class="item-name">{{ $item->name }}</span>
    </div>
</a>
