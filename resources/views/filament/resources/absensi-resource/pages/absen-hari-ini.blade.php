<x-filament-panels::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">📍 Absen Hari Ini - {{ now()->format('d M Y') }}</h2>

        {{-- Status Absen --}}
        <div>
            @if ($absenHariIni)
                <x-filament::card class="bg-green-100 text-green-800">
                    <p><strong>Absen Masuk:</strong> {{ $absenHariIni->jam_masuk ?? '-' }}</p>
                    <p><strong>Absen Pulang:</strong> {{ $absenHariIni->jam_keluar ?? '-' }}</p>
                </x-filament::card>
            @else
                <x-filament::card class="bg-yellow-100 text-yellow-800">
                    Belum absen hari ini.
                </x-filament::card>
            @endif
        </div>

        {{-- Lokasi & Peta --}}
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="flex">
                <div class="flex items-center">
                    <label for="latitude" class="block text-sm font-medium text-gray-700 w-1/2">Latitude :</label>
                    <x-filament::input
                        wire:model.defer="latitude"
                        id="latitude"
                        readonly
                    />
                </div>

                <div class="flex items-center">
                    <label for="longitude" class="block text-sm font-medium text-gray-700 w-1/2">Longitude :</label>
                    <x-filament::input
                        wire:model.defer="longitude"
                        id="longitude"
                        readonly
                    />
                </div>
            </div>
            <div>
                <div id="map" class="rounded-lg h-64 border"></div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex flex-wrap gap-4">
            <x-filament::button
                color="success"
                wire:click="absenMasuk"
                :disabled="($absenHariIni && $absenHariIni->jam_masuk)"
            >
                Absen Masuk
            </x-filament::button>

            <x-filament::button
                color="danger"
                wire:click="absenPulang"
                :disabled="!$bolehPulang || $absenHariIni || $absenHariIni->jam_keluar"
            >
                Absen Pulang
            </x-filament::button>
        </div>
    </div>

    {{-- Map script tetap sama --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" ...></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" ... />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let map = L.map('map').setView([0, 0], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    const livewire = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

                    livewire.set('latitude', lat);
                    livewire.set('longitude', lng);

                    map.setView([lat, lng], 16);
                    L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Anda").openPopup();
                });
            }
        });
    </script>
</x-filament-panels::page>
