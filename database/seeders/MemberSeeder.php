<?php

namespace Database\Seeders;

use App\Enums\Role;
use Database\Seeders\Concerns\MakesTestUsers;
use Illuminate\Database\Seeder;

/**
 * Twenty invented members across countries and career stages. The first eight are the
 * example people of the mockup (docs/mockup/data.json, `people` and `me`), with the roles
 * the mockup's rights editor gives them. Every institution carries "(example)".
 */
class MemberSeeder extends Seeder
{
    use MakesTestUsers;

    /** @var list<array{string, string, string, string, string, string, bool, list<Role>}> first, last, institution, country, career stage, interests, listed, roles */
    private const MEMBERS = [
        ['Maja', 'Lindqvist', 'Nordic University (example)', 'Sweden', 'PhD student', 'Esports psychology, team communication, qualitative methods', true, [Role::Admin]],
        ['Rafael', 'Monteiro', 'Universidade do Sul (example)', 'Brazil', 'Researcher with a PhD', 'University esports, survey research', true, []],
        ['Ji-ho', 'Baek', 'Hanbit Institute of Technology (example)', 'South Korea', 'PhD student', 'Eye tracking, FPS performance', true, []],
        ['Amara', 'Okafor', 'University of the Midlands (example)', 'United Kingdom', 'Researcher with a PhD', 'Governance, integrity', true, [Role::DatabaseLead, Role::Reviewer]],
        ['Lukas', 'Brandt', 'Rhein University of Applied Sciences (example)', 'Germany', 'Professor', 'Esports management, sponsorship', true, [Role::ConferenceTeam, Role::FinalEditor]],
        ['Priya', 'Raman', 'Arena Performance Lab (example)', 'India', 'Practitioner in industry', 'Player health, coaching', true, []],
        ['Aiko', 'Tanabe', 'Kansai Gakuen University (example)', 'Japan', 'Researcher with a PhD', 'Grassroots esports, education', true, [Role::Reviewer]],
        ['Elif', 'Demir', 'Bosphorus Social Research Centre (example)', 'Turkey', 'PhD student', 'Fandom, streaming', true, []],
        ['Noah', 'Fischer', 'Alpine University (example)', 'Austria', 'Student', 'Esports law, player contracts', false, []],
        ['Chloé', 'Martin', 'Université de la Loire (example)', 'France', 'PhD student', 'Esports broadcasting, media studies', true, []],
        ['Mateo', 'Ruiz', 'Universidad de los Andes Australes (example)', 'Chile', 'Researcher with a PhD', 'Esports economics, labour markets', true, []],
        ['Hannah', 'Kowalski', 'Vistula Valley University (example)', 'Poland', 'Professor', 'Sport science, cognitive load', true, [Role::Editor]],
        ['Oliver', 'Bennett', 'Harbour City College (example)', 'Australia', 'Student', 'Women in esports, community building', false, []],
        ['Sofia', 'Rossi', 'Università del Lago (example)', 'Italy', 'Researcher with a PhD', 'Game studies, competitive design', true, [Role::LectureTeam]],
        ['Daniel', 'Mensah', 'Coastal University (example)', 'Ghana', 'PhD student', 'Esports in education, digital inclusion', true, []],
        ['Emma', 'Visser', 'Delta University of Applied Sciences (example)', 'Netherlands', 'Practitioner in industry', 'Tournament operations, event management', false, []],
        ['Lucas', 'Silva', 'Lisbon Institute of Media (example)', 'Portugal', 'Other', 'Esports journalism', true, []],
        ['Mei', 'Chen', 'Lakeside University (example)', 'Canada', 'Researcher with a PhD', 'Human–computer interaction, peripherals', true, []],
        ['Jonas', 'Berg', 'Fjord University College (example)', 'Norway', 'PhD student', 'Physical activity, health interventions', true, []],
        ['Grace', 'Wanjiru', 'Highlands University (example)', 'Kenya', 'Student', 'Esports policy, youth participation', false, []],
    ];

    public function run(): void
    {
        foreach (self::MEMBERS as [$first, $last, $institution, $country, $stage, $interests, $listed, $roles]) {
            $this->testUser([
                'first_name' => $first,
                'last_name' => $last,
                'email' => strtolower(str_replace(['é', ' '], ['e', ''], $first.'.'.$last)).'@example.test',
                'institution' => $institution,
                'country' => $country,
                'career_stage' => $stage,
                'interests' => $interests,
                'discord_name' => in_array($first, ['Maja', 'Rafael', 'Ji-ho'], true) ? strtolower($first).'.example' : null,
                'show_discord_in_directory' => in_array($first, ['Maja', 'Rafael'], true),
                'open_to_collaboration' => in_array($first, ['Maja', 'Rafael', 'Priya', 'Aiko', 'Mateo', 'Mei'], true),
                'listed_in_directory' => $listed,
            ], $roles);
        }
    }
}
