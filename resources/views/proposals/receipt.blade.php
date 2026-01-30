<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Penerimaan Proposal</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .wrap { width: 100%; }
        .center { text-align: center; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .subtitle { margin-top: 4px; color: #555; }
        .badge { display: inline-block; padding: 6px 10px; border: 1px solid #ddd; border-radius: 999px; font-size: 12px; }
        .box { border: 1px solid #ddd; padding: 14px; border-radius: 8px; margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 0; vertical-align: top; }
        .muted { color: #666; }
        .hr { height: 1px; background: #e5e5e5; margin: 14px 0; }
        .note { margin-top: 12px; padding: 10px; border: 1px solid #e8e8e8; border-radius: 8px; background: #fafafa; }
        ol { margin: 8px 0 0 18px; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="center">
        <p class="title">BUKTI PENERIMAAN PROPOSAL</p>
        <div class="subtitle">Web Office Church Management</div>

        <div style="margin-top: 10px;">
            <span class="badge">No Proposal: <b>{{ $proposal->proposal_no }}</b></span>
            <span class="badge" style="margin-left: 6px;">
                Tanggal Disetujui: <b>{{ optional($proposal->romo_approved_at)->format('d-m-Y H:i') }}</b>
            </span>
        </div>
    </div>

    <div class="box">
        <table>
            <tr>
                <td width="160" class="muted">Judul</td>
                <td><b>{{ $proposal->judul }}</b></td>
            </tr>
            <tr>
                <td class="muted">Bidang</td>
                <td>{{ $proposal->bidang?->nama_bidang ?? '-' }}</td>
            </tr>
            <tr>
                <td class="muted">Sie</td>
                <td>{{ $proposal->sie?->nama_sie ?? '-' }}</td>
            </tr>
            <tr>
                <td class="muted">Pengaju</td>
                <td>{{ $proposal->pengaju?->name ?? '-' }} ({{ $proposal->pengaju?->email ?? '-' }})</td>
            </tr>
            <tr>
                <td class="muted">Tujuan/Keterangan</td>
                <td style="white-space: pre-line;">{{ $proposal->tujuan }}</td>
            </tr>
        </table>

        @if(!empty($proposal->notes))
            <div class="hr"></div>
            <div class="muted"><b>Catatan</b></div>
            <div style="white-space: pre-line; margin-top: 6px;">{{ $proposal->notes }}</div>
        @endif

        <div class="note">
            <b>Perhatian:</b>
            <ol class="muted">
                <li>Dokumen ini merupakan persetujuan Romo Paroki dan tidak diperlukan tanda tangan. Silahkan melanjutkan prosedur ke Sistem Informasi Gereja Keuskupan Surabaya.</li>
                <li>Proposal fisik mohon disampaikan ke Romo Paroki untuk ditandatangan dan menjadi arsip Gereja paling lambat sebelum tanggal kegiatan dilaksanakan.</li>
            </ol>
        </div>
    </div>

    <div class="center muted" style="margin-top: 14px;">
        Dokumen ini dihasilkan otomatis oleh sistem.
    </div>

</div>
</body>
</html>
