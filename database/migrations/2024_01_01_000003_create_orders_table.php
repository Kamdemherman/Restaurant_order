<?php
// FILE: 2024_01_01_000003_create_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('value', 8, 2);
            $table->decimal('min_order_amount', 8, 2)->default(0);
            $table->decimal('max_discount_amount', 8, 2)->nullable();
            $table->boolean('is_single_use')->default(true);
            $table->boolean('is_used')->default(false);
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('subtotal', 8, 2);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->text('delivery_address');
            $table->decimal('delivery_lat', 10, 8)->nullable();
            $table->decimal('delivery_lng', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->enum('payment_method', ['cash_on_delivery'])->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 8, 2);
            $table->json('selected_addons')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('printer_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['network', 'usb', 'file'])->default('network');
            $table->string('host')->nullable();
            $table->integer('port')->nullable()->default(9100);
            $table->string('usb_device')->nullable();
            $table->integer('paper_width')->default(80);
            $table->boolean('auto_print')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('gdpr_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['export', 'delete'])->default('export');
            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_requests');
        Schema::dropIfExists('printer_configs');
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
