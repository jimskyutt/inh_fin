<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add title column if it doesn't exist
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            
            // Add user_id column for the notification recipient
            if (!Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            
            // Add related_user_id for the user who triggered the notification
            if (!Schema::hasColumn('notifications', 'related_user_id')) {
                $table->unsignedBigInteger('related_user_id')->nullable()->after('user_id');
                $table->foreign('related_user_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Add post_id for post-related notifications
            if (!Schema::hasColumn('notifications', 'post_id')) {
                $table->unsignedBigInteger('post_id')->nullable()->after('related_user_id');
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            }
            
            // Add is_read column to track read status
            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('data');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('notifications', 'related_user_id')) {
                $table->dropForeign(['related_user_id']);
                $table->dropColumn('related_user_id');
            }
            
            if (Schema::hasColumn('notifications', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropColumn('post_id');
            }
            
            // Drop other columns
            $table->dropColumn(['title', 'is_read']);
        });
    }
};
