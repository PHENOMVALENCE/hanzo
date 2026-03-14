<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('name');
            $table->string('business_type')->nullable()->after('company_name'); // sole_trader, sme, corporation, ngo, government
            $table->string('industry')->nullable()->after('business_type');
            $table->integer('years_in_operation')->nullable()->after('industry');
            $table->string('website')->nullable()->after('years_in_operation');
            $table->json('sourcing_categories')->nullable()->after('website');
            $table->string('import_volume')->nullable()->after('sourcing_categories'); // under_5k, 5k_20k, 20k_100k, 100k_plus
            $table->string('hear_about')->nullable()->after('import_volume');
            $table->string('business_registration_path')->nullable()->after('hear_about');
            $table->string('import_license_path')->nullable()->after('business_registration_path');
            $table->string('tax_id')->nullable()->after('import_license_path');
            $table->text('admin_requested_info')->nullable()->after('approval_message');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_title', 'business_type', 'industry', 'years_in_operation',
                'website', 'sourcing_categories', 'import_volume', 'hear_about',
                'business_registration_path', 'import_license_path', 'tax_id', 'admin_requested_info',
            ]);
        });
    }
};
