<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropConversationIdFromMessagesTable extends Migration
{
    public function up()
{
    Schema::table('messages', function (Blueprint $table) {
        // Drop foreign key first by its name (usually <table>_<column>_foreign)
        $table->dropForeign(['conversation_id']);
        // Then drop the column
        $table->dropColumn('conversation_id');
    });
}

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add the column back if you want to rollback
            $table->unsignedBigInteger('conversation_id')->nullable(); 
            // Adjust type and nullability to your original column definition
        });
    }
}
