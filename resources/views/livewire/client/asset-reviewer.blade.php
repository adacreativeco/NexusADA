<div style="animation: fadeSlideIn 0.3s ease-out; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #fff; font-family: 'Geist', sans-serif;">{{ $asset->name ?? 'Görsel Varlık İncelemesi' }}</h1>
            <p style="font-size: 13px; color: var(--text-secondary);">Görsel üzerine tıklayarak nokta atışı revizyon notu ekleyebilirsiniz.</p>
        </div>
        <a href="{{ route('client.projects') }}" class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">← Projelere Dön</a>
    </div>

    @if(session()->has('success'))
        <div style="margin-bottom: 20px; padding: 12px 16px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; color: #34d399; font-size: 13px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        {{-- Interactive Canvas Area --}}
        <div class="client-card" style="position: relative; overflow: hidden; display: flex; justify-content: center; align-items: center; background: #000; min-height: 480px; cursor: crosshair;"
             onclick="let rect = this.getBoundingClientRect(); let x = ((event.clientX - rect.left) / rect.width) * 100; let y = ((event.clientY - rect.top) / rect.height) * 100; @this.set('pinX', x.toFixed(1)); @this.set('pinY', y.toFixed(1));">
            <img src="{{ $asset->file_url ?? url('images/preview-1.png') }}" alt="{{ $asset->name ?? 'Asset' }}" style="max-width: 100%; max-height: 520px; object-fit: contain;">

            {{-- Render existing pins --}}
            @foreach($comments as $idx => $com)
                @if(!empty($com->metadata['pin_x']))
                    <div style="position: absolute; left: {{ $com->metadata['pin_x'] }}%; top: {{ $com->metadata['pin_y'] }}%; width: 24px; height: 24px; background: #6366f1; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; border: 2px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.5); transform: translate(-50%, -50%);">
                        {{ $idx + 1 }}
                    </div>
                @endif
            @endforeach

            {{-- Render active draft pin --}}
            @if($pinX !== null)
                <div style="position: absolute; left: {{ $pinX }}%; top: {{ $pinY }}%; width: 26px; height: 26px; background: #ef4444; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border: 2px solid #fff; box-shadow: 0 0 15px rgba(239,68,68,0.8); transform: translate(-50%, -50%);">
                    ★
                </div>
            @endif
        </div>

        {{-- Threaded Revision Feedback Sidebar --}}
        <div class="client-card" style="display: flex; flex-direction: column; height: 520px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 16px;">Geri Bildirimler & Notlar</h3>

            <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                @forelse($comments as $idx => $comment)
                    <div style="padding: 12px; background: var(--bg-hover, rgba(255,255,255,0.03)); border-radius: 8px; border: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-secondary); margin-bottom: 6px;">
                            <span style="font-weight: 700; color: #fff;">
                                @if(!empty($comment->metadata['pin_x']))
                                    <span style="display: inline-block; width: 16px; height: 16px; background: #6366f1; color: #fff; border-radius: 50%; text-align: center; line-height: 16px; font-size: 9px; margin-right: 4px;">{{ $idx + 1 }}</span>
                                @endif
                                {{ $comment->metadata['author_name'] ?? 'Kullanıcı' }}
                            </span>
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="font-size: 13px; color: var(--text-primary); margin: 0; line-height: 1.4;">{{ $comment->body }}</p>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-secondary); font-size: 13px; margin: auto;">
                        Henüz not eklenmemiş. Görsele tıklayarak ilk pini bırakabilirsiniz.
                    </div>
                @endforelse
            </div>

            {{-- New Comment Form --}}
            <form wire:submit.prevent="addPinComment" style="border-top: 1px solid var(--border); padding-top: 16px;">
                <div style="margin-bottom: 10px;">
                    <textarea wire:model.defer="commentText" rows="3" class="form-control" style="width: 100%; font-size: 13px; resize: none;" placeholder="{{ $pinX !== null ? 'Seçilen noktaya ait revizyon notunuz...' : 'Genel revizyon notunuz...' }}"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; font-size: 13px; padding: 10px;">
                    Notu Gönder
                </button>
            </form>
        </div>
    </div>
</div>
