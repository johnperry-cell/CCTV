public function up(): void
{
    Schema::create('repair_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('camera_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->boolean('is_repaired')->default(false);
        $table->timestamp('repair_start_timestamp')->nullable();
        $table->timestamp('repair_end_timestamp')->nullable();
        $table->timestamps();
    });
}
