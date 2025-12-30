@component('mail::message')
# Akun Disetujui

Halo {{ $user->name }},

Akun Anda di **Web Office Gereja** telah **disetujui**.

Sekarang Anda dapat login menggunakan email:

**{{ $user->email }}**

Silakan masuk ke aplikasi dan mulai menggunakan fitur sesuai peran Anda.

Terima kasih.

-- Admin Web Office --

@endcomponent
