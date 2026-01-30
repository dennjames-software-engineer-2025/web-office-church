<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar LPJ</h2>
            <p class="text-sm text-gray-500">LPJ yang kamu submit / review akan muncul di sini.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($lpjs->isEmpty())
                    <div class="text-center py-10">
                        <div class="text-gray-900 font-semibold">Belum ada LPJ</div>
                        <div class="text-sm text-gray-600 mt-1">LPJ yang kamu submit / review akan muncul di sini.</div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3">Proposal</th>
                                    <th class="px-4 py-3">LPJ</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Tahap</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($lpjs as $lpj)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900">{{ $lpj->proposal?->judul ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                No Proposal: <span class="font-semibold text-gray-700">{{ $lpj->proposal?->proposal_no ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900 font-semibold">#{{ $lpj->id }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                File: {{ $lpj->files?->count() ?? 0 }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $lpj->status }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $lpj->stage }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('lpj.show', [$lpj->proposal, $lpj]) }}"
                                               class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $lpjs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
