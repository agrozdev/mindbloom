<div style="display:flex; flex-direction:column; gap:.5rem;">
  <div style="font-size:.875rem;">
    <div><strong>До:</strong> {{ $reader->name }} &lt;{{ $reader->email }}&gt;</div>
    <div><strong>Тема:</strong> {{ $subject }}</div>
  </div>
  <iframe
    title="Преглед на имейла"
    sandbox=""
    srcdoc="{{ $html }}"
    style="width:100%; height:560px; border:1px solid rgba(127,127,127,.3); border-radius:.5rem; background:#F4F4F5;"
  ></iframe>
  <p style="font-size:.8125rem; opacity:.7; margin:0;">В прегледа връзката за отзив е примерна; при изпращане всеки имейл получава своя.</p>
</div>
