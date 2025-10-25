public function up(): void
{
    Schema::create('status_labels', function (Blueprint $table) {
        $table->id();
        $table->string('descriptions');
        $table->string('new_status');
        $table->string('camera_name');
        $table->boolean('is_critical')->default(false);
        $table->boolean('requires_replacement')->default(false);
        $table->string('warranty_implication');
        $table->timestamps();
    });
}
