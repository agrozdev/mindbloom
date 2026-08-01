<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $services = [
            [
                'slug' => 'individualna-terapiya',
                'title' => 'Индивидуална терапия',
                'excerpt' => 'Лична, поверителна работа в удобно за вас време — пространство да разберете себе си и да откриете собствения си път напред.',
                'description' => 'Индивидуалните сесии са изградени около вас — вашия темп, вашата история и вашите цели. Съчетаваме различни терапевтични подходи в цялостен, holistic процес, насочен към причините, а не само към симптомите. Работим заедно, докато не откриете по-ясен и устойчив начин да живеете с себе си и с другите.',
            ],
            [
                'slug' => 'grupova-terapiya',
                'title' => 'Групова терапия',
                'excerpt' => 'Утвърден метод за работа върху себе си в подкрепяща група от 10–15 души — обратна връзка и опит, който трудно се постига сам.',
                'description' => 'В групата срещате различни гледни точки и получавате обратна връзка, каквато индивидуалната работа не може да предложи. Груповата динамика създава безопасно пространство за нови модели на общуване, разпознаване на повтарящи се модели и истинска промяна чрез споделения опит на участниците.',
            ],
            [
                'slug' => 'uyrkshopi',
                'title' => 'Уъркшопи сред природата',
                'excerpt' => 'Няколкодневни групови сесии извън София, сред природата — интензивна работа върху себе си в спокойна и подкрепяща среда.',
                'description' => 'Уъркшопите са потапящо преживяване извън обичайната среда — по няколко часа работа дневно, съчетани с почивка сред природата. Отдалечеността от ежедневието позволява по-дълбока и по-бърза работа върху темите, които ви вълнуват, заедно с настаняване и грижа за целия престой.',
            ],
        ];

        foreach ($services as $i => $service) {
            Service::create([
                ...$service,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        $events = [
            [
                'slug' => 'uyrkshop-sred-priroda',
                'title' => 'Уъркшоп сред природата',
                'location' => 'Извън София',
                'starts_at' => now()->addWeeks(2),
                'excerpt' => 'Тридневен уъркшоп сред природата — работа върху себе си, споделяне и почивка от ежедневието.',
            ],
            [
                'slug' => 'seminar-za-spravyane-sas-stresa',
                'title' => 'Семинар за справяне със стреса',
                'location' => 'Онлайн',
                'starts_at' => now()->addWeeks(4),
                'excerpt' => 'Практически семинар с техники за разпознаване и овладяване на стреса в ежедневието.',
            ],
            [
                'slug' => 'grupova-sreshta-za-podkrepa',
                'title' => 'Групова среща за подкрепа',
                'location' => 'MindBloom, София',
                'starts_at' => now()->addWeeks(6),
                'excerpt' => 'Отворена подкрепяща среща — споделяне на опит в приятелска и поверителна обстановка.',
            ],
        ];

        foreach ($events as $event) {
            Event::create([
                ...$event,
                'description' => $event['excerpt'].' Присъединете се към нас за вечер на споделяне и практика.',
                'is_active' => true,
            ]);
        }

        $posts = [
            [
                'slug' => 'razbirane-na-trevozhnostta',
                'title' => 'Разбиране на тревожността',
                'category' => 'Психично здраве',
            ],
            [
                'slug' => 'silata-na-osaznatoto-dishane',
                'title' => 'Силата на осъзнатото дишане',
                'category' => 'Осъзнатост',
            ],
            [
                'slug' => 'izgrazhdane-na-zdravoslovni-vrazki',
                'title' => 'Изграждане на здравословни връзки',
                'category' => 'Връзки',
            ],
        ];

        foreach ($posts as $post) {
            Post::create([
                ...$post,
                'excerpt' => $post['title'].' — въведение в темата и практични стъпки напред.',
                'body' => $post['title'].' — въведение в темата и практични стъпки напред.',
                'is_published' => true,
                'published_at' => now()->subDays(random_int(1, 30)),
            ]);
        }
    }
}
