<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

/**
 * One-time backfill of meta_description for the 13 real, existing blog
 * categories. Each description is grounded in that category's actual post
 * titles (not a generic template sentence) — see resources/views/blog/
 * category.blade.php, which used to build these inline before this column
 * existed.
 *
 * Safe to re-run: matches existing rows by slug and only fills the
 * description in, it doesn't create categories that aren't already there.
 */
class CategoryMetaDescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $descriptions = [
            'vatreshen-impuls-3-7-g' => 'Приказки за най-малките (3-7 г) за вътрешния глас, светлината в сърцето и разпознаване на собствените чувства.',
            'kompas-za-roditeli-3-7-g' => 'Приказки-компас за родители на деца 3-7 г — за съня, ревността и малките бури в детските емоции.',
            'roden-ot-oganya-3-7-g' => 'Приказки за деца 3-7 г за вътрешната искра, смелостта и тихия пламък на увереността в себе си.',
            'vatreshen-impuls-7-11-g' => 'Приказки от "Гората на мислите" за деца 7-11 г — за въпросите, съмненията и вътрешната мъдрост.',
            'vatreshen-impuls-11-15-g' => 'Приказки за тийнейджъри 11-15 г за маските на съвършенството и натиска на групата.',
            'kompas-za-roditeli-7-11g' => 'Приказки-компас за родители на деца 7-11 г — за грешките, връзката и невидимите нишки в семейството.',
            'kompas-za-roditeli-11-15g' => 'Приказки-компас за родители на тийнейджъри 11-15 г — за разбирателството и усещането, че всички те гледат.',
            'roden-ot-oganya-7-11g' => 'Приказки за деца 7-11 г за достатъчността, желанието да си пръв и намирането на собствен фокус.',
            'roden-ot-oganya-11-15g' => 'Приказки за тийнейджъри 11-15 г за собствения ритъм, съмнението и приемането на себе си.',
            'patyat-kam-vatreshnata-svoboda' => 'Приказки за възрастни за вътрешната свобода — за дълговете към себе си, бурите и следите, които оставяме.',
            'razgrashtane-na-vatreshnata-lekota' => 'Приказки и разсъждения за вътрешната лекота, простотата и мъдростта — вдъхновени от българската поезия и мит.',
            'ime-ili-neshto-poveche' => 'Приказка за името, идентичността и въпроса кое ни прави себе си.',
            'magiyata-na-temperamenta' => 'Приказки за четирите темперамента — холерик, флегматик, меланхолик и сангвиник — през детски образи.',
        ];

        foreach ($descriptions as $slug => $description) {
            PostCategory::where('slug', $slug)->update(['meta_description' => $description]);
        }
    }
}
