<a href="/item/{{ $item->id }}" class="item-detail__link">
    <div class="item-card">
        <div class="item-image__content">
            @if ($item->delivery_address)
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
