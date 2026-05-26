<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Franchises
        Schema::create('franchises', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('owner_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('managed_by')->nullable();
            $table->date('agreement_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('state_id')->references('id')->on('territories');
            $table->foreign('managed_by')->references('id')->on('users')->nullOnDelete();
        });

        // Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->enum('property_type', ['villa', 'duplex', 'penthouse', 'apartment', 'bungalow', 'other'])->default('villa');
            $table->integer('num_floors')->default(2);
            $table->string('budget_range')->nullable();
            $table->enum('source', ['direct', 'franchise', 'referral', 'digital', 'walk_in', 'other'])->default('direct');
            $table->unsignedBigInteger('franchise_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('franchise_id')->references('id')->on('franchises')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        // Leads
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('assigned_to');
            $table->enum('stage', ['new','contacted','site_visit_scheduled','quotation_sent','negotiation','won','lost','on_hold'])->default('new');
            $table->datetime('follow_up_at')->nullable();
            $table->date('site_visit_date')->nullable();
            $table->string('lost_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('assigned_to')->references('id')->on('users');
        });

        // Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('family', ['orbit', 'apex', 'nova']);
            $table->string('variant');
            $table->string('description')->nullable();
            $table->integer('capacity_persons')->default(2);
            $table->string('door_type')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Product Add-ons
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(); // null = applies to all
            $table->string('name');
            $table->string('category'); // floors, finish, lighting, drive, installation, amc
            $table->decimal('price', 12, 2)->default(0);
            $table->string('unit')->default('lump_sum');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        // Quotations
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('product_id');
            $table->integer('version')->default(1);
            $table->json('configuration')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['draft','pending_bdm','pending_zm','pending_sd','approved','rejected','revision_requested','expired','won','lost'])->default('draft');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });

        // Quotation Line Items
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->string('description');
            $table->string('category')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
        });

        // Approval History
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('role_level'); // bdm, zm, sd
            $table->enum('status', ['pending','approved','rejected','revision_requested'])->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
            $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
            $table->foreign('approver_id')->references('id')->on('users')->nullOnDelete();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('products');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('franchises');
    }
};
