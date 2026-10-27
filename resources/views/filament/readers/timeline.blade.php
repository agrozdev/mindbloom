@php
  use App\Filament\Resources\Readers\Actions\SendReaderEmailAction;
  use App\Models\Review;

  $reader = $getRecord();
  $reads = $reader->storyReads()->with('post.category')->orderByDesc('last_seen')->get();
  $emails = $reader->emailLogs()->with(['post', 'sender'])->orderByDesc('sent_at')->get();
  $reviews = $reader->reviews()->with('post')->latest()->get();
  $cell = 'padding:.5rem .75rem; text-align:left; vertical-align:top; border-bottom:1px solid rgba(127,127,127,.2);';
@endphp

<div style="display:flex; flex-direction:column; gap:1.5rem;">
  <x-filament::section heading="Прочетени приказки">
    @if ($reads->isEmpty())
      <p style="opacity:.7;">Все още няма прочитания.</p>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.875rem;">
          <thead>
            <tr>
              <th style="{{ $cell }}">Приказка</th>
              <th style="{{ $cell }}">Статус</th>
              <th style="{{ $cell }}">Активно време</th>
              <th style="{{ $cell }}">Скрол</th>
              <th style="{{ $cell }}">Посещения</th>
              <th style="{{ $cell }}">Първо</th>
              <th style="{{ $cell }}">Последно</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($reads as $read)
              <tr>
                <td style="{{ $cell }}">
                  @if ($read->post)
                    <a href="{{ route('blog.show', [$read->post->category, $read->post]) }}" target="_blank" style="text-decoration:underline;">{{ $read->post->title }}</a>
                  @else
                    <em>изтрита приказка</em>
                  @endif
                </td>
                <td style="{{ $cell }}">
                  <x-filament::badge :color="match ($read->status) { 'read' => 'success', 'opened' => 'info', default => 'gray' }">{{ $read->statusLabel() }}</x-filament::badge>
                </td>
                <td style="{{ $cell }}">{{ SendReaderEmailAction::duration($read->max_seconds) }}</td>
                <td style="{{ $cell }}">{{ $read->max_scroll }}%</td>
                <td style="{{ $cell }}">{{ $read->visits }}</td>
                <td style="{{ $cell }}">{{ $read->first_seen?->format('d.m.Y H:i') }}</td>
                <td style="{{ $cell }}">{{ $read->last_seen?->format('d.m.Y H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </x-filament::section>

  <x-filament::section heading="Изпратени имейли">
    @if ($emails->isEmpty())
      <p style="opacity:.7;">Все още не са изпращани имейли.</p>
    @else
      <div style="display:flex; flex-direction:column; gap:.75rem;">
        @foreach ($emails as $email)
          <details style="border:1px solid rgba(127,127,127,.25); border-radius:.5rem; padding:.75rem 1rem;">
            <summary style="cursor:pointer; display:flex; flex-wrap:wrap; gap:.5rem 1rem; align-items:center;">
              <strong>{{ $email->subject }}</strong>
              <x-filament::badge :color="$email->status === 'sent' ? 'success' : 'danger'">{{ $email->status === 'sent' ? 'изпратен' : 'грешка' }}</x-filament::badge>
              <span style="font-size:.8125rem; opacity:.75;">
                {{ $email->sent_at?->format('d.m.Y H:i') }}
                @if ($email->sender) · {{ $email->sender->name }} @endif
                @if ($email->post) · „{{ $email->post->title }}“ @endif
              </span>
            </summary>
            @if ($email->error)
              <p style="color:#dc2626; font-size:.8125rem; margin:.75rem 0 0;">{{ $email->error }}</p>
            @endif
            <iframe title="Съдържание на имейла" sandbox="" srcdoc="{{ $email->body }}" style="width:100%; height:360px; margin-top:.75rem; border:1px solid rgba(127,127,127,.2); border-radius:.375rem; background:#fff;"></iframe>
          </details>
        @endforeach
      </div>
    @endif
  </x-filament::section>

  <x-filament::section heading="Отзиви">
    @if ($reviews->isEmpty())
      <p style="opacity:.7;">Няма отзиви.</p>
    @else
      <div style="display:flex; flex-direction:column; gap:.75rem;">
        @foreach ($reviews as $review)
          <div style="border:1px solid rgba(127,127,127,.25); border-radius:.5rem; padding:.75rem 1rem; font-size:.875rem;">
            <div style="display:flex; flex-wrap:wrap; gap:.5rem 1rem; align-items:center; margin-bottom:.5rem;">
              <span style="color:#E8A317; font-size:1.125rem;">{{ str_repeat('★', $review->rating) }}<span style="opacity:.3;">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
              <strong>{{ $review->post?->title ?? 'изтрита приказка' }}</strong>
              <x-filament::badge :color="match ($review->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ Review::STATUS_LABELS[$review->status] ?? $review->status }}</x-filament::badge>
              <span style="opacity:.75;">{{ $review->created_at->format('d.m.Y H:i') }} · {{ $review->can_publish ? 'може да се публикува' : 'без разрешение за публикуване' }}</span>
            </div>
            @if ($review->answer_feeling)
              <p style="margin:0 0 .25rem;"><strong>Какво почувствахте:</strong> {{ $review->answer_feeling }}</p>
            @endif
            @if ($review->answer_lesson)
              <p style="margin:0;"><strong>Послание / урок:</strong> {{ $review->answer_lesson }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </x-filament::section>
</div>
