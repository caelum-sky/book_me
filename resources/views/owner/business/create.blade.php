<x-layout title="Set Up Your Business">
    <div class="max-w-lg mx-auto px-4 py-16">
        <h1 class="text-2xl font-bold mb-1 text-white">Set up your business</h1>
        <p class="text-zinc-400 text-sm mb-8">This information will be reviewed by our team before you can publish listings.</p>

        <form method="POST" action="{{ route('owner.business.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-1 text-zinc-200">Business name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1 text-zinc-200">Description <span class="text-zinc-500 font-normal">(optional)</span></label>
                <textarea id="description" name="description" rows="4"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="contact_email" class="block text-sm font-medium mb-1 text-zinc-200">Contact email</label>
                    <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    @error('contact_email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium mb-1 text-zinc-200">Contact phone</label>
                    <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    @error('contact_phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1 text-zinc-200">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address') }}"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1 text-zinc-200">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    @error('city') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium mb-1 text-zinc-200">Province</label>
                    <input id="province" name="province" type="text" value="{{ old('province') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    @error('province') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-500 transition">
                Submit for review
            </button>
        </form>
    </div>
</x-layout>