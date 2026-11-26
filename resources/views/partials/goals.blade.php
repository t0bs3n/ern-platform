{{-- The goals of the ERN, as in the mockup (data.json `goals`). Used on the start page and About Us. --}}
<div class="goals-list">
    @foreach ([
        ['globe', 'Connect Researchers Worldwide', 'Building bridges across institutions, disciplines, and continents.'],
        ['award', 'Rigor + Relevance', 'Advancing esports research with academic rigor and real-world impact.'],
        ['cap', 'Teach the Next Generation', 'Supporting emerging scholars and fostering educational initiatives.'],
        ['users', 'Promote Diversity & Inclusion', 'Creating an inclusive space for diverse perspectives and voices.'],
        ['hand', 'Link to Practitioners', 'Bridging the gap between research and industry application.'],
        ['compass', 'Help Newcomers', 'Guiding new researchers into the field of esports research.'],
        ['bulb', 'Tackle key challenges for esports and the digital society', 'Addressing critical issues facing esports and digital communities.'],
        ['pen', 'Drive innovation through interdisciplinary collaboration', 'Fostering cross-disciplinary partnerships to advance esports research.'],
    ] as [$icon, $heading, $text])
        <div class="goal-item"><div class="goal-item-content"><div class="goal-icon"><x-icon :name="$icon" /></div><div><h3>{{ $heading }}</h3><p>{{ $text }}</p></div></div></div>
    @endforeach
</div>
