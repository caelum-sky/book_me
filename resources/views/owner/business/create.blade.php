<x-dashboard-layout title="Set Up Your Business" active="business" subheading="Tell us about your business so an admin can review it.">
    <form method="POST" action="{{ route('owner.business.store') }}" class="bm-form-card" style="max-width:760px;">
        @csrf

        <div class="bm-field">
            <label for="name">Business name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="description">Description <span class="hint">(optional)</span></label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field-row">
            <div class="bm-field">
                <label for="contact_email">Contact email</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}">
                @error('contact_email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="bm-field">
                <label for="contact_phone">Contact phone</label>
                <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}">
                @error('contact_phone') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="bm-field">
            <label for="address">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address') }}">
            @error('address') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field-row">
            <div class="bm-field">
                <label for="city">City</label>
                <input id="city" name="city" type="text" value="{{ old('city') }}">
                @error('city') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="bm-field">
                <label for="province">Province</label>
                <input id="province" name="province" type="text" value="{{ old('province') }}">
                @error('province') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="bm-btn primary full">Submit for review</button>
    </form>
</x-dashboard-layout>
