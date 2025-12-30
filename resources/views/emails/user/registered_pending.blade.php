@component('mail::message')
# Pendaftaran Berhasil Diterima

Halo {{ $user->name }},

Terima kasih sudah mendaftar di **Web Office Gereja**.

Saat ini status akun Anda adalah **pending** dan sedang menunggu approval dari pengurus.

Anda akan mendapatkan email lagi ketika akun:
- **disetujui (approve)**, atau
- **ditolak (reject)** dengan alasan yang dijelaskan.

Terima kasih.

-- Admin Web Office --

@endcomponent