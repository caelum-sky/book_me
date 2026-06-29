<x-dashboard-layout title="New Listing" active="listings" subheading="Create a listing and submit it for super-admin review.">
    <form method="POST" action="{{ route('owner.listings.store') }}" enctype="multipart/form-data" class="bm-form-card" style="max-width:820px;">
        @csrf

        @include('owner.listings._form', ['listing' => null, 'types' => $types])

        <button type="submit" class="bm-btn primary full">Create listing</button>
    </form>
</x-dashboard-layout>
