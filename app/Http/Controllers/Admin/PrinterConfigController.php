<?php
// FILE: app/Http/Controllers/Admin/PrinterConfigController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrinterConfig;
use App\Services\PrinterService;
use Illuminate\Http\Request;

class PrinterConfigController extends Controller
{
    public function __construct(private PrinterService $printer)
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $configs = PrinterConfig::all();
        return view('admin.printer.index', compact('configs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'type'        => ['required','in:network,usb,file'],
            'host'        => ['nullable','string','max:255'],
            'port'        => ['nullable','integer','min:1','max:65535'],
            'usb_device'  => ['nullable','string'],
            'paper_width' => ['required','integer','in:58,80'],
            'auto_print'  => ['nullable','boolean'],
        ]);
        $data['auto_print'] = $request->boolean('auto_print');
        PrinterConfig::create($data);
        return back()->with('success', 'Printer configuration saved.');
    }

    public function update(Request $request, PrinterConfig $printerConfig)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'type'        => ['required','in:network,usb,file'],
            'host'        => ['nullable','string'],
            'port'        => ['nullable','integer'],
            'paper_width' => ['required','integer'],
            'auto_print'  => ['nullable','boolean'],
            'is_active'   => ['nullable','boolean'],
        ]);
        $data['auto_print'] = $request->boolean('auto_print');
        $data['is_active']  = $request->boolean('is_active');
        $printerConfig->update($data);
        return back()->with('success', 'Printer updated.');
    }

    public function testPrint(PrinterConfig $printerConfig)
    {
        $result = $this->printer->testPrint($printerConfig);
        if ($result) {
            return back()->with('success', 'Test page sent to printer!');
        }
        return back()->withErrors(['print' => 'Test print failed. Check connection.']);
    }

    public function destroy(PrinterConfig $printerConfig)
    {
        $printerConfig->delete();
        return back()->with('success', 'Printer removed.');
    }
}
