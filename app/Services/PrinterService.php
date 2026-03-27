<?php
// FILE: app/Services/PrinterService.php
namespace App\Services;

use App\Models\Order;
use App\Models\PrinterConfig;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    /**
     * Print an order receipt to the active printer.
     */
    public function printOrder(Order $order, bool $force = false): bool
    {
        if (!$force && $order->is_printed) {
            return true; // already printed
        }

        $config = PrinterConfig::where('is_active', true)->first();

        if (!$config) {
            Log::warning('PrinterService: No active printer configured.');
            return false;
        }

        if (!$config->auto_print && !$force) {
            return false;
        }

        try {
            $receipt = $this->buildReceipt($order, $config);
            $result  = $this->send($receipt, $config);

            if ($result) {
                $order->update(['is_printed' => true, 'printed_at' => now()]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error("PrinterService error: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Build ESC/POS byte string for the receipt.
     */
    private function buildReceipt(Order $order, PrinterConfig $config): string
    {
        $order->loadMissing('items','user','coupon');
        $width = $config->paper_width === 58 ? 32 : 48;

        // ESC/POS commands
        $ESC   = chr(0x1B);
        $GS    = chr(0x1D);
        $reset = $ESC . chr(0x40);          // Initialize
        $bold  = $ESC . chr(0x45) . chr(1); // Bold on
        $nobold= $ESC . chr(0x45) . chr(0); // Bold off
        $center= $ESC . 'a' . chr(1);       // Center align
        $left  = $ESC . 'a' . chr(0);       // Left align
        $cut   = $GS  . 'V' . chr(0x41) . chr(3); // Partial cut

        $buf  = $reset;
        $buf .= $center . $bold;
        $buf .= $this->line(config('app.name'), $width) . "\n";
        $buf .= $nobold;
        $buf .= $this->line('ORDER RECEIPT', $width) . "\n";
        $buf .= str_repeat('-', $width) . "\n";
        $buf .= $left;
        $buf .= "Order: {$order->order_number}\n";
        $buf .= "Date : {$order->created_at->format('d/m/Y H:i')}\n";
        $buf .= "Name : {$order->user->name}\n";
        $buf .= "Phone: " . ($order->user->phone ?? 'N/A') . "\n";
        $buf .= str_repeat('-', $width) . "\n";
        $buf .= $bold . $this->columns('Item', 'Qty', 'Price', $width) . $nobold . "\n";
        $buf .= str_repeat('-', $width) . "\n";

        foreach ($order->items as $item) {
            $buf .= $this->columns($item->name, (string)$item->quantity, number_format($item->subtotal, 2), $width) . "\n";
            if (!empty($item->selected_addons)) {
                foreach ($item->selected_addons as $addon) {
                    $buf .= "  + {$addon['name']} +" . number_format($addon['price'], 2) . "\n";
                }
            }
            if ($item->notes) $buf .= "  Note: {$item->notes}\n";
        }

        $buf .= str_repeat('-', $width) . "\n";
        $buf .= $this->columns('Subtotal', '', number_format($order->subtotal, 2), $width) . "\n";

        if ($order->discount_amount > 0) {
            $label = $order->coupon ? "Coupon ({$order->coupon->code})" : 'Discount';
            $buf  .= $this->columns($label, '', '-' . number_format($order->discount_amount, 2), $width) . "\n";
        }

        $buf .= str_repeat('=', $width) . "\n";
        $buf .= $bold . $this->columns('TOTAL', '', number_format($order->total, 2), $width) . $nobold . "\n";
        $buf .= str_repeat('=', $width) . "\n";
        $buf .= "Payment: Cash on Delivery\n";
        $buf .= "\n";

        if ($order->notes) {
            $buf .= "Notes: {$order->notes}\n\n";
        }

        $buf .= "Delivery Address:\n{$order->delivery_address}\n\n";
        $buf .= $center . "Thank you for your order!\n";
        $buf .= "\n\n\n";
        $buf .= $cut;

        return $buf;
    }

    /**
     * Send raw bytes to printer.
     */
    private function send(string $data, PrinterConfig $config): bool
    {
        return match($config->type) {
            'network' => $this->sendNetwork($data, $config->host, $config->port ?? 9100),
            'usb'     => $this->sendUsb($data, $config->usb_device),
            'file'    => $this->sendFile($data),
            default   => false,
        };
    }

    private function sendNetwork(string $data, string $host, int $port): bool
    {
        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$socket) {
            Log::error("Printer network error [{$errno}]: {$errstr}");
            return false;
        }
        fwrite($socket, $data);
        fclose($socket);
        return true;
    }

    private function sendUsb(string $data, ?string $device): bool
    {
        $device = $device ?: '/dev/usb/lp0';
        if (!file_exists($device) || !is_writable($device)) {
            Log::error("USB printer device {$device} not writable.");
            return false;
        }
        return file_put_contents($device, $data) !== false;
    }

    private function sendFile(string $data): bool
    {
        $path = storage_path('logs/print_output_' . now()->format('YmdHis') . '.bin');
        return file_put_contents($path, $data) !== false;
    }

    /**
     * Send a test page to a specific printer config.
     */
    public function testPrint(PrinterConfig $config): bool
    {
        $ESC  = chr(0x1B);
        $GS   = chr(0x1D);
        $data = $ESC . chr(0x40)        // Reset
              . $ESC . 'a' . chr(1)     // Center
              . "*** TEST PAGE ***\n"
              . config('app.name') . "\n"
              . now()->format('d/m/Y H:i:s') . "\n"
              . "Printer OK\n\n\n"
              . $GS . 'V' . chr(0x41) . chr(3); // Cut

        return $this->send($data, $config);
    }

    private function line(string $text, int $width): string
    {
        return str_pad($text, $width, ' ', STR_PAD_BOTH);
    }

    private function columns(string $col1, string $col2, string $col3, int $width): string
    {
        $c3w = 10;
        $c2w = 4;
        $c1w = $width - $c2w - $c3w;
        return str_pad(substr($col1, 0, $c1w - 1), $c1w)
             . str_pad($col2, $c2w)
             . str_pad($col3, $c3w, ' ', STR_PAD_LEFT);
    }
}
