<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('institution_id')->index();
            $table->string('member_staff_id')->index();
            $table->integer('loan_type');
            $table->string('month');
            $table->string('year');
            $table->date('date');
            $table->decimal('amount_loaned', 10, 2);
            $table->decimal('interest', 10, 2)->default(0.00);
            $table->decimal('amount_paid', 10, 2);
            $table->date('return_date')->nullable();
            $table->integer('added_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
