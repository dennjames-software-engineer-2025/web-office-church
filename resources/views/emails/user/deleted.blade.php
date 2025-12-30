@component('mail::message')
# Akun Anda Dihapus

Halo {{ $user->name }},

Kami informasikan bahwa akun Anda di **Web Office Gereja** telah **dihapus** oleh Admin.

@if($user->alasan_dihapus)
**Alasan penghapusan akun:**

> {{ $user->alasan_dihapus }}
@endif

Jika Anda merasa ini adalah kesalahan, silakan hubungi pengurus gereja atau admin Web Office.

Terima kasih atas pelayanan dan partisipasi Anda.

Salam hormat,  
**Admin Web Office Gereja**
@endcomponent
