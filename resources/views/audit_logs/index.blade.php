@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">System Audit Trail</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Immutable security log of operations, status updates and user actions</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">User / Actor</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Target Entity</th>
                        <th class="py-3 px-4">Changes / Details</th>
                        <th class="py-3 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition font-mono">
                        <td class="py-3 px-4 text-slate-500">
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                        </td>
                        <td class="py-3 px-4 font-sans font-medium text-slate-800">
                            {{ $log->user->name ?? 'System Seeder' }}
                            <span class="block text-[10px] text-slate-400 font-sans">{{ $log->user->email ?? 'cli' }}</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-blue-700">
                            {{ $log->action }}
                        </td>
                        <td class="py-3 px-4 font-sans text-slate-600">
                            {{ $log->entity_type }} #{{ $log->entity_id }}
                        </td>
                        <td class="py-3 px-4 text-[11px] text-slate-600 max-w-xs truncate">
                            @if($log->new_values)
                                <code class="text-slate-700 bg-slate-100 px-1 py-0.5 rounded">{{ json_encode($log->new_values) }}</code>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-400">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">No audit logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

