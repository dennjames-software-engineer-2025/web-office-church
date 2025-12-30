@component('mail::message')
# Pendaftaran Ditolak

Halo {{ $user->name }},

Mohon maaf, pendaftaran akun Anda di **Web Office Gereja** telah **ditolak**.

@if($user->alasan_ditolak)
**Alasan penolakan:**

> {{ $user->alasan_ditolak }}
@endif

Anda dapat memperbaiki data sesuai alasan di atas dan **mendaftar kembali menggunakan email yang sama**.

Terima kasih.

-- Admin Web Office --

@endcomponent
