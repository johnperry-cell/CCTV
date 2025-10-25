public function up(): void
{
    Schema::create('status_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('camera_id')->constrained('cameras')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('status_label_id')->constrained('status_labels')->onDelete('cascade'); // Link to status_labels
        $table->string('reason');
        $table->dateTime('status_change_timestamp');
        $table->string('new_status');
        $table->string('urgency');
        $table->timestamps();
    });
}
