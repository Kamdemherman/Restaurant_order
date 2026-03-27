{{-- FILE: resources/views/admin/printer/index.blade.php --}}
@extends('layouts.admin')
@section('title','Printer Configuration')
@section('content')
<div class="row g-4">
    {{-- Add / Edit Printer form --}}
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-4"><i class="bi bi-printer me-2 text-primary"></i>Add / Edit Printer</h6>
            <form method="POST" action="{{ route('admin.printer.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium">Printer Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Kitchen Printer" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Connection Type</label>
                    <select name="type" class="form-select" id="printerType" onchange="togglePrinterFields()">
                        <option value="network">Network (TCP/IP)</option>
                        <option value="usb">USB / Serial</option>
                        <option value="file">File (debug/test)</option>
                    </select>
                </div>
                <div id="networkFields">
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-medium">IP Address / Host</label>
                            <input type="text" name="host" class="form-control" placeholder="192.168.1.100">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-medium">Port</label>
                            <input type="number" name="port" class="form-control" value="9100" min="1" max="65535">
                        </div>
                    </div>
                </div>
                <div id="usbFields" style="display:none">
                    <div class="mb-3">
                        <label class="form-label fw-medium">USB Device Path</label>
                        <input type="text" name="usb_device" class="form-control" placeholder="/dev/usb/lp0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Paper Width</label>
                    <select name="paper_width" class="form-select">
                        <option value="80">80mm (standard)</option>
                        <option value="58">58mm (mini)</option>
                    </select>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="auto_print" id="auto_print" value="1" checked>
                    <label class="form-check-label" for="auto_print">Auto-print new orders</label>
                </div>
                <button type="submit" class="btn w-100" style="background:#e94560;color:#fff">
                    <i class="bi bi-plus-circle me-1"></i>Add Printer
                </button>
            </form>
        </div>

        {{-- ESC/POS Help --}}
        <div class="card p-4 mt-3">
            <h6 class="fw-bold mb-2">📝 Setup Guide</h6>
            <p class="small text-muted mb-2">For <strong>network printers</strong>: ensure the printer is on the same LAN and its IP is static.</p>
            <p class="small text-muted mb-2">For <strong>USB/serial</strong>: the web server user must have write access to the device (e.g. <code>chmod a+rw /dev/usb/lp0</code>).</p>
            <p class="small text-muted mb-0">ESC/POS compatible printers: Epson TM series, Star TSP series, BIXOLON, Sewoo.</p>
        </div>
    </div>

    {{-- Existing printers --}}
    <div class="col-lg-7">
        <h6 class="fw-bold mb-3">Configured Printers</h6>
        @forelse($configs as $config)
        <div class="card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="fw-bold mb-0">{{ $config->name }}</h6>
                    <small class="text-muted">
                        {{ strtoupper($config->type) }}
                        @if($config->type === 'network') – {{ $config->host }}:{{ $config->port }} @endif
                        @if($config->type === 'usb') – {{ $config->usb_device }} @endif
                        | {{ $config->paper_width }}mm paper
                    </small>
                </div>
                <span class="badge {{ $config->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $config->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                {{-- Test print --}}
                <form method="POST" action="{{ route('admin.printer.test', $config) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer me-1"></i>Test Print
                    </button>
                </form>

                {{-- Toggle active --}}
                <form method="POST" action="{{ route('admin.printer.update', $config) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $config->name }}">
                    <input type="hidden" name="type" value="{{ $config->type }}">
                    <input type="hidden" name="paper_width" value="{{ $config->paper_width }}">
                    <input type="hidden" name="is_active" value="{{ $config->is_active ? 0 : 1 }}">
                    <input type="hidden" name="auto_print" value="{{ $config->auto_print ? 1 : 0 }}">
                    <button class="btn btn-sm {{ $config->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        {{ $config->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                {{-- Delete --}}
                <form method="POST" action="{{ route('admin.printer.destroy', $config) }}"
                      onsubmit="return confirm('Remove this printer?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-printer-fill fs-1 mb-3 d-block opacity-25"></i>
            <p>No printers configured yet.<br>Add one using the form on the left.</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function togglePrinterFields() {
    const type = document.getElementById('printerType').value;
    document.getElementById('networkFields').style.display = type === 'network' ? '' : 'none';
    document.getElementById('usbFields').style.display    = type === 'usb'     ? '' : 'none';
}
</script>
@endpush
@endsection
