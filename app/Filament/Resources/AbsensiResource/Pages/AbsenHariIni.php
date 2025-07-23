<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Absensi;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AbsenHariIni extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.resources.absensi-resource.pages.absen-hari-ini';
    protected static ?string $title = 'Absen Hari Ini';

    protected static ?string $navigationLabel = 'Absen Hari Ini';
    protected static ?string $navigationGroup = 'Karyawan';


    public $latitude;
    public $longitude;
    public $absenHariIni;
    public $isTerlambat = false;
    public $bolehPulang = false;


    public function mount(): void
    {
        $this->latitude = null;
        $this->longitude = null;
        $user = auth()->user();
        $today = now()->toDateString();

        $this->absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $batasWaktu = now()->setTime(9, 15, 0); 
        $this->isTerlambat = now()->greaterThan($batasWaktu);

        if ($this->absenHariIni && $this->absenHariIni->jam_masuk) {
            $jamMasuk = \Carbon\Carbon::parse($this->absenHariIni->jam_masuk);
            $batasWaktuPulang = $jamMasuk->copy()->addHours(8);

            $this->bolehPulang = now()->greaterThanOrEqualTo($batasWaktuPulang);
        } else {
            $this->bolehPulang = false;
        }

        // if ($this->isTerlambat && !$this->absenHariIni) {
        //     $this->absenHariIni = Absensi::create([
        //         'user_id' => $user->id,
        //         'tanggal' => $today,
        //         'status' => 'hadir',
        //     ]);
        // }
    }

    public function absenMasuk()
    {
        if (!$this->latitude || !$this->longitude) {
            throw ValidationException::withMessages([
                'latitude' => 'Latitude dan Longitude wajib diisi.',
            ]);
        }

        $user = Auth::user();
        $today = Carbon::today();

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->jam_masuk) {
            Notification::make()->title('Sudah absen masuk hari ini')->danger()->send();
            return;
        }

        $now = now();
        $batasWaktu = now()->setTime(9, 15, 0);

        $keterlambatanMenit = 0;
        if ($now->greaterThan($batasWaktu)) {
            $keterlambatanMenit = $batasWaktu->diffInMinutes($now);
        }

        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            [
                'jam_masuk' => $now->format('H:i:s'),
                'lokasi_masuk_latitude' => $this->latitude,
                'lokasi_masuk_longitude' => $this->longitude,
                'status' => 'terlambat',
                'keterangan' => $keterlambatanMenit > 0 ? "Terlambat $keterlambatanMenit menit" : null,
            ]
        );

        $this->absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->status === 'alfa') {
            Notification::make()->title('Anda sudah dianggap alfa hari ini')->danger()->send();
            return;
        }

        if ($keterlambatanMenit > 0) {
            Notification::make()->title("Absen Masuk berhasil, Anda terlambat $keterlambatanMenit menit")->warning()->send();
        } else {
            Notification::make()->title('Absen Masuk berhasil')->success()->send();
        }
    }


    public function absenPulang()
    {
        if (!$this->latitude || !$this->longitude) {
            throw ValidationException::withMessages([
                'latitude' => 'Latitude dan Longitude wajib diisi.',
            ]);
        }

        $user = Auth::user();
        $today = Carbon::today();

        $absen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absen || !$absen->jam_masuk) {
            Notification::make()->title('Belum absen masuk')->danger()->send();
            return;
        }

        if ($absen->jam_keluar) {
            Notification::make()->title('Sudah absen pulang')->danger()->send();
            return;
        }

        $absen->update([
            'jam_keluar' => now()->format('H:i:s'),
            'lokasi_keluar_latitude' => $this->latitude,
            'lokasi_keluar_longitude' => $this->longitude,
        ]);

        $this->absenHariIni = Absensi::where('user_id', $user->id)
        ->whereDate('tanggal', $today)
        ->first();

        Notification::make()->title('Absen Pulang berhasil')->success()->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('akses-manager') || Gate::allows('akses-karyawan');
    }

}
