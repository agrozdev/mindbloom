<p><strong>Име:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</p>
<p><strong>Имейл:</strong> {{ $data['email'] }}</p>
@if (!empty($data['phone']))
  <p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
@endif
@if (!empty($data['service']))
  <p><strong>Интерес към:</strong> {{ $data['service'] }}</p>
@endif
<p><strong>Съобщение:</strong></p>
<p>{{ $data['message'] }}</p>
