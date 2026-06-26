<x-dashboard-layout title="Business Settings" active="business" subheading="Manage your business profile and customer-facing details">

    <div class="bm-form-card" style="max-width:680px;">
        <form method="POST" action="{{ route('owner.business.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                <div style="width:64px;height:64px;border-radius:14px;overflow:hidden;background:var(--panel);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    @if($business->logoUrl())
                        <img src="{{ $business->logoUrl() }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-tertiary);"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/></svg>
                    @endif
                </div>
                <div>
                    <label for="logo" class="bm-btn secondary sm" style="cursor:pointer;display:inline-block;">Upload logo</label>
                    <input id="logo" name="logo" type="file" accept="image/*" style="display:none;" onchange="document.getElementById('logo-filename').textContent = this.files[0]?.name || ''">
                    <div id="logo-filename" style="font-size:11.5px;color:#a08bff;margin-top:6px;font-weight:600;"></div>
                    <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:2px;">PNG or JPG, square works best</div>
                </div>
            </div>

            <div class="bm-field">
                <label for="name">Business name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $business->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $business->description) }}</textarea>
            </div>

            <div class="bm-field-row">
                <div class="bm-field">
                    <label for="contact_email">Contact email</label>
                    <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $business->contact_email) }}">
                </div>
                <div class="bm-field">
                    <label for="contact_phone">Contact phone</label>
                    <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone', $business->contact_phone) }}">
                </div>
            </div>

            <div class="bm-field">
                <label for="address">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address', $business->address) }}">
            </div>

            <div class="bm-field-row">
                <div class="bm-field">
                    <label for="city">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $business->city) }}">
                </div>
                <div class="bm-field">
                    <label for="province">Province</label>
                    <input id="province" name="province" type="text" value="{{ old('province', $business->province) }}">
                </div>
            </div>

            <div style="border-top:1px solid var(--border);margin:20px 0 18px;padding-top:18px;">
                <h3 style="font-size:14px;font-weight:700;margin:0 0 14px;">Storefront customization</h3>
                <div class="bm-field-row">
                    <div class="bm-field">
                        <label for="theme_color">Theme color</label>
                        <input id="theme_color" name="design_settings[theme_color]" type="color"
                            value="{{ old('design_settings.theme_color', $business->design_settings['theme_color'] ?? '#7c5cff') }}"
                            style="height:42px;padding:4px;cursor:pointer;">
                    </div>
                    <div class="bm-field">
                        <label for="banner_text">Banner text</label>
                        <input id="banner_text" name="design_settings[banner_text]" type="text"
                            value="{{ old('design_settings.banner_text', $business->design_settings['banner_text'] ?? '') }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="bm-btn primary full">Save changes</button>
        </form>
    </div>

</x-dashboard-layout>