@extends('layouts.app')

@section('title', 'Начало')
@section('meta_description', 'MindBloom — пространството, в което промяната намира своя път, във Варна с индивидуална терапия, групова терапия и уъркшопи.')

@section('header')
  @include('partials.header-home2')
@endsection

@section('content')
  <div class="mad-media-element" data-bg-image-src="{{ asset('images/1920x1064_img1.jpg') }}"
    style="background-image:url('{{ asset('images/1920x1064_img1.jpg') }}')">
    <div class="mad-media-inner">
      <div class="container">
        <style>
          .mad-hero-highlight {
            display: inline;
            line-height: 1.5;
            background: rgba(255, 255, 255, .55);
            border-radius: .375rem;
            padding: .03em .35em;
            box-decoration-break: clone;
            -webkit-box-decoration-break: clone;
          }
        </style>
        <div class="mad-pre-title" style="font-weight:700; margin-bottom:1.5rem;">
          <span class="mad-hero-highlight">Пространството, в което промяната намира своя път · MindBloom</span>
        </div>
        <h1 class="mad-title">
          <span class="mad-hero-highlight">Затвори очи за момент… <br /> и усети какво има отвъд шума на ежедневието.</span>
        </h1>
        <p class="content-element-6">
          <b><span class="mad-hero-highlight" style="font-family:'Marck Script', cursive;">Вътре в теб съществува пространство на спокойствие, мъдрост и възстановяване. Място, до което можеш да
            достигнеш, когато си позволиш да се вслушаш по-дълбоко в себе си.</span></b>
        </p>
        <a href="{{ route('contact') }}" class="btn btn-huge">Направи първата крачка</a>
      </div>
    </div>
  </div>

  <div class="mad-content no-pd">
    <div class="container-fluid">

      <div class="mad-section align-center mad-section--stretched mad-colorizer--scheme-color with-texture12">
        <div class="mad-pre-title">Здравейте</div>
        <h2 class="mad-title">Вашата история. <br /> Вашето ново начало…</h2>
        <div class="content-element-6 mad-text-medium">
          <p>Всяка истинска приказка започва с история, изпълнена с красиви глави - любов, успехи, срещи и сбъднати мечти. Понякога има страници, които бихме искали да пренапишем, както и такива, които дълго време, сме пазили заключени дълбоко в себе си.</p>
          <p>Вярваме, че дори най-сложната история може да намери своя красив смисъл, затова не можем да Ви покажем готова приказка…</p>
          <p><strong>Създаваме я заедно с вас.</strong></p>
          <p>С внимание към детайла и пространство, в което няма нужда да играете роля.</p>
          <p>Вашата история е уникална и заслужава индивидуален подход, създаден специално за вас. Понякога промяната започва с едно изречение, друг път с един спомен или с позволението да споделите.</p>
          <p><strong>Вашата история. Вашата глава.<br>
          Вашата приказка.</strong></p>
        </div>
        <a href="https://patreon.com/NellyGocheva?utm_medium=unknown&utm_source=join_link&utm_campaign=creatorshare_creator&utm_content=copyLink" class="btn btn-big" target="_blank" rel="noopener noreferrer">
          <i class="fab fa-patreon" style="margin-right:.5rem;"></i> Подкрепете ни
        </a>
      </div>

      @if ($services->isNotEmpty())
        <div class="mad-section mad-changing-bg with-overlay no-pd mad-section--stretched-content mad-colorizer--scheme-">
          <div class="mad-colorizer-bg-color">
            <div class="mad-entity-bg with-overlay" data-bg-image-src="{{ asset('images/entity_bg1.jpg') }}"></div>
          </div>
          <div class="mad-entities-wrap">
            <div class="mad-entities type-5 item-col-4 no-gutters">
              @foreach ($services as $service)
                <div class="mad-col">
                  <article class="mad-entity mad-change-bg" data-bg-img="{{ ($loop->index % 4) + 1 }}">
                    <div class="mad-entity-content">
                      <div class="mad-entity-header">
                        <h5 class="mad-entity-title">
                          <a href="{{ route('services.show', $service) }}">{{ $service->title }}</a>
                        </h5>
                        <p style="font-family:'Marck Script', cursive;">{{ $service->excerpt }}</p>
                        <a href="{{ route('services.show', $service) }}" class="mad-text-link">Прочети още</a>
                      </div>
                    </div>
                  </article>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      @if ($events->isNotEmpty())
        <div class="mad-section">
          <div class="mad-title-wrap align-center">
            <div class="mad-pre-title">Присъединете се</div>
            <h2 class="mad-title">Предстоящи събития</h2>
          </div>
          <div class="row vr-size-1 hr-size-1">
            @foreach ($events as $event)
              <div class="col-lg-4">
                <article class="mad-entity">
                  @if ($event->image)
                    <div class="mad-entity-media">
                      <a href="{{ route('events.show', $event) }}"><img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" /></a>
                    </div>
                  @endif
                  <div class="mad-entity-content">
                    <div class="mad-entity-date">
                      <span>{{ $event->starts_at?->format('d') }}</span>
                      <span>{{ $event->starts_at?->translatedFormat('M') }}</span>
                    </div>
                    <div class="mad-entity-header">
                      <h4 class="mad-entity-title">
                        <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                      </h4>
                      <p style="font-family:'Marck Script', cursive;">{{ $event->excerpt }}</p>
                      <div class="mad-entity-footer">
                        <a href="{{ route('events.show', $event) }}" class="mad-text-link">Прочети още</a>
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <div class="mad-section">
        <div class="row no-row-gap mad-column-reverse align-items-center">
          <div class="col-xl-6">
            <div class="mad-img-content">
              <div class="row justify-content-center">
                <div class="col-xxl-8">
                  <div class="mad-pre-title">накратко за нас</div>
                  <h2 class="mad-title">Защо MindBloom</h2>
                  <div class="mad-text-medium content-element-6">
                    <p>Понякога е нужно само едно решение, за да започне истинската промяна. Докато четете тези
                      редове, може би вече усещате желанието да оставите зад себе си всичко, което черпи от
                      енергията Ви — напрежението, съмненията и умората.</p>
                    <p>Тук ще бъдете посрещнати с разбиране, уважение и професионална подкрепа. В безопасна и
                      спокойна среда ще работим заедно, за да откриете вътрешните си ресурси, да възстановите
                      увереността си и да изградите по-спокоен и удовлетворяващ начин на живот.</p>
                    <p>Защото най-ценната промяна не идва отвън, а започва отвътре — в момента, когато избирате да
                      се доверите на себе си и да направите първата и най-важна крачка към промяната.</p>
                  </div>
                  <a href="{{ route('about') }}" class="btn style-2 btn-big">Прочетете за нас</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6">
            <div class="mad-img"><img src="{{ asset('images/880x808_img1.jpg') }}" alt="" /></div>
          </div>
        </div>
      </div>

      <div class="mad-section mad-section--stretched mad-colorizer--scheme-color-2 with-texture2">
        <div class="container">
          <div class="mad-testimonials with-quotes align-center">
            <div class="mad-grid mad-grid--cols-1 owl-carousel">
              <div class="mad-grid-item">
                <div class="mad-testimonial">
                  <div class="mad-testimonial-info">
                    <blockquote>
                      <p>„Не очаквах, че четенето на приказки да се превърне в терапия и да ми повлияе толкова силно. В началото просто четях и се опитвах да разбера какво общо има историята с мен, но започнах да разпознавам свои чувства, страхове и ситуации в героите. Някои неща, за които ми е трудно да говоря директно, сякаш станаха много по-лесни за осмисляне чрез приказките."</p>
                    </blockquote>
                  </div>
                  <div class="mad-author">
                    <div class="mad-author-info">
                      <span class="mad-author-name">— Клиент на MindBloom</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mad-grid-item">
                <div class="mad-testimonial">
                  <div class="mad-testimonial-info">
                    <blockquote>
                      <p>„Понякога човек влиза през една врата, без да знае, че зад нея го очаква нещо различно. Така започна и моето преживяване с приказките. С всяка среща думите сякаш отваряха малки вратички към местата в мен, които дълго бях оставяла незабележими, а историите вече не изглеждаха толкова страшни. Понякога е достатъчно да погледнеш на нея с други очи, а когато започнеш да го правиш, нещо вътре в теб тихичко се променя."</p>
                    </blockquote>
                  </div>
                  <div class="mad-author">
                    <div class="mad-author-info">
                      <span class="mad-author-name">— Клиент на MindBloom</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mad-grid-item">
                <div class="mad-testimonial">
                  <div class="mad-testimonial-info">
                    <blockquote>
                      <p>„За мен това не беше просто четене на приказка, а един много нежен начин да погледна себе си по-различен начин."</p>
                    </blockquote>
                  </div>
                  <div class="mad-author">
                    <div class="mad-author-info">
                      <span class="mad-author-name">— Клиент на MindBloom</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @if ($posts->isNotEmpty())
        <div class="mad-section">
          <div class="mad-title-wrap align-center">
            <div class="mad-pre-title">От блога</div>
            <h2 class="mad-title">Последни вдъхновяващи истории</h2>
          </div>
          <div class="mad-entities item-col-2">
            @foreach ($posts as $post)
              <div class="mad-col">
                <article class="mad-entity">
                  @if ($post->featured_image)
                    <div class="mad-entity-media">
                      <a href="{{ route('blog.show', [$post->category, $post]) }}"><img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" /></a>
                    </div>
                  @endif
                  <div class="mad-entity-content">
                    <div class="mad-entity-header">
                      <div class="mad-entity-tags">
                        @if ($post->category)
                          <span>{{ $post->category->name }}</span>
                        @endif
                        @if ($post->published_at)
                          <span>{{ $post->published_at->translatedFormat('j F Y') }}</span>
                        @endif
                      </div>
                      <h5 class="mad-entity-title">
                        <a href="{{ route('blog.show', [$post->category, $post]) }}">{{ $post->title }}</a>
                      </h5>
                      <p>{{ $post->excerpt }}</p>
                      <a href="{{ route('blog.show', [$post->category, $post]) }}" class="mad-text-link">Прочети още</a>
                    </div>
                  </div>
                </article>
              </div>
            @endforeach
          </div>
          <div class="align-center">
            <a href="{{ route('blog.index') }}" class="btn style-2 btn-big">Всички вдъхновяващи истории</a>
          </div>
        </div>
      @endif

    </div>
  </div>
@endsection
