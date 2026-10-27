<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gated stories, reading tracking and manual feedback emails.
     *
     * The new tables are forced to InnoDB (several existing tables are MyISAM)
     * so the cascades between them work. There is no FK to posts/users, which
     * may be MyISAM: Post cleans up its own rows when deleted.
     */
    public function up(): void
    {
        Schema::create('readers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('token', 64)->unique();
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_version', 20)->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->unsignedBigInteger('signup_story_id')->nullable()->index();
            $table->timestamp('unsubscribed_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('story_reads', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('reader_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('post_id')->index();
            $table->string('status', 20)->default('visited')->index();
            $table->unsignedInteger('max_seconds')->default(0);
            $table->unsignedTinyInteger('max_scroll')->default(0);
            $table->unsignedInteger('visits')->default(0);
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->unique(['reader_id', 'post_id']);
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('reader_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->string('feedback_token', 64)->nullable()->unique();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->string('status', 20)->index();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('reader_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('post_id')->index();
            $table->foreignId('email_log_id')->nullable()->unique()->constrained('email_logs')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('answer_feeling')->nullable();
            $table->text('answer_lesson')->nullable();
            $table->boolean('can_publish')->default(false);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $this->seedTemplates();
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('story_reads');
        Schema::dropIfExists('readers');
    }

    /**
     * Seeded here rather than in a seeder, so `migrate --force` on live
     * creates them too.
     */
    private function seedTemplates(): void
    {
        $button = fn (string $label, string $href) => '<p><a href="'.$href.'"><strong>'.$label.'</strong></a></p>';
        $signature = '<p>С топлина,<br>Нели Гочева, MindBloom</p>';
        $now = now();

        DB::table('email_templates')->insert([
            [
                'name' => 'Как ви се стори приказката',
                'subject' => 'Как ви се стори „{приказка}“?',
                'body' => '<p>Здравейте, {име},</p>'
                    .'<p>Преди няколко дни прочетохте приказката „{приказка}“. Ще се радваме да ни кажете как ви се е сторила.</p>'
                    .'<p>Оценете с един клик: {звезди}</p>'
                    .'<p>Ако имате минутка, отговорете и на два въпроса:</p>'
                    .'<ul><li>Какво почувствахте, докато четяхте?</li><li>Какво послание или урок открихте в нея?</li></ul>'
                    .$button('Сподели мнението си →', '{линк_отзив}')
                    .$signature,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Нова приказка',
                'subject' => 'Нова приказка за вас, {име}',
                'body' => '<p>Здравейте, {име},</p>'
                    .'<p>Имаме нова приказка, която мислим, че ще ви хареса – „{приказка}“. Написахме я с много обич и вярваме, че ще открие път и до вашето сърце.</p>'
                    .'<p>Намерете си тихо кътче, чаша чай и няколко минути само за себе си.</p>'
                    .$button('Прочети приказката →', '{линк_приказка}')
                    .$signature,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Покана за консултация',
                'subject' => '{име}, искате ли да поговорим?',
                'body' => '<p>Здравейте, {име},</p>'
                    .'<p>Благодарим ви, че четете нашите приказки. Понякога една история докосва нещо, за което ни се иска да поговорим по-надълго.</p>'
                    .'<p>Ако усещате, че е така, ще се радвам да се срещнем на индивидуална консултация – на живо във Варна или онлайн, в спокойно и сигурно пространство, само за вас.</p>'
                    .$button('Запиши консултация →', rtrim((string) config('app.url'), '/').'/contact')
                    .'<p>Без никакъв натиск – когато и ако се почувствате готови.</p>'
                    .$signature,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
