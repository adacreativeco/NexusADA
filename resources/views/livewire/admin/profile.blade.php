<div>
    @section('title', 'Profilim')
    @section('breadcrumb', 'Profil')

    @if(session('message'))
        <div style="padding: 12px 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--nx-radius-md); color: #10b981; font-size: 14px; margin-bottom: 20px;">
            {{ session('message') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--nx-radius-md); color: #ef4444; font-size: 14px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 24px; max-width: 800px;">
        {{-- Temel Bilgiler (Görsel Maksatlı) --}}
        <div class="nx-card" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Profil Bilgileri</h3>
            </div>
            <div style="padding: 24px;">
                <p style="margin: 0 0 8px; font-size: 14px; color: var(--nx-text-primary);"><strong>Ad:</strong> {{ auth()->user()->name }}</p>
                <p style="margin: 0; font-size: 14px; color: var(--nx-text-primary);"><strong>E-posta:</strong> {{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Tehlikeli Bölge --}}
        <div class="nx-card" style="padding: 0; border: 1px solid rgba(239, 68, 68, 0.3);">
            <div style="padding: 20px 24px; border-bottom: 1px solid rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600; color: #ef4444;">Tehlikeli Bölge</h3>
            </div>
            <div style="padding: 24px;">
                <p style="margin: 0 0 16px; font-size: 13px; color: var(--nx-text-secondary); line-height: 1.5;">
                    Hesabınızı sildiğinizde, atanmış olduğunuz tüm görevler ve yaptığınız yorumlar anonimleştirilerek saklanacaktır. Bu işlem kesinlikle <strong>geri alınamaz</strong>. Eğer bir kiracı (tenant) yöneticisiyseniz öncelikle yöneticiliği başkasına devretmelisiniz.
                </p>

                <div x-data="{ showModal: false }">
                    <button @click="showModal = true" style="background: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: var(--nx-radius-md); font-weight: 600; font-size: 13px; cursor: pointer;">
                        Hesabımı Sil
                    </button>

                    {{-- Modal --}}
                    <div x-show="showModal" style="display: none;" x-transition>
                        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 99998;" @click="showModal = false"></div>
                        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 99999; pointer-events: none;">
                            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-lg); padding: 32px; width: 100%; max-width: 440px; pointer-events: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
                                <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 600; color: #ef4444;">Hesabı Sil</h3>
                                <p style="margin: 0 0 24px; font-size: 14px; color: var(--nx-text-secondary); line-height: 1.5;">
                                    Devam etmek için şifrenizi girmeli ve aşağıdaki kutuya tam olarak <strong style="color: var(--nx-text-primary);">HESABIMI SİL</strong> yazmalısınız.
                                </p>

                                <form action="{{ route('admin.account.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 8px; color: var(--nx-text-primary);">Mevcut Şifreniz</label>
                                        <input type="password" name="password" required class="nx-input" style="width: 100%;" placeholder="Şifrenizi girin">
                                    </div>

                                    <div style="margin-bottom: 24px;">
                                        <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 8px; color: var(--nx-text-primary);">Onay Metni</label>
                                        <input type="text" name="confirmation" required class="nx-input" style="width: 100%;" placeholder="HESABIMI SİL" pattern="HESABIMI SİL">
                                    </div>

                                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                        <button type="button" @click="showModal = false" class="nx-btn nx-btn-secondary" style="padding: 10px 16px;">Vazgeç</button>
                                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: var(--nx-radius-md); font-weight: 600; font-size: 13px; cursor: pointer;">
                                            Kalıcı Olarak Sil
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
