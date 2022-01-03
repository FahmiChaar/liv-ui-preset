<script>
    window.appLocal = {!! json_encode(app()->getLocale()) !!};
    window.baseUrl = {!! json_encode(url('/')) !!};
</script>

<script src="{{ mix('js/app.js') }}" defer></script>