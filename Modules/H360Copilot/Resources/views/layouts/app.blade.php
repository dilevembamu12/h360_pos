{{-- Dans la section <head> --}}
<link rel="stylesheet" href="{{ asset('modules/h360copilot/css/copilot.css') }}">

{{-- Juste avant </body> --}}
@include('h360copilot::partials.chatbot')
<script>var copilot_ask_url = "{{ route('h360_copilot.ask') }}";</script>
<script src="{{ asset('modules/h360copilot/js/copilot.js') }}"></script>