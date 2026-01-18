<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_tools', function (Blueprint $table) {
            $table->id();

            $table->string("gtm_id")->nullable();
            $table->string("ga4_measurement_id")->nullable();
            $table->string("google_ads_conversion_id")->nullable();
            $table->string("google_ads_conversion_label")->nullable();

            $table->string("pixel_id")->nullable();
            $table->string("pixel_api_token")->nullable();

            $table->string("clarity_id")->nullable();
            $table->string("bing_ads_uet_tag")->nullable();

            $table->string("tiktok_pixel_id")->nullable();

            $table->string("pinterest_tag_id")->nullable();

            $table->string("linkedin_insight_tag")->nullable();

            $table->string("twitter_pixel_id")->nullable();

            $table->string("snap_pixel_id")->nullable();

            $table->string("test_event_code")->nullable();

            $table->string("hotjar_id")->nullable();

            $table->text("custom_header_script")->nullable();
            $table->text("custom_footer_script")->nullable();

            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_tools');
    }
};
