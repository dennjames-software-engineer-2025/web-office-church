<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">LPJ dalam Proposal</h2>
            <p class="text-sm text-gray-500">Daftar semua LPJ yang pernah dikirim untuk proposal ini.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <a href="{{ route('proposals.show', $proposal) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            ← Kembali
                        </a>
                    </div>

                    <div class="mt-5">
                        <div class="text-sm text-gray-500">Proposal</div>
                        <div class="text-2xl font-semibold text-gray-900 break-words">{{ $proposal->judul }}</div>

                        <div class="mt-2 text-xs text-gray-600">
                            No Proposal: <span class="font-semibold">{{ $proposal->proposal_no ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if(($lpjs ?? collect())->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada LPJ</div>
                            <div class="text-sm text-gray-600 mt-1">LPJ yang dikirim akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">LPJ</th>
                                        <th class="px-4 py-3">Tanggal LPJ</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Tahap</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($lpjs as $lpj)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $lpj->lpj_name ?: ('LPJ #' . $lpj->id) }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    File: {{ $lpj->files?->count() ?? 0 }} • Dikirim: {{ optional($lpj->created_at)->format('d-m-Y | H:i') }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $lpj->lpj_date ? $lpj->lpj_date->format('d-m-Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $lpj->status }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $lpj->stage }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('lpj.show', [$proposal, $lpj]) }}"
                                                   class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
