<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_messages', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Sender (User)
            $table->integer('sender_user_id')->nullable()->unsigned();
            $table->foreign('sender_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            
            // Link to the Receiver (User). Nullable for broadcast/system messages? Or group chat?
            $table->integer('receiver_user_id')->nullable()->unsigned();
            $table->foreign('receiver_user_id')->nullable()->references('id')->on('users')->nullOnDelete();

            $table->string('subject')->nullable();
            $table->text('body');
            $table->dateTime('sent_at');
            $table->dateTime('read_at')->nullable(); // Timestamp when the message was read
            $table->boolean('is_read')->default(false);

            // Optional: Link to a conversation if implementing grouped chats
            // $table->foreignId('conversation_id')->nullable()->constrained('hospital_conversations')->cascadeOnDelete();

            $table->timestamps(); // created_at and updated_at (these might represent message creation/update time)

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('sender_user_id');
            $table->index('receiver_user_id');
            $table->index('sent_at');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_messages');
    }
}