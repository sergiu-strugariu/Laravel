<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('user_statuses')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->foreign('module_id')->references('id')->on('modules')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->foreign('paper_type_id')->references('id')->on('paper_types')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('language_paper_type_id')->references('id')->on('language_paper_type')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('question_choices', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('project_type_id')->references('id')->on('project_types')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('project_participants', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('project_participants', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('assessor_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('task_status_id')->references('id')->on('task_statuses')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('added_by_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('task_followers', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('task_followers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('task_assessors_history', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('task_assessors_history', function (Blueprint $table) {
            $table->foreign('assessor_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->foreign('paper_id')->references('id')->on('papers')
                ->onDelete('cascade') // !! important - to delete the paper answer when paper is deleted
                ->onUpdate('restrict');
        });

        Schema::table('paper_report', function (Blueprint $table) {
            $table->foreign('paper_id')->references('id')->on('papers')
                ->onDelete('cascade') // !! important - to delete the paper report when paper is deleted
                ->onUpdate('restrict');
        });

        Schema::table('paper_report', function (Blueprint $table) {
            $table->foreign('assessor_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('language_paper_type', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('language_paper_type', function (Blueprint $table) {
            $table->foreign('paper_type_id')->references('id')->on('paper_types')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::table('papers', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('task_statuses')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });


    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_status_id_foreign');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_client_id_foreign');
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign('permissions_module_id_foreign');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign('groups_language_id_foreign');
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropForeign('user_groups_user_id_foreign');
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropForeign('user_groups_group_id_foreign');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign('papers_task_id_foreign');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign('papers_paper_type_id_foreign');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_language_paper_type_id_foreign');
        });
        Schema::table('question_choices', function (Blueprint $table) {
            $table->dropForeign('question_choices_question_id_foreign');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_user_id_foreign');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_project_type_id_foreign');
        });
        Schema::table('project_participants', function (Blueprint $table) {
            $table->dropForeign('project_participants_project_id_foreign');
        });
        Schema::table('project_participants', function (Blueprint $table) {
            $table->dropForeign('project_participants_user_id_foreign');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_project_id_foreign');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_language_id_foreign');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_assessor_id_foreign');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_task_status_id_foreign');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_added_by_id_foreign');
        });
        Schema::table('task_assessors_history', function (Blueprint $table) {
            $table->dropForeign('task_id_foreign');
        });
        Schema::table('task_assessors_history', function (Blueprint $table) {
            $table->dropForeign('assessor_id_foreign');
        });
        Schema::table('task_followers', function (Blueprint $table) {
            $table->dropForeign('task_followers_task_id_foreign');
        });
        Schema::table('task_followers', function (Blueprint $table) {
            $table->dropForeign('task_followers_user_id_foreign');
        });
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->dropForeign('paper_answers_question_id_foreign');
        });
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->dropForeign('paper_answers_paper_id_foreign');
        });
        Schema::table('paper_reports', function (Blueprint $table) {
            $table->dropForeign('paper_reports_paper_id_foreign');
        });
        Schema::table('paper_reports', function (Blueprint $table) {
            $table->dropForeign('paper_reports_assessor_id_foreign');
        });
        Schema::table('language_paper_type', function (Blueprint $table) {
            $table->dropForeign('language_paper_type_language_id_foreign');
        });
        Schema::table('language_paper_type', function (Blueprint $table) {
            $table->dropForeign('language_paper_type_paper_type_id_foreign');
        });
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign('logs_task_id_foreign');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign('papers_status_id_foreign');
        });

    }
}